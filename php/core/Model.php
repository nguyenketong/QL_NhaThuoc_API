<?php
/**
 * Base Model
 */
class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function all($orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        return $this->db->query($sql)->fetchAll();
    }

    public function find($id, $primaryKey = null)
    {
        $pk = $primaryKey ?? $this->primaryKey;
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $pk = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function where($conditions, $params = [], $orderBy = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE $conditions";
        if ($orderBy) $sql .= " ORDER BY $orderBy";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count($conditions = '1=1', $params = [])
    {
        // Support both old style (string conditions) and new style (array filters)
        if (is_array($conditions)) {
            // New style: $conditions is actually $filters array
            $filters = $conditions;
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1";
            $params = [];
            
            foreach ($filters as $key => $value) {
                if ($key === 'search') continue; // Skip search, handled by child
                $sql .= " AND $key = ?";
                $params[] = $value;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        }
        
        // Old style: string conditions
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE $conditions");
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function insert($data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");
        $stmt->execute(array_values($data));
        return $this->db->lastInsertId();
    }

    // Alias for insert
    public function create($data)
    {
        return $this->insert($data);
    }

    public function update($id, $data, $primaryKey = null)
    {
        $pk = $primaryKey ?? $this->primaryKey;
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $set WHERE $pk = ?");
        $values = array_values($data);
        $values[] = $id;
        return $stmt->execute($values);
    }

    public function delete($id, $primaryKey = null)
    {
        $pk = $primaryKey ?? $this->primaryKey;
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE $pk = ?");
        return $stmt->execute([$id]);
    }
}
