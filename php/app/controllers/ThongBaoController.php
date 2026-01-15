<?php
/**
 * ThongBaoController - Quản lý thông báo
 */
class ThongBaoController extends Controller
{
    // GET: thongBao/laySoLuongChuaDoc (AJAX)
    public function laySoLuongChuaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['soLuong' => 0]);
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM THONG_BAO WHERE MaNguoiDung = ? AND DaDoc = 0");
            $stmt->execute([$maNguoiDung]);
            $soLuong = $stmt->fetchColumn();
            $this->json(['soLuong' => (int)$soLuong]);
        } catch (Exception $e) {
            $this->json(['soLuong' => 0]);
        }
    }

    // GET: thongBao/layDanhSach (AJAX)
    public function layDanhSach()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['thongBaos' => []]);
        }

        try {
            $stmt = $this->db->prepare("SELECT MaThongBao, TieuDe, NoiDung, LoaiThongBao, DaDoc, DuongDan, 
                                        DATE_FORMAT(NgayTao, '%d/%m/%Y %H:%i') as NgayTaoFormat 
                                        FROM THONG_BAO 
                                        WHERE MaNguoiDung = ? 
                                        ORDER BY NgayTao DESC LIMIT 10");
            $stmt->execute([$maNguoiDung]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Convert to camelCase for JavaScript
            $thongBaos = array_map(function($row) {
                return [
                    'maThongBao' => $row['MaThongBao'],
                    'tieuDe' => $row['TieuDe'] ?? 'Thông báo',
                    'noiDung' => $row['NoiDung'] ?? '',
                    'loaiThongBao' => $row['LoaiThongBao'] ?? 'HeThong',
                    'daDoc' => (bool)$row['DaDoc'],
                    'duongDan' => $row['DuongDan'] ?? '#',
                    'ngayTao' => $row['NgayTaoFormat'] ?? ''
                ];
            }, $rows);

            $this->json(['thongBaos' => $thongBaos]);
        } catch (Exception $e) {
            $this->json(['thongBaos' => []]);
        }
    }

    // POST: thongBao/danhDauDaDoc (AJAX)
    public function danhDauDaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['success' => false]);
        }

        $id = (int)($_POST['id'] ?? 0);
        
        try {
            $stmt = $this->db->prepare("UPDATE THONG_BAO SET DaDoc = 1 WHERE MaThongBao = ? AND MaNguoiDung = ?");
            $stmt->execute([$id, $maNguoiDung]);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false]);
        }
    }

    // POST: thongBao/danhDauTatCaDaDoc (AJAX)
    public function danhDauTatCaDaDoc()
    {
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            $this->json(['success' => false]);
        }

        try {
            $stmt = $this->db->prepare("UPDATE THONG_BAO SET DaDoc = 1 WHERE MaNguoiDung = ? AND DaDoc = 0");
            $stmt->execute([$maNguoiDung]);
            $this->json(['success' => true]);
        } catch (Exception $e) {
            $this->json(['success' => false]);
        }
    }
}
