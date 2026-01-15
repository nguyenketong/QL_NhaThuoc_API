<?php
/**
 * TacDungPhu Controller - Quản lý tác dụng phụ (MVC + RESTful API)
 */
class TacDungPhuController extends AdminController
{
    public function index()
    {
        $sql = "SELECT * FROM tac_dung_phu ORDER BY TenTacDungPhu";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            $total = $this->db->query("SELECT COUNT(*) FROM tac_dung_phu")->fetchColumn();
            $sql .= " LIMIT $limit OFFSET $offset";
            $this->jsonPaginate($this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC), $total, $page, $limit);
        }
        
        $this->view('tac-dung-phu/index', ['title' => 'Quản lý tác dụng phụ', 'danhSach' => $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC)]);
    }

    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=tac-dung-phu'); }

        $stmt = $this->db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) { if ($this->isApi) $this->jsonError('Tác dụng phụ không tồn tại', 404); $this->redirect('?controller=tac-dung-phu'); }
        if ($this->isApi) $this->json($item, 'Chi tiết tác dụng phụ');
        $this->view('tac-dung-phu/show', ['title' => 'Chi tiết', 'tacDungPhu' => $item]);
    }

    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        if ($this->isApi) { $errors = $this->validate($input, ['TenTacDungPhu' => 'required']); if ($errors) $this->jsonError('Validation failed', 422, $errors); }

        $stmt = $this->db->prepare("INSERT INTO tac_dung_phu (TenTacDungPhu, MoTa) VALUES (?, ?)");
        $stmt->execute([$input['TenTacDungPhu'] ?? '', $input['MoTa'] ?? '']);
        $id = $this->db->lastInsertId();

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Thêm tác dụng phụ thành công!');
        $this->redirect('?controller=tac-dung-phu');
    }

    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=tac-dung-phu'); }

        $stmt = $this->db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$item) { if ($this->isApi) $this->jsonError('Tác dụng phụ không tồn tại', 404); $this->redirect('?controller=tac-dung-phu'); }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $stmt = $this->db->prepare("UPDATE tac_dung_phu SET TenTacDungPhu = ?, MoTa = ? WHERE MaTacDungPhu = ?");
        $stmt->execute([$input['TenTacDungPhu'] ?? $item['TenTacDungPhu'], $input['MoTa'] ?? $item['MoTa'], $id]);

        if ($this->isApi) $this->show($id);
        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect('?controller=tac-dung-phu');
    }

    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) { if ($this->isApi) $this->jsonError('ID is required', 400); $this->redirect('?controller=tac-dung-phu'); }

        $this->db->prepare("DELETE FROM tac_dung_phu WHERE MaTacDungPhu = ?")->execute([$id]);
        if ($this->isApi) $this->json(null, 'Xóa thành công');
        $this->setFlash('success', 'Xóa thành công!');
        $this->redirect('?controller=tac-dung-phu');
    }

    public function create() { if ($this->isPost()) { $this->store(); return; } $this->view('tac-dung-phu/create', ['title' => 'Thêm tác dụng phụ']); }
    public function edit($id = null) { $id = $id ?? $_GET['id'] ?? null; $stmt = $this->db->prepare("SELECT * FROM tac_dung_phu WHERE MaTacDungPhu = ?"); $stmt->execute([$id]); $item = $stmt->fetch(PDO::FETCH_ASSOC); if (!$item) { $this->redirect('?controller=tac-dung-phu'); return; } if ($this->isPost()) { $this->update($id); return; } $this->view('tac-dung-phu/edit', ['title' => 'Sửa tác dụng phụ', 'tacDungPhu' => $item]); }
    public function delete($id = null) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=tac-dung-phu'); }
}
