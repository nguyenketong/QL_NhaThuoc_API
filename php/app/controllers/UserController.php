<?php
/**
 * UserController - Quản lý người dùng (MVC + RESTful API)
 * Hỗ trợ cả Website (HTML) và API (JSON)
 */
class UserController extends Controller
{
    private $nguoiDungModel;

    public function __construct()
    {
        parent::__construct();
        $this->nguoiDungModel = $this->model('NguoiDungModel');
    }

    // ==================== RESTful API Methods ====================

    /**
     * GET /user?format=json - Danh sách người dùng (Admin)
     */
    public function index()
    {
        if ($this->isApi) {
            $this->requireAdmin();
            
            list($page, $limit, $offset) = $this->getPagination();
            $users = $this->nguoiDungModel->getAll([], $limit, $offset);
            $total = $this->nguoiDungModel->count();
            
            $this->jsonPaginate($users, $total, $page, $limit);
        }
        
        $this->redirect('user/profile');
    }

    /**
     * GET /user/show/{id}?format=json
     */
    public function show($id = null)
    {
        if (!$this->isApi) {
            $this->redirect('user/profile');
        }
        
        $this->requireLogin();
        
        $currentUserId = $this->getUserId();
        $role = $_SESSION['user_role'] ?? 'User';
        
        if ($role !== 'Admin' && $id != $currentUserId) {
            $this->jsonError('Forbidden', 403);
        }
        
        $user = $this->nguoiDungModel->getById($id);
        
        if (!$user) {
            $this->jsonError('Người dùng không tồn tại', 404);
        }
        
        unset($user['MatKhau']);
        $this->json($user, 'Chi tiết người dùng');
    }

    /**
     * POST /user/store?format=json - Tạo người dùng (Admin)
     */
    public function store()
    {
        if (!$this->isApi) {
            $this->redirect('user/phoneLogin');
        }
        
        $this->requireAdmin();
        
        $input = $this->getJsonInput();
        $errors = $this->validate($input, [
            'HoTen' => 'required|min:2',
            'SoDienThoai' => 'required|min:10'
        ]);
        
        if ($errors) $this->jsonError('Validation failed', 422, $errors);
        
        if ($this->nguoiDungModel->findByPhone($input['SoDienThoai'])) {
            $this->jsonError('Số điện thoại đã tồn tại', 409);
        }
        
        if (!empty($input['MatKhau'])) {
            $input['MatKhau'] = password_hash($input['MatKhau'], PASSWORD_DEFAULT);
        }
        
        $input['NgayTao'] = date('Y-m-d H:i:s');
        $input['VaiTro'] = $input['VaiTro'] ?? 'User';
        
        $id = $this->nguoiDungModel->create($input);
        
        if ($id) {
            $user = $this->nguoiDungModel->getById($id);
            unset($user['MatKhau']);
            $this->json($user, 'Tạo người dùng thành công', 201);
        }
        
        $this->jsonError('Không thể tạo người dùng', 500);
    }

    /**
     * PUT /user/update/{id}?format=json
     */
    public function update($id = null)
    {
        if (!$this->isApi) {
            $this->redirect('user/profile');
        }
        
        $this->requireLogin();
        
        if (!$id) $this->jsonError('ID is required', 400);
        
        $currentUserId = $this->getUserId();
        $role = $_SESSION['user_role'] ?? 'User';
        
        if ($role !== 'Admin' && $id != $currentUserId) {
            $this->jsonError('Forbidden', 403);
        }
        
        $user = $this->nguoiDungModel->getById($id);
        if (!$user) $this->jsonError('Người dùng không tồn tại', 404);
        
        $input = $this->getJsonInput();
        
        if ($role !== 'Admin') unset($input['VaiTro']);
        
        if (!empty($input['MatKhau'])) {
            $input['MatKhau'] = password_hash($input['MatKhau'], PASSWORD_DEFAULT);
        }
        
        $result = $this->nguoiDungModel->update($id, $input, 'MaNguoiDung');
        
        if ($result) {
            $user = $this->nguoiDungModel->getById($id);
            unset($user['MatKhau']);
            $this->json($user, 'Cập nhật thành công');
        }
        
        $this->jsonError('Không thể cập nhật', 500);
    }

