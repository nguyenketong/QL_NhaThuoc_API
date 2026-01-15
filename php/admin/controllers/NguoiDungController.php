<?php
/**
 * NguoiDung Controller - Quản lý người dùng (MVC + RESTful API)
 */
class NguoiDungController extends AdminController
{
    /**
     * GET: Danh sách người dùng
     * API: GET /admin/?controller=nguoi-dung&format=json
     */
    public function index()
    {
        $sql = "SELECT nd.MaNguoiDung, nd.HoTen, nd.SoDienThoai, nd.Email, nd.DiaChi, nd.VaiTro, nd.NgayTao,
                   (SELECT COUNT(*) FROM don_hang WHERE MaNguoiDung = nd.MaNguoiDung) as SoDonHang,
                   (SELECT COALESCE(SUM(TongTien), 0) FROM don_hang WHERE MaNguoiDung = nd.MaNguoiDung AND TrangThai = 'Hoan thanh') as TongChiTieu
            FROM nguoi_dung nd ORDER BY nd.NgayTao DESC";
        
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            $countStmt = $this->db->query("SELECT COUNT(*) FROM nguoi_dung");
            $total = $countStmt->fetchColumn();
            
            $sql .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->query($sql);
            $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->jsonPaginate($danhSach, $total, $page, $limit);
        }
        
        $stmt = $this->db->query($sql);
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('nguoi-dung/index', [
            'title' => 'Quản lý khách hàng',
            'danhSach' => $danhSach
        ]);
    }

    /**
     * GET: Chi tiết người dùng
     * API: GET /admin/?controller=nguoi-dung&action=show&id=1&format=json
     */
    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nguoi-dung');
        }

        $stmt = $this->db->prepare("SELECT MaNguoiDung, HoTen, SoDienThoai, Email, DiaChi, VaiTro, NgayTao FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $nguoiDung = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('Người dùng không tồn tại', 404);
            $this->redirect('?controller=nguoi-dung');
        }

        $stmt = $this->db->prepare("SELECT * FROM don_hang WHERE MaNguoiDung = ? ORDER BY NgayDatHang DESC");
        $stmt->execute([$id]);
        $nguoiDung['don_hang'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($this->isApi) {
            $this->json($nguoiDung, 'Chi tiết người dùng');
        }

        $this->view('nguoi-dung/details', [
            'title' => 'Chi tiết khách hàng',
            'nguoiDung' => $nguoiDung,
            'donHangs' => $nguoiDung['don_hang']
        ]);
    }

    /**
     * POST: Tạo người dùng mới
     * API: POST /admin/?controller=nguoi-dung&action=store&format=json
     */
    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        
        if ($this->isApi) {
            $errors = $this->validate($input, ['HoTen' => 'required', 'SoDienThoai' => 'required']);
            if ($errors) $this->jsonError('Validation failed', 422, $errors);
        }

        $data = [
            'HoTen' => $input['HoTen'] ?? '',
            'SoDienThoai' => $input['SoDienThoai'] ?? '',
            'Email' => $input['Email'] ?? null,
            'DiaChi' => $input['DiaChi'] ?? null,
            'VaiTro' => $input['VaiTro'] ?? 'User',
            'NgayTao' => date('Y-m-d H:i:s')
        ];

        if (!empty($input['MatKhau'])) {
            $data['MatKhau'] = password_hash($input['MatKhau'], PASSWORD_DEFAULT);
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO nguoi_dung ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        $id = $this->db->lastInsertId();

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Thêm người dùng thành công!');
        $this->redirect('?controller=nguoi-dung');
    }

    /**
     * PUT: Cập nhật người dùng
     * API: PUT /admin/?controller=nguoi-dung&action=update&id=1&format=json
     */
    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nguoi-dung');
        }

        $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $nguoiDung = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('Người dùng không tồn tại', 404);
            $this->redirect('?controller=nguoi-dung');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;

        $data = [
            'HoTen' => $input['HoTen'] ?? $nguoiDung['HoTen'],
            'SoDienThoai' => $input['SoDienThoai'] ?? $nguoiDung['SoDienThoai'],
            'Email' => $input['Email'] ?? $nguoiDung['Email'],
            'DiaChi' => $input['DiaChi'] ?? $nguoiDung['DiaChi'],
            'VaiTro' => $input['VaiTro'] ?? $nguoiDung['VaiTro']
        ];

        if (!empty($input['MatKhau'])) {
            $data['MatKhau'] = password_hash($input['MatKhau'], PASSWORD_DEFAULT);
        }

        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = :$key";
        }
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE nguoi_dung SET " . implode(', ', $sets) . " WHERE MaNguoiDung = :id");
        $stmt->execute($data);

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect("?controller=nguoi-dung&action=details&id=$id");
    }

    /**
     * DELETE: Xóa người dùng
     * API: DELETE /admin/?controller=nguoi-dung&action=destroy&id=1&format=json
     */
    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=nguoi-dung');
        }

        $stmt = $this->db->prepare("SELECT VaiTro FROM nguoi_dung WHERE MaNguoiDung = ?");
        $stmt->execute([$id]);
        $nguoiDung = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('Người dùng không tồn tại', 404);
            $this->redirect('?controller=nguoi-dung');
        }

        if ($nguoiDung['VaiTro'] == 'Admin') {
            if ($this->isApi) $this->jsonError('Không thể xóa tài khoản Admin', 403);
            $this->setFlash('error', 'Không thể xóa tài khoản Admin!');
            $this->redirect('?controller=nguoi-dung');
        }

        $this->db->prepare("DELETE FROM nguoi_dung WHERE MaNguoiDung = ?")->execute([$id]);

        if ($this->isApi) {
            $this->json(null, 'Xóa người dùng thành công');
        }

        $this->setFlash('success', 'Xóa người dùng thành công!');
        $this->redirect('?controller=nguoi-dung');
    }

    // ==================== Website Methods ====================
    
    public function details($id) { $this->show($id); }
    public function delete($id) { if ($this->isPost()) $this->destroy($id); else $this->redirect('?controller=nguoi-dung'); }
    public function capNhatVaiTro() { if ($this->isPost()) $this->update($_POST['id'] ?? null); else $this->redirect('?controller=nguoi-dung'); }
}
