<?php
/**
 * NuocSanXuat Controller - Quản lý nước sản xuất (MVC + RESTful API)
 */
class NuocSanXuatController extends AdminController
{
    public function index()
    {
        $sql = "SELECT nsx.*, (SELECT COUNT(*) FROM thuoc WHERE MaNuocSX = nsx.MaNuocSX) as SoLuongThuoc
                FROM nuoc_san_xuat nsx ORDER BY nsx.TenNuocSX";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            $total = $this->db->query("SELECT COUNT(*) FROM nuoc_san_xuat")->fetchColumn();
            $sql .= " LIMIT $limit OFFSET $offset";
            $this->jsonPaginate($this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
        }
        
        $this->view('nuoc-san-xuat/index', ['title' => 'Quản lý nước sản xuất', 'danhSach' => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=nuoc-san-xuat'); }

        $stmt = $this->db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) { if ($this->isApi) $this->jsonError('Nước sản xuất không tồn tại', 404); $this->redirect('?controller=nuoc-san-xuat'); }
        if ($this->isApi) $this->json($item, 'Chi tiết nước sản xuất');
        $this->view('nuoc-san-xuat/show', ['title' => 'Chi tiết', 'nuocSX' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        if ($this->isApi) { $errors = $this->validate($input, ['TenNuocSX' => 'required']); if ($errors) $this->jsonError('Validation failed', 422, $errors); }

        $stmt = $this->db->prepare("INSERT INTO nuoc_san_xuat (TenNuocSX) VALUES (?)");
        $stmt->execute([$input['TenNuocSX'] ?? '']);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm nước sản xuất thành công!');
        $this->redirect('?controller=nuoc-san-xuat');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=nuoc-san-xuat'); }

        $stmt = $this->db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) { if ($this->isApi) $this->jsonError('Nước sản xuất không tồn tại', 404); $this->redirect('?controller=nuoc-san-xuat'); }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $stmt = $this->db->prepare("UPDATE nuoc_san_xuat SET TenNuocSX = ? WHERE MaNuocSX = ?");
        $stmt->execute([$input['TenNuocSX'] ?? $item['TenNuocSX'], $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect('?controller=nuoc-san-xuat');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=nuoc-san-xuat'); }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaNuocSX = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            if ($this->isApi) $this->jsonError('Không thể xóa! Nước sản xuất này có sản phẩm', 400);
            $this->setFlash('error', 'Không thể xóa! Nước sản xuất này có sản phẩm.');
            $this->redirect('?controller=nuoc-san-xuat');
        }

        $this->db->prepare("DELETE FROM nuoc_san_xuat WHERE MaNuocSX = ?")->execute([$id]);
        if ($this->isApi) $this->json(null, 'Xóa thành công');
        $this->setFlash('success', 'Xóa thành công!');
        $this->redirect('?controller=nuoc-san-xuat');
    }

    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('nuoc-san-xuat/create', ['title' => 'Thêm nước sản xuất']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM nuoc_san_xuat WHERE MaNuocSX = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=nuoc-san-xuat'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('nuoc-san-xuat/edit', ['title' => 'Sửa nước sản xuất', 'nuocSX' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=nuoc-san-xuat'); }
}
