<?php
/**
 * HomeController - MVC + RESTful API
 * Hỗ trợ cả Website (HTML) và API (JSON)
 */
class HomeController extends Controller
{
    private $thuocModel;
    private $nhomThuocModel;
    private $thuongHieuModel;
    private $baiVietModel;

    public function __construct()
    {
        parent::__construct();
        $this->thuocModel = $this->model('ThuocModel');
        $this->nhomThuocModel = $this->model('NhomThuocModel');
        $this->thuongHieuModel = $this->model('ThuongHieuModel');
        $this->baiVietModel = $this->model('BaiVietModel');
    }

    /**
     * GET / hoặc /?format=json
     */
    public function index()
    {
        // Lấy dữ liệu trực tiếp từ database
        $allThuoc = $this->thuocModel ? $this->thuocModel->getAll(['IsActive' => 1], 50, 0) : [];
        $nhomThuocs = $this->nhomThuocModel ? $this->nhomThuocModel->getAll([], 100, 0) : [];
        $thuongHieus = $this->thuongHieuModel ? $this->thuongHieuModel->getAll([], 100, 0) : [];
        $baiViets = $this->baiVietModel ? $this->baiVietModel->getAll(['IsNoiBat' => 1], 5, 0) : [];
        
        // Lọc sản phẩm mới (IsNew = 1)
        $sanPhamMoi = array_filter($allThuoc, fn($t) => !empty($t['IsNew']));
        
        // Lọc sản phẩm khuyến mãi (có PhanTramGiam > 0)
        $sanPhamKhuyenMai = array_filter($allThuoc, fn($t) => !empty($t['PhanTramGiam']) && $t['PhanTramGiam'] > 0);
        
        // Lấy sản phẩm bán chạy từ chi_tiet_don_hang (đơn hàng đã giao)
        $sanPhamBanChay = $this->getSanPhamBanChay(8);
        
        // Nếu chưa có đơn hàng, lấy sản phẩm Hot hoặc ngẫu nhiên
        if (empty($sanPhamBanChay)) {
            $sanPhamBanChay = array_filter($allThuoc, fn($t) => !empty($t['IsHot']));
            if (empty($sanPhamBanChay)) {
                $sanPhamBanChay = array_slice($allThuoc, 0, 8);
            }
        }
        
        $data = [
            'sanPhamMoi' => array_slice(array_values($sanPhamMoi), 0, 8),
            'sanPhamKhuyenMai' => array_slice(array_values($sanPhamKhuyenMai), 0, 8),
            'sanPhamBanChay' => array_values($sanPhamBanChay),
            'nhomThuocs' => $nhomThuocs,
            'thuongHieus' => $thuongHieus,
            'baiViets' => $baiViets
        ];
        
        // API Response
        if ($this->isApi) {
            $this->json($data, 'Trang chủ');
        }
        
        // Web Response
        $data['title'] = 'Trang chủ - ' . STORE_NAME;
        $this->view('home/index', $data);
    }

    /**
     * Lấy sản phẩm bán chạy từ chi_tiet_don_hang
     */
    private function getSanPhamBanChay($limit = 8)
    {
        try {
            $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX,
                           COALESCE(SUM(ct.SoLuong), 0) as TongDaBan
                    FROM thuoc t
                    LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                    LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                    LEFT JOIN nuoc_san_xuat nsx ON t.MaNuocSX = nsx.MaNuocSX
                    LEFT JOIN chi_tiet_don_hang ct ON t.MaThuoc = ct.MaThuoc
                    LEFT JOIN don_hang dh ON ct.MaDonHang = dh.MaDonHang AND dh.TrangThai = 'Da giao'
                    WHERE t.IsActive = 1
                    GROUP BY t.MaThuoc
                    HAVING TongDaBan > 0
                    ORDER BY TongDaBan DESC
                    LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * GET /home/stats?format=json - Thống kê cho API
     */
    public function stats()
    {
        $nguoiDungModel = $this->model('NguoiDungModel');
        $donHangModel = $this->model('DonHangModel');
        
        $stats = [
            'tong_thuoc' => $this->thuocModel ? $this->thuocModel->count(['IsActive' => 1]) : 0,
            'tong_nguoi_dung' => $nguoiDungModel ? $nguoiDungModel->count() : 0,
            'tong_don_hang' => $donHangModel ? $donHangModel->count() : 0
        ];
        
        if ($this->isApi) {
            $this->json($stats, 'Thống kê');
        }
        
        $this->redirect('');
    }

    public function gioiThieu()
    {
        if ($this->isApi) {
            $this->json([
                'ten_cua_hang' => STORE_NAME,
                'dia_chi' => STORE_ADDRESS ?? '',
                'dien_thoai' => STORE_PHONE ?? '',
                'email' => STORE_EMAIL ?? ''
            ], 'Giới thiệu');
        }
        
        $this->view('home/gioi-thieu', ['title' => 'Giới thiệu - ' . STORE_NAME]);
    }

    public function lienHe()
    {
        if ($this->isApi) {
            $this->json([
                'ten_cua_hang' => STORE_NAME,
                'dia_chi' => STORE_ADDRESS ?? '',
                'dien_thoai' => STORE_PHONE ?? '',
                'email' => STORE_EMAIL ?? ''
            ], 'Liên hệ');
        }
        
        $this->view('home/lien-he', ['title' => 'Liên hệ - ' . STORE_NAME]);
    }
}
