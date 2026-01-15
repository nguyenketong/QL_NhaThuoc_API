<?php
/**
 * ThanhPhan Controller - Quản lý thành phần (MVC + RESTful API)
 */
class ThanhPhanController extends AdminController
{
    public function index()
    {
        $sql = "SELECT * FROM thanh_phan ORDER BY TenThanhPhan";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            $total = $this->db->query("SELECT COUNT(*) FROM thanh_phan")->fetchColumn();
            $sql .= " LIMIT $limit OFFSET $offset";
            $this->jsonPaginate($this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
        }
        
        $this->view('thanh-phan/index', ['title' => 'Quản lý thành phần', 'danhSach' => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=thanh-phan'); }

        $stmt = $this->db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) { if ($this->isApi) $this->jsonError('Thành phần không tồn tại', 404); $this->redirect('?controller=thanh-phan'); }
        if ($this->isApi) $this->json($item, 'Chi tiết thành phần');
        $this->view('thanh-phan/show', ['title' => 'Chi tiết', 'thanhPhan' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        if ($this->isApi) { $errors = $this->validate($input, ['TenThanhPhan' => 'required']); if ($errors) $this->jsonError('Validation failed', 422, $errors); }

        $stmt = $this->db->prepare("INSERT INTO thanh_phan (TenThanhPhan, MoTa) VALUES (?, ?)");
        $stmt->execute([$input['TenThanhPhan'] ?? '', $input['MoTa'] ?? '']);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm thành phần thành công!');
        $this->redirect('?controller=thanh-phan');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=thanh-phan'); }

        $stmt = $this->db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) { if ($this->isApi) $this->jsonError('Thành phần không tồn tại', 404); $this->redirect('?controller=thanh-phan'); }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $stmt = $this->db->prepare("UPDATE thanh_phan SET TenThanhPhan = ?, MoTa = ? WHERE MaThanhPhan = ?");
        $stmt->execute([$input['TenThanhPhan'] ?? $item['TenThanhPhan'], $input['MoTa'] ?? $item['MoTa'], $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect('?controller=thanh-phan');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=thanh-phan'); }

        $this->db->prepare("DELETE FROM thanh_phan WHERE MaThanhPhan = ?")->execute([$id]);
        if ($this->isApi) $this->json(null, 'Xóa thành công');
        $this->setFlash('success', 'Xóa thành công!');
        $this->redirect('?controller=thanh-phan');
    }

    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('thanh-phan/create', ['title' => 'Thêm thành phần']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM thanh_phan WHERE MaThanhPhan = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=thanh-phan'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('thanh-phan/edit', ['title' => 'Sửa thành phần', 'thanhPhan' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=thanh-phan'); }
}
