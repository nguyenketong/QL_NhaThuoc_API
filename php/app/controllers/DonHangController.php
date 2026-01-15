<?php
/**
 * DonHangController - Quản lý đơn hàng (MVC + RESTful API)
 */
class DonHangController extends Controller
{
    private $donHangModel;

    public function __construct()
    {
        parent::__construct();
        $this->donHangModel = $this->model('DonHangModel');
    }

    // ==================== RESTful API Methods ====================

    /**
     * GET /don-hang hoặc /don-hang?format=json
     */
    public function index()
    {
        $this->requireLogin();
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $userId = $this->getUserId();
            $role = $_SESSION['user_role'] ?? 'User';
            
            // Admin xem tất cả, User chỉ xem của mình
            if ($role === 'Admin') {
                $donHangs = $this->donHangModel->getAll([], $limit, $offset);
                $total = $this->donHangModel->count();
            } else {
                $donHangs = $this->donHangModel->getByNguoiDung($userId, $limit, $offset);
                $total = $this->donHangModel->countByNguoiDung($userId);
            }
            
            $this->jsonPaginate($donHangs, $total, $page, $limit);
        }
        
        $this->danhSach();
    }

    /**
     * GET /don-hang/{id}?format=json
     */
    public function show($id = null)
    {
        $this->requireLogin();
        
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('donHang/danhSach');
        }

        $donHang = $this->donHangModel->getById($id);
        
        if (!$donHang) {
            if ($this->isApi) $this->jsonError('Đơn hàng không tồn tại', 404);
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        // Kiểm tra quyền
        $role = $_SESSION['user_role'] ?? 'User';
        if ($role !== 'Admin' && $donHang['MaNguoiDung'] != $this->getUserId()) {
            if ($this->isApi) $this->jsonError('Forbidden', 403);
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $donHang['chi_tiet'] = $this->donHangModel->getChiTiet($id);

        if ($this->isApi) {
            $this->json($donHang, 'Chi tiết đơn hàng');
        }

        $this->chiTiet($id);
    }

    /**
     * POST /don-hang?format=json - Tạo đơn hàng
     */
    public function store()
    {
        $this->requireLogin();
        
        if (!$this->isApi) {
            $this->redirect('gioHang/thanhToan');
        }
        
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'DiaChiGiaoHang' => 'required',
            'chi_tiet' => 'required'
        ]);
        
        if ($errors) {
            $this->jsonError('Validation failed', 422, $errors);
        }
        
        if (empty($input['chi_tiet']) || !is_array($input['chi_tiet'])) {
            $this->jsonError('Chi tiết đơn hàng không hợp lệ', 422);
        }
        
        try {
            $this->db->beginTransaction();
            
            // Tính tổng tiền
            $tongTien = 0;
            foreach ($input['chi_tiet'] as $item) {
                $tongTien += ($item['DonGia'] ?? 0) * ($item['SoLuong'] ?? 0);
            }
            
            // Tạo đơn hàng
            $donHangData = [
                'MaNguoiDung' => $this->getUserId(),
                'NgayDatHang' => date('Y-m-d H:i:s'),
                'DiaChiGiaoHang' => $input['DiaChiGiaoHang'],
                'PhuongThucThanhToan' => $input['PhuongThucThanhToan'] ?? 'Tiền mặt',
                'TongTien' => $tongTien,
                'TrangThai' => 'Cho xu ly',
                'GhiChu' => $input['GhiChu'] ?? ''
            ];
            
            $maDonHang = $this->donHangModel->create($donHangData);
            
            // Thêm chi tiết
            foreach ($input['chi_tiet'] as $item) {
                $thanhTien = ($item['DonGia'] ?? 0) * ($item['SoLuong'] ?? 0);
                $stmt = $this->db->prepare("INSERT INTO chi_tiet_don_hang (MaDonHang, MaThuoc, SoLuong, DonGia, ThanhTien) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$maDonHang, $item['MaThuoc'], $item['SoLuong'], $item['DonGia'], $thanhTien]);
            }
            
            $this->db->commit();
            
            $donHang = $this->donHangModel->getById($maDonHang);
            $donHang['chi_tiet'] = $this->donHangModel->getChiTiet($maDonHang);
            
            $this->json($donHang, 'Tạo đơn hàng thành công', 201);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->jsonError('Không thể tạo đơn hàng: ' . $e->getMessage(), 500);
        }
    }

    /**
     * PUT /don-hang/{id}?format=json - Cập nhật trạng thái (Admin)
     */
    public function update($id = null)
    {
        $this->requireLogin();
        
        if (!$this->isApi) {
            $this->redirect('donHang/danhSach');
        }
        
        if (!$id) $this->jsonError('ID is required', 400);
        
        $donHang = $this->donHangModel->getById($id);
        if (!$donHang) $this->jsonError('Đơn hàng không tồn tại', 404);
        
        $role = $_SESSION['user_role'] ?? 'User';
        $input = $this->getJsonInput();
        
        // User chỉ có thể hủy đơn hàng của mình
        if ($role !== 'Admin') {
            if ($donHang['MaNguoiDung'] != $this->getUserId()) {
                $this->jsonError('Forbidden', 403);
            }
            
            // User chỉ được hủy đơn đang chờ xử lý
            if (!empty($input['TrangThai']) && $input['TrangThai'] === 'Da huy') {
                if ($donHang['TrangThai'] !== 'Cho xu ly') {
                    $this->jsonError('Không thể hủy đơn hàng này', 400);
                }
                $input = ['TrangThai' => 'Da huy'];
            } else {
                $this->jsonError('Forbidden', 403);
            }
        }
        
        $result = $this->donHangModel->update($id, $input, 'MaDonHang');
        
        if ($result) {
            $donHang = $this->donHangModel->getById($id);
            $this->json($donHang, 'Cập nhật thành công');
        }
        
        $this->jsonError('Không thể cập nhật', 500);
    }

    /**
     * DELETE /don-hang/{id}?format=json - Xóa đơn hàng (Admin)
     */
    public function destroy($id = null)
    {
        $this->requireAdmin();
        
        if (!$this->isApi) {
            $this->redirect('donHang/danhSach');
        }
        
        if (!$id) $this->jsonError('ID is required', 400);
        
        $donHang = $this->donHangModel->getById($id);
        if (!$donHang) $this->jsonError('Đơn hàng không tồn tại', 404);
        
        // Hard delete (hoặc cập nhật trạng thái)
        $result = $this->donHangModel->delete($id, 'MaDonHang');
        
        if ($result) {
            $this->json(null, 'Xóa đơn hàng thành công');
        }
        
        $this->jsonError('Không thể xóa', 500);
    }

    // ==================== Website Methods (HTML) ====================

    // GET: donHang/danhSach
    public function danhSach()
    {
        $this->requireLogin();

        $donHangModel = $this->model('DonHangModel');
        $danhSach = $donHangModel->getByNguoiDung($this->getUserId());

        $data = [
            'title' => 'Đơn hàng của tôi - ' . STORE_NAME,
            'danhSach' => $danhSach
        ];

        $this->view('don-hang/danh-sach', $data);
    }

    // GET: donHang/chiTiet/{id}
    public function chiTiet($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $chiTiet = $donHangModel->getChiTiet($id);

        $data = [
            'title' => 'Chi tiết đơn hàng #' . $id . ' - ' . STORE_NAME,
            'donHang' => $donHang,
            'chiTiet' => $chiTiet
        ];

        $this->view('don-hang/chi-tiet', $data);
    }

    // GET: donHang/theoDoi/{id}
    public function theoDoi($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $data = [
            'title' => 'Theo dõi đơn hàng #' . $id,
            'donHang' => $donHang
        ];

        $this->view('don-hang/theo-doi', $data);
    }

    // GET: donHang/huy/{id}
    public function huy($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        // Chỉ hủy được đơn hàng đang chờ xử lý
        if ($donHang['TrangThai'] !== 'Cho xu ly') {
            $this->setFlash('error', 'Không thể hủy đơn hàng này!');
            $this->redirect('donHang/chiTiet/' . $id);
        }

        // Không được hủy nếu đã thanh toán
        if (!empty($donHang['DaThanhToan'])) {
            $this->setFlash('error', 'Đơn hàng đã thanh toán không thể hủy!');
            $this->redirect('donHang/chiTiet/' . $id);
        }

        try {
            // Cập nhật trạng thái (không cần hoàn tồn kho vì chưa trừ)
            $donHangModel->updateTrangThai($id, 'Da huy');
            $this->setFlash('success', 'Đã hủy đơn hàng thành công!');

        } catch (Exception $e) {
            $this->setFlash('error', 'Không thể hủy đơn hàng!');
        }

        $this->redirect('donHang/danhSach');
    }

    // GET: donHang/thanhToanQR/{id}
    public function thanhToanQR($id = null)
    {
        $this->requireLogin();

        if (!$id) {
            $this->redirect('donHang/danhSach');
        }

        $donHangModel = $this->model('DonHangModel');
        $donHang = $donHangModel->getById($id);

        if (!$donHang || $donHang['MaNguoiDung'] != $this->getUserId()) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng!');
            $this->redirect('donHang/danhSach');
        }

        $data = [
            'title' => 'Thanh toán QR - Đơn hàng #' . $id,
            'donHang' => $donHang
        ];

        $this->view('don-hang/thanh-toan-qr', $data);
    }
}
