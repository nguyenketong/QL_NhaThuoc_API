<?php
class DonHangModel extends Model
{
    protected $table = 'don_hang';
    protected $primaryKey = 'MaDonHang';

    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT dh.*, nd.HoTen, nd.SoDienThoai 
                FROM don_hang dh 
                LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung
                WHERE 1=1";
        $params = [];

        if (!empty($filters['MaNguoiDung'])) {
            $sql .= " AND dh.MaNguoiDung = ?";
            $params[] = $filters['MaNguoiDung'];
        }
        if (!empty($filters['TrangThai'])) {
            $sql .= " AND dh.TrangThai = ?";
            $params[] = $filters['TrangThai'];
        }

        $sql .= " ORDER BY dh.NgayDatHang DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM don_hang WHERE 1=1";
        $params = [];

        if (!empty($filters['MaNguoiDung'])) {
            $sql .= " AND MaNguoiDung = ?";
            $params[] = $filters['MaNguoiDung'];
        }
        if (!empty($filters['TrangThai'])) {
            $sql .= " AND TrangThai = ?";
            $params[] = $filters['TrangThai'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getByUser($maNguoiDung, $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM don_hang WHERE MaNguoiDung = ? ORDER BY NgayDatHang DESC";
        $params = [$maNguoiDung];

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Alias cho getByUser
    public function getByNguoiDung($maNguoiDung, $limit = 0, $offset = 0)
    {
        return $this->getByUser($maNguoiDung, $limit, $offset);
    }

    public function countByNguoiDung($maNguoiDung)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM don_hang WHERE MaNguoiDung = ?");
        $stmt->execute([$maNguoiDung]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT dh.*, nd.HoTen, nd.SoDienThoai 
                FROM don_hang dh 
                LEFT JOIN nguoi_dung nd ON dh.MaNguoiDung = nd.MaNguoiDung
                WHERE dh.MaDonHang = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getChiTiet($maDonHang)
    {
        $stmt = $this->db->prepare("SELECT ct.*, t.TenThuoc, t.HinhAnh 
                FROM chi_tiet_don_hang ct
                JOIN thuoc t ON ct.MaThuoc = t.MaThuoc
                WHERE ct.MaDonHang = ?");
        $stmt->execute([$maDonHang]);
        return $stmt->fetchAll();
    }

    public function updateTrangThai($maDonHang, $trangThai)
    {
        $stmt = $this->db->prepare("UPDATE don_hang SET TrangThai = ? WHERE MaDonHang = ?");
        return $stmt->execute([$trangThai, $maDonHang]);
    }

    public function huyDon($maDonHang, $maNguoiDung)
    {
        // Kiểm tra đơn hàng thuộc về user và đang ở trạng thái có thể hủy
        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE MaDonHang = ? AND MaNguoiDung = ? AND TrangThai = 'Cho xu ly'");
        $stmt->execute([$maDonHang, $maNguoiDung]);
        $don = $stmt->fetch();
        
        if (!$don) return false;
        
        // Không được hủy nếu đã thanh toán
        if (!empty($don['DaThanhToan'])) return false;
        
        return $this->updateTrangThai($maDonHang, 'Da huy');
    }
}
