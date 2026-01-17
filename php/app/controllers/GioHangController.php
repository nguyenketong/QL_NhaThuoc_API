<?php
/**
 * GioHangController - Quản lý giỏ hàng
 * Lưu trữ: Session (ưu tiên) + Cookie (backup)
 * 
 * QUY TRÌNH:
 * 1. Thêm sản phẩm -> themAjax() -> lưu session + cookie -> trả về số lượng
 * 2. Xem giỏ hàng -> index() -> đọc session/cookie -> hiển thị
 * 3. Cập nhật số lượng -> capNhatSoLuong() -> lưu session + cookie
 * 4. Xóa sản phẩm -> xoa() -> lưu session + cookie
 * 5. Thanh toán -> thanhToan() -> kiểm tra đăng nhập -> datHang()
 */
class GioHangController extends Controller
{
    private $cookieName = 'GioHang';
    private $cookiePath = '/';

    public function __construct()
    {
        parent::__construct();
        // Set cookie path từ BASE_URL
        $this->cookiePath = parse_url(BASE_URL, PHP_URL_PATH) ?: '/';
    }

    // ==================== HELPER METHODS ====================

    /**
     * Đọc giỏ hàng từ Session hoặc Cookie
     */
    private function getCart()
    {
        // 1. Ưu tiên đọc từ Session
        if (isset($_SESSION['GioHang']) && is_array($_SESSION['GioHang']) && !empty($_SESSION['GioHang'])) {
            return $_SESSION['GioHang'];
        }

        // 2. Đọc từ Cookie nếu Session rỗng
        if (isset($_COOKIE[$this->cookieName])) {
            $cart = json_decode($_COOKIE[$this->cookieName], true);
            if (is_array($cart) && !empty($cart)) {
                $_SESSION['GioHang'] = $cart; // Sync to session
                return $cart;
            }
        }

        return [];
    }

    /**
     * Lưu giỏ hàng vào Session và Cookie
     */
    private function saveCart($cart)
    {
        // Đảm bảo cart là array
        if (!is_array($cart)) {
            $cart = [];
        }

        // Reindex array
        $cart = array_values($cart);

        // Lưu vào Session
        $_SESSION['GioHang'] = $cart;

        // Lưu vào Cookie (30 ngày) - lưu ở path "/"" để đọc được từ mọi nơi
        $cartJson = json_encode($cart);
        setcookie($this->cookieName, $cartJson, time() + 30 * 24 * 3600, '/');
        $_COOKIE[$this->cookieName] = $cartJson;
    }

    /**
     * Xóa giỏ hàng
     */
    private function clearCart()
    {
        // Xóa Session
        unset($_SESSION['GioHang']);
        $_SESSION['GioHang'] = [];
        
        // Xóa Cookie với path từ BASE_URL
        setcookie($this->cookieName, '', time() - 3600, $this->cookiePath);
        
        // Xóa Cookie với path "/" (để xóa cookie cũ nếu có)
        setcookie($this->cookieName, '', time() - 3600, '/');
        
        // Xóa Cookie với path "/Ql_NhaThuoc_API/php" (hardcode để chắc chắn)
        setcookie($this->cookieName, '', time() - 3600, '/Ql_NhaThuoc_API/php');
        setcookie($this->cookieName, '', time() - 3600, '/Ql_NhaThuoc_API/php/');
        
        unset($_COOKIE[$this->cookieName]);
    }

    /**
     * Tìm sản phẩm trong giỏ hàng
     */
    private function findProduct($cart, $maThuoc)
    {
        foreach ($cart as $index => $item) {
            if ((int)$item['MaThuoc'] === (int)$maThuoc) {
                return $index;
            }
        }
        return false;
    }

    /**
     * Tính tổng tiền giỏ hàng
     */
    private function calculateTotal($cart, $onlySelected = false)
    {
        $total = 0;
        foreach ($cart as $item) {
            if ($onlySelected && empty($item['DuocChon'])) {
                continue;
            }
            if (!empty($item['KhongKhaDung'])) {
                continue;
            }
            $total += ($item['GiaBan'] ?? 0) * ($item['SoLuong'] ?? 0);
        }
        return $total;
    }

