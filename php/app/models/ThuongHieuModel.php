<?php
/**
 * ThuongHieuModel - Model quản lý thương hiệu
 */
class ThuongHieuModel extends Model
{
    protected $table = 'thuong_hieu';
    protected $primaryKey = 'MaThuongHieu';

    public function getAll($filters = [], $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM thuong_hieu WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND TenThuongHieu LIKE ?";
            $params[] = "%{$filters['search']}%";
        }

        $sql .= " ORDER BY TenThuongHieu";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM thuong_hieu WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND TenThuongHieu LIKE ?";
            $params[] = "%{$filters['search']}%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
