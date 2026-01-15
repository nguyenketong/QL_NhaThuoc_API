<?php
/**
 * Dashboard Controller - Thống kê tổng quan (MVC + RESTful API)
 */
class DashboardController extends AdminController
{
    public function index()
    {
        try {
            // 1. Tổng số thuốc
            $tongThuoc = $this->db->query("SELECT COUNT(*) FROM thuoc")->fetchColumn();

            // 2. Tổng số đơn hàng
            $tongDonHang = $this->db->query("SELECT COUNT(*) FROM don_hang")->fetchColumn();

            // 3. Tổng số khách hàng
            $tongKhachHang = $this->db->query("SELECT COUNT(*) FROM nguoi_dung WHERE VaiTro = 'User' OR VaiTro IS NULL")->fetchColumn();

            // 4. Đơn hàng theo trạng thái
            $stmt = $this->db->query("
                SELECT 
                    SUM(CASE WHEN TrangThai = 'Cho xu ly' THEN 1 ELSE 0 END) as ChoXuLy,
                    SUM(CASE WHEN TrangThai = 'Dang giao' THEN 1 ELSE 0 END) as DangGiao,
                    SUM(CASE WHEN TrangThai = 'Hoan thanh' THEN 1 ELSE 0 END) as HoanThanh,
                    SUM(CASE WHEN TrangThai = 'Da huy' THEN 1 ELSE 0 END) as DaHuy
                FROM don_hang
            ");
            $trangThaiDH = $stmt->fetch(PDO::FETCH_ASSOC);

            // 5. Doanh thu tháng này
            $doanhThuThang = $this->db->query("
                SELECT COALESCE(SUM(TongTien), 0) FROM don_hang 
                WHERE TrangThai = 'Hoan thanh' 
                AND MONTH(NgayDatHang) = MONTH(CURRENT_DATE())
                AND YEAR(NgayDatHang) = YEAR(CURRENT_DATE())
            ")->fetchColumn();

            // 5b. Doanh thu hôm nay
            $doanhThuHomNay = $this->db->query("
                SELECT COALESCE(SUM(TongTien), 0) FROM don_hang 
                WHERE TrangThai = 'Hoan thanh' AND DATE(NgayDatHang) = CURRENT_DATE()
            ")->fetchColumn();

            // 6. Đơn hàng gần đây (Top 5)
            $donHangGanDay = $this->db->query("
                SELECT dh.MaDonHang, dh.NgayDatHang, dh.TrangThai, dh.TongTien, nd.HoTen
                FROM don_hang dh
                LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung
                ORDER BY dh.NgayDatHang DESC LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);

            // 7. Top thuốc bán chạy (Top 5)
            $topThuocBanChay = $this->db->query("
                SELECT t.TenThuoc, COALESCE(SUM(ct.SoLuong), 0) as TongBan
                FROM thuoc t
                LEFT JOIN chi_tiet_don_hang ct ON t.MaThuoc = ct.MaThuoc
                LEFT JOIN don_hang dh ON ct.MaDonHang = dh.MaDonHang AND dh.TrangThai = 'Hoan thanh'
                GROUP BY t.MaThuoc, t.TenThuoc ORDER BY TongBan DESC LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);

            // 8. Doanh thu 7 ngày gần nhất
            $chartLabels = [];
            $chartData = [];
            for ($i = 6; $i >= 0; $i--) {
                $ngay = date('Y-m-d', strtotime("-$i days"));
                $chartLabels[] = date('d/m', strtotime($ngay));
                $stmt = $this->db->prepare("SELECT COALESCE(SUM(TongTien), 0) FROM don_hang WHERE DATE(NgayDatHang) = ? AND TrangThai != 'Da huy'");
                $stmt->execute([$ngay]);
                $chartData[] = (float)$stmt->fetchColumn();
            }

            $data = [
                'tongThuoc' => $tongThuoc,
                'tongDonHang' => $tongDonHang,
                'tongKhachHang' => $tongKhachHang,
                'choXuLy' => $trangThaiDH['ChoXuLy'] ?? 0,
                'dangGiao' => $trangThaiDH['DangGiao'] ?? 0,
                'hoanThanh' => $trangThaiDH['HoanThanh'] ?? 0,
                'daHuy' => $trangThaiDH['DaHuy'] ?? 0,
                'doanhThuThang' => $doanhThuThang,
                'doanhThuHomNay' => $doanhThuHomNay,
                'donHangGanDay' => $donHangGanDay,
                'topThuocBanChay' => $topThuocBanChay,
                'chartLabels' => $chartLabels,
                'chartData' => $chartData
            ];

            // API Response
            if ($this->isApi) {
                $this->json($data, 'Dashboard statistics');
            }

            // HTML Response
            $data['title'] = 'Dashboard';
            $this->view('dashboard/index', $data);

        } catch (PDOException $e) {
            if ($this->isApi) {
                $this->jsonError('Database error: ' . $e->getMessage(), 500);
            }
            $this->view('dashboard/index', [
                'title' => 'Dashboard',
                'error' => 'Không thể tải dữ liệu thống kê'
            ]);
        }
    }

    /**
     * API: GET /admin/?controller=dashboard&action=stats&format=json
     */
    public function stats()
    {
        $this->index();
    }
}