    /**
     * Lấy thông tin thuốc từ DB
     */
    private function getProductInfo($maThuoc)
    {
        $stmt = $this->db->prepare("SELECT * FROM thuoc WHERE MaThuoc = ?");
        $stmt->execute([$maThuoc]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tính giá bán (có khuyến mãi)
     * - Đang KM: tính giá giảm từ GiaGoc
     * - Hết KM: trả về GiaGoc (giá gốc trước KM)
     */
    private function calculatePrice($thuoc)
    {
        $phanTramGiam = $thuoc['PhanTramGiam'] ?? 0;
        $giaGoc = $thuoc['GiaGoc'] ?? $thuoc['GiaBan'] ?? 0;

        if ($phanTramGiam > 0) {
            $now = date('Y-m-d H:i:s');
            $batDau = $thuoc['NgayBatDauKM'] ?? null;
            $ketThuc = $thuoc['NgayKetThucKM'] ?? null;

            $dangKhuyenMai = (empty($batDau) || $batDau <= $now) 
                          && (empty($ketThuc) || $ketThuc >= $now);

            if ($dangKhuyenMai) {
                return $giaGoc * (100 - $phanTramGiam) / 100;
            }
        }

        // Hết KM hoặc không có KM: trả về giá gốc
        return $giaGoc;
    }

    // ==================== PUBLIC METHODS ====================

    /**
     * GET: /gio-hang - Hiển thị giỏ hàng
     */
    public function index()
    {
        $cart = $this->getCart();

        // Cập nhật thông tin sản phẩm từ DB
        foreach ($cart as &$item) {
            $thuoc = $this->getProductInfo($item['MaThuoc']);

            if ($thuoc) {
                $item['TenThuoc'] = $thuoc['TenThuoc'];
                $item['HinhAnh'] = $thuoc['HinhAnh'] ?? '';
                $item['SoLuongTon'] = $thuoc['SoLuongTon'] ?? 0;
                $item['GiaBan'] = $this->calculatePrice($thuoc);
                $item['NgungKinhDoanh'] = !$thuoc['IsActive'];
                $item['KhongKhaDung'] = $item['NgungKinhDoanh'] || $item['SoLuongTon'] <= 0;

                if ($item['KhongKhaDung']) {
                    $item['DuocChon'] = false;
                }
            } else {
                // Sản phẩm không tồn tại
                $item['NgungKinhDoanh'] = true;
                $item['KhongKhaDung'] = true;
                $item['DuocChon'] = false;
            }
        }
        unset($item);

        $this->saveCart($cart);

        // API Response
        if ($this->isApi) {
            $this->json([
                'items' => array_values($cart),
                'soLuong' => count($cart),
                'tongTien' => $this->calculateTotal($cart, true)
            ], 'Giỏ hàng');
        }

        // Web Response
        $this->view('gio-hang/index', [
            'title' => 'Giỏ hàng - ' . STORE_NAME,
            'gioHang' => $cart
        ]);
    }

    /**
     * POST: /gioHang/themAjax - Thêm sản phẩm (AJAX)
     */
    public function themAjax()
    {
        header('Content-Type: application/json; charset=utf-8');

        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $soLuong = max(1, (int)($_POST['soLuong'] ?? 1));

        // Validate
        if ($maThuoc <= 0) {
            echo json_encode(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ!']);
            exit;
        }

        // Lấy thông tin thuốc
        $thuoc = $this->getProductInfo($maThuoc);

        if (!$thuoc) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
            exit;
        }

        if (!$thuoc['IsActive']) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã ngừng kinh doanh!']);
            exit;
        }

        $soLuongTon = $thuoc['SoLuongTon'] ?? 0;
        if ($soLuongTon <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm đã hết hàng!']);
            exit;
        }

        // Lấy giỏ hàng hiện tại
        $cart = $this->getCart();
        $index = $this->findProduct($cart, $maThuoc);
        $soLuongTrongGio = ($index !== false) ? $cart[$index]['SoLuong'] : 0;

        // Kiểm tra tồn kho
        if ($soLuongTrongGio + $soLuong > $soLuongTon) {
            echo json_encode(['success' => false, 'message' => "Chỉ còn $soLuongTon sản phẩm trong kho!"]);
            exit;
        }

        // Tính giá
        $giaBan = $this->calculatePrice($thuoc);

        // Thêm hoặc cập nhật
        if ($index !== false) {
            $cart[$index]['SoLuong'] += $soLuong;
            $cart[$index]['GiaBan'] = $giaBan;
        } else {
            $cart[] = [
                'MaThuoc' => $maThuoc,
                'TenThuoc' => $thuoc['TenThuoc'],
                'HinhAnh' => $thuoc['HinhAnh'] ?? '',
                'GiaBan' => $giaBan,
                'SoLuong' => $soLuong,
                'DuocChon' => true
            ];
        }

        // Lưu giỏ hàng
        $this->saveCart($cart);

        echo json_encode([
            'success' => true,
            'soLuong' => count($cart),
            'message' => 'Đã thêm vào giỏ hàng!'
        ]);
        exit;
    }

    /**
     * GET: /gioHang/laySoLuong - Lấy số lượng sản phẩm (AJAX)
     */
    public function laySoLuong()
    {
        header('Content-Type: application/json; charset=utf-8');
        $cart = $this->getCart();
        echo json_encode(['soLuong' => count($cart)]);
        exit;
    }

    /**
     * POST: /gioHang/capNhatSoLuong - Cập nhật số lượng (AJAX)
     */
    public function capNhatSoLuong()
    {
        header('Content-Type: application/json; charset=utf-8');

        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $soLuong = (int)($_POST['soLuong'] ?? 0);

        $cart = $this->getCart();
        $index = $this->findProduct($cart, $maThuoc);

        if ($index === false) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ!']);
            exit;
        }

        if ($soLuong <= 0) {
            // Xóa sản phẩm
            array_splice($cart, $index, 1);
        } else {
            // Kiểm tra tồn kho
            $thuoc = $this->getProductInfo($maThuoc);
            $soLuongTon = $thuoc['SoLuongTon'] ?? 0;

            if ($soLuong > $soLuongTon) {
                echo json_encode(['success' => false, 'message' => "Chỉ còn $soLuongTon sản phẩm!"]);
                exit;
            }

            $cart[$index]['SoLuong'] = $soLuong;
        }

        $this->saveCart($cart);

        echo json_encode([
            'success' => true,
            'soLuong' => count($cart),
            'tongTien' => $this->calculateTotal($cart, true)
        ]);
        exit;
    }

