<?php
/**
 * NhomThuoc Controller - Quản lý nhóm thuốc (MVC + RESTful API)
 */
class NhomThuocController extends AdminController
{
    /**
     * GET: Danh sách nhóm thuốc
     * API: GET /admin/?controller=nhom-thuoc&format=json
     */
    public function index()
    {
        $sql = "SELECT nt.*, ntc.TenNhomThuoc as TenDanhMucCha,
                   (SELECT COUNT(*) FROM thuoc WHERE MaNhomThuoc = nt.MaNhomThuoc) as SoLuongThuoc
            FROM nhom_thuoc nt
            LEFT JOIN nhom_thuoc ntc ON nt.MaDanhMucCha = ntc.MaNhomThuoc
            ORDER BY nt.TenNhomThuoc";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $countStmt = $this->db->query("SELECT COUNT(*) FROM nhom_thuoc");
            $total = $countStmt->fetchColumn();
            
            $sql .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->query($sql);
            $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->jsonPaginate($danhSach, $total, $page, $limit);
        }
        
        $stmt = $this->db->query($sql);
        $allItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Sắp xếp: danh mục cha trước, sau đó là các danh mục con
        $danhSach = [];
        $danhMucCha = array_filter($allItems, fn($item) => empty($item['MaDanhMucCha']));
        
        foreach ($danhMucCha as $cha) {
            $cha['isParent'] = true;
            $danhSach[] = $cha;
            foreach ($allItems as $con) {
                if ($con['MaDanhMucCha'] == $cha['MaNhomThuoc']) {
                    $con['isParent'] = false;
                    $danhSach[] = $con;
                }
            }
        }

        $this->view('nhom-thuoc/index', [
            'title' => 'Quản lý nhóm thuốc',
            'danhSach' => $danhSach
        ]);
    }

    /**
     * GET: Chi tiết nhóm thuốc
     * API: GET /admin/?controller=nhom-thuoc&action=show&id=1&format=json
     */
    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nhom-thuoc');
        }

        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $nhomThuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nhomThuoc) {
            if ($this->isApi) $this->jsonError('Nhóm thuốc không tồn tại', 404);
            $this->redirect('?controller=nhom-thuoc');
        }

        if ($this->isApi) {
            $this->json($nhomThuoc, 'Chi tiết nhóm thuốc');
        }

        $this->view('nhom-thuoc/show', ['title' => 'Chi tiết nhóm thuốc', 'nhomThuoc' => $nhomThuoc]);
    }

    /**
     * POST: Tạo nhóm thuốc mới
     * API: POST /admin/?controller=nhom-thuoc&action=store&format=json
     */
    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        
        if ($this->isApi) {
            $errors = $this->validate($input, ['TenNhomThuoc' => 'required|min:2']);
            if ($errors) $this->jsonError('Validation failed', 422, $errors);
        }

        $stmt = $this->db->prepare("INSERT INTO nhom_thuoc (TenNhomThuoc, MoTa, MaDanhMucCha) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['TenNhomThuoc'] ?? '',
            $input['MoTa'] ?? '',
            $input['MaDanhMucCha'] ?: null
        ]);
        $id = $this->db->lastInsertId();

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Thêm nhóm thuốc thành công!');
        $this->redirect('?controller=nhom-thuoc');
    }

    /**
     * PUT: Cập nhật nhóm thuốc
     * API: PUT /admin/?controller=nhom-thuoc&action=update&id=1&format=json
     */
    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nhom-thuoc');
        }

        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            if ($this->isApi) $this->jsonError('Nhóm thuốc không tồn tại', 404);
            $this->redirect('?controller=nhom-thuoc');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;

        $stmt = $this->db->prepare("UPDATE nhom_thuoc SET TenNhomThuoc = ?, MoTa = ?, MaDanhMucCha = ? WHERE MaNhomThuoc = ?");
        $stmt->execute([
            $input['TenNhomThuoc'] ?? '',
            $input['MoTa'] ?? '',
            $input['MaDanhMucCha'] ?: null,
            $id
        ]);

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Cập nhật nhóm thuốc thành công!');
        $this->redirect('?controller=nhom-thuoc');
    }

    /**
     * DELETE: Xóa nhóm thuốc
     * API: DELETE /admin/?controller=nhom-thuoc&action=destroy&id=1&format=json
     */
    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nhom-thuoc');
        }

        // Kiểm tra có thuốc không
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            if ($this->isApi) $this->jsonError('Không thể xóa! Nhóm thuốc này có sản phẩm', 400);
            $this->setFlash('error', 'Không thể xóa! Nhóm thuốc này có sản phẩm.');
            $this->redirect('?controller=nhom-thuoc');
        }

        // Kiểm tra có danh mục con không
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM nhom_thuoc WHERE MaDanhMucCha = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            if ($this->isApi) $this->jsonError('Không thể xóa! Nhóm thuốc này có danh mục con', 400);
            $this->setFlash('error', 'Không thể xóa! Nhóm thuốc này có danh mục con.');
            $this->redirect('?controller=nhom-thuoc');
        }

        $this->db->prepare("DELETE FROM nhom_thuoc WHERE MaNhomThuoc = ?")->execute([$id]);

        if ($this->isApi) {
            $this->json(null, 'Xóa nhóm thuốc thành công');
        }

        $this->setFlash('success', 'Xóa nhóm thuốc thành công!');
        $this->redirect('?controller=nhom-thuoc');
    }

    // ==================== Website Methods ====================
    
    public function create()
    {
        if ($this->isPost()) {
            $this->store();
            return;
        }

        $stmt = $this->db->query("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha IS NULL ORDER BY TenNhomThuoc");
        $this->view('nhom-thuoc/create', [
            'title' => 'Thêm nhóm thuốc',
            'danhMucChaList' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    public function edit($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        
        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaNhomThuoc = ?");
        $stmt->execute([$id]);
        $nhomThuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nhomThuoc) {
            $this->redirect('?controller=nhom-thuoc');
            return;
        }

        if ($this->isPost()) {
            $this->update($id);
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM nhom_thuoc WHERE MaDanhMucCha IS NULL AND MaNhomThuoc != ? ORDER BY TenNhomThuoc");
        $stmt->execute([$id]);
        
        $this->view('nhom-thuoc/edit', [
            'title' => 'Sửa nhóm thuốc',
            'nhomThuoc' => $nhomThuoc,
            'danhMucChaList' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    public function delete($id = null)
    {
        if ($this->isPost()) {
            $this->destroy($id);
        }
        $this->redirect('?controller=nhom-thuoc');
    }
}
