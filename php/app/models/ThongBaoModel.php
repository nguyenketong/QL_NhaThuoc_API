<?php
/**
 * ThongBaoModel - Quản lý thông báo
 */
class ThongBaoModel extends Model
{
    protected $table = 'THONG_BAO';

    public function getByNguoiDung($maNguoiDung, $limit = 20)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM THONG_BAO 
            WHERE MaNguoiDung = ? 
            ORDER BY NgayTao DESC 
            LIMIT ?
        ");
        $stmt->execute([$maNguoiDung, $limit]);
        return $stmt->fetchAll();
    }

    public function getChuaDoc($maNguoiDung)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM THONG_BAO 
            WHERE MaNguoiDung = ? AND DaDoc = 0 
            ORDER BY NgayTao DESC
        ");
        $stmt->execute([$maNguoiDung]);
        return $stmt->fetchAll();
    }

    public function demChuaDoc($maNguoiDung)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM THONG_BAO 
            WHERE MaNguoiDung = ? AND DaDoc = 0
        ");
        $stmt->execute([$maNguoiDung]);
        return $stmt->fetchColumn();
    }

    public function danhDauDaDoc($maThongBao, $maNguoiDung)
    {
        $stmt = $this->db->prepare("
            UPDATE THONG_BAO SET DaDoc = 1 
            WHERE MaThongBao = ? AND MaNguoiDung = ?
        ");
        return $stmt->execute([$maThongBao, $maNguoiDung]);
    }

    public function danhDauTatCaDaDoc($maNguoiDung)
    {
        $stmt = $this->db->prepare("
            UPDATE THONG_BAO SET DaDoc = 1 
            WHERE MaNguoiDung = ?
        ");
        return $stmt->execute([$maNguoiDung]);
    }

    public function taoThongBao($maNguoiDung, $tieuDe, $noiDung, $loai = 'HeThong', $duongDan = '', $maDonHang = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO THONG_BAO (MaNguoiDung, MaDonHang, TieuDe, NoiDung, LoaiThongBao, DuongDan, NgayTao, DaDoc)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 0)
        ");
        return $stmt->execute([$maNguoiDung, $maDonHang, $tieuDe, $noiDung, $loai, $duongDan]);
    }
}
