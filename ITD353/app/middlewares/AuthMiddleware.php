<?php
/**
 * app/middlewares/AuthMiddleware.php
 * Protect routes that require login or admin role
 */

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('error', 'กรุณาเข้าสู่ระบบก่อน');
        redirect('/login');
    }
    if (auth()['role'] !== 'banned' && isset($_SESSION['user']['is_banned']) && $_SESSION['user']['is_banned']) {
        logoutUser();
        flash('error', 'บัญชีของคุณถูกระงับการใช้งาน');
        redirect('/login');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        view('errors/403');
        exit;
    }
}

function requireGuest(): void
{
    if (isLoggedIn()) {
        redirect('/');
    }
}
