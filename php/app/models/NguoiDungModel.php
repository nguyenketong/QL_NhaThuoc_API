<?php
class NguoiDungModel extends Model
{
    protected $table = 'nguoi_dung';
    protected $primaryKey = 'MaNguoiDung';

    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT MaNguoiDung, HoTen, SoDienThoai, Email, DiaChi, Avatar, VaiTro, NgayTao 
                FROM nguoi_dung WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (HoTen LIKE ? OR SoDienThoai LIKE ? OR Email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (!empty($filters['VaiTro'])) {
            $sql .= " AND VaiTro = ?";
            $params[] = $filters['VaiTro'];
        }


        $sql .= " ORDER BY NgayTao DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (HoTen LIKE ? OR SoDienThoai LIKE ? OR Email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (!empty($filters['VaiTro'])) {
            $sql .= " AND VaiTro = ?";
            $params[] = $filters['VaiTro'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT nd.MaNguoiDung, nd.HoTen, nd.SoDienThoai, nd.Email, nd.DiaChi, nd.Avatar, nd.VaiTro, nd.NgayTao,
                   (SELECT COUNT(*) FROM don_hang WHERE MaNguoiDung = nd.MaNguoiDung) as so_don_hang,
                   (SELECT COALESCE(SUM(TongTien), 0) FROM don_hang WHERE MaNguoiDung = nd.MaNguoiDung AND TrangThai = 'Hoan thanh') as tong_tien_da_mua
            FROM nguoi_dung nd 
            WHERE nd.MaNguoiDung = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByPhone($sdt)
    {
        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ?");
        $stmt->execute([$sdt]);
        return $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function createOrGetByPhone($sdt)
    {
        $user = $this->findByPhone($sdt);
        if (!$user) {
            $this->insert([
                'SoDienThoai' => $sdt,
                'LoaiDangNhap' => 'Phone',
                'VaiTro' => 'User',
                'NgayTao' => date('Y-m-d H:i:s')
            ]);
            return $this->findByPhone($sdt);
        }
        return $user;
    }

    public function saveOtp($maNguoiDung, $otp)
    {
        // Dùng MySQL NOW() + INTERVAL để đảm bảo cùng timezone với verifyOtp
        $stmt = $this->db->prepare("UPDATE nguoi_dung SET OTP = ?, OTP_Expire = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE MaNguoiDung = ?");
        return $stmt->execute([$otp, $maNguoiDung]);
    }

    public function verifyOtp($sdt, $otp)
    {
        // Cast OTP thành string để so sánh chính xác
        $otp = (string)$otp;
        
        // Debug: Lấy thông tin từ DB để so sánh
        $debug = $this->db->prepare("SELECT OTP, OTP_Expire, NOW() as mysql_now FROM nguoi_dung WHERE SoDienThoai = ?");
        $debug->execute([$sdt]);
        $debugData = $debug->fetch();
        
        // Log debug info
        error_log("verifyOtp Debug - Input OTP: $otp, DB OTP: " . ($debugData['OTP'] ?? 'NULL') . 
                  ", Expire: " . ($debugData['OTP_Expire'] ?? 'NULL') . 
                  ", MySQL NOW: " . ($debugData['mysql_now'] ?? 'NULL'));
        
        // So sánh OTP trước (không check thời gian)
        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ? AND OTP = ?");
        $stmt->execute([$sdt, $otp]);
        $user = $stmt->fetch();
        
        if (!$user) {
            // OTP không khớp
            error_log("OTP không khớp! Input: $otp, DB: " . ($debugData['OTP'] ?? 'NULL'));
            return false;
        }
        
        // Kiểm tra thời gian hết hạn
        if (strtotime($debugData['OTP_Expire']) < strtotime($debugData['mysql_now'])) {
            error_log("OTP đã hết hạn! Expire: " . $debugData['OTP_Expire'] . ", Now: " . $debugData['mysql_now']);
            return false;
        }
        
        // Xóa OTP sau khi xác thực thành công
        $this->db->prepare("UPDATE nguoi_dung SET OTP = NULL, OTP_Expire = NULL WHERE MaNguoiDung = ?")
                 ->execute([$user['MaNguoiDung']]);
        
        return $user;
    }

    public function updateProfile($maNguoiDung, $data)
    {
        return $this->update($maNguoiDung, $data, 'MaNguoiDung');
    }
}
