<?php
class ThuocModel extends Model
{
    protected $table = 'thuoc';
    protected $primaryKey = 'MaThuoc';

    public function getAll($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT DISTINCT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN nuoc_san_xuat nsx ON t.MaNuocSX = nsx.MaNuocSX";
        
        // Join với ct_doi_tuong nếu có filter đối tượng
        if (!empty($filters['MaDoiTuong']) || !empty($filters['doi_tuong'])) {
            $sql .= " INNER JOIN ct_doi_tuong ctdt ON t.MaThuoc = ctdt.MaThuoc";
        }
        
        $sql .= " WHERE 1=1";
        $params = [];

        if (!empty($filters['MaNhomThuoc'])) {
            $sql .= " AND t.MaNhomThuoc = ?";
            $params[] = $filters['MaNhomThuoc'];
        }
        if (!empty($filters['maNhom'])) {
            $sql .= " AND t.MaNhomThuoc = ?";
            $params[] = $filters['maNhom'];
        }
        if (!empty($filters['MaThuongHieu'])) {
            $sql .= " AND t.MaThuongHieu = ?";
            $params[] = $filters['MaThuongHieu'];
        }
        if (!empty($filters['maThuongHieu'])) {
            $sql .= " AND t.MaThuongHieu = ?";
            $params[] = $filters['maThuongHieu'];
        }
        // Filter theo đối tượng sử dụng
        if (!empty($filters['MaDoiTuong'])) {
            $sql .= " AND ctdt.MaDoiTuong = ?";
            $params[] = $filters['MaDoiTuong'];
        }
        if (!empty($filters['doi_tuong'])) {
            $sql .= " AND ctdt.MaDoiTuong = ?";
            $params[] = $filters['doi_tuong'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (t.TenThuoc LIKE ? OR t.MoTa LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (!empty($filters['tuKhoa'])) {
            $sql .= " AND (t.TenThuoc LIKE ? OR t.MoTa LIKE ?)";
            $params[] = "%{$filters['tuKhoa']}%";
            $params[] = "%{$filters['tuKhoa']}%";
        }
        if (isset($filters['IsActive'])) {
            $sql .= " AND t.IsActive = ?";
            $params[] = $filters['IsActive'];
        }
        if (isset($filters['isActive'])) {
            $sql .= " AND t.IsActive = ?";
            $params[] = $filters['isActive'];
        }

        $sql .= " ORDER BY t.MaThuoc DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT t.MaThuoc) as total FROM thuoc t";
        
        // Join với ct_doi_tuong nếu có filter đối tượng
        if (!empty($filters['MaDoiTuong']) || !empty($filters['doi_tuong'])) {
            $sql .= " INNER JOIN ct_doi_tuong ctdt ON t.MaThuoc = ctdt.MaThuoc";
        }
        
        $sql .= " WHERE 1=1";
        $params = [];

        if (!empty($filters['MaNhomThuoc'])) {
            $sql .= " AND t.MaNhomThuoc = ?";
            $params[] = $filters['MaNhomThuoc'];
        }
        if (!empty($filters['MaThuongHieu'])) {
            $sql .= " AND t.MaThuongHieu = ?";
            $params[] = $filters['MaThuongHieu'];
        }
        // Filter theo đối tượng sử dụng
        if (!empty($filters['MaDoiTuong'])) {
            $sql .= " AND ctdt.MaDoiTuong = ?";
            $params[] = $filters['MaDoiTuong'];
        }
        if (!empty($filters['doi_tuong'])) {
            $sql .= " AND ctdt.MaDoiTuong = ?";
            $params[] = $filters['doi_tuong'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (t.TenThuoc LIKE ? OR t.MoTa LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (isset($filters['IsActive'])) {
            $sql .= " AND t.IsActive = ?";
            $params[] = $filters['IsActive'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    // Lấy danh sách đối tượng sử dụng
    public function getAllDoiTuong()
    {
        $sql = "SELECT * FROM doi_tuong_su_dung ORDER BY TenDoiTuong";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function generateCode()
    {
        $sql = "SELECT MAX(CAST(SUBSTRING(MaThuoc, 3) AS UNSIGNED)) as max_id FROM thuoc WHERE MaThuoc LIKE 'TH%'";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $nextId = ($result['max_id'] ?? 0) + 1;
        return 'TH' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN nuoc_san_xuat nsx ON t.MaNuocSX = nsx.MaNuocSX
                WHERE t.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getSanPhamMoi($limit = 10)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsNew = 1 AND t.IsActive = 1
                ORDER BY t.NgayTao DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getSanPhamKhuyenMai($limit = 10)
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsActive = 1 AND t.PhanTramGiam > 0
                AND (t.NgayBatDauKM IS NULL OR t.NgayBatDauKM <= ?)
                AND (t.NgayKetThucKM IS NULL OR t.NgayKetThucKM >= ?)
                ORDER BY t.PhanTramGiam DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$now, $now, $limit]);
        return $stmt->fetchAll();
    }

    public function getSanPhamBanChay($limit = 10)
    {
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu 
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                WHERE t.IsActive = 1 AND t.SoLuongDaBan > 0
                ORDER BY t.SoLuongDaBan DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getThanhPhan($maThuoc)
    {
        $sql = "SELECT ct.*, tp.TenThanhPhan, tp.MoTa 
                FROM ct_thanh_phan ct
                JOIN thanh_phan tp ON ct.MaThanhPhan = tp.MaThanhPhan
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }

    public function getTacDungPhu($maThuoc)
    {
        $sql = "SELECT ct.*, tdp.TenTacDungPhu, tdp.MoTa 
                FROM ct_tac_dung_phu ct
                JOIN tac_dung_phu tdp ON ct.MaTacDungPhu = tdp.MaTacDungPhu
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }

    public function getDoiTuong($maThuoc)
    {
        $sql = "SELECT ct.*, dt.TenDoiTuong, dt.MoTa 
                FROM ct_doi_tuong ct
                JOIN doi_tuong_su_dung dt ON ct.MaDoiTuong = dt.MaDoiTuong
                WHERE ct.MaThuoc = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maThuoc]);
        return $stmt->fetchAll();
    }
}
