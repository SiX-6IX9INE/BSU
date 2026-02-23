<?php
/**
 * app/controllers/AdminController.php
 * Dashboard, Issues, Categories, Users management
 */

class AdminController
{
    private Issue    $issueModel;
    private Category $categoryModel;
    private User     $userModel;
    private Comment  $commentModel;

    public function __construct()
    {
        $this->issueModel    = new Issue();
        $this->categoryModel = new Category();
        $this->userModel     = new User();
        $this->commentModel  = new Comment();
    }

    // -------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------
    public function dashboard(): void
    {
        requireAdmin();
        $statsToday    = $this->issueModel->statsToday();
        $statsWeek     = $this->issueModel->statsWeek();
        $statsByStatus = $this->issueModel->countByStatus();
        $statsByCategory = $this->issueModel->statsByCategory();

        // ปัญหาล่าสุด 5 รายการ
        $recentIssues = $this->issueModel->list([], 5, 0);

        view('layout/header', ['pageTitle' => 'Admin Dashboard']);
        view('admin/dashboard', compact('statsToday','statsWeek','statsByStatus','statsByCategory','recentIssues'));
        view('layout/footer');
    }

    // -------------------------------------------------------
    // Issues management
    // -------------------------------------------------------
    public function issues(): void
    {
        requireAdmin();
        $filters = [
            'status'      => $_GET['status']   ?? '',
            'urgency'     => $_GET['urgency']  ?? '',
            'category_id' => (int)($_GET['cat'] ?? 0),
            'search'      => trim($_GET['q']   ?? ''),
            'sort'        => $_GET['sort']     ?? 'latest',
        ];
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $total   = $this->issueModel->count($filters);
        $pag     = paginate($total, $perPage, $page, url('admin/issues') . '?' . http_build_query(array_merge($_GET, ['page' => ''])));

        $issues     = $this->issueModel->list($filters, $perPage, $pag['offset']);
        $categories = $this->categoryModel->allForAdmin();

        view('layout/header', ['pageTitle' => 'จัดการปัญหา']);
        view('admin/issues', compact('issues','categories','filters','pag'));
        view('layout/footer');
    }

    public function issueDetail(int $id): void
    {
        requireAdmin();
        $issue      = $this->issueModel->findById($id);
        if (!$issue) redirect('/admin/issues');

        $images     = $this->issueModel->images($id);
        $statusLogs = $this->issueModel->statusLogs($id);
        $comments   = $this->commentModel->byIssue($id);
        $categories = $this->categoryModel->allForAdmin();

        view('layout/header', ['pageTitle' => 'แก้ไขปัญหา #' . $id]);
        view('admin/issue_edit', compact('issue','images','statusLogs','comments','categories'));
        view('layout/footer');
    }

    public function updateIssue(int $id): void
    {
        requireAdmin();
        verifyCsrf();

        $status    = $_POST['status']     ?? '';
        $note      = trim($_POST['note']  ?? '');
        $isPinned  = isset($_POST['is_pinned']) ? 1 : 0;
        $urgency   = $_POST['urgency']    ?? 'medium';

        $this->issueModel->updateStatus($id, $status, auth()['id'], $note);
        $this->issueModel->update($id, ['is_pinned' => $isPinned, 'urgency' => $urgency]);

        flash('success', 'อัปเดตปัญหาสำเร็จ');
        redirect('/admin/issues/' . $id);
    }

    public function deleteIssue(int $id): void
    {
        requireAdmin();
        verifyCsrf();
        $this->issueModel->delete($id);
        flash('success', 'ลบปัญหาแล้ว');
        redirect('/admin/issues');
    }

    public function togglePinComment(int $commentId): void
    {
        requireAdmin();
        $comment = $this->commentModel->findById($commentId);
        if ($comment) {
            $this->commentModel->togglePin($commentId);
        }
        redirect('/admin/issues/' . ($comment['issue_id'] ?? '') . '#comments');
    }

    // -------------------------------------------------------
    // Categories
    // -------------------------------------------------------
    public function categories(): void
    {
        requireAdmin();
        $categories = $this->categoryModel->allForAdmin();
        view('layout/header', ['pageTitle' => 'จัดการหมวดหมู่']);
        view('admin/categories', compact('categories'));
        view('layout/footer');
    }

    public function createCategory(): void
    {
        requireAdmin();
        verifyCsrf();

        $name  = trim($_POST['name']  ?? '');
        $slug  = trim($_POST['slug']  ?? '');
        $icon  = trim($_POST['icon']  ?? '📌');
        $color = trim($_POST['color'] ?? '#3b82f6');
        $order = (int)($_POST['sort_order'] ?? 0);

        if (!$name || !$slug) {
            flash('error', 'กรุณากรอกชื่อและ slug');
            redirect('/admin/categories');
        }
        if ($this->categoryModel->slugExists($slug)) {
            flash('error', 'Slug ซ้ำ');
            redirect('/admin/categories');
        }

        $this->categoryModel->create(compact('name','slug','icon','color') + ['sort_order' => $order]);
        flash('success', 'เพิ่มหมวดหมู่แล้ว');
        redirect('/admin/categories');
    }

    public function updateCategory(int $id): void
    {
        requireAdmin();
        verifyCsrf();

        $data = [
            'name'       => trim($_POST['name']  ?? ''),
            'slug'       => trim($_POST['slug']  ?? ''),
            'icon'       => trim($_POST['icon']  ?? '📌'),
            'color'      => trim($_POST['color'] ?? '#3b82f6'),
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'is_active'  => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($this->categoryModel->slugExists($data['slug'], $id)) {
            flash('error', 'Slug ซ้ำ');
            redirect('/admin/categories');
        }

        $this->categoryModel->update($id, $data);
        flash('success', 'อัปเดตหมวดหมู่แล้ว');
        redirect('/admin/categories');
    }

    public function deleteCategory(int $id): void
    {
        requireAdmin();
        verifyCsrf();
        if (!$this->categoryModel->delete($id)) {
            flash('error', 'ไม่สามารถลบหมวดหมู่ที่มีปัญหาอยู่ได้');
        } else {
            flash('success', 'ลบหมวดหมู่แล้ว');
        }
        redirect('/admin/categories');
    }

    // -------------------------------------------------------
    // Users
    // -------------------------------------------------------
    public function users(): void
    {
        requireAdmin();
        $search  = trim($_GET['q'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $total   = $this->userModel->countAll($search);
        $pag     = paginate($total, $perPage, $page, url('admin/users') . '?q=' . urlencode($search) . '&page=');

        $users = $this->userModel->all($perPage, $pag['offset'], $search);
        view('layout/header', ['pageTitle' => 'จัดการผู้ใช้']);
        view('admin/users', compact('users','pag','search'));
        view('layout/footer');
    }

    public function updateUser(int $id): void
    {
        requireAdmin();
        verifyCsrf();

        if (isset($_POST['role'])) {
            $this->userModel->setRole($id, $_POST['role']);
        }
        if (isset($_POST['ban'])) {
            $this->userModel->setBan($id, (bool)$_POST['ban']);
        }

        flash('success', 'อัปเดตผู้ใช้แล้ว');
        redirect('/admin/users');
    }
}
