<?php
class NhomThuocModel extends Model
{
    protected $table = 'nhom_thuoc';
    protected $primaryKey = 'MaNhomThuoc';

    public function getAll($filters = [], $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM nhom_thuoc WHERE 1=1";
        $params = [];

        if (isset($filters['MaDanhMucCha'])) {
            if ($filters['MaDanhMucCha'] === null) {
                $sql .= " AND MaDanhMucCha IS NULL";
            } else {
                $sql .= " AND MaDanhMucCha = ?";
                $params[] = $filters['MaDanhMucCha'];
            }
        }

        $sql .= " ORDER BY TenNhomThuoc";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM nhom_thuoc WHERE 1=1";
        $params = [];

        if (isset($filters['MaDanhMucCha'])) {
            if ($filters['MaDanhMucCha'] === null) {
                $sql .= " AND MaDanhMucCha IS NULL";
            } else {
                $sql .= " AND MaDanhMucCha = ?";
                $params[] = $filters['MaDanhMucCha'];
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getDanhMucCha()
    {
        return $this->db->query("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc")->fetchAll();
    }

    public function getDanhMucCon($maCha)
    {
        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha = ? ORDER BY TenNhomThuoc");
        $stmt->execute([$maCha]);
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        return $this->find($id, 'MaNhomThuoc');
    }
}
