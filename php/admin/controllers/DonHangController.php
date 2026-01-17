<?php
/**
 * DonHang Controller - Quản lý đơn hàng (MVC + RESTful API)
 */
class DonHangController extends AdminController
{
    /**
     * Tạo thông báo cho user khi cập nhật trạng thái đơn hàng
     */
    private function taoThongBaoDonHang($maNguoiDung, $maDonHang, $trangThai)
    {
        $thongBao = $this->layNoiDungThongBao($maDonHang, $trangThai);
        
        $stmt = $this->db->prepare("
            INSERT INTO thong_bao (MaNguoiDung, MaDonHang, TieuDe, NoiDung, LoaiThongBao, DaDoc, NgayTao, DuongDan)
            VALUES (?, ?, ?, ?, 'DonHang', 0, NOW(), ?)
        ");
        $stmt->execute([
            $maNguoiDung,
            $maDonHang,
            $thongBao['tieuDe'],
            $thongBao['noiDung'],
            "/don-hang/chi-tiet/$maDonHang"
        ]);
    }

    /**
     * Lấy nội dung thông báo theo trạng thái
     */
    private function layNoiDungThongBao($maDonHang, $trangThai)
    {
        $messages = [
            'Cho xu ly' => [
                'tieuDe' => "Đơn hàng #$maDonHang đang chờ xử lý",
                'noiDung' => "Đơn hàng của bạn đã được tiếp nhận và đang chờ xử lý."
            ],
            'Dang giao' => [
                'tieuDe' => "Đơn hàng #$maDonHang đang giao",
                'noiDung' => "Đơn hàng của bạn đã được giao cho đơn vị vận chuyển. Vui lòng chú ý điện thoại!"
            ],
            'Hoan thanh' => [
                'tieuDe' => "Đơn hàng #$maDonHang hoàn thành",
                'noiDung' => "Đơn hàng đã giao thành công. Cảm ơn bạn đã mua hàng!"
            ],
            'Da huy' => [
                'tieuDe' => "Đơn hàng #$maDonHang đã hủy",
                'noiDung' => "Đơn hàng của bạn đã bị hủy. Vui lòng liên hệ hotline nếu cần hỗ trợ."
            ],
            'Xac nhan thanh toan' => [
                'tieuDe' => "Đơn hàng #$maDonHang đã xác nhận thanh toán",
                'noiDung' => "Chúng tôi đã nhận được tiền chuyển khoản của bạn. Đơn hàng sẽ sớm được xử lý!"
            ]
        ];

        return $messages[$trangThai] ?? [
            'tieuDe' => "Cập nhật đơn hàng #$maDonHang",
            'noiDung' => "Đơn hàng của bạn đã được cập nhật trạng thái: $trangThai"
        ];
    }

    /**
     * GET: Danh sách đơn hàng
     * API: GET /admin/?controller=don-hang&format=json
     */
    public function index()
    {
        $trangThai = $_GET['trangThai'] ?? '';
        
        $sql = "SELECT dh.*, nd.HoTen, nd.SoDienThoai,
                (SELECT COUNT(*) FROM chi_tiet_don_hang WHERE MaDonHang = dh.MaDonHang) as SoSanPham
                FROM don_hang dh
                LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung
                WHERE 1=1";
        $params = [];
        
        if ($trangThai) {
            $sql .= " AND dh.TrangThai = :trangThai";
            $params['trangThai'] = $trangThai;
        }
        $sql .= " ORDER BY dh.NgayDatHang DESC";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $countSql = "SELECT COUNT(*) FROM don_hang dh WHERE 1=1";
            if ($trangThai) $countSql .= " AND dh.TrangThai = :trangThai";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            $sql .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->jsonPaginate($danhSach, $total, $page, $limit);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('don-hang/index', [
            'title' => 'Quản lý đơn hàng',
            'danhSach' => $danhSach,
            'trangThaiFilter' => $trangThai
        ]);
    }

    /**
     * GET: Chi tiết đơn hàng
     * API: GET /admin/?controller=don-hang&action=show&id=1&format=json
     */
    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=don-hang');
        }

        $stmt = $this->db->prepare("
            SELECT dh.*, nd.HoTen, nd.SoDienThoai, nd.DiaChi as DiaChiND
            FROM don_hang dh
            LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung
            WHERE dh.MaDonHang = ?
        ");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donHang) {
            if ($this->isApi) $this->jsonError('Đơn hàng không tồn tại', 404);
            $this->redirect('?controller=don-hang');
        }

