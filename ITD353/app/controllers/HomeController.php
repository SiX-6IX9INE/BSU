<?php
/**
 * app/controllers/HomeController.php
 * หน้าแรก: feed ปัญหาล่าสุด พร้อมฟิลเตอร์
 */

class HomeController
{
    public function index(): void
    {
        $issueModel    = new Issue();
        $categoryModel = new Category();

        $filters = [
            'status'      => $_GET['status']      ?? '',
            'urgency'     => $_GET['urgency']      ?? '',
            'category_id' => (int)($_GET['cat']   ?? 0),
            'search'      => trim($_GET['q']       ?? ''),
            'date_from'   => $_GET['date_from']    ?? '',
            'date_to'     => $_GET['date_to']      ?? '',
            'sort'        => $_GET['sort']         ?? 'latest',
        ];

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $total   = $issueModel->count($filters);
        $pag     = paginate($total, $perPage, $page, url('') . '?' . http_build_query(array_merge($_GET, ['page' => ''])));

        $issues     = $issueModel->list($filters, $perPage, $pag['offset']);
        $categories = $categoryModel->all();

        view('layout/header', ['pageTitle' => 'หน้าแรก – ' . APP_NAME]);
        view('home/index', compact('issues', 'categories', 'filters', 'pag'));
        view('layout/footer');
    }

    public function about(): void
    {
        view('layout/header', ['pageTitle' => 'เกี่ยวกับเรา – ' . APP_NAME]);
        view('about/index');
        view('layout/footer');
    }
}
