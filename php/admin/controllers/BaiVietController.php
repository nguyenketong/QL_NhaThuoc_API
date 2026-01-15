<?php
/**
 * BaiViet Controller - Quản lý bài viết (MVC + RESTful API)
 */
class BaiVietController extends AdminController
{
    public function index()
    {
        $sql = "SELECT * FROM baiviet ORDER BY NgayDang DESC";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            try {
                $total = $this->db->query("SELECT COUNT(*) FROM baiviet")->fetchColumn();
                $sql .= " LIMIT $limit OFFSET $offset";
                $stmt = $this->db->query($sql);
                $this->jsonPaginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
            } catch (PDOException $e) {
                $this->jsonError('Database error', 500);
            }
        }
        
        try {
            $danhSach = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $danhSach = [];
        }

        $this->view('bai-viet/index', ['title' => 'Quản lý bài viết', 'danhSach' => $danhSach]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=bai-viet');
        }

        $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            if ($this->isApi) $this->jsonError('Bài viết không tồn tại', 404);
            $this->redirect('?controller=bai-viet');
        }

        if ($this->isApi) $this->json($item, 'Chi tiết bài viết');
        $this->view('bai-viet/show', ['title' => 'Chi tiết bài viết', 'baiViet' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        
        if ($this->isApi) {
            $errors = $this->validate($input, ['TieuDe' => 'required', 'NoiDung' => 'required']);
            if ($errors) $this->jsonError('Validation failed', 422, $errors);
        }

        $hinhAnh = $this->isApi ? ($input['HinhAnh'] ?? null) : $this->processImage($_FILES['hinhAnhFile'] ?? null, $input['HinhAnh'] ?? null, null, 'images/baiviet');

        $stmt = $this->db->prepare("INSERT INTO baiviet (TieuDe, MoTaNgan, NoiDung, HinhAnh, NgayDang, LuotXem, IsNoiBat, IsActive) VALUES (?, ?, ?, ?, NOW(), 0, ?, 1)");
        $stmt->execute([$input['TieuDe'] ?? '', $input['MoTaNgan'] ?? '', $input['NoiDung'] ?? '', $hinhAnh, isset($input['IsNoiBat']) ? 1 : 0]);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm bài viết thành công!');
        $this->redirect('?controller=bai-viet');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=bai-viet');
        }

        $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            if ($this->isApi) $this->jsonError('Bài viết không tồn tại', 404);
            $this->redirect('?controller=bai-viet');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $hinhAnh = $this->isApi ? ($input['HinhAnh'] ?? $item['HinhAnh']) : $this->processImage($_FILES['hinhAnhFile'] ?? null, $input['HinhAnh'] ?? null, $item['HinhAnh'], 'images/baiviet');

        $stmt = $this->db->prepare("UPDATE baiviet SET TieuDe = ?, MoTaNgan = ?, NoiDung = ?, HinhAnh = ?, IsNoiBat = ? WHERE MaBaiViet = ?");
        $stmt->execute([$input['TieuDe'] ?? $item['TieuDe'], $input['MoTaNgan'] ?? $item['MoTaNgan'], $input['NoiDung'] ?? $item['NoiDung'], $hinhAnh, isset($input['IsNoiBat']) ? 1 : 0, $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật bài viết thành công!');
        $this->redirect('?controller=bai-viet');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=bai-viet');
        }

        $this->db->prepare("DELETE FROM baiviet WHERE MaBaiViet = ?")->execute([$id]);

        if ($this->isApi) $this->json(null, 'Xóa bài viết thành công');
        $this->setFlash('success', 'Xóa bài viết thành công!');
        $this->redirect('?controller=bai-viet');
    }

    // Website Methods
    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('bai-viet/create', ['title' => 'Thêm bài viết']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=bai-viet'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('bai-viet/edit', ['title' => 'Sửa bài viết', 'baiViet' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=bai-viet'); }
    public function toggleNoiBat($id) { $this->db->prepare("UPDATE baiviet SET IsNoiBat = NOT COALESCE(IsNoiBat, 0) WHERE MaBaiViet = ?")->execute([$id]); if ($this->isApi) $this->json(null, 'Cập nhật thành công'); $this->redirect('?controller=bai-viet'); }
    public function toggleActive($id) { $this->db->prepare("UPDATE baiviet SET IsActive = NOT COALESCE(IsActive, 0) WHERE MaBaiViet = ?")->execute([$id]); if ($this->isApi) $this->json(null, 'Cập nhật thành công'); $this->redirect('?controller=bai-viet'); }
}
