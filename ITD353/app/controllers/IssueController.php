<?php
/**
 * app/controllers/IssueController.php
 * แจ้งปัญหา, ดูรายละเอียด, โหวต, คอมเมนต์
 */

class IssueController
{
    private Issue    $issueModel;
    private Category $categoryModel;
    private Comment  $commentModel;
    private Vote     $voteModel;

    public function __construct()
    {
        $this->issueModel    = new Issue();
        $this->categoryModel = new Category();
        $this->commentModel  = new Comment();
        $this->voteModel     = new Vote();
    }

    // -------------------------------------------------------
    // New issue form
    // -------------------------------------------------------
    public function newForm(): void
    {
        requireLogin();
        $categories = $this->categoryModel->all();
        view('layout/header', ['pageTitle' => 'แจ้งปัญหาใหม่', 'includeLeaflet' => true]);
        view('issue/new', compact('categories'));
        view('layout/footer');
    }

    // -------------------------------------------------------
    // Store new issue
    // -------------------------------------------------------
    public function store(): void
    {
        requireLogin();
        verifyCsrf();

        // Honeypot check
        if (!empty($_POST['_hp_field'])) {
            redirect('/');  // bot detected
        }

        // Minimum time check (anti-spam: form must be open > 3 sec)
        $formTime = (int)($_POST['_form_time'] ?? 0);
        if ((time() - $formTime) < 3) {
            flash('error', 'กรุณากรอกข้อมูลให้ครบก่อนส่ง');
            redirect('/issue/new');
        }

        // Rate limit
        if (!checkRateLimit('submit_issue')) {
            flash('error', 'คุณแจ้งปัญหาบ่อยเกินไป กรุณารอสักครู่');
            redirect('/issue/new');
        }

        // Validate
        $title       = trim($_POST['title']         ?? '');
        $categoryId  = (int)($_POST['category_id']  ?? 0);
        $description = trim($_POST['description']   ?? '');
        $urgency     = $_POST['urgency']             ?? 'medium';
        $locationTxt = trim($_POST['location_text'] ?? '');
        $lat         = $_POST['latitude']            ?? null;
        $lng         = $_POST['longitude']           ?? null;

        $errors = [];
        if (mb_strlen($title) < 5)       $errors[] = 'หัวข้อต้องมีอย่างน้อย 5 ตัวอักษร';
        if (!$categoryId)                 $errors[] = 'กรุณาเลือกหมวดหมู่';
        if (mb_strlen($description) < 10) $errors[] = 'รายละเอียดต้องมีอย่างน้อย 10 ตัวอักษร';
        if (!in_array($urgency, ['low','medium','high'])) $errors[] = 'ระดับความเร่งด่วนไม่ถูกต้อง';

        if ($errors) {
            foreach ($errors as $e) flash('error', $e);
            redirect('/issue/new');
        }

        // Create issue
        $issueId = $this->issueModel->create([
            'user_id'       => auth()['id'],
            'category_id'   => $categoryId,
            'title'         => $title,
            'description'   => $description,
            'urgency'       => $urgency,
            'location_text' => $locationTxt,
            'latitude'      => $lat  ? (float)$lat  : null,
            'longitude'     => $lng  ? (float)$lng  : null,
        ]);

        // Handle image uploads
        $this->handleUploads($issueId);

        flash('success', 'แจ้งปัญหาสำเร็จ! เลขอ้างอิง: ' . generateTicketId($issueId));
        redirect('/issue/' . $issueId);
    }

    // -------------------------------------------------------
    // Detail view
    // -------------------------------------------------------
    public function detail(int $id): void
    {
        $issue = $this->issueModel->findById($id);
        if (!$issue) {
            http_response_code(404);
            view('layout/header', ['pageTitle' => 'ไม่พบหน้านี้']);
            view('errors/404');
            view('layout/footer');
            return;
        }

        $images     = $this->issueModel->images($id);
        $statusLogs = $this->issueModel->statusLogs($id);
        $comments   = $this->commentModel->byIssue($id);
        $hasVoted   = isLoggedIn()
            ? $this->voteModel->hasVoted($id, auth()['id'])
            : false;

        $hasLeaflet = ($issue['latitude'] && $issue['longitude']);

        view('layout/header', ['pageTitle' => e($issue['title']), 'includeLeaflet' => $hasLeaflet]);
        view('issue/detail', compact('issue','images','statusLogs','comments','hasVoted'));
        view('layout/footer');
    }

    // -------------------------------------------------------
    // Vote toggle (AJAX)
    // -------------------------------------------------------
    public function vote(int $id): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['ok' => false, 'message' => 'กรุณาเข้าสู่ระบบ'], 401);
        }

        $userId = auth()['id'];
        if ($this->voteModel->hasVoted($id, $userId)) {
            $this->voteModel->remove($id, $userId);
            $voted = false;
        } else {
            $this->voteModel->add($id, $userId);
            $voted = true;
        }

        $issue = $this->issueModel->findById($id);
        jsonResponse(['ok' => true, 'voted' => $voted, 'count' => $issue['vote_count']]);
    }

    // -------------------------------------------------------
    // Add comment
    // -------------------------------------------------------
    public function comment(int $id): void
    {
        requireLogin();
        verifyCsrf();

        $body = trim($_POST['body'] ?? '');
        if (mb_strlen($body) < 2) {
            flash('error', 'ความคิดเห็นสั้นเกินไป');
            redirect('/issue/' . $id);
        }

        $this->commentModel->create($id, auth()['id'], $body);
        flash('success', 'เพิ่มความคิดเห็นแล้ว');
        redirect('/issue/' . $id . '#comments');
    }

    // -------------------------------------------------------
    // Delete comment
    // -------------------------------------------------------
    public function deleteComment(int $commentId): void
    {
        requireLogin();
        verifyCsrf();

        $comment = $this->commentModel->findById($commentId);
        if (!$comment) redirect('/');

        $this->commentModel->delete($commentId, auth()['id'], isAdmin());
        flash('success', 'ลบความคิดเห็นแล้ว');
        redirect('/issue/' . $comment['issue_id'] . '#comments');
    }

    // -------------------------------------------------------
    // Private: handle image uploads
    // -------------------------------------------------------
    private function handleUploads(int $issueId): void
    {
        if (empty($_FILES['images']['name'][0])) return;

        $files = $_FILES['images'];
        $count = min(count($files['name']), MAX_UPLOAD_COUNT);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            if ($files['size'][$i] > MAX_UPLOAD_SIZE) continue;

            // Validate MIME via finfo
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($files['tmp_name'][$i]);
            if (!in_array($mime, ALLOWED_MIME)) continue;

            $ext      = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $filename = 'img_' . $issueId . '_' . $i . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($ext);
            $dest     = UPLOAD_PATH . '/' . $filename;

            if (!is_dir(UPLOAD_PATH)) mkdir(UPLOAD_PATH, 0775, true);

            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                $this->issueModel->addImage($issueId, $filename, $i);
            }
        }
    }
}
