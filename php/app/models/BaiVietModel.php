<?php
/**
 * BaiVietModel - Model quản lý bài viết
 */
class BaiVietModel extends Model
{
    protected $table = 'baiviet';
    protected $primaryKey = 'MaBaiViet';

    public function getAll($filters = [], $limit = 0, $offset = 0)
    {
        $sql = "SELECT * FROM baiviet WHERE 1=1";
        $params = [];

        if (isset($filters['IsActive'])) {
            $sql .= " AND IsActive = ?";
            $params[] = $filters['IsActive'];
        }

        if (isset($filters['IsNoiBat'])) {
            $sql .= " AND IsNoiBat = ?";
            $params[] = $filters['IsNoiBat'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND TieuDe LIKE ?";
            $params[] = "%{$filters['search']}%";
        }

        $sql .= " ORDER BY NgayDang DESC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countFiltered($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM baiviet WHERE 1=1";
        $params = [];

        if (isset($filters['IsActive'])) {
            $sql .= " AND IsActive = ?";
            $params[] = $filters['IsActive'];
        }

        if (isset($filters['IsNoiBat'])) {
            $sql .= " AND IsNoiBat = ?";
            $params[] = $filters['IsNoiBat'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getNoiBat($limit = 5)
    {
        $sql = "SELECT * FROM baiviet WHERE IsActive = 1 AND IsNoiBat = 1 ORDER BY NgayDang DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
