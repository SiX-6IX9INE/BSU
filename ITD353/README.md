# Community Issue Reporter 🏘️

ระบบรับแจ้งปัญหาในชุมชน (ถนน ไฟฟ้า น้ำประปา ขยะ ฯลฯ) สร้างด้วย PHP 8.x + MySQL 8 + Vanilla JS  
ไม่พึ่ง framework หนัก – รันได้ทันทีบน XAMPP / shared hosting

---

## โครงสร้างโปรเจกต์

```
ITD353/
├── public/               ← document root (ชี้ Apache ที่นี่)
│   ├── index.php         ← Front Controller / Router
│   └── assets/
│       ├── css/app.css
│       └── js/app.js
├── app/
│   ├── config.php        ← DB config, base URL, env flags
│   ├── helpers.php       ← escape, csrf, auth, flash, redirect
│   ├── middlewares/
│   │   └── AuthMiddleware.php
│   ├── controllers/
│   │   ├── HomeController.php
│   │   ├── IssueController.php
│   │   ├── AuthController.php
│   │   ├── ProfileController.php
│   │   └── AdminController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Issue.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   └── Vote.php
│   └── views/
│       ├── layout/       ← header, navbar, footer
│       ├── home/
│       ├── issue/
│       ├── auth/
│       ├── profile/
│       ├── admin/
│       └── about/
├── storage/
│   └── uploads/          ← ไฟล์รูปที่ upload (ต้อง writable)
├── schema.sql
└── README.md
```

---

## ติดตั้งบน XAMPP

### 1. วาง Project

```
C:\xampp\htdocs\bsu\ITD353\
```

### 2. สร้าง Virtual Host (แนะนำ) หรือเข้าผ่าน URL ตรง

**วิธีที่ 1 – ใช้ผ่าน URL ตรง (ง่ายสุด)**  
เปิด: `http://localhost/bsu/ITD353/`

**วิธีที่ 2 – Virtual Host** (แนะนำ)  
เพิ่มใน `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/bsu/ITD353/public"
    ServerName community.local
    <Directory "C:/xampp/htdocs/bsu/ITD353/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

เพิ่ม `127.0.0.1 community.local` ใน `C:\Windows\System32\drivers\etc\hosts`  
แล้วเปิด: `http://community.local/`

> **หมายเหตุ:** ถ้าเข้าผ่าน URL ตรง (ไม่ใช้ Virtual Host) Apache ต้องเปิด `AllowOverride All`
> สำหรับ directory `C:/xampp/htdocs/bsu/ITD353/` ด้วย เพื่อให้ root `.htaccess` ทำงานได้

### 3. สร้าง Database

เปิด phpMyAdmin → Import → เลือก `schema.sql`  
หรือรันใน terminal:

```bash
mysql -u root -p < schema.sql
```

### 4. ตั้งค่า app/config.php

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'community_issues');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', 'http://localhost/bsu/ITD353');
```

### 5. ตั้ง Permission uploads (Linux/Mac)

```bash
chmod -R 775 storage/uploads
chown -R www-data:www-data storage/uploads
```

บน Windows/XAMPP ไม่ต้องทำ – PHP เขียนไฟล์ได้เลย

### 6. เปิดใช้ mod_rewrite (XAMPP)

ตรวจสอบใน `C:\xampp\apache\conf\httpd.conf` ว่า uncomment บรรทัดนี้แล้ว:

```
LoadModule rewrite_module modules/mod_rewrite.so
```

---

## Login ทดสอบ

| Role  | Email | Password |
| ----- | ----- | -------- |
| Admin | test  | 123      |

สมัครสมาชิก user ทั่วไปได้จากหน้า `/register`

---

## ฟีเจอร์หลัก

- ✅ แจ้งปัญหาพร้อม หมวดหมู่ / ความเร่งด่วน / ที่ตั้ง / รูป 1–3 ใบ
- ✅ ติดตามสถานะ (New → Reviewing → In Progress → Resolved / Rejected)
- ✅ Timeline สถานะบนหน้า detail
- ✅ โหวตยืนยันปัญหา (1 user 1 ครั้ง)
- ✅ คอมเมนต์ + admin ปักหมุดคอมเมนต์
- ✅ ค้นหา / กรอง / เรียงลำดับ issue
- ✅ Admin Dashboard + กราฟ CSS/Canvas
- ✅ Dark / Light mode (localStorage)
- ✅ Responsive mobile-first
- ✅ CSRF token, PDO prepared statements, XSS escape
- ✅ Rate limit IP (5 issue ต่อ 10 นาที)
- ✅ Honeypot anti-spam
- ✅ Leaflet map (CDN) pin ที่ตั้ง
- ✅ Toast notifications, Skeleton loading, Empty states
- ✅ Web Share API + Copy link

---

## แผนขยายในอนาคต

| ฟีเจอร์           | วิธีขยาย                                                     |
| ----------------- | ------------------------------------------------------------ |
| แจ้งเตือน LINE    | ใช้ LINE Notify API หรือ LINE Messaging API                  |
| แจ้งเตือน Email   | PHPMailer / SMTP – ใส่ใน `app/services/Mailer.php`           |
| Export CSV        | เพิ่ม endpoint `/admin/export` ส่ง header CSV                |
| Push Notification | Web Push API + service worker                                |
| OAuth Login       | Google / Facebook OAuth2 ผ่าน library เบาอย่าง league/oauth2 |
| Full-text Search  | MySQL FULLTEXT index บน `issues.title, description`          |
| Multi-language    | เพิ่ม `app/lang/th.php`, `app/lang/en.php`                   |

---

## Leaflet Map (CDN)

Leaflet โหลดผ่าน CDN ใน `views/layout/header.php` เฉพาะหน้าที่ต้องการแผนที่  
ไม่ต้องใช้ API key – ใช้ OpenStreetMap tiles ฟรี

---

## ไฟล์ .htaccess

`public/.htaccess` เปิด Pretty URL ทุก request ส่งไป `index.php`  
ต้องการ Apache mod_rewrite เปิดอยู่