        $stmt = $this->db->prepare("
            SELECT ct.*, t.TenThuoc, t.HinhAnh
            FROM chi_tiet_don_hang ct
            LEFT JOIN thuoc t ON ct.MaThuoc = t.MaThuoc
            WHERE ct.MaDonHang = ?
        ");
        $stmt->execute([$id]);
        $donHang['chi_tiet'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($this->isApi) {
            $this->json($donHang, 'Chi tiết đơn hàng');
        }

        $this->view('don-hang/details', [
            'title' => 'Chi tiết đơn hàng #' . $id,
            'donHang' => $donHang,
            'chiTiet' => $donHang['chi_tiet']
        ]);
    }

    /**
     * PUT: Cập nhật trạng thái đơn hàng
     * API: PUT /admin/?controller=don-hang&action=update&id=1&format=json
     */
    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=don-hang');
        }

        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$donHang) {
            if ($this->isApi) $this->jsonError('Đơn hàng không tồn tại', 404);
            $this->redirect('?controller=don-hang');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $trangThaiMoi = $input['TrangThai'] ?? $input['trangThai'] ?? '';
        $trangThaiHienTai = $donHang['TrangThai'];

        // Validate trạng thái
        if ($trangThaiHienTai == 'Da huy' || $trangThaiHienTai == 'Hoan thanh') {
            if ($this->isApi) $this->jsonError('Không thể thay đổi trạng thái đơn hàng này', 400);
            $this->setFlash('error', 'Không thể thay đổi trạng thái đơn hàng này!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
        }

        if ($trangThaiHienTai == 'Dang giao' && $trangThaiMoi == 'Da huy') {
            if ($this->isApi) $this->jsonError('Đơn hàng đang giao không thể hủy', 400);
            $this->setFlash('error', 'Đơn hàng đang giao không thể hủy!');
            $this->redirect("?controller=don-hang&action=details&id=$id");
        }

        // Trừ tồn kho khi chuyển sang Đang giao/Hoàn thành
        if ($trangThaiHienTai == 'Cho xu ly' && ($trangThaiMoi == 'Dang giao' || $trangThaiMoi == 'Hoan thanh')) {
            $stmt = $this->db->prepare("SELECT * FROM chi_tiet_don_hang WHERE MaDonHang = ?");
            $stmt->execute([$id]);
            $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($chiTiet as $ct) {
                $this->db->prepare("UPDATE thuoc SET SoLuongTon = COALESCE(SoLuongTon, 0) - ?, SoLuongDaBan = COALESCE(SoLuongDaBan, 0) + ? WHERE MaThuoc = ?")
                    ->execute([$ct['SoLuong'], $ct['SoLuong'], $ct['MaThuoc']]);
            }
        }

        // Cập nhật trạng thái
        $this->db->prepare("UPDATE don_hang SET TrangThai = ? WHERE MaDonHang = ?")->execute([$trangThaiMoi, $id]);

        // Tạo thông báo cho user
        if ($trangThaiMoi != $trangThaiHienTai && $donHang['MaNguoiDung']) {
            $this->taoThongBaoDonHang($donHang['MaNguoiDung'], $id, $trangThaiMoi);
        }

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Cập nhật trạng thái thành công!');
        $this->redirect("?controller=don-hang&action=details&id=$id");
    }

    /**
     * DELETE: Xóa đơn hàng
     * API: DELETE /admin/?controller=don-hang&action=destroy&id=1&format=json
     */
    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=don-hang');
        }

        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            if ($this->isApi) $this->jsonError('Đơn hàng không tồn tại', 404);
            $this->redirect('?controller=don-hang');
        }

        $this->db->prepare("DELETE FROM chi_tiet_don_hang WHERE MaDonHang = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM don_hang WHERE MaDonHang = ?")->execute([$id]);

        if ($this->isApi) {
            $this->json(null, 'Xóa đơn hàng thành công');
        }

        $this->setFlash('success', 'Xóa đơn hàng thành công!');
        $this->redirect('?controller=don-hang');
    }

    // ==================== Website Methods ====================
    
    public function details($id) { $this->show($id); }
    
    public function countPending()
    {
        if (isset($_GET['ajax']) || $this->isApi) {
            $stmt = $this->db->query("SELECT COUNT(*) FROM don_hang WHERE TrangThai = 'Cho xu ly'");
            $count = $stmt->fetchColumn();
            $this->json(['count' => (int)$count], 'Số đơn chờ xử lý');
        }
        $this->redirect('?controller=don-hang');
    }

    public function capNhatTrangThai()
    {
        if ($this->isPost()) {
            $this->update($_POST['id'] ?? null);
        }
        $this->redirect('?controller=don-hang');
    }

    public function xacNhanThanhToan()
    {
        if (!$this->isPost()) {
            $this->redirect('?controller=don-hang');
        }

        $id = $_POST['id'] ?? 0;
        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ?");
        $stmt->execute([$id]);
        $donHang = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donHang && $donHang['PhuongThucThanhToan'] == 'Chuyển khoản' && $donHang['TrangThai'] == 'Cho xu ly') {
            $this->db->prepare("UPDATE don_hang SET DaThanhToan = 1, TrangThai = 'Dang giao' WHERE MaDonHang = ?")->execute([$id]);

            $stmt = $this->db->prepare("SELECT * FROM chi_tiet_don_hang WHERE MaDonHang = ?");
            $stmt->execute([$id]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ct) {
                $this->db->prepare("UPDATE thuoc SET SoLuongTon = COALESCE(SoLuongTon, 0) - ?, SoLuongDaBan = COALESCE(SoLuongDaBan, 0) + ? WHERE MaThuoc = ?")
                    ->execute([$ct['SoLuong'], $ct['SoLuong'], $ct['MaThuoc']]);
            }

            // Tạo thông báo xác nhận thanh toán
            if ($donHang['MaNguoiDung']) {
                $this->taoThongBaoDonHang($donHang['MaNguoiDung'], $id, 'Xac nhan thanh toan');
                $this->taoThongBaoDonHang($donHang['MaNguoiDung'], $id, 'Dang giao');
            }

            $this->setFlash('success', 'Đã xác nhận thanh toán!');
        } else {
            $this->setFlash('error', 'Không thể xác nhận thanh toán!');
        }

        $this->redirect("?controller=don-hang&action=details&id=$id");
    }
}
