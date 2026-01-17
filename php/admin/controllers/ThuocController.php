<?php
/**
 * Thuoc Controller - Quản lý thuốc (MVC + RESTful API)
 */
class ThuocController extends AdminController
{
    /**
     * GET: Danh sách thuốc
     * API: GET /admin/?controller=thuoc&format=json
     */
    public function index()
    {
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN nuoc_san_xuat nsx ON t.MaNuocSX = nsx.MaNuocSX
                WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND t.TenThuoc LIKE :search";
            $params['search'] = "%$search%";
        }
        $sql .= " ORDER BY t.MaThuoc DESC";
        
        // API Response với pagination
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            // Count total
            $countSql = "SELECT COUNT(*) FROM thuoc t WHERE 1=1";
            if ($search) {
                $countSql .= " AND t.TenThuoc LIKE :search";
            }
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetchColumn();
            
            // Get data with limit
            $sql .= " LIMIT $limit OFFSET $offset";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->jsonPaginate($danhSach, $total, $page, $limit);
        }
        
        // HTML Response
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $danhSach = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->view('thuoc/index', [
            'title' => 'Quản lý thuốc',
            'danhSach' => $danhSach,
            'search' => $search
        ]);
    }

    /**
     * GET: Chi tiết thuốc
     * API: GET /admin/?controller=thuoc&action=show&id=1&format=json
     */
    public function show($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuoc');
        }

        $stmt = $this->db->prepare("SELECT t.*, nt.TenNhomThuoc, th.TenThuongHieu, nsx.TenNuocSX
                FROM thuoc t
                LEFT JOIN nhom_thuoc nt ON t.MaNhomThuoc = nt.MaNhomThuoc
                LEFT JOIN thuong_hieu th ON t.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN nuoc_san_xuat nsx ON t.MaNuocSX = nsx.MaNuocSX
                WHERE t.MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thuoc) {
            if ($this->isApi) $this->jsonError('Thuốc không tồn tại', 404);
            $this->redirect('?controller=thuoc');
        }

        // Lấy thành phần, tác dụng phụ, đối tượng
        $stmt = $this->db->prepare("SELECT ct.*, tp.TenThanhPhan FROM ct_thanh_phan ct JOIN thanh_phan tp ON ct.MaThanhPhan = tp.MaThanhPhan WHERE ct.MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc['thanh_phan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT ct.*, tdp.TenTacDungPhu FROM ct_tac_dung_phu ct JOIN tac_dung_phu tdp ON ct.MaTacDungPhu = tdp.MaTacDungPhu WHERE ct.MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc['tac_dung_phu'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT ct.*, dt.TenDoiTuong FROM ct_doi_tuong ct JOIN doi_tuong_su_dung dt ON ct.MaDoiTuong = dt.MaDoiTuong WHERE ct.MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc['doi_tuong'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($this->isApi) {
            $this->json($thuoc, 'Chi tiết thuốc');
        }

        $this->view('thuoc/show', ['title' => 'Chi tiết thuốc', 'thuoc' => $thuoc]);
    }

    /**
     * POST: Tạo thuốc mới
     * API: POST /admin/?controller=thuoc&action=store&format=json
     */
    public function store()
    {
        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        
        if ($this->isApi) {
            $errors = $this->validate($input, [
                'TenThuoc' => 'required|min:2',
                'GiaBan' => 'required|numeric'
            ]);
            if ($errors) $this->jsonError('Validation failed', 422, $errors);
        }

        $data = [
            'TenThuoc' => $input['TenThuoc'] ?? '',
            'MoTa' => $input['MoTa'] ?? '',
            'DonViTinh' => $input['DonViTinh'] ?? '',
            'GiaBan' => $input['GiaBan'] ?? 0,
            'GiaGoc' => $input['GiaGoc'] ?: null,
            'PhanTramGiam' => $input['PhanTramGiam'] ?: null,
            'NgayBatDauKM' => !empty($input['NgayBatDauKM']) ? $input['NgayBatDauKM'] : null,
            'NgayKetThucKM' => !empty($input['NgayKetThucKM']) ? $input['NgayKetThucKM'] : null,
            'SoLuongTon' => $input['SoLuongTon'] ?? 0,
            'MaNhomThuoc' => $input['MaNhomThuoc'] ?: null,
            'MaThuongHieu' => $input['MaThuongHieu'] ?: null,
            'MaNuocSX' => $input['MaNuocSX'] ?: null,
            'HinhAnh' => $input['HinhAnh'] ?? null,
            'IsHot' => isset($input['IsHot']) ? 1 : 0,
            'IsNew' => isset($input['IsNew']) ? 1 : 0,
            'IsActive' => isset($input['IsActive']) ? 1 : 0,
            'NgayTao' => date('Y-m-d H:i:s')
        ];

        if (!$this->isApi) {
            $data['HinhAnh'] = $this->processImage($_FILES['hinhAnhFile'] ?? null, $input['HinhAnh'] ?? null, null, 'images');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO thuoc ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
        $maThuoc = $this->db->lastInsertId();

        // Lưu thành phần, tác dụng phụ, đối tượng
        $this->saveRelations($maThuoc, $input);

        if ($this->isApi) {
            $this->show($maThuoc);
        }

        $this->setFlash('success', 'Thêm thuốc thành công!');
        $this->redirect('?controller=thuoc');
    }

    /**
     * PUT: Cập nhật thuốc
     * API: PUT /admin/?controller=thuoc&action=update&id=1&format=json
     */
    public function update($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuoc');
        }

        $stmt = $this->db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thuoc) {
            if ($this->isApi) $this->jsonError('Thuốc không tồn tại', 404);
            $this->redirect('?controller=thuoc');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;

        // Xử lý checkbox - khi form submit, nếu không tick thì không có trong $_POST
        // Nên cần set về 0 khi không có, thay vì giữ giá trị cũ
        $isFormSubmit = $_SERVER['REQUEST_METHOD'] === 'POST';
        
        $data = [
            'TenThuoc' => $input['TenThuoc'] ?? $thuoc['TenThuoc'],
            'MoTa' => $input['MoTa'] ?? $thuoc['MoTa'],
            'DonViTinh' => $input['DonViTinh'] ?? $thuoc['DonViTinh'],
            'GiaBan' => $input['GiaBan'] ?? $thuoc['GiaBan'],
            'GiaGoc' => $input['GiaGoc'] ?? $thuoc['GiaGoc'],
            'PhanTramGiam' => $input['PhanTramGiam'] ?? $thuoc['PhanTramGiam'],
            'NgayBatDauKM' => !empty($input['NgayBatDauKM']) ? $input['NgayBatDauKM'] : ($thuoc['NgayBatDauKM'] ?? null),
            'NgayKetThucKM' => !empty($input['NgayKetThucKM']) ? $input['NgayKetThucKM'] : ($thuoc['NgayKetThucKM'] ?? null),
            'SoLuongTon' => $input['SoLuongTon'] ?? $thuoc['SoLuongTon'],
            'MaNhomThuoc' => $input['MaNhomThuoc'] ?? $thuoc['MaNhomThuoc'],
            'MaThuongHieu' => $input['MaThuongHieu'] ?? $thuoc['MaThuongHieu'],
            'MaNuocSX' => $input['MaNuocSX'] ?? $thuoc['MaNuocSX'],
            'IsHot' => $isFormSubmit ? (isset($input['IsHot']) ? 1 : 0) : ($thuoc['IsHot'] ?? 0),
            'IsNew' => $isFormSubmit ? (isset($input['IsNew']) ? 1 : 0) : ($thuoc['IsNew'] ?? 0),
            'IsActive' => $isFormSubmit ? (isset($input['IsActive']) ? 1 : 0) : ($thuoc['IsActive'] ?? 1)
        ];

        if ($this->isApi) {
            $data['HinhAnh'] = $input['HinhAnh'] ?? $thuoc['HinhAnh'];
        } else {
            $data['HinhAnh'] = $this->processImage($_FILES['hinhAnhFile'] ?? null, $input['HinhAnh'] ?? null, $thuoc['HinhAnh'], 'images');
        }

        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = :$key";
        }
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE thuoc SET " . implode(', ', $sets) . " WHERE MaThuoc = :id");
        $stmt->execute($data);

        // Cập nhật relations
        $this->db->prepare("DELETE FROM ct_thanh_phan WHERE MaThuoc = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM ct_tac_dung_phu WHERE MaThuoc = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM ct_doi_tuong WHERE MaThuoc = ?")->execute([$id]);
        $this->saveRelations($id, $input);

        if ($this->isApi) {
            $this->show($id);
        }

        $this->setFlash('success', 'Cập nhật thuốc thành công!');
        $this->redirect('?controller=thuoc');
    }

    /**
     * DELETE: Xóa thuốc
     * API: DELETE /admin/?controller=thuoc&action=destroy&id=1&format=json
     */
    public function destroy($id = null)
    {
        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('?controller=thuoc');
        }

        $stmt = $this->db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            if ($this->isApi) $this->jsonError('Thuốc không tồn tại', 404);
            $this->redirect('?controller=thuoc');
        }

        // Xóa relations
        $this->db->prepare("DELETE FROM ct_thanh_phan WHERE MaThuoc = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM ct_tac_dung_phu WHERE MaThuoc = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM ct_doi_tuong WHERE MaThuoc = ?")->execute([$id]);
        
        // Xóa thuốc
        $this->db->prepare("DELETE FROM thuoc WHERE MaThuoc = ?")->execute([$id]);

        if ($this->isApi) {
            $this->json(null, 'Xóa thuốc thành công');
        }

        $this->setFlash('success', 'Xóa thuốc thành công!');
        $this->redirect('?controller=thuoc');
    }

    private function saveRelations($maThuoc, $input)
    {
        // Thành phần
        $thanhPhanIds = $input['ThanhPhanIds'] ?? [];
        $hamLuongs = $input['HamLuongs'] ?? [];
        foreach ($thanhPhanIds as $i => $tpId) {
            if ($tpId > 0) {
                $stmt = $this->db->prepare("INSERT INTO ct_thanh_phan (MaThuoc, MaThanhPhan, HamLuong) VALUES (?, ?, ?)");
                $stmt->execute([$maThuoc, $tpId, $hamLuongs[$i] ?? null]);
            }
        }

        // Tác dụng phụ
        $tacDungPhuIds = $input['TacDungPhuIds'] ?? [];
        $mucDos = $input['MucDos'] ?? [];
        foreach ($tacDungPhuIds as $i => $tdpId) {
            if ($tdpId > 0) {
                $stmt = $this->db->prepare("INSERT INTO ct_tac_dung_phu (MaThuoc, MaTacDungPhu, MucDo) VALUES (?, ?, ?)");
                $stmt->execute([$maThuoc, $tdpId, $mucDos[$i] ?? null]);
            }
        }

        // Đối tượng
        $doiTuongIds = $input['DoiTuongIds'] ?? [];
        foreach ($doiTuongIds as $dtId) {
            if ($dtId > 0) {
                $stmt = $this->db->prepare("INSERT INTO ct_doi_tuong (MaThuoc, MaDoiTuong) VALUES (?, ?)");
                $stmt->execute([$maThuoc, $dtId]);
            }
        }
    }

    // ==================== Website Methods (HTML) ====================

    public function create()
    {
        if ($this->isPost()) {
            $this->store();
            return;
        }

        $this->loadDropdowns();
        $this->view('thuoc/create', ['title' => 'Thêm thuốc mới']);
    }

    public function edit($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        
        $stmt = $this->db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $thuoc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$thuoc) {
            $this->redirect('?controller=thuoc');
            return;
        }

        if ($this->isPost()) {
            $this->update($id);
            return;
        }

        // Lấy thành phần, tác dụng phụ, đối tượng đã chọn
        $stmt = $this->db->prepare("SELECT * FROM ct_thanh_phan WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedThanhPhans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT * FROM ct_tac_dung_phu WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedTacDungPhus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("SELECT MaDoiTuong FROM ct_doi_tuong WHERE MaThuoc = ?");
        $stmt->execute([$id]);
        $selectedDoiTuongs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $this->loadDropdowns();
        $this->view('thuoc/edit', [
            'title' => 'Sửa thuốc',
            'thuoc' => $thuoc,
            'selectedThanhPhans' => $selectedThanhPhans,
            'selectedTacDungPhus' => $selectedTacDungPhus,
            'selectedDoiTuongs' => $selectedDoiTuongs
        ]);
    }

    public function delete($id = null)
    {
        if ($this->isPost()) {
            $this->destroy($id);
        }
        $this->redirect('?controller=thuoc');
    }

    private function loadDropdowns()
    {
        $this->data['nhomThuocs'] = $this->db->query("SELECT * FROM nhom_thuoc ORDER BY TenNhomThuoc")->fetchAll(PDO::FETCH_ASSOC);
        $this->data['thuongHieus'] = $this->db->query("SELECT * FROM thuong_hieu ORDER BY TenThuongHieu")->fetchAll(PDO::FETCH_ASSOC);
        $this->data['nuocSXs'] = $this->db->query("SELECT * FROM nuoc_san_xuat ORDER BY TenNuocSX")->fetchAll(PDO::FETCH_ASSOC);
        $this->data['thanhPhans'] = $this->db->query("SELECT * FROM thanh_phan ORDER BY TenThanhPhan")->fetchAll(PDO::FETCH_ASSOC);
        $this->data['tacDungPhus'] = $this->db->query("SELECT * FROM tac_dung_phu ORDER BY TenTacDungPhu")->fetchAll(PDO::FETCH_ASSOC);
        $this->data['doiTuongs'] = $this->db->query("SELECT * FROM doi_tuong_su_dung ORDER BY TenDoiTuong")->fetchAll(PDO::FETCH_ASSOC);
    }
}
