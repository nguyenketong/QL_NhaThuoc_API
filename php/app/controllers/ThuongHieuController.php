<?php
/**
 * ThuongHieuController - Quản lý thương hiệu (MVC + RESTful API)
 */
class ThuongHieuController extends Controller
{
    private $thuongHieuModel;

    public function __construct()
    {
        parent::__construct();
        $this->thuongHieuModel = $this->model('ThuongHieuModel');
    }

    // ==================== RESTful API Methods ====================

    /**
     * GET /thuong-hieu?format=json
     */
    public function index()
    {
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $thuongHieus = $this->thuongHieuModel->getAll([], $limit, $offset);
            $total = $this->thuongHieuModel->count();
            
            $this->jsonPaginate($thuongHieus, $total, $page, $limit);
        }
        
        $this->danhSach();
    }

    /**
     * GET /thuong-hieu/{id}?format=json
     */
    public function show($id = null)
    {
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('thuongHieu/danhSach');
        }

        $thuongHieu = $this->thuongHieuModel->getById($id);
        
        if (!$thuongHieu) {
            if ($this->isApi) $this->jsonError('Thương hiệu không tồn tại', 404);
            $this->setFlash('error', 'Không tìm thấy thương hiệu!');
            $this->redirect('thuongHieu/danhSach');
        }

        if ($this->isApi) {
            $this->json($thuongHieu, 'Chi tiết thương hiệu');
        }

        $this->chiTiet($id);
    }

    /**
     * POST /thuong-hieu?format=json - Tạo thương hiệu (Admin)
     */
    public function store()
    {
        $this->requireAdmin();
        
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'TenThuongHieu' => 'required|min:2|max:100'
        ]);
        
        if ($errors) {
            $this->jsonError('Validation failed', 422, $errors);
        }

        $id = $this->thuongHieuModel->create($input);
        
        if ($id) {
            $thuongHieu = $this->thuongHieuModel->getById($id);
            $this->json($thuongHieu, 'Tạo thương hiệu thành công', 201);
        }
        
        $this->jsonError('Không thể tạo thương hiệu', 500);
    }

    /**
     * PUT /thuong-hieu/{id}?format=json - Cập nhật (Admin)
     */
    public function update($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $thuongHieu = $this->thuongHieuModel->getById($id);
        if (!$thuongHieu) $this->jsonError('Thương hiệu không tồn tại', 404);

        $input = $this->getJsonInput();

        $result = $this->thuongHieuModel->update($id, $input, 'MaThuongHieu');
        
        if ($result) {
            $thuongHieu = $this->thuongHieuModel->getById($id);
            $this->json($thuongHieu, 'Cập nhật thành công');
        }
        
        $this->jsonError('Không thể cập nhật', 500);
    }

    /**
     * DELETE /thuong-hieu/{id}?format=json - Xóa (Admin)
     */
    public function destroy($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $thuongHieu = $this->thuongHieuModel->getById($id);
        if (!$thuongHieu) $this->jsonError('Thương hiệu không tồn tại', 404);

        $result = $this->thuongHieuModel->delete($id, 'MaThuongHieu');
        
        if ($result) {
            $this->json(null, 'Xóa thương hiệu thành công');
        }
        
        $this->jsonError('Không thể xóa', 500);
    }

    // ==================== Website Methods (HTML) ====================

    public function danhSach()
    {
        $stmt = $this->db->query("SELECT * FROM thuong_hieu ORDER BY TenThuongHieu");
        
        $data = [
            'title' => 'Thương hiệu đối tác - ' . STORE_NAME,
            'danhSach' => $stmt->fetchAll()
        ];

        $this->view('thuong-hieu/danh-sach', $data);
    }

    public function chiTiet($id = null, $page = 1, $sapXep = null)
    {
        if (!$id) {
            $this->redirect('thuongHieu/danhSach');
        }

        $page = (int)($_GET['page'] ?? 1);
        $sapXep = $_GET['sapXep'] ?? null;

        // Lấy thông tin thương hiệu
        $stmt = $this->db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $thuongHieu = $stmt->fetch();

        if (!$thuongHieu) {
            $this->setFlash('error', 'Không tìm thấy thương hiệu!');
            $this->redirect('thuongHieu/danhSach');
        }

        // Lấy sản phẩm của thương hiệu
        $sql = "SELECT t.*, nt.TenNhomThuoc 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                WHERE t.MaThuongHieu = ?";
        
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
        $stmt->execute([$id]);
        $allThuocs = $stmt->fetchAll();

        // Phân trang
        $pageSize = 12;
        $totalItems = count($allThuocs);
        $totalPages = ceil($totalItems / $pageSize);
        $thuocs = array_slice($allThuocs, ($page - 1) * $pageSize, $pageSize);

        $data = [
            'title' => $thuongHieu['TenThuongHieu'] . ' - ' . STORE_NAME,
            'thuongHieu' => $thuongHieu,
            'thuocs' => $thuocs,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'sapXep' => $sapXep
        ];

        $this->view('thuong-hieu/chi-tiet', $data);
    }
}
