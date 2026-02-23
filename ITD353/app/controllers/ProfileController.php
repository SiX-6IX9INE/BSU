<?php
/**
 * app/controllers/ProfileController.php
 * หน้าโปรไฟล์ผู้ใช้
 */

class ProfileController
{
    public function show(): void
    {
        requireLogin();
        $userModel = new User();
        $user = $userModel->findById(auth()['id']);

        // issues ของตัวเอง
        $issueModel = new Issue();
        $myIssues   = $issueModel->list(['user_id' => auth()['id']], 20, 0);

        view('layout/header', ['pageTitle' => 'โปรไฟล์ของฉัน']);
        view('profile/me', compact('user', 'myIssues'));
        view('layout/footer');
    }

    public function update(): void
    {
        requireLogin();
        verifyCsrf();

        $name  = trim($_POST['name']  ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if (mb_strlen($name) < 2) {
            flash('error', 'ชื่อต้องมีอย่างน้อย 2 ตัวอักษร');
            redirect('/me');
        }

        $userModel = new User();
        $userModel->update(auth()['id'], ['name' => $name, 'phone' => $phone]);

        // Update session
        $_SESSION['user']['name'] = $name;

        flash('success', 'อัปเดตโปรไฟล์สำเร็จ');
        redirect('/me');
    }

    public function changePassword(): void
    {
        requireLogin();
        verifyCsrf();

        $current = $_POST['current_password'] ?? '';
        $new_pw  = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $userModel = new User();
        $user = $userModel->findById(auth()['id']);

        if (!password_verify($current, $user['password'])) {
            flash('error', 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
            redirect('/me');
        }
        if (strlen($new_pw) < 8 || $new_pw !== $confirm) {
            flash('error', 'รหัสผ่านใหม่ต้องมีอย่างน้อย 8 ตัวอักษรและต้องตรงกัน');
            redirect('/me');
        }

        $userModel->updatePassword(auth()['id'], $new_pw);
        flash('success', 'เปลี่ยนรหัสผ่านสำเร็จ');
        redirect('/me');
    }
}
