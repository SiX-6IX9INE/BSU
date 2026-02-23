<?php
/**
 * app/controllers/AuthController.php
 * Login, Register, Logout, Forgot/Reset password
 */

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // -------------------------------------------------------
    // Register
    // -------------------------------------------------------
    public function registerForm(): void
    {
        requireGuest();
        view('layout/header', ['pageTitle' => 'สมัครสมาชิก']);
        view('auth/register');
        view('layout/footer');
    }

    public function register(): void
    {
        requireGuest();
        verifyCsrf();

        $name     = trim($_POST['name']     ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']       ?? '';
        $confirm  = $_POST['confirm']        ?? '';

        // validation
        $errors = [];
        if (mb_strlen($name) < 2)          $errors[] = 'ชื่อต้องมีอย่างน้อย 2 ตัวอักษร';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'อีเมลไม่ถูกต้อง';
        if (strlen($password) < 8)         $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
        if ($password !== $confirm)         $errors[] = 'รหัสผ่านไม่ตรงกัน';
        if ($this->userModel->emailExists($email)) $errors[] = 'อีเมลนี้ถูกใช้งานแล้ว';

        if ($errors) {
            foreach ($errors as $e) flash('error', $e);
            redirect('/register');
        }

        $id = $this->userModel->create(['name' => $name, 'email' => $email, 'password' => $password]);
        $user = $this->userModel->findById($id);
        loginUser($user);
        flash('success', 'ยินดีต้อนรับสู่ ' . APP_NAME . ' 🎉');
        redirect('/');
    }

    // -------------------------------------------------------
    // Login
    // -------------------------------------------------------
    public function loginForm(): void
    {
        requireGuest();
        view('layout/header', ['pageTitle' => 'เข้าสู่ระบบ']);
        view('auth/login');
        view('layout/footer');
    }

    public function login(): void
    {
        requireGuest();
        verifyCsrf();

        $email    = trim($_POST['email']    ?? '');
        $password = $_POST['password']       ?? '';

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
            redirect('/login');
        }

        if ($user['is_banned']) {
            flash('error', 'บัญชีของคุณถูกระงับการใช้งาน');
            redirect('/login');
        }

        loginUser($user);
        flash('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับ ' . $user['name']);

        $intended = $_SESSION['intended_url'] ?? '/';
        unset($_SESSION['intended_url']);
        redirect($intended);
    }

    // -------------------------------------------------------
    // Logout
    // -------------------------------------------------------
    public function logout(): void
    {
        logoutUser();
        flash('success', 'ออกจากระบบแล้ว');
        redirect('/');
    }

    // -------------------------------------------------------
    // Forgot Password
    // -------------------------------------------------------
    public function forgotForm(): void
    {
        requireGuest();
        view('layout/header', ['pageTitle' => 'ลืมรหัสผ่าน']);
        view('auth/forgot');
        view('layout/footer');
    }

    public function forgot(): void
    {
        requireGuest();
        verifyCsrf();

        $email = trim($_POST['email'] ?? '');
        $user  = $this->userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->setResetToken($user['id'], $token);
            $link = url('reset-password?token=' . $token);

            // DEV MODE: แสดงลิงก์แทนส่งอีเมล
            if (APP_DEBUG) {
                flash('info', '🔑 [DEV] Reset link: <a href="' . e($link) . '">' . e($link) . '</a>');
                redirect('/forgot-password');
            }
            // Production: ส่ง email จริง (ต้องติดตั้ง PHPMailer)
        }

        // ตอบเหมือนกันทั้งมีและไม่มี email เพื่อป้องกัน enumeration
        flash('success', 'หากอีเมลนี้มีในระบบ เราจะส่งลิงก์รีเซ็ตรหัสผ่านให้คุณ');
        redirect('/forgot-password');
    }

    public function resetForm(): void
    {
        $token = $_GET['token'] ?? '';
        $user  = $token ? (new User())->findByResetToken($token) : null;

        if (!$user) {
            flash('error', 'ลิงก์หมดอายุหรือไม่ถูกต้อง');
            redirect('/login');
        }

        view('layout/header', ['pageTitle' => 'ตั้งรหัสผ่านใหม่']);
        view('auth/reset', ['token' => $token]);
        view('layout/footer');
    }

    public function reset(): void
    {
        verifyCsrf();
        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';

        $user = (new User())->findByResetToken($token);
        if (!$user) {
            flash('error', 'ลิงก์หมดอายุ');
            redirect('/login');
        }
        if (strlen($password) < 8 || $password !== $confirm) {
            flash('error', 'รหัสผ่านไม่ถูกต้องหรือไม่ตรงกัน');
            redirect('/reset-password?token=' . urlencode($token));
        }

        $m = new User();
        $m->updatePassword($user['id'], $password);
        $m->clearResetToken($user['id']);
        flash('success', 'เปลี่ยนรหัสผ่านสำเร็จ กรุณาเข้าสู่ระบบใหม่');
        redirect('/login');
    }
}
