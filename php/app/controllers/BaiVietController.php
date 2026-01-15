<?php
/**
 * BaiVietController - Quản lý bài viết (User side + API)
 */
class BaiVietController extends Controller
{
    private $pageSize = 10;

    // ==================== RESTful API Methods ====================

    /**
     * GET /bai-viet?format=json - Danh sách bài viết
     */
    public function index()
    {
        if ($this->isApi) {
            list($page, $limit, $offset) = $this->getPagination();
            
            try {
                $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE IsActive = 1 ORDER BY NgayDang DESC LIMIT ? OFFSET ?");
                $stmt->execute([$limit, $offset]);
                $baiViets = $stmt->fetchAll();
                
                $stmt = $this->db->query("SELECT COUNT(*) FROM baiviet WHERE IsActive = 1");
                $total = $stmt->fetchColumn();
                
                $this->jsonPaginate($baiViets, $total, $page, $limit);
            } catch (PDOException $e) {
                $this->jsonError('Không thể lấy danh sách bài viết', 500);
            }
        }
        
        $this->danhSach();
    }

    /**
     * GET /bai-viet/show/{id}?format=json - Chi tiết bài viết
     */
    public function show($id = null)
    {
        if (!$id) {
            if ($this->isApi) $this->jsonError('ID is required', 400);
            $this->redirect('baiViet/danhSach');
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ? AND IsActive = 1");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();

            if (!$baiViet) {
                if ($this->isApi) $this->jsonError('Bài viết không tồn tại', 404);
                $this->setFlash('error', 'Không tìm thấy bài viết!');
                $this->redirect('baiViet/danhSach');
            }

            // Tăng lượt xem
            $stmt = $this->db->prepare("UPDATE baiviet SET LuotXem = COALESCE(LuotXem, 0) + 1 WHERE MaBaiViet = ?");
            $stmt->execute([$id]);

            if ($this->isApi) {
                $this->json($baiViet, 'Chi tiết bài viết');
            }

            $this->chiTiet($id);
        } catch (PDOException $e) {
            if ($this->isApi) $this->jsonError('Có lỗi xảy ra', 500);
            $this->setFlash('error', 'Có lỗi xảy ra!');
            $this->redirect('baiViet/danhSach');
        }
    }

    /**
     * POST /bai-viet/store?format=json - Tạo bài viết (Admin)
     */
    public function store()
    {
        $this->requireAdmin();
        
        $input = $this->getJsonInput();
        
        $errors = $this->validate($input, [
            'TieuDe' => 'required|min:5|max:200'
        ]);
        
        if ($errors) {
            $this->jsonError('Validation failed', 422, $errors);
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO baiviet (TieuDe, MoTaNgan, NoiDung, HinhAnh, NgayDang, IsNoiBat, IsActive) VALUES (?, ?, ?, ?, NOW(), ?, 1)");
            $stmt->execute([
                $input['TieuDe'],
                $input['MoTaNgan'] ?? '',
                $input['NoiDung'] ?? '',
                $input['HinhAnh'] ?? '',
                $input['IsNoiBat'] ?? 0
            ]);
            
            $id = $this->db->lastInsertId();
            
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();
            
            $this->json($baiViet, 'Tạo bài viết thành công', 201);
        } catch (PDOException $e) {
            $this->jsonError('Không thể tạo bài viết', 500);
        }
    }

    /**
     * PUT /bai-viet/update/{id}?format=json - Cập nhật bài viết (Admin)
     */
    public function update($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        try {
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();
            
            if (!$baiViet) $this->jsonError('Bài viết không tồn tại', 404);

            $input = $this->getJsonInput();
            
            $fields = [];
            $values = [];
            
            foreach (['TieuDe', 'MoTaNgan', 'NoiDung', 'HinhAnh', 'IsNoiBat', 'IsActive'] as $field) {
                if (isset($input[$field])) {
                    $fields[] = "$field = ?";
                    $values[] = $input[$field];
                }
            }
            
            if (empty($fields)) {
                $this->jsonError('Không có dữ liệu để cập nhật', 400);
            }
            
            $values[] = $id;
            $sql = "UPDATE baiviet SET " . implode(', ', $fields) . " WHERE MaBaiViet = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($values);
            
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();
            
            $this->json($baiViet, 'Cập nhật thành công');
        } catch (PDOException $e) {
            $this->jsonError('Không thể cập nhật', 500);
        }
    }

    /**
     * DELETE /bai-viet/destroy/{id}?format=json - Xóa bài viết (Admin)
     */
    public function destroy($id = null)
    {
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);

        try {
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();
            
            if (!$baiViet) $this->jsonError('Bài viết không tồn tại', 404);

            // Soft delete
            $stmt = $this->db->prepare("UPDATE baiviet SET IsActive = 0 WHERE MaBaiViet = ?");
            $stmt->execute([$id]);
            
            $this->json(null, 'Xóa bài viết thành công');
        } catch (PDOException $e) {
            $this->jsonError('Không thể xóa', 500);
        }
    }

    // ==================== Website Methods (HTML) ====================

    // GET: baiViet/gocSucKhoe (alias cho danhSach)
    public function gocSucKhoe()
    {
        $this->danhSach();
    }

    // GET: baiViet/danhSach
    public function danhSach()
    {
        $page = (int)($_GET['page'] ?? 1);

        try {
            // Đếm tổng số bài viết
            $stmt = $this->db->query("SELECT COUNT(*) FROM baiviet WHERE IsActive = 1");
            $totalItems = $stmt->fetchColumn();
            $totalPages = ceil($totalItems / $this->pageSize);

            // Lấy bài viết theo trang
            $offset = ($page - 1) * $this->pageSize;
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE IsActive = 1 ORDER BY NgayDang DESC LIMIT ? OFFSET ?");
            $stmt->execute([$this->pageSize, $offset]);
            $baiViets = $stmt->fetchAll();
        } catch (PDOException $e) {
            $baiViets = [];
            $totalItems = 0;
            $totalPages = 0;
        }

        $data = [
            'title' => 'Góc sức khỏe - ' . STORE_NAME,
            'baiViets' => $baiViets,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ];

        $this->view('bai-viet/danh-sach', $data);
    }

    // GET: baiViet/chiTiet/{id}
    public function chiTiet($id = null)
    {
        if (!$id) {
            $this->redirect('baiViet/danhSach');
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet = ? AND IsActive = 1");
            $stmt->execute([$id]);
            $baiViet = $stmt->fetch();

            if (!$baiViet) {
                $this->setFlash('error', 'Không tìm thấy bài viết!');
                $this->redirect('baiViet/danhSach');
            }

            // Tăng lượt xem
            $stmt = $this->db->prepare("UPDATE baiviet SET LuotXem = COALESCE(LuotXem, 0) + 1 WHERE MaBaiViet = ?");
            $stmt->execute([$id]);

            // Bài viết liên quan
            $stmt = $this->db->prepare("SELECT * FROM baiviet WHERE MaBaiViet != ? AND IsActive = 1 ORDER BY NgayDang DESC LIMIT 4");
            $stmt->execute([$id]);
            $baiVietLienQuan = $stmt->fetchAll();

            $data = [
                'title' => $baiViet['TieuDe'] . ' - ' . STORE_NAME,
                'baiViet' => $baiViet,
                'baiVietLienQuan' => $baiVietLienQuan
            ];

            $this->view('bai-viet/chi-tiet', $data);
        } catch (PDOException $e) {
            $this->setFlash('error', 'Có lỗi xảy ra!');
            $this->redirect('baiViet/danhSach');
        }
    }
}
