<?php
/**
 * NhomThuocController - Quản lý nhóm thuốc (MVC + RESTful API)
 */
class NhomThuocController extends Controller
{
    private $nhomThuocModel;

    public function __construct()
    {
        parent::__construct();
        $this->nhomThuocModel = $this->model('NhomThuocModel');
    }

    // ==================== RESTful API Methods ====================

    /**
     * GET /nhom-thuoc?format=json
     */
    public function index()
    {
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $nhomThuocs = $this->nhomThuocModel->getAll([], $limit, $offset);
            $total = $this->nhomThuocModel->count();
            
            $this->jsonPaginate($nhomThuocs, $total, $page, $limit);
        }
        
        $this->danhSach();
    }

    /**
     * GET /nhom-thuoc/{id}?format=json
     */
    public function show($id = null)
    {
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('nhomThuoc/danhSach');
        }

        $nhomThuoc = $this->nhomThuocModel->getById($id);
        
        if (!$nhomThuoc) {
            if ($this->isApi) $this->jsonError('Nhóm thuốc không tồn tại', 404);
            $this->setFlash('error', 'Không tìm thấy nhóm thuốc!');
            $this->redirect('nhomThuoc/danhSach');
        }

        if ($this->isApi) {
            $this->json($nhomThuoc, 'Chi tiết nhóm thuốc');
        }

        $this->chiTiet($id);
    }

    /**
     * POST /nhom-thuoc?format=json - Tạo nhóm thuốc (Admin)
     */
    public function store()
    {
        $this->requireAdmin();
        
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'TenNhomThuoc' => 'required|min:2|max:100'
        ]);
        
        if ($errors) {
            $this->jsonError('Validation failed', 422, $errors);
        }

        $id = $this->nhomThuocModel->create($input);
        
        if ($id) {
            $nhomThuoc = $this->nhomThuocModel->getById($id);
            $this->json($nhomThuoc, 'Tạo nhóm thuốc thành công', 201);
        }
        
        $this->jsonError('Không thể tạo nhóm thuốc', 500);
    }

    /**
     * PUT /nhom-thuoc/{id}?format=json - Cập nhật (Admin)
     */
    public function update($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $nhomThuoc = $this->nhomThuocModel->getById($id);
        if (!$nhomThuoc) $this->jsonError('Nhóm thuốc không tồn tại', 404);

        $input = $this->getJsonInput();

        $result = $this->nhomThuocModel->update($id, $input, 'MaNhomThuoc');
        
        if ($result) {
            $nhomThuoc = $this->nhomThuocModel->getById($id);
            $this->json($nhomThuoc, 'Cập nhật thành công');
        }
        
        $this->jsonError('Không thể cập nhật', 500);
    }

    /**
     * DELETE /nhom-thuoc/{id}?format=json - Xóa (Admin)
     */
    public function destroy($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $nhomThuoc = $this->nhomThuocModel->getById($id);
        if (!$nhomThuoc) $this->jsonError('Nhóm thuốc không tồn tại', 404);

        $result = $this->nhomThuocModel->delete($id, 'MaNhomThuoc');
        
        if ($result) {
            $this->json(null, 'Xóa nhóm thuốc thành công');
        }
        
        $this->jsonError('Không thể xóa', 500);
    }

    // ==================== Website Methods (HTML) ====================

    public function danhSach()
    {
        // Lấy nhóm cha (MaDanhMucCha = NULL)
        $stmt = $this->db->query("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc");
        $nhomChas = $stmt->fetchAll();

        // Đếm số sản phẩm cho mỗi nhóm
        foreach ($nhomChas as &$nhom) {
            // Lấy danh mục con
            $stmtCon = $this->db->prepare("SELECT MaNhomThuoc FROM nhom_thuoc WHERE MaDanhMucCha = ?");
            $stmtCon->execute([$nhom['MaNhomThuoc']]);
            $danhMucCon = $stmtCon->fetchAll(PDO::FETCH_COLUMN);

            // Đếm sản phẩm trực tiếp
            $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaNhomThuoc = ?");
            $stmtCount->execute([$nhom['MaNhomThuoc']]);
            $soLuong = $stmtCount->fetchColumn();

            // Đếm sản phẩm từ danh mục con
            if (!empty($danhMucCon)) {
                $placeholders = implode(',', array_fill(0, count($danhMucCon), '?'));
                $stmtCount = $this->db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaNhomThuoc IN ($placeholders)");
                $stmtCount->execute($danhMucCon);
                $soLuong += $stmtCount->fetchColumn();
            }

            $nhom['SoLuongSanPham'] = $soLuong;
            $nhom['DanhMucCon'] = $danhMucCon;
        }

        $data = [
            'title' => 'Danh mục thuốc - ' . STORE_NAME,
            'nhomThuocs' => $nhomChas
        ];

        $this->view('nhom-thuoc/danh-sach', $data);
    }

    public function chiTiet($id = null)
    {
        if (!$id) {
            $this->redirect('nhomThuoc/danhSach');
        }

        $page = (int)($_GET['page'] ?? 1);
        $sapXep = $_GET['sapXep'] ?? null;

        // Lấy thông tin nhóm thuốc
        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $nhomThuoc = $stmt->fetch();

        if (!$nhomThuoc) {
            $this->setFlash('error', 'Không tìm thấy nhóm thuốc!');
            $this->redirect('nhomThuoc/danhSach');
        }

        // Lấy danh mục con
        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha = ?");
        $stmt->execute([$id]);
        $danhMucCon = $stmt->fetchAll();

        // Lấy tất cả ID nhóm (bao gồm nhóm hiện tại và con)
        $nhomIds = [$id];
        foreach ($danhMucCon as $dm) {
            $nhomIds[] = $dm['MaNhomThuoc'];
        }

        // Lấy sản phẩm
        $placeholders = implode(',', array_fill(0, count($nhomIds), '?'));
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.MaNhomThuoc IN ($placeholders)";
        
        // Sắp xếp
        $sql .= match($sapXep) {
            'gia-tang' => " ORDER BY t.GiaBan ASC",
            'gia-giam' => " ORDER BY t.GiaBan DESC",
            'ten-az' => " ORDER BY t.TenThuoc ASC",
            'ten-za' => " ORDER BY t.TenThuoc DESC",
            'moi-nhat' => " ORDER BY t.NgayTao DESC",
            default => " ORDER BY t.MaThuoc DESC"
        };

        $stmt = $this->db->prepare($sql);
        $stmt->execute($nhomIds);
        $allThuocs = $stmt->fetchAll();

        // Phân trang
        $pageSize = 12;
        $totalItems = count($allThuocs);
        $totalPages = ceil($totalItems / $pageSize);
        $thuocs = array_slice($allThuocs, ($page - 1) * $pageSize, $pageSize);

        // Lấy danh mục cha nếu có
        $danhMucCha = null;
        if (!empty($nhomThuoc['MaDanhMucCha'])) {
            $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
            $stmt->execute([$nhomThuoc['MaDanhMucCha']]);
            $danhMucCha = $stmt->fetch();
        }

        $data = [
            'title' => $nhomThuoc['TenNhomThuoc'] . ' - ' . STORE_NAME,
            'nhomThuoc' => $nhomThuoc,
            'danhMucCha' => $danhMucCha,
            'danhMucCon' => $danhMucCon,
            'thuocs' => $thuocs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'sapXep' => $sapXep
        ];

        $this->view('nhom-thuoc/chi-tiet', $data);
    }
}
