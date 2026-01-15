<?php
/**
 * DoiTuong Controller - Quản lý đối tượng sử dụng (MVC + RESTful API)
 */
class DoiTuongController extends AdminController
{
    public function index()
    {
        $sql = "SELECT * FROM doi_tuong_su_dung ORDER BY TenDoiTuong";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            $total = $this->db->query("SELECT COUNT(*) FROM doi_tuong_su_dung")->fetchColumn();
            $sql .= " LIMIT $limit OFFSET $offset";
            $this->jsonPaginate($this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
        }
        
        $this->view('doi-tuong/index', ['title' => 'Quản lý đối tượng sử dụng', 'danhSach' => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=doi-tuong'); }

        $stmt = $this->db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) { if ($this->isApi) $this->jsonError('Đối tượng không tồn tại', 404); $this->redirect('?controller=doi-tuong'); }
        if ($this->isApi) $this->json($item, 'Chi tiết đối tượng');
        $this->view('doi-tuong/show', ['title' => 'Chi tiết', 'doiTuong' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        if ($this->isApi) { $errors = $this->validate($input, ['TenDoiTuong' => 'required']); if ($errors) $this->jsonError('Validation failed', 422, $errors); }

        $stmt = $this->db->prepare("INSERT INTO doi_tuong_su_dung (TenDoiTuong, MoTa) VALUES (?, ?)");
        $stmt->execute([$input['TenDoiTuong'] ?? '', $input['MoTa'] ?? '']);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm đối tượng sử dụng thành công!');
        $this->redirect('?controller=doi-tuong');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=doi-tuong'); }

        $stmt = $this->db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) { if ($this->isApi) $this->jsonError('Đối tượng không tồn tại', 404); $this->redirect('?controller=doi-tuong'); }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $stmt = $this->db->prepare("UPDATE doi_tuong_su_dung SET TenDoiTuong = ?, MoTa = ? WHERE MaDoiTuong = ?");
        $stmt->execute([$input['TenDoiTuong'] ?? $item['TenDoiTuong'], $input['MoTa'] ?? $item['MoTa'], $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect('?controller=doi-tuong');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=doi-tuong'); }

        $this->db->prepare("DELETE FROM doi_tuong_su_dung WHERE MaDoiTuong = ?")->execute([$id]);
        if ($this->isApi) $this->json(null, 'Xóa thành công');
        $this->setFlash('success', 'Xóa thành công!');
        $this->redirect('?controller=doi-tuong');
    }

    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('doi-tuong/create', ['title' => 'Thêm đối tượng sử dụng']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM doi_tuong_su_dung WHERE MaDoiTuong = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=doi-tuong'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('doi-tuong/edit', ['title' => 'Sửa đối tượng', 'doiTuong' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=doi-tuong'); }
}