    /**
     * POST: /gioHang/capNhatChon - Cập nhật trạng thái chọn (AJAX)
     */
    public function capNhatChon()
    {
        header('Content-Type: application/json; charset=utf-8');

        $maThuoc = (int)($_POST['maThuoc'] ?? 0);
        $duocChon = filter_var($_POST['duocChon'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $cart = $this->getCart();
        $index = $this->findProduct($cart, $maThuoc);

        if ($index !== false && empty($cart[$index]['KhongKhaDung'])) {
            $cart[$index]['DuocChon'] = $duocChon;
            $this->saveCart($cart);
        }

        echo json_encode([
            'success' => true,
            'tongTien' => $this->calculateTotal($cart, true)
        ]);
        exit;
    }

    /**
     * POST: /gioHang/chonTatCa - Chọn/bỏ chọn tất cả (AJAX)
     */
    public function chonTatCa()
    {
        header('Content-Type: application/json; charset=utf-8');

        $chon = filter_var($_POST['chon'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $cart = $this->getCart();
        foreach ($cart as &$item) {
            if (empty($item['KhongKhaDung'])) {
                $item['DuocChon'] = $chon;
            }
        }
        unset($item);

        $this->saveCart($cart);

        echo json_encode([
            'success' => true,
            'tongTien' => $this->calculateTotal($cart, true)
        ]);
        exit;
    }

    /**
     * GET: /gioHang/xoa/{maThuoc} - Xóa sản phẩm
     */
    public function xoa($maThuoc = null)
    {
        $maThuoc = (int)$maThuoc;
        $cart = $this->getCart();
        $index = $this->findProduct($cart, $maThuoc);

        if ($index !== false) {
            array_splice($cart, $index, 1);
            $this->saveCart($cart);
        }

        // API Response
        if ($this->isApi) {
            $this->json([
                'soLuong' => count($cart),
                'tongTien' => $this->calculateTotal($cart, true)
            ], 'Đã xóa khỏi giỏ hàng');
        }

        $this->redirect('gio-hang');
    }

    /**
     * GET: /gioHang/xoaTatCa - Xóa toàn bộ giỏ hàng
     */
    public function xoaTatCa()
    {
        $this->clearCart();
        $this->setFlash('success', 'Đã xóa toàn bộ giỏ hàng!');
        $this->redirect('gio-hang');
    }

    /**
     * GET: /gioHang/thanhToan - Trang thanh toán
     */
    public function thanhToan()
    {
        if (!$this->isLoggedIn()) {
            $this->setFlash('error', 'Vui lòng đăng nhập để thanh toán!');
            $_SESSION['redirect_after_login'] = 'gioHang/thanhToan';
            $this->redirect('user/phoneLogin');
        }

        $cart = $this->getCart();

        // Cập nhật và lọc sản phẩm
        foreach ($cart as &$item) {
            $thuoc = $this->getProductInfo($item['MaThuoc']);

            if ($thuoc) {
                $item['SoLuongTon'] = $thuoc['SoLuongTon'] ?? 0;
                $item['NgungKinhDoanh'] = !$thuoc['IsActive'];
                $item['KhongKhaDung'] = $item['NgungKinhDoanh'] || $item['SoLuongTon'] <= 0;
            } else {
                $item['NgungKinhDoanh'] = true;
                $item['KhongKhaDung'] = true;
            }
        }
        unset($item);

        // Lọc sản phẩm được chọn và khả dụng
        $cartCheckout = array_filter($cart, function($item) {
            return !empty($item['DuocChon']) && empty($item['KhongKhaDung']);
        });

        if (empty($cartCheckout)) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            $this->redirect('gio-hang');
        }

        // Lấy thông tin người dùng
        $nguoiDungModel = $this->model('NguoiDungModel');
        $nguoiDung = $nguoiDungModel->getById($this->getUserId());

        $this->view('gio-hang/thanh-toan', [
            'title' => 'Thanh toán - ' . STORE_NAME,
            'gioHang' => array_values($cartCheckout),
            'nguoiDung' => $nguoiDung
        ]);
    }

    /**
     * POST: /gioHang/datHang - Đặt hàng
     */
    public function datHang()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('user/phoneLogin');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('gio-hang');
        }

        $diaChiGiaoHang = trim($_POST['diaChiGiaoHang'] ?? '');
        $phuongThucThanhToan = $_POST['phuongThucThanhToan'] ?? 'Tiền mặt';

        if (empty($diaChiGiaoHang)) {
            $this->setFlash('error', 'Vui lòng nhập địa chỉ giao hàng!');
            $this->redirect('gioHang/thanhToan');
        }

        $cart = $this->getCart();

        // Lọc sản phẩm được chọn và khả dụng
        $cartOrder = array_filter($cart, function($item) {
            return !empty($item['DuocChon']) && empty($item['KhongKhaDung']);
        });

        if (empty($cartOrder)) {
            $this->setFlash('error', 'Vui lòng chọn sản phẩm để thanh toán!');
            $this->redirect('gio-hang');
        }

        // Tính tổng tiền
        $tongTien = $this->calculateTotal($cartOrder, false);

        try {
            $this->db->beginTransaction();

            // Tạo đơn hàng
            $stmt = $this->db->prepare("
                INSERT INTO don_hang (MaNguoiDung, NgayDatHang, DiaChiGiaoHang, PhuongThucThanhToan, TongTien, TrangThai) 
                VALUES (?, NOW(), ?, ?, ?, 'Cho xu ly')
            ");
            $stmt->execute([$this->getUserId(), $diaChiGiaoHang, $phuongThucThanhToan, $tongTien]);
            $maDonHang = $this->db->lastInsertId();

            // Thêm chi tiết đơn hàng
            foreach ($cartOrder as $item) {
                $thanhTien = $item['GiaBan'] * $item['SoLuong'];
                $stmt = $this->db->prepare("
                    INSERT INTO chi_tiet_don_hang (MaDonHang, MaThuoc, SoLuong, DonGia, ThanhTien) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$maDonHang, $item['MaThuoc'], $item['SoLuong'], $item['GiaBan'], $thanhTien]);
            }

            $this->db->commit();

            // Xóa sản phẩm đã đặt khỏi giỏ hàng
            $cartRemaining = array_filter($cart, function($item) {
                return empty($item['DuocChon']) || !empty($item['KhongKhaDung']);
            });

            if (!empty($cartRemaining)) {
                $this->saveCart(array_values($cartRemaining));
            } else {
                $this->clearCart();
            }

            $this->setFlash('success', 'Đặt hàng thành công!');

            if ($phuongThucThanhToan === 'Chuyển khoản') {
                $this->redirect('donHang/thanhToanQR/' . $maDonHang);
            }

            $this->redirect('donHang/chiTiet/' . $maDonHang);

        } catch (Exception $e) {
            $this->db->rollBack();
            $this->setFlash('error', 'Đặt hàng thất bại: ' . $e->getMessage());
            $this->redirect('gioHang/thanhToan');
        }
    }

    // ==================== RESTful API Methods ====================

    /**
     * POST /gio-hang?format=json - Thêm sản phẩm (API)
     */
    public function store()
    {
        $input = $this->getJsonInput();
        $_POST = $input; // Reuse themAjax logic
        $_POST['maThuoc'] = $input['maThuoc'] ?? $input['MaThuoc'] ?? 0;
        $_POST['soLuong'] = $input['soLuong'] ?? $input['SoLuong'] ?? 1;
        $this->themAjax();
    }

    /**
     * PUT /gio-hang/{maThuoc}?format=json - Cập nhật (API)
     */
    public function update($maThuoc = null)
    {
        $input = $this->getJsonInput();
        $_POST['maThuoc'] = $maThuoc ?? $input['maThuoc'] ?? 0;
        $_POST['soLuong'] = $input['soLuong'] ?? $input['SoLuong'] ?? 0;
        $this->capNhatSoLuong();
    }

    /**
     * DELETE /gio-hang/{maThuoc}?format=json - Xóa (API)
     */
    public function destroy($maThuoc = null)
    {
        $this->xoa($maThuoc);
    }
}
