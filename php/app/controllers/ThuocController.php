<?php
/**
 * ThuocController - MVC + RESTful API
 * Hỗ trợ cả Website (HTML) và API (JSON)
 */
class ThuocController extends Controller
{
    private $thuocModel;
    private $nhomThuocModel;
    private $thuongHieuModel;

    public function __construct()
    {
        parent::__construct();
        $this->thuocModel = $this->model('ThuocModel');
        $this->nhomThuocModel = $this->model('NhomThuocModel');
        $this->thuongHieuModel = $this->model('ThuongHieuModel');
    }

    // ==================== RESTful API Methods ====================

    /**
     * GET /thuoc hoặc /thuoc?format=json
     */
    public function index()
    {
        list($page, $limit, $offset) = $this->getPagination();
        
        $filters = ['IsActive' => 1];
        if (!empty($_GET['nhom_thuoc'])) $filters['MaNhomThuoc'] = $_GET['nhom_thuoc'];
        if (!empty($_GET['thuong_hieu'])) $filters['MaThuongHieu'] = $_GET['thuong_hieu'];
        if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];

        $thuocs = $this->thuocModel->getAll($filters, $limit, $offset);
        $total = $this->thuocModel->countFiltered($filters);

        if ($this->isApi) {
            $this->jsonPaginate($thuocs, $total, $page, $limit);
        }