    /**
     * DELETE /user/destroy/{id}?format=json (Admin)
     */
    public function destroy($id = null)
    {
        if (!$this->isApi) $this->redirect('');
        
        $this->requireAdmin();
        
        if (!$id) $this->jsonError('ID is required', 400);
        
        $user = $this->nguoiDungModel->getById($id);
        if (!$user) $this->jsonError('Người dùng không tồn tại', 404);
        
        $result = $this->nguoiDungModel->update($id, ['IsActive' => 0], 'MaNguoiDung');
        
        if ($result) $this->json(null, 'Xóa người dùng thành công');
        
        $this->jsonError('Không thể xóa', 500);
    }

    // ==================== Auth API ====================

    /**
     * POST /user/requestOtp?format=json - Gửi OTP qua API
     */
    public function requestOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isApi) $this->jsonError('Method not allowed', 405);
            $this->redirect('user/phoneLogin');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $soDienThoai = preg_replace('/[^0-9]/', '', $input['SoDienThoai'] ?? $input['phone'] ?? '');

        if (strlen($soDienThoai) < 10) {
            if ($this->isApi) $this->jsonError('Số điện thoại không hợp lệ', 400);
            $this->setFlash('error', 'Số điện thoại không hợp lệ!');
            $this->redirect('user/phoneLogin');
        }

        $otp = rand(100000, 999999);
        
        $nguoiDung = $this->nguoiDungModel->findByPhone($soDienThoai);
        
        if (!$nguoiDung) {
            $this->nguoiDungModel->create([
                'SoDienThoai' => $soDienThoai,
                'HoTen' => 'Khách hàng ' . substr($soDienThoai, -4),
                'VaiTro' => 'User',
                'OTP' => $otp,
                'OTP_Expire' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
                'NgayTao' => date('Y-m-d H:i:s')
            ]);
        } else {
            $this->nguoiDungModel->saveOtp($nguoiDung['MaNguoiDung'], $otp);
        }

        // Gửi SMS thật nếu OTP_MODE = 'real'
        if (defined('OTP_MODE') && OTP_MODE === 'real') {
            $result = $this->sendEsmsOtp($soDienThoai, $otp);
            if (!$result['success']) {
                if ($this->isApi) $this->jsonError($result['message'], 500);
                $this->setFlash('error', $result['message']);
                $this->redirect('user/phoneLogin');
            }
        }

        if ($this->isApi) {
            $response = ['SoDienThoai' => $soDienThoai];
            // Dev mode: trả về OTP để test
            if (defined('OTP_MODE') && OTP_MODE === 'dev') {
                $response['otp'] = $otp;
            }
            $this->json($response, 'OTP đã được gửi');
        }

        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $soDienThoai;
        $_SESSION['otp_time'] = time();
        $this->redirect('user/verifyOtp');
    }

    /**
     * POST /user/verifyOtpApi?format=json - Xác nhận OTP qua API
     */
    public function verifyOtpApi()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isApi) $this->jsonError('Method not allowed', 405);
            $this->redirect('user/phoneLogin');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $soDienThoai = preg_replace('/[^0-9]/', '', $input['SoDienThoai'] ?? $input['phone'] ?? '');
        $otpInput = $input['otp'] ?? $input['OTP'] ?? '';

        if (empty($soDienThoai) || empty($otpInput)) {
            if ($this->isApi) $this->jsonError('Vui lòng nhập số điện thoại và OTP', 400);
            $this->redirect('user/phoneLogin');
        }

        $nguoiDung = $this->nguoiDungModel->verifyOtp($soDienThoai, $otpInput);

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('OTP không đúng hoặc đã hết hạn', 401);
            $this->setFlash('error', 'OTP không đúng hoặc đã hết hạn!');
            $this->redirect('user/phoneLogin');
        }

        // Login success
        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        $_SESSION['user_name'] = $nguoiDung['HoTen'];
        $_SESSION['user_role'] = $nguoiDung['VaiTro'] ?? 'User';
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        if ($this->isApi) {
            unset($nguoiDung['MatKhau'], $nguoiDung['OTP'], $nguoiDung['OTP_Expire']);
            $this->json(['user' => $nguoiDung], 'Đăng nhập thành công');
        }

        $this->setFlash('success', 'Đăng nhập thành công!');
        $this->redirect('');
    }

    /**
     * POST /user/login?format=json
     * Body: { "phone": "0795930020", "password": "admin123" }
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/phoneLogin');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $phone = $input['phone'] ?? $input['SoDienThoai'] ?? '';
        $password = $input['password'] ?? $input['MatKhau'] ?? '';

        if (empty($phone) || empty($password)) {
            if ($this->isApi) $this->jsonError('Vui lòng nhập số điện thoại và mật khẩu', 400);
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin!');
            $this->redirect('user/phoneLogin');
        }

        $nguoiDung = $this->nguoiDungModel->findByPhone($phone);

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('Số điện thoại không tồn tại', 401);
            $this->setFlash('error', 'Số điện thoại không tồn tại!');
            $this->redirect('user/phoneLogin');
        }

        // Verify password
        $isValid = false;
        if (!empty($nguoiDung['MatKhau'])) {
            if (password_verify($password, $nguoiDung['MatKhau'])) {
                $isValid = true;
            } elseif ($password === $nguoiDung['MatKhau']) {
                $isValid = true; // Plain text fallback
            }
        }

        if (!$isValid) {
            if ($this->isApi) $this->jsonError('Mật khẩu không đúng', 401);
            $this->setFlash('error', 'Mật khẩu không đúng!');
            $this->redirect('user/phoneLogin');
        }

        // Login success - set session
        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        $_SESSION['user_name'] = $nguoiDung['HoTen'];
        $_SESSION['user_role'] = $nguoiDung['VaiTro'] ?? 'User';
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        if ($this->isApi) {
            unset($nguoiDung['MatKhau'], $nguoiDung['OTP'], $nguoiDung['OTP_Expire']);
            $this->json([
                'user' => $nguoiDung,
                'role' => $nguoiDung['VaiTro'] ?? 'User',
                'session_id' => session_id()
            ], 'Đăng nhập thành công');
        }

        $this->setFlash('success', 'Đăng nhập thành công!');
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirectUrl ?: '');
    }

    /**
     * POST /user/register?format=json
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/phoneLogin');
        }

        $input = $this->isApi ? $this->getJsonInput() : $_POST;
        $name = $input['name'] ?? $input['HoTen'] ?? '';
        $phone = $input['phone'] ?? $input['SoDienThoai'] ?? '';
        $password = $input['password'] ?? $input['MatKhau'] ?? '';

        if (empty($name) || empty($phone) || empty($password)) {
            if ($this->isApi) $this->jsonError('Vui lòng nhập đầy đủ thông tin', 400);
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin!');
            $this->redirect('user/phoneLogin');
        }

        if ($this->nguoiDungModel->findByPhone($phone)) {
            if ($this->isApi) $this->jsonError('Số điện thoại đã được đăng ký', 409);
            $this->setFlash('error', 'Số điện thoại đã được đăng ký!');
            $this->redirect('user/phoneLogin');
        }

        $id = $this->nguoiDungModel->create([
            'HoTen' => $name,
            'SoDienThoai' => $phone,
            'MatKhau' => password_hash($password, PASSWORD_DEFAULT),
            'VaiTro' => 'User',
            'NgayTao' => date('Y-m-d H:i:s')
        ]);

        if ($id) {
            $nguoiDung = $this->nguoiDungModel->getById($id);
            
            $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
            $_SESSION['user_name'] = $nguoiDung['HoTen'];
            $_SESSION['user_role'] = 'User';
            setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

            if ($this->isApi) {
                unset($nguoiDung['MatKhau']);
                $this->json(['user' => $nguoiDung], 'Đăng ký thành công', 201);
            }

            $this->setFlash('success', 'Đăng ký thành công!');
            $this->redirect('');
        }

        if ($this->isApi) $this->jsonError('Không thể đăng ký', 500);
        $this->setFlash('error', 'Đăng ký thất bại!');
        $this->redirect('user/phoneLogin');
    }

    // ==================== Website Methods ====================

    public function phoneLogin()
    {
        if ($this->isLoggedIn()) $this->redirect('');
        
        $googleLoginUrl = '';
        if (defined('GOOGLE_CLIENT_ID') && !empty(GOOGLE_CLIENT_ID) && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID') {
            $params = [
                'client_id' => GOOGLE_CLIENT_ID,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'response_type' => 'code',
                'scope' => 'email profile',
                'access_type' => 'online',
                'prompt' => 'select_account'
            ];
            $googleLoginUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }
        
        $this->view('user/phone-login', [
            'title' => 'Đăng nhập - ' . STORE_NAME,
            'googleLoginUrl' => $googleLoginUrl
        ]);
    }

    public function googleCallback()
    {
        $code = $_GET['code'] ?? '';
        
        if (empty($code)) {
            $this->setFlash('error', 'Đăng nhập Google thất bại!');
            $this->redirect('user/phoneLogin');
        }

        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $tokenData = [
            'code' => $code,
            'client_id' => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'grant_type' => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $tokenResponse = curl_exec($ch);
        curl_close($ch);

        $tokenResult = json_decode($tokenResponse, true);
        
        if (empty($tokenResult['access_token'])) {
            $this->setFlash('error', 'Không thể lấy token từ Google!');
            $this->redirect('user/phoneLogin');
        }

        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenResult['access_token'];
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userResponse, true);
        
        if (empty($googleUser['email'])) {
            $this->setFlash('error', 'Không thể lấy thông tin từ Google!');
            $this->redirect('user/phoneLogin');
        }

        $nguoiDung = $this->nguoiDungModel->findByEmail($googleUser['email']);

        if (!$nguoiDung) {
            $maNguoiDung = $this->nguoiDungModel->create([
                'Email' => $googleUser['email'],
                'HoTen' => $googleUser['name'] ?? 'Google User',
                'Avatar' => $googleUser['picture'] ?? '',
                'GoogleId' => $googleUser['id'] ?? '',
                'LoaiDangNhap' => 'Google',
                'VaiTro' => 'User',
                'NgayTao' => date('Y-m-d H:i:s')
            ]);
            $nguoiDung = $this->nguoiDungModel->getById($maNguoiDung);
        } else {
            $this->nguoiDungModel->update($nguoiDung['MaNguoiDung'], [
                'HoTen' => $googleUser['name'] ?? $nguoiDung['HoTen'],
                'Avatar' => $googleUser['picture'] ?? $nguoiDung['Avatar'],
                'GoogleId' => $googleUser['id'] ?? ''
            ], 'MaNguoiDung');
        }

        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        $_SESSION['user_name'] = $nguoiDung['HoTen'];
        $_SESSION['user_role'] = $nguoiDung['VaiTro'] ?? 'User';
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        $this->setFlash('success', 'Đăng nhập Google thành công!');
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirectUrl ?: '');
    }

    public function sendOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('user/phoneLogin');

        $soDienThoai = preg_replace('/[^0-9]/', '', $_POST['soDienThoai'] ?? '');

        if (strlen($soDienThoai) < 10) {
            $this->setFlash('error', 'Số điện thoại không hợp lệ!');
            $this->redirect('user/phoneLogin');
        }

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $soDienThoai;
        $_SESSION['otp_time'] = time();

        $nguoiDung = $this->nguoiDungModel->findByPhone($soDienThoai);
        
        if (!$nguoiDung) {
            $this->nguoiDungModel->create([
                'SoDienThoai' => $soDienThoai,
                'HoTen' => 'Khách hàng ' . substr($soDienThoai, -4),
                'VaiTro' => 'User',
                'OTP' => $otp,
                'OTP_Expire' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ]);
        } else {
            $this->nguoiDungModel->saveOtp($nguoiDung['MaNguoiDung'], $otp);
        }

        if (defined('OTP_MODE') && OTP_MODE === 'real') {
            $result = $this->sendEsmsOtp($soDienThoai, $otp);
            if (!$result['success']) {
                $this->setFlash('error', $result['message']);
                $this->redirect('user/phoneLogin');
            }
        }

        $this->redirect('user/verifyOtp');
    }

    public function verifyOtp()
    {
        $soDienThoai = $_SESSION['otp_phone'] ?? '';
        if (empty($soDienThoai)) $this->redirect('user/phoneLogin');

        $this->view('user/verify-otp', [
            'title' => 'Xác nhận OTP - ' . STORE_NAME,
            'soDienThoai' => $soDienThoai,
            'devOtp' => (defined('OTP_MODE') && OTP_MODE === 'dev') ? ($_SESSION['otp'] ?? '') : null
        ]);
    }

    public function confirmOtp()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('user/phoneLogin');

        $otpInput = $_POST['otp'] ?? '';
        $soDienThoai = $_SESSION['otp_phone'] ?? '';
        $otpSaved = $_SESSION['otp'] ?? '';
        $otpTime = $_SESSION['otp_time'] ?? 0;

        if (time() - $otpTime > 300) {
            $this->setFlash('error', 'Mã OTP đã hết hạn!');
            $this->redirect('user/phoneLogin');
        }

        if ($otpInput != $otpSaved) {
            $this->setFlash('error', 'Mã OTP không đúng!');
            $this->redirect('user/verifyOtp');
        }

        $nguoiDung = $this->nguoiDungModel->findByPhone($soDienThoai);

        if (!$nguoiDung) {
            $maNguoiDung = $this->nguoiDungModel->create([
                'SoDienThoai' => $soDienThoai,
                'HoTen' => 'Khách hàng ' . substr($soDienThoai, -4),
                'VaiTro' => 'User'
            ]);
            $nguoiDung = $this->nguoiDungModel->getById($maNguoiDung);
        }

        $_SESSION['user_id'] = $nguoiDung['MaNguoiDung'];
        $_SESSION['user_name'] = $nguoiDung['HoTen'];
        $_SESSION['user_role'] = $nguoiDung['VaiTro'] ?? 'User';
        setcookie('UserId', $nguoiDung['MaNguoiDung'], time() + 30 * 24 * 3600, '/');

        unset($_SESSION['otp'], $_SESSION['otp_phone'], $_SESSION['otp_time']);

        $this->setFlash('success', 'Đăng nhập thành công!');
        $redirectUrl = $_SESSION['redirect_after_login'] ?? '';
        unset($_SESSION['redirect_after_login']);
        $this->redirect($redirectUrl ?: '');
    }

    public function profile()
    {
        $this->requireLogin();

        $nguoiDung = $this->nguoiDungModel->getById($this->getUserId());

        if (!$nguoiDung) {
            if ($this->isApi) $this->jsonError('User không tồn tại', 404);
            $this->redirect('user/logout');
        }

        // API response
        if ($this->isApi) {
            unset($nguoiDung['MatKhau'], $nguoiDung['OTP'], $nguoiDung['OTP_Expire']);
            $this->json($nguoiDung, 'Thông tin user');
        }

        $this->view('user/profile', [
            'title' => 'Thông tin tài khoản - ' . STORE_NAME,
            'nguoiDung' => $nguoiDung,
            'tongDonHang' => $nguoiDung['so_don_hang'] ?? 0,
            'tongChiTieu' => $nguoiDung['tong_tien_da_mua'] ?? 0
        ]);
    }

    public function updateProfile()
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('user/profile');

        $updateData = [
            'HoTen' => $_POST['hoTen'] ?? '',
            'DiaChi' => $_POST['diaChi'] ?? ''
        ];

        $result = $this->nguoiDungModel->update($this->getUserId(), $updateData, 'MaNguoiDung');

        if ($result) {
            $_SESSION['user_name'] = $updateData['HoTen'];
            $this->setFlash('success', 'Cập nhật thông tin thành công!');
        } else {
            $this->setFlash('error', 'Cập nhật thất bại!');
        }
        
        $this->redirect('user/profile');
    }

    public function diaChi()
    {
        $this->requireLogin();

        $this->view('user/dia-chi', [
            'title' => 'Quản lý địa chỉ - ' . STORE_NAME,
            'nguoiDung' => $this->nguoiDungModel->getById($this->getUserId()),
            'activeMenu' => 'diachi'
        ]);
    }

    public function logout()
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        setcookie('UserId', '', time() - 3600, '/');
        
        if ($this->isApi) $this->json(null, 'Đăng xuất thành công');
        
        $this->setFlash('success', 'Đăng xuất thành công!');
        $this->redirect('');
    }

    private function sendEsmsOtp($phone, $otp)
    {
        $content = "Ma OTP cua ban la: $otp. Ma co hieu luc trong 5 phut.";
        
        $data = [
            'ApiKey' => ESMS_API_KEY,
            'SecretKey' => ESMS_SECRET_KEY,
            'Phone' => $phone,
            'Content' => $content,
            'SmsType' => 8
        ];

        $ch = curl_init(ESMS_BASE_URL . '/SendMultipleMessage_V4_post_json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) return ['success' => false, 'message' => 'Lỗi kết nối: ' . $error];

        $result = json_decode($response, true);
        
        if (isset($result['CodeResult']) && $result['CodeResult'] == '100') {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => $result['ErrorMessage'] ?? 'Không thể gửi OTP'];
    }
}
