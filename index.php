<?php
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

function is_project_dir(string $path): bool
{
    return is_file($path . '/index.php')
        || is_file($path . '/index.html')
        || is_file($path . '/public/index.php')
        || is_file($path . '/.htaccess');
}

$projects = [];
foreach (scandir(__DIR__) ?: [] as $entry) {
    if ($entry === '.' || $entry === '..' || $entry[0] === '.') {
        continue;
    }
    $full = __DIR__ . DIRECTORY_SEPARATOR . $entry;
    if (is_dir($full) && is_project_dir($full)) {
        $projects[] = $entry;
    }
}
natcasesort($projects);
$projects = array_values($projects);

function svg_folder(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h6a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>';
}
function svg_arrow(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSU — โปรเจกต์ทั้งหมด</title>
    <meta name="description" content="รวมงานทุกตัวใน BSU ไว้ที่เดียว กดเข้าดูได้เลย">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%23070e22'/%3E%3Cpath d='M6 11a2 2 0 0 1 2-2h5l2 2h9a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2z' fill='none' stroke='%2338bdf8' stroke-width='2' stroke-linejoin='round'/%3E%3C/svg%3E">

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                if (stored !== 'light') document.documentElement.classList.add('dark');
            } catch (e) { document.documentElement.classList.add('dark'); }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy-50:#eef3fb; --navy-100:#d7e2f5; --navy-200:#aec4ea; --navy-300:#7e9eda;
            --navy-400:#4f74c4; --navy-500:#3354a8; --navy-600:#274187; --navy-700:#1f336b;
            --navy-800:#16244c; --navy-900:#0d1733; --navy-950:#070e22;
            --sky-300:#7dd3fc; --sky-400:#38bdf8; --sky-500:#0ea5e9; --sky-600:#0284c7;

            --bg: #ffffff;
            --text: var(--navy-900);
            --muted: var(--navy-500);
            --dim: var(--navy-400);
            --accent: var(--sky-500);
            --glass-bg: rgba(255,255,255,.70);
            --glass-border: rgba(174,196,234,.70);
            --line-soft: rgba(174,196,234,.45);
            --grid: rgba(79,116,196,.06);
            --glow-1: var(--sky-500);
            --glow-2: var(--navy-400);
        }
        html.dark {
            --bg: var(--navy-950);
            --text: var(--navy-100);
            --muted: var(--navy-300);
            --dim: var(--navy-500);
            --accent: var(--sky-400);
            --glass-bg: rgba(13,23,51,.50);
            --glass-border: rgba(31,51,107,.60);
            --line-soft: rgba(31,51,107,.40);
            --grid: rgba(79,116,196,.06);
            --glow-1: var(--sky-500);
            --glow-2: var(--navy-500);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0; min-height: 100vh;
            font-family: Kanit, ui-sans-serif, system-ui, sans-serif;
            background-color: var(--bg); color: var(--text);
            -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
            transition: background-color .5s, color .5s;
            position: relative; overflow-x: hidden;
        }
        body::after {
            content: ""; position: fixed; inset: 0; z-index: 1; pointer-events: none; opacity: .035;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
        }
        body::before {
            content: ""; position: fixed; inset: 0; z-index: -20;
            background-image:
                linear-gradient(to right, var(--grid) 1px, transparent 1px),
                linear-gradient(to bottom, var(--grid) 1px, transparent 1px);
            background-size: 64px 64px;
            -webkit-mask-image: radial-gradient(ellipse 85% 60% at 50% 0%, #000 35%, transparent 100%);
            mask-image: radial-gradient(ellipse 85% 60% at 50% 0%, #000 35%, transparent 100%);
        }
        .glow { position: fixed; z-index: -20; border-radius: 9999px; filter: blur(80px); opacity: .28; pointer-events: none; animation: glow-pulse 6s ease-in-out infinite; }
        .glow.a { width: 30rem; height: 30rem; top: -10rem; right: -8rem; background: var(--glow-1); }
        .glow.b { width: 24rem; height: 24rem; bottom: -12rem; left: -8rem; background: var(--glow-2); animation-delay: 2s; }
        @keyframes glow-pulse { 0%,100%{opacity:.18} 50%{opacity:.34} }

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { border-radius: 9999px; background: var(--navy-300); }
        html.dark ::-webkit-scrollbar-thumb { background: var(--navy-700); }
        ::-webkit-scrollbar-thumb:hover { background: var(--sky-500); }

        .wrap { max-width: 66rem; margin: 0 auto; padding: 3.5rem 1.5rem 4rem; position: relative; z-index: 2; }
        .mono { font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; }

        .section-label {
            display: inline-flex; align-items: center; gap: .5rem; margin-bottom: .85rem;
            font-family: "JetBrains Mono", Kanit, ui-monospace, monospace;
            font-size: .75rem; font-weight: 500; text-transform: uppercase; letter-spacing: .25em;
            color: var(--accent);
        }
        .section-label .num { color: var(--dim); font-weight: 700; }
        .section-label .bar { width: 1.75rem; height: 1px; background: currentColor; opacity: .5; }

        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 3rem; }
        .brand { display: inline-flex; align-items: center; gap: .55rem; font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-weight: 700; font-size: .92rem; }
        .brand .accent { color: var(--accent); }
        .brand .slash { color: var(--dim); }
        .theme-btn {
            flex-shrink: 0; width: 2.6rem; height: 2.6rem; border-radius: .8rem;
            display: grid; place-items: center; cursor: pointer;
            background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text);
            -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
            transition: border-color .3s, transform .3s, color .3s;
        }
        .theme-btn:hover { border-color: var(--accent); color: var(--accent); transform: translateY(-2px); }
        .theme-btn svg { width: 1.15rem; height: 1.15rem; }
        html.dark .theme-btn .i-sun { display: none; }
        html:not(.dark) .theme-btn .i-moon { display: none; }

        .hero { padding: 1rem 0 2.5rem; }
        h1 {
            margin: 0; font-weight: 700; letter-spacing: -.025em; line-height: 1.05;
            font-size: clamp(2.4rem, 7vw, 4rem);
        }
        .text-gradient {
            background-image: linear-gradient(110deg, var(--sky-400), var(--navy-400), var(--navy-600));
            background-size: 200% auto; -webkit-background-clip: text; background-clip: text; color: transparent;
            animation: gradient-x 6s ease infinite;
        }
        html.dark .text-gradient { background-image: linear-gradient(110deg, var(--sky-300), var(--sky-400), var(--navy-300)); }
        @keyframes gradient-x { 0%,100%{background-position:0% 50%} 50%{background-position:100% 50%} }
        .cursor { display: inline-block; width: .5rem; height: 1em; background: var(--accent); margin-left: .25rem; vertical-align: -3px; animation: blink 1.1s step-end infinite; }
        @keyframes blink { 0%,49%{opacity:1} 50%,100%{opacity:0} }
        .lead { margin: 1.15rem 0 0; max-width: 38rem; color: var(--muted); line-height: 1.75; font-size: 1.02rem; }
        .lead code, .lead .mono { color: var(--accent); }

        .meta-row { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.6rem; }
        .chip {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .45rem .85rem; border-radius: 9999px;
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            -webkit-backdrop-filter: blur(6px); backdrop-filter: blur(6px);
            font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .72rem; color: var(--muted);
        }
        .chip .led { width: .45rem; height: .45rem; border-radius: 9999px; background: #28c840; box-shadow: 0 0 0 3px rgba(40,200,64,.15); }
        .chip b { color: var(--text); font-weight: 700; }

        .modules { margin-top: 3.25rem; }
        .grid { margin-top: 1.35rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.1rem; }
        .glass-card {
            position: relative; display: flex; flex-direction: column; gap: 1.1rem;
            padding: 1.3rem 1.35rem 1.2rem; border-radius: 1rem; overflow: hidden;
            text-decoration: none; color: inherit;
            background: var(--glass-bg); border: 1px solid var(--glass-border);
            -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);
            transition: transform .3s, border-color .3s, box-shadow .3s;
        }
        .glass-card::after {
            content: ""; position: absolute; left: 0; top: 0; height: 100%; width: 2px;
            background: var(--accent); transform: scaleY(0); transform-origin: top; transition: transform .3s;
        }
        .glass-card:hover { transform: translateY(-5px); border-color: var(--accent); box-shadow: 0 26px 44px -24px rgba(14,165,233,.4); }
        .glass-card:hover::after { transform: scaleY(1); }

        .card-top { display: flex; align-items: center; justify-content: space-between; }
        .idx { font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .78rem; font-weight: 700; color: var(--dim); }
        .status { display: inline-flex; align-items: center; gap: .4rem; font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .64rem; text-transform: uppercase; letter-spacing: .16em; color: var(--muted); }
        .status .led { width: .45rem; height: .45rem; border-radius: 9999px; background: #28c840; box-shadow: 0 0 0 3px rgba(40,200,64,.15); }

        .card-main { display: flex; align-items: center; gap: .9rem; }
        .ficon { flex-shrink: 0; width: 2.75rem; height: 2.75rem; border-radius: .75rem; display: grid; place-items: center; color: var(--accent); background: rgba(56,189,248,.12); border: 1px solid var(--glass-border); }
        .ficon svg { width: 1.3rem; height: 1.3rem; }
        .name { font-size: 1.22rem; font-weight: 600; line-height: 1.2; }
        .path { margin-top: .18rem; font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .72rem; color: var(--dim); }

        .card-foot { display: flex; align-items: center; justify-content: space-between; padding-top: .95rem; border-top: 1px dashed var(--line-soft); }
        .enter { display: inline-flex; align-items: center; gap: .45rem; font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .74rem; color: var(--muted); }
        .enter .kw { color: var(--accent); }
        .go-ico { width: 1.5rem; height: 1.5rem; display: grid; place-items: center; color: var(--muted); transition: transform .3s, color .3s; }
        .go-ico svg { width: 1.1rem; height: 1.1rem; }
        .glass-card:hover .go-ico { color: var(--accent); transform: translateX(4px); }

        .empty { margin-top: 1.35rem; padding: 3.5rem 1rem; text-align: center; color: var(--muted);
            border: 1px dashed var(--glass-border); border-radius: 1rem; background: var(--glass-bg);
            font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; }

        footer { margin-top: 3.25rem; padding-top: 1.5rem; border-top: 1px solid var(--line-soft);
            display: flex; flex-wrap: wrap; gap: .5rem 1.25rem; justify-content: space-between;
            font-family: "JetBrains Mono", Kanit, ui-monospace, monospace; font-size: .7rem; color: var(--dim); }

        .reveal { opacity: 0; transform: translateY(24px); animation: fade-up .7s cubic-bezier(.16,1,.3,1) forwards; }
        @keyframes fade-up { to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) {
            .reveal { animation: none; opacity: 1; transform: none; }
            .cursor, .glow, .text-gradient { animation: none; }
        }
    </style>
</head>
<body>
    <div class="glow a"></div>
    <div class="glow b"></div>

    <div class="wrap">

        <div class="topbar reveal">
            <span class="brand"><span class="accent">~</span>/BSU<span class="slash">/</span>portal</span>
            <button class="theme-btn" id="theme-toggle" type="button" aria-label="สลับธีม" title="สลับธีม">
                <svg class="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
                <svg class="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
            </button>
        </div>

        <section class="hero reveal" style="animation-delay:.05s">
            <span class="section-label"><span class="bar"></span> Projects</span>
            <h1>Project <span class="text-gradient">BSU</span><span class="cursor"></span></h1>
            <p class="lead">
                รวมทุกงานทุก Project ในหน้านี้สามารถกดเลือกดูได้ที่ข้างล่าง
            </p>
            <div class="meta-row">
                <span class="chip"><span class="led"></span> <b><?= count($projects) ?></b> Project</span>
                <span class="chip">Auto-indexed</span>
                <span class="chip"><?= htmlspecialchars($base ?: '/', ENT_QUOTES) ?></span>
            </div>
        </section>

        <section class="modules reveal" style="animation-delay:.12s">
            <span class="section-label"><span class="num">02 //</span> Modules </span>

            <?php if (empty($projects)): ?>
                <div class="empty">// ยังไม่มีโฟลเดอร์โปรเจกต์ในไดเรกทอรีนี้</div>
            <?php else: ?>
                <div class="grid">
                    <?php $i = 0; foreach ($projects as $name): $i++; ?>
                        <a class="glass-card" href="<?= htmlspecialchars($base . '/' . rawurlencode($name) . '/', ENT_QUOTES) ?>" target="_blank">
                            <div class="card-top">
                                <span class="idx"><?= str_pad((string)$i, 2, '0', STR_PAD_LEFT) ?></span>
                                <span class="status"><span class="led"></span> online</span>
                            </div>
                            <div class="card-main">
                                <span class="ficon"><?= svg_folder() ?></span>
                                <span>
                                    <span class="name"><?= htmlspecialchars($name, ENT_QUOTES) ?></span>
                                    <span class="path"><?= htmlspecialchars($base . '/' . $name . '/', ENT_QUOTES) ?></span>
                                </span>
                            </div>
                            <div class="card-foot">
                                <span class="enter"><span class="kw">cd</span> ./<?= htmlspecialchars($name, ENT_QUOTES) ?></span>
                                <span class="go-ico"><?= svg_arrow() ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <footer class="reveal" style="animation-delay:.18s">
            <span>BSU</span>
            <span><?= count($projects) ?> Project · <?= date('Y') ?></span>
        </footer>
    </div>

    <script>
        (function () {
            var html = document.documentElement;
            var btn = document.getElementById("theme-toggle");
            if (btn) btn.addEventListener("click", function () {
                var isDark = html.classList.toggle("dark");
                try { localStorage.setItem("theme", isDark ? "dark" : "light"); } catch (e) {}
            });
        })();
    </script>
</body>
</html>
