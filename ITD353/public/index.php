<?php
/**
 * public/index.php  –  Front Controller / Router
 * ทุก request ถูกส่งมาที่นี่ผ่าน .htaccess mod_rewrite
 */

// -------------------------------------------------------
// Bootstrap
// -------------------------------------------------------
require_once dirname(__DIR__) . '/app/config.php';
require_once APP_PATH . '/helpers.php';
require_once APP_PATH . '/middlewares/AuthMiddleware.php';

// Auto-load models & controllers
spl_autoload_register(function (string $class): void {
    $dirs = [
        APP_PATH . '/models/'      => true,
        APP_PATH . '/controllers/' => true,
    ];
    foreach ($dirs as $dir => $_) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// -------------------------------------------------------
// Simple Router
// -------------------------------------------------------
$uri    = strtok($_SERVER['REQUEST_URI'], '?');
$uri    = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Strip base sub-path – derive from BASE_URL so it works
// whether accessed via root (.htaccess forward) or /public/ directly
$basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}
$uri = rtrim($uri, '/') ?: '/';

// -------------------------------------------------------
// Route definitions
// -------------------------------------------------------
function dispatch(string $method, string $uri): void
{
    // -------- Public routes --------
    if ($method === 'GET' && $uri === '/') {
        (new HomeController())->index();
        return;
    }

    if ($method === 'GET' && $uri === '/about') {
        (new HomeController())->about();
        return;
    }

    // Auth
    if ($method === 'GET'  && $uri === '/register') { (new AuthController())->registerForm(); return; }
    if ($method === 'POST' && $uri === '/register') { (new AuthController())->register();     return; }
    if ($method === 'GET'  && $uri === '/login')    { (new AuthController())->loginForm();    return; }
    if ($method === 'POST' && $uri === '/login')    { (new AuthController())->login();        return; }
    if ($method === 'GET'  && $uri === '/logout')   { (new AuthController())->logout();       return; }
    if ($method === 'GET'  && $uri === '/forgot-password') { (new AuthController())->forgotForm(); return; }
    if ($method === 'POST' && $uri === '/forgot-password') { (new AuthController())->forgot();       return; }
    if ($method === 'GET'  && $uri === '/reset-password')  { (new AuthController())->resetForm();    return; }
    if ($method === 'POST' && $uri === '/reset-password')  { (new AuthController())->reset();        return; }

    // Issue
    if ($method === 'GET'  && $uri === '/issue/new') { (new IssueController())->newForm(); return; }
    if ($method === 'POST' && $uri === '/issue/new') { (new IssueController())->store();   return; }

    if (preg_match('#^/issue/(\d+)$#', $uri, $m) && $method === 'GET') {
        (new IssueController())->detail((int)$m[1]); return;
    }
    if (preg_match('#^/issue/(\d+)/vote$#', $uri, $m) && $method === 'POST') {
        (new IssueController())->vote((int)$m[1]); return;
    }
    if (preg_match('#^/issue/(\d+)/comment$#', $uri, $m) && $method === 'POST') {
        (new IssueController())->comment((int)$m[1]); return;
    }
    if (preg_match('#^/comment/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
        (new IssueController())->deleteComment((int)$m[1]); return;
    }

    // Profile
    if ($method === 'GET'  && $uri === '/me')              { (new ProfileController())->show();           return; }
    if ($method === 'POST' && $uri === '/me/update')       { (new ProfileController())->update();         return; }
    if ($method === 'POST' && $uri === '/me/password')     { (new ProfileController())->changePassword(); return; }

    // -------- Admin routes --------
    if ($method === 'GET' && $uri === '/admin')             { (new AdminController())->dashboard();  return; }

    if ($method === 'GET'  && $uri === '/admin/issues')     { (new AdminController())->issues();     return; }
    if (preg_match('#^/admin/issues/(\d+)$#', $uri, $m) && $method === 'GET') {
        (new AdminController())->issueDetail((int)$m[1]); return;
    }
    if (preg_match('#^/admin/issues/(\d+)/update$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->updateIssue((int)$m[1]); return;
    }
    if (preg_match('#^/admin/issues/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->deleteIssue((int)$m[1]); return;
    }
    if (preg_match('#^/admin/comment/(\d+)/pin$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->togglePinComment((int)$m[1]); return;
    }

    if ($method === 'GET'  && $uri === '/admin/categories') { (new AdminController())->categories();      return; }
    if ($method === 'POST' && $uri === '/admin/categories/create') { (new AdminController())->createCategory(); return; }
    if (preg_match('#^/admin/categories/(\d+)/update$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->updateCategory((int)$m[1]); return;
    }
    if (preg_match('#^/admin/categories/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->deleteCategory((int)$m[1]); return;
    }

    if ($method === 'GET' && $uri === '/admin/users') { (new AdminController())->users(); return; }
    if (preg_match('#^/admin/users/(\d+)/update$#', $uri, $m) && $method === 'POST') {
        (new AdminController())->updateUser((int)$m[1]); return;
    }

    // 404
    http_response_code(404);
    view('layout/header', ['pageTitle' => '404 – ไม่พบหน้านี้']);
    view('errors/404');
    view('layout/footer');
}

dispatch($method, $uri);
