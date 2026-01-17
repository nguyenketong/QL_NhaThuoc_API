<?php
/**
 * ThongBaoController - Quản lý thông báo
 */
class ThongBaoController extends Controller
{
    // GET: thongBao/laySoLuongChuaDoc (AJAX)
    public function laySoLuongChuaDoc()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            echo json_encode(['soLuong' => 0]);
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM thong_bao WHERE MaNguoiDung = ? AND DaDoc = 0");
            $stmt->execute([$maNguoiDung]);
            $soLuong = $stmt->fetchColumn();
            echo json_encode(['soLuong' => (int)$soLuong]);
        } catch (Exception $e) {
            echo json_encode(['soLuong' => 0, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // GET: thongBao/layDanhSach (AJAX)
    public function layDanhSach()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            echo json_encode(['thongBaos' => [], 'debug' => 'No user logged in']);
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT MaThongBao, TieuDe, NoiDung, LoaiThongBao, DaDoc, DuongDan, 
                                        DATE_FORMAT(NgayTao, '%d/%m/%Y %H:%i') as NgayTaoFormat 
                                        FROM thong_bao 
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

            echo json_encode(['thongBaos' => $thongBaos, 'userId' => $maNguoiDung, 'count' => count($thongBaos)]);
        } catch (Exception $e) {
            echo json_encode(['thongBaos' => [], 'error' => $e->getMessage()]);
        }
        exit;
    }

    // POST: thongBao/danhDauDaDoc (AJAX)
    public function danhDauDaDoc()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            echo json_encode(['success' => false]);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        
        try {
            $stmt = $this->db->prepare("UPDATE thong_bao SET DaDoc = 1 WHERE MaThongBao = ? AND MaNguoiDung = ?");
            $stmt->execute([$id, $maNguoiDung]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // POST: thongBao/danhDauTatCaDaDoc (AJAX)
    public function danhDauTatCaDaDoc()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            echo json_encode(['success' => false]);
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE thong_bao SET DaDoc = 1 WHERE MaNguoiDung = ? AND DaDoc = 0");
            $stmt->execute([$maNguoiDung]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    // GET: thongBao/test - Tạo thông báo test cho user hiện tại
    public function test()
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $maNguoiDung = $this->getUserId();
        if (!$maNguoiDung) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
            exit;
        }

        try {
            // Tạo thông báo test
            $stmt = $this->db->prepare("
                INSERT INTO thong_bao (MaNguoiDung, MaDonHang, TieuDe, NoiDung, LoaiThongBao, DaDoc, NgayTao, DuongDan)
                VALUES (?, NULL, ?, ?, 'HeThong', 0, NOW(), '#')
            ");
            $stmt->execute([
                $maNguoiDung,
                'Thông báo test ' . date('H:i:s'),
                'Đây là thông báo test được tạo lúc ' . date('d/m/Y H:i:s')
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Đã tạo thông báo test',
                'userId' => $maNguoiDung
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
}
