<?php
/**
 * Auth Controller - Đăng nhập Admin (MVC + RESTful API)
 */
class AuthController extends AdminController
{
    const DEFAULT_PASSWORD = 'admin123';

    /**
     * POST /admin/?controller=auth&action=login&format=json
     */
    public function login()
    {
        // Nếu đã đăng nhập
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            if ($this->isApi) {
                $this->json([
                    'admin_id' => $_SESSION['admin_id'],
                    'admin_name' => $_SESSION['admin_name'],
                    'admin_phone' => $_SESSION['admin_phone']
                ], 'Already logged in');
            }
            $this->redirect('');
            return;
        }

        $error = null;

        if ($this->isPost() || ($this->isApi && $_SERVER['REQUEST_METHOD'] === 'POST')) {
            $input = $this->isApi ? $this->getJsonInput() : $_POST;
            $soDienThoai = $input['soDienThoai'] ?? $input['phone'] ?? '';
            $matKhau = $input['matKhau'] ?? $input['password'] ?? '';

            // Validate
            if (empty($soDienThoai) || empty($matKhau)) {
                if ($this->isApi) $this->jsonError('Vui lòng nhập số điện thoại và mật khẩu', 400);
                $error = 'Vui lòng nhập đầy đủ thông tin!';
            } else {
                $stmt = $this->db->prepare("SELECT * FROM nguoi_dung WHERE SoDienThoai = ? AND VaiTro = 'Admin'");
                $stmt->execute([$soDienThoai]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$admin) {
                    if ($this->isApi) $this->jsonError('Tài khoản không có quyền quản trị', 401);
                    $error = 'Tài khoản không có quyền quản trị!';
                } else {
                    $matKhauDB = $admin['MatKhau'] ?? null;
                    $isValidPassword = false;
                    $isDefaultPassword = false;

                    if (empty($matKhauDB) || $matKhauDB === self::DEFAULT_PASSWORD) {
                        if ($matKhau === self::DEFAULT_PASSWORD) {
                            $isValidPassword = true;
                            $isDefaultPassword = true;
                        }
                    } elseif (password_verify($matKhau, $matKhauDB)) {
                        $isValidPassword = true;
                    } elseif ($matKhau === $matKhauDB) {
                        $isValidPassword = true;
                        $isDefaultPassword = ($matKhau === self::DEFAULT_PASSWORD);
                    }

                    if (!$isValidPassword) {
                        if ($this->isApi) $this->jsonError('Mật khẩu không đúng', 401);
                        $error = 'Mật khẩu không đúng!';
                    } else {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $admin['MaNguoiDung'];
                        $_SESSION['admin_name'] = $admin['HoTen'] ?? $admin['SoDienThoai'];
                        $_SESSION['admin_phone'] = $admin['SoDienThoai'];
                        $_SESSION['require_password_change'] = $isDefaultPassword;

                        setcookie('AdminLoggedIn', 'true', time() + (7 * 24 * 60 * 60), '/', '', false, true);

                        if ($this->isApi) {
                            $this->json([
                                'admin_id' => $admin['MaNguoiDung'],
                                'admin_name' => $admin['HoTen'] ?? $admin['SoDienThoai'],
                                'admin_phone' => $admin['SoDienThoai'],
                                'require_password_change' => $isDefaultPassword
                            ], 'Đăng nhập thành công');
                        }

                        if ($isDefaultPassword) {
                            $this->setFlash('warning', 'Bạn đang sử dụng mật khẩu mặc định. Vui lòng đổi mật khẩu!');
                            $this->redirect('?controller=auth&action=changePassword');
                        } else {
                            $this->setFlash('success', 'Đăng nhập Admin thành công!');
                            $this->redirect('');
                        }
                        return;
                    }
                }
            }
        }

        $this->viewWithoutLayout('auth/login', ['error' => $error]);
    }

    /**
     * POST /admin/?controller=auth&action=changePassword&format=json
     */
    public function changePassword()
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            if ($this->isApi) $this->jsonError('Unauthorized', 401);
            $this->redirect('?controller=auth&action=login');
            return;
        }

        $error = null;
        $requireChange = $_SESSION['require_password_change'] ?? false;

        if ($this->isPost() || ($this->isApi && $_SERVER['REQUEST_METHOD'] === 'POST')) {
            $input = $this->isApi ? $this->getJsonInput() : $_POST;
            $matKhauCu = $input['matKhauCu'] ?? $input['old_password'] ?? '';
            $matKhauMoi = $input['matKhauMoi'] ?? $input['new_password'] ?? '';
            $xacNhanMatKhau = $input['xacNhanMatKhau'] ?? $input['confirm_password'] ?? $matKhauMoi;

            if (empty($matKhauCu) || empty($matKhauMoi)) {
                if ($this->isApi) $this->jsonError('Vui lòng nhập đầy đủ thông tin', 400);
                $error = 'Vui lòng nhập đầy đủ thông tin!';
            } elseif (strlen($matKhauMoi) < 6) {
                if ($this->isApi) $this->jsonError('Mật khẩu mới phải có ít nhất 6 ký tự', 422);
                $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            } elseif ($matKhauMoi !== $xacNhanMatKhau) {
                if ($this->isApi) $this->jsonError('Xác nhận mật khẩu không khớp', 422);
                $error = 'Xác nhận mật khẩu không khớp!';
            } elseif ($matKhauMoi === self::DEFAULT_PASSWORD) {
                if ($this->isApi) $this->jsonError('Mật khẩu mới không được trùng với mật khẩu mặc định', 422);
                $error = 'Mật khẩu mới không được trùng với mật khẩu mặc định!';
            } else {
                $adminId = $_SESSION['admin_id'];
                $stmt = $this->db->prepare("SELECT MatKhau FROM nguoi_dung WHERE MaNguoiDung = ?");
                $stmt->execute([$adminId]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                $matKhauDB = $admin['MatKhau'] ?? null;

                $isValidOldPassword = false;
                if (empty($matKhauDB) || $matKhauDB === self::DEFAULT_PASSWORD) {
                    $isValidOldPassword = ($matKhauCu === self::DEFAULT_PASSWORD);
                } elseif (password_verify($matKhauCu, $matKhauDB)) {
                    $isValidOldPassword = true;
                } elseif ($matKhauCu === $matKhauDB) {
                    $isValidOldPassword = true;
                }

                if (!$isValidOldPassword) {
                    if ($this->isApi) $this->jsonError('Mật khẩu cũ không đúng', 401);
                    $error = 'Mật khẩu cũ không đúng!';
                } else {
                    $hashedPassword = password_hash($matKhauMoi, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("UPDATE nguoi_dung SET MatKhau = ? WHERE MaNguoiDung = ?");
                    $stmt->execute([$hashedPassword, $adminId]);

                    $_SESSION['require_password_change'] = false;

                    if ($this->isApi) $this->json(null, 'Đổi mật khẩu thành công');
                    $this->setFlash('success', 'Đổi mật khẩu thành công!');
                    $this->redirect('');
                    return;
                }
            }
        }

        $this->viewWithoutLayout('auth/change-password', ['error' => $error, 'requireChange' => $requireChange]);
    }

    /**
     * POST /admin/?controller=auth&action=logout&format=json
     */
    public function logout()
    {
        unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_phone'], $_SESSION['require_password_change']);
        setcookie('AdminLoggedIn', '', time() - 3600, '/');

        if ($this->isApi) $this->json(null, 'Đăng xuất thành công');
        $this->redirect('?controller=auth&action=login');
    }
}
