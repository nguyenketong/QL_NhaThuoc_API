<?php
/**
 * ThuongHieu Controller - Quản lý thương hiệu (MVC + RESTful API)
 */
class ThuongHieuController extends AdminController
{
    public function index()
    {
        $sql = "SELECT th.*, (SELECT COUNT(*) FROM thuoc WHERE MaThuongHieu = th.MaThuongHieu) as SoLuongThuoc
                FROM thuong_hieu th ORDER BY th.TenThuongHieu";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            $total = $this->db->query("SELECT COUNT(*) FROM thuong_hieu")->fetchColumn();
            $sql .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->query($sql);
            $this->jsonPaginate($stmt->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
        }
        
        $this->view('thuong-hieu/index', [
            'title' => 'Quản lý thương hiệu',
            'danhSach' => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC)
        ]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuong-hieu');
        }

        $stmt = $this->db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            if ($this->isApi) $this->jsonError('Thương hiệu không tồn tại', 404);
            $this->redirect('?controller=thuong-hieu');
        }

        if ($this->isApi) $this->json($item, 'Chi tiết thương hiệu');
        $this->view('thuong-hieu/show', ['title' => 'Chi tiết thương hiệu', 'thuongHieu' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        
        if ($this->isApi) {
            $errors = $this->validate($input, ['TenThuongHieu' => 'required']);
            if ($errors) $this->jsonError('Validation failed', 422, $errors);
        }

        $hinhAnh = $this->isApi ? ($input['HinhAnh'] ?? null) : $this->processImage($_FILES['LogoFile'] ?? null, $input['HinhAnh'] ?? null, null, 'images/brands');

        $stmt = $this->db->prepare("INSERT INTO thuong_hieu (TenThuongHieu, QuocGia, DiaChi, HinhAnh) VALUES (?, ?, ?, ?)");
        $stmt->execute([$input['TenThuongHieu'] ?? '', $input['QuocGia'] ?? '', $input['DiaChi'] ?? '', $hinhAnh]);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm thương hiệu thành công!');
        $this->redirect('?controller=thuong-hieu');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuong-hieu');
        }

        $stmt = $this->db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            if ($this->isApi) $this->jsonError('Thương hiệu không tồn tại', 404);
            $this->redirect('?controller=thuong-hieu');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $hinhAnh = $this->isApi ? ($input['HinhAnh'] ?? $item['HinhAnh']) : $this->processImage($_FILES['LogoFile'] ?? null, $input['HinhAnh'] ?? null, $item['HinhAnh'], 'images/brands');

        $stmt = $this->db->prepare("UPDATE thuong_hieu SET TenThuongHieu = ?, QuocGia = ?, DiaChi = ?, HinhAnh = ? WHERE MaThuongHieu = ?");
        $stmt->execute([$input['TenThuongHieu'] ?? $item['TenThuongHieu'], $input['QuocGia'] ?? $item['QuocGia'], $input['DiaChi'] ?? $item['DiaChi'], $hinhAnh, $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật thương hiệu thành công!');
        $this->redirect('?controller=thuong-hieu');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuong-hieu');
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM thuoc WHERE MaThuongHieu = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            if ($this->isApi) $this->jsonError('Không thể xóa! Thương hiệu này có sản phẩm', 400);
            $this->setFlash('error', 'Không thể xóa! Thương hiệu này có sản phẩm.');
            $this->redirect('?controller=thuong-hieu');
        }

        $this->db->prepare("DELETE FROM thuong_hieu WHERE MaThuongHieu = ?")->execute([$id]);

        if ($this->isApi) $this->json(null, 'Xóa thương hiệu thành công');
        $this->setFlash('success', 'Xóa thương hiệu thành công!');
        $this->redirect('?controller=thuong-hieu');
    }

    // Website Methods
    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('thuong-hieu/create', ['title' => 'Thêm thương hiệu']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM thuong_hieu WHERE MaThuongHieu = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=thuong-hieu'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('thuong-hieu/edit', ['title' => 'Sửa thương hiệu', 'thuongHieu' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=thuong-hieu'); }
}