        $this->danhSach();
    }

    /**
     * GET /thuoc/show/{id}
     */
    public function show($id = null)
    {
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('thuoc/danhSach');
        }

        $thuoc = $this->thuocModel->getById($id);
        
        if (!$thuoc) {
            if ($this->isApi) $this->jsonError('Thuốc không tồn tại', 404);
            $this->redirect('thuoc/danhSach');
        }

        $thuoc['thanh_phan'] = $this->thuocModel->getThanhPhan($id);
        $thuoc['tac_dung_phu'] = $this->thuocModel->getTacDungPhu($id);
        $thuoc['doi_tuong_su_dung'] = $this->thuocModel->getDoiTuong($id);

        if ($this->isApi) {
            $this->json($thuoc, 'Chi tiết thuốc');
        }

        $this->chiTiet($id);
    }

    /**
     * POST /thuoc/store - Tạo thuốc mới (Admin only)
     */
    public function store()
    {
        $this->requireAdmin();
        
        $input = $this->getJsonInput();
        $errors = $this->validate($input, [
            'TenThuoc' => 'required|min:2|max:255',
            'GiaBan' => 'required|numeric'
        ]);
        
        if ($errors) $this->jsonError('Validation failed', 422, $errors);

        $input['NgayTao'] = date('Y-m-d H:i:s');
        $input['IsActive'] = $input['IsActive'] ?? 1;

        $id = $this->thuocModel->create($input);
        
        if ($id) {
            $thuoc = $this->thuocModel->getById($id);
            $this->json($thuoc, 'Tạo thuốc thành công', 201);
        }
        
        $this->jsonError('Không thể tạo thuốc', 500);
    }

    /**
     * PUT /thuoc/update/{id}
     */
    public function update($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $thuoc = $this->thuocModel->getById($id);
        if (!$thuoc) $this->jsonError('Thuốc không tồn tại', 404);

        $input = $this->getJsonInput();
        
        // Chỉ lấy các field hợp lệ của bảng thuoc
        $allowedFields = [
            'TenThuoc', 'MaNhomThuoc', 'MaNuocSX', 'MaThuongHieu', 
            'GiaBan', 'GiaGoc', 'PhanTramGiam', 'DonViTinh', 'MoTa', 
            'HinhAnh', 'SoLuongTon', 'SoLuongDaBan', 'IsActive'
        ];
        
        $data = [];
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $input)) {
                $data[$field] = $input[$field];
            }
        }
        
        if (empty($data)) {
            $this->jsonError('Không có dữ liệu hợp lệ để cập nhật', 400);
        }

        $result = $this->thuocModel->update($id, $data, 'MaThuoc');
        
        if ($result) {
            $thuoc = $this->thuocModel->getById($id);
            $this->json($thuoc, 'Cập nhật thành công');
        }
        
        $this->jsonError('Không thể cập nhật', 500);
    }

    /**
     * DELETE /thuoc/destroy/{id}
     */
    public function destroy($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        $thuoc = $this->thuocModel->getById($id);
        if (!$thuoc) $this->jsonError('Thuốc không tồn tại', 404);

        $result = $this->thuocModel->update($id, ['IsActive' => 0], 'MaThuoc');
        
        if ($result) $this->json(null, 'Xóa thuốc thành công');
        
        $this->jsonError('Không thể xóa', 500);
    }

    // ==================== Website Methods (HTML) ====================

    public function danhSach()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = ['IsActive' => 1];
        if (!empty($_GET['nhom'])) $filters['MaNhomThuoc'] = $_GET['nhom'];
        if (!empty($_GET['thuong_hieu'])) $filters['MaThuongHieu'] = $_GET['thuong_hieu'];
        if (!empty($_GET['doi_tuong'])) $filters['doi_tuong'] = $_GET['doi_tuong'];
        if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];

        $danhSachThuoc = $this->thuocModel->getAll($filters, $limit, $offset);
        $total = $this->thuocModel->countFiltered($filters);
        $totalPages = ceil($total / $limit);
        
        $nhomThuocs = $this->nhomThuocModel ? $this->nhomThuocModel->getAll([], 100, 0) : [];
        $thuongHieus = $this->thuongHieuModel ? $this->thuongHieuModel->getAll([], 100, 0) : [];
        $doiTuongs = $this->thuocModel->getAllDoiTuong();
        
        $data = [
            'title' => 'Danh sách thuốc - ' . STORE_NAME,
            'danhSachThuoc' => $danhSachThuoc,
            'pagination' => [
                'total' => $total,
                'per_page' => $limit,
                'current_page' => $page,
                'total_pages' => $totalPages
            ],
            'nhomThuocs' => $nhomThuocs,
            'thuongHieus' => $thuongHieus,
            'doiTuongs' => $doiTuongs,
            'filters' => $filters
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }

    public function chiTiet($id = null)
    {
        if (!$id) $this->redirect('thuoc/danhSach');
        
        $thuoc = $this->thuocModel->getById($id);
        
        if (!$thuoc) {
            $this->redirect('thuoc/danhSach');
        }
        
        $thuoc['thanh_phan'] = $this->thuocModel->getThanhPhan($id);
        $thuoc['tac_dung_phu'] = $this->thuocModel->getTacDungPhu($id);
        $thuoc['doi_tuong_su_dung'] = $this->thuocModel->getDoiTuong($id);
        
        $data = [
            'title' => $thuoc['TenThuoc'] . ' - ' . STORE_NAME,
            'thuoc' => $thuoc,
            'thanhPhans' => $thuoc['thanh_phan'],
            'tacDungPhus' => $thuoc['tac_dung_phu'],
            'doiTuongs' => $thuoc['doi_tuong_su_dung']
        ];
        
        $this->view('thuoc/chi-tiet', $data);
    }

    public function timKiem()
    {
        $tuKhoa = $_GET['tuKhoa'] ?? '';
        if (empty($tuKhoa)) $this->redirect('thuoc/danhSach');
        
        $filters = ['IsActive' => 1, 'search' => $tuKhoa];
        $danhSachThuoc = $this->thuocModel->getAll($filters, 50, 0);
        $nhomThuocs = $this->nhomThuocModel ? $this->nhomThuocModel->getAll([], 100, 0) : [];
        $thuongHieus = $this->thuongHieuModel ? $this->thuongHieuModel->getAll([], 100, 0) : [];
        
        $data = [
            'title' => 'Tìm kiếm: ' . $tuKhoa,
            'danhSachThuoc' => $danhSachThuoc,
            'nhomThuocs' => $nhomThuocs,
            'thuongHieus' => $thuongHieus,
            'tuKhoa' => $tuKhoa
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }

    public function khuyenMai()
    {
        $allThuoc = $this->thuocModel->getAll(['IsActive' => 1], 100, 0);
        
        $danhSach = array_filter($allThuoc, function($t) {
            return !empty($t['PhanTramGiam']) && $t['PhanTramGiam'] > 0;
        });
        
        $nhomThuocs = $this->nhomThuocModel ? $this->nhomThuocModel->getAll([], 100, 0) : [];
        $thuongHieus = $this->thuongHieuModel ? $this->thuongHieuModel->getAll([], 100, 0) : [];
        
        $data = [
            'title' => 'Sản phẩm khuyến mãi - ' . STORE_NAME,
            'danhSachThuoc' => array_values($danhSach),
            'nhomThuocs' => $nhomThuocs,
            'thuongHieus' => $thuongHieus,
            'isKhuyenMai' => true
        ];
        
        $this->view('thuoc/khuyen-mai', $data);
    }

    public function theoNhom($id = null)
    {
        if (!$id) $this->redirect('thuoc/danhSach');
        
        $nhom = $this->nhomThuocModel ? $this->nhomThuocModel->getById($id) : null;
        if (!$nhom) {
            $this->redirect('thuoc/danhSach');
        }
        
        $filters = ['IsActive' => 1, 'MaNhomThuoc' => $id];
        $danhSachThuoc = $this->thuocModel->getAll($filters, 50, 0);
        $nhomThuocs = $this->nhomThuocModel->getAll([], 100, 0);
        $thuongHieus = $this->thuongHieuModel ? $this->thuongHieuModel->getAll([], 100, 0) : [];
        
        $data = [
            'title' => $nhom['TenNhomThuoc'] . ' - ' . STORE_NAME,
            'danhSachThuoc' => $danhSachThuoc,
            'nhomThuocs' => $nhomThuocs,
            'thuongHieus' => $thuongHieus,
            'tenNhom' => $nhom['TenNhomThuoc']
        ];
        
        $this->view('thuoc/danh-sach', $data);
    }
}
