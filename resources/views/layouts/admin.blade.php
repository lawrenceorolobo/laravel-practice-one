<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin | Quizly')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        surface: { 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 700: '#1a1f2e', 800: '#131825', 900: '#0d1117' },
                        accent: { DEFAULT: '#6366f1', light: '#818cf8', dark: '#4f46e5' },
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; }

        /* ═══════════════════════════════════════════
           THEME SYSTEM — Light (default) + Dark
           ═══════════════════════════════════════════ */
        :root {
            --bg: #f8fafc;
            --bg-alt: #f1f5f9;
            --surface: #ffffff;
            --surface-hover: #f8fafc;
            --surface-raised: #f1f5f9;
            --border: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --text-faint: #cbd5e1;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-bg: rgba(99,102,241,0.08);
            --accent-text: #6366f1;
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-lg: 0 4px 12px rgba(0,0,0,0.08);
            --scrollbar-thumb: #cbd5e1;
            --scrollbar-hover: #94a3b8;
            --skel-bg: #e2e8f0;
            --skel-wave: rgba(0,0,0,0.04);
            --overlay: rgba(0,0,0,0.3);
            --toast-bg: #ffffff;
            --toggle-off: #cbd5e1;
            --input-bg: #ffffff;
        }
        .dark {
            --bg: #0d1117;
            --bg-alt: #161b22;
            --surface: #161b22;
            --surface-hover: #1c2128;
            --surface-raised: #1c2128;
            --border: #21262d;
            --border-subtle: rgba(33,38,45,0.5);
            --text-primary: #e6edf3;
            --text-secondary: #8b949e;
            --text-muted: #484f58;
            --text-faint: #30363d;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-bg: rgba(99,102,241,0.12);
            --accent-text: #818cf8;
            --shadow: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.4);
            --scrollbar-thumb: #30363d;
            --scrollbar-hover: #484f58;
            --skel-bg: #21262d;
            --skel-wave: rgba(255,255,255,0.04);
            --overlay: rgba(0,0,0,0.6);
            --toast-bg: #161b22;
            --toggle-off: #30363d;
            --input-bg: #0d1117;
        }

        body { background: var(--bg); color: var(--text-primary); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--scrollbar-hover); }

        /* ── Sidebar ── */
        .sidebar { background: var(--surface); width: 260px; border-right: 1px solid var(--border); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 10px; color: var(--text-secondary); font-size: 13.5px; font-weight: 500; transition: all .15s; cursor: pointer; text-decoration: none; margin: 2px 0; }
        .nav-item:hover { background: var(--accent-bg); color: var(--text-primary); }
        .nav-item.active { background: var(--accent-bg); color: var(--accent-text); }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; }

        /* ── Top Bar ── */
        .topbar { background: var(--bg); border-bottom: 1px solid var(--border); backdrop-filter: blur(12px); }

        /* ── Cards ── */
        .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 14px; box-shadow: var(--shadow); overflow: hidden; }

        /* ── Skeleton ── */
        .skel { background: var(--skel-bg); border-radius: 6px; position: relative; overflow: hidden; }
        .skel::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, transparent 0%, var(--skel-wave) 50%, transparent 100%); animation: skel-wave 1.8s ease-in-out infinite; }
        @keyframes skel-wave { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
        .skel-circle { border-radius: 50%; }
        .skel-pill { border-radius: 9999px; }

        /* backward compat */
        .skeleton, .skeleton-glass { background: var(--skel-bg); border-radius: 6px; position: relative; overflow: hidden; border: none !important; box-shadow: none !important; }
        .skeleton::after, .skeleton-glass::after { content:''; position:absolute; inset:0; background: linear-gradient(90deg, transparent, var(--skel-wave), transparent); animation: skel-wave 1.8s ease-in-out infinite; }
        .skeleton-circle { border-radius: 50%; }

        /* ── Accent borders ── */
        .card-accent { border-left: 3px solid; }
        .card-accent-indigo { border-left-color: #6366f1; }
        .card-accent-emerald { border-left-color: #10b981; }
        .card-accent-amber { border-left-color: #f59e0b; }
        .card-accent-purple { border-left-color: #a855f7; }
        .card-accent-red { border-left-color: #ef4444; }
        .card-accent-cyan { border-left-color: #06b6d4; }

        /* ── Table rows ── */
        .tr-click { cursor: pointer; transition: background .15s; }
        .tr-click:hover { background: var(--accent-bg) !important; }

        /* ── Detail Drawer ── */
        .detail-drawer { position: fixed; inset: 0; z-index: 60; display: none; }
        .detail-drawer.open { display: flex; }
        .detail-drawer .drawer-overlay { position: absolute; inset: 0; background: var(--overlay); backdrop-filter: blur(4px); }
        .detail-drawer .drawer-panel {
            position: absolute; right: 0; top: 0; bottom: 0; width: 100%; max-width: 480px;
            background: var(--surface); border-left: 1px solid var(--border);
            transform: translateX(100%); transition: transform .3s cubic-bezier(0.4,0,0.2,1);
            overflow-y: auto;
        }
        .detail-drawer.open .drawer-panel { transform: translateX(0); }

        /* ── Badge ── */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .badge-success { background: rgba(16,185,129,0.1); color: #10b981; }
        .badge-warning { background: rgba(245,158,11,0.1); color: #f59e0b; }
        .badge-danger { background: rgba(239,68,68,0.1); color: #ef4444; }
        .badge-info { background: rgba(99,102,241,0.1); color: #6366f1; }
        .badge-neutral { background: var(--accent-bg); color: var(--text-secondary); }
        .dark .badge-success { background: rgba(16,185,129,0.12); color: #34d399; }
        .dark .badge-warning { background: rgba(245,158,11,0.12); color: #fbbf24; }
        .dark .badge-danger { background: rgba(239,68,68,0.12); color: #f87171; }
        .dark .badge-info { background: rgba(99,102,241,0.12); color: #818cf8; }

        /* ── Form inputs ── */
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], textarea, select {
            background: var(--input-bg) !important; border: 1px solid var(--border) !important; color: var(--text-primary) !important; border-radius: 10px !important;
            transition: border-color .15s, box-shadow .15s; font-size: 13px; padding: 8px 12px;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--accent) !important; box-shadow: 0 0 0 3px rgba(99,102,241,0.15) !important; outline: none !important;
        }
        input::placeholder, textarea::placeholder { color: var(--text-muted) !important; }
        textarea { resize: vertical; }

        /* ── Select / Dropdown ── */
        select {
            appearance: none !important; -webkit-appearance: none !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important; background-position: right 12px center !important;
            padding-right: 32px !important; cursor: pointer;
        }
        .dark select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23484f58' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") !important;
        }

        /* ── Buttons ── */
        .btn-primary { background: var(--accent); color: #fff; padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; transition: all .15s; border: none; cursor: pointer; }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
        .btn-ghost { background: transparent; border: 1px solid var(--border); color: var(--text-primary); padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 500; transition: all .15s; cursor: pointer; }
        .btn-ghost:hover { background: var(--surface-raised); border-color: var(--text-muted); }

        /* ── Toggle Switch ── */
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-switch .slider { position: absolute; cursor: pointer; inset: 0; background: var(--toggle-off); border-radius: 9999px; transition: background .2s; }
        .toggle-switch .slider::before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        .toggle-switch input:checked + .slider { background: #10b981; }
        .toggle-switch input:checked + .slider::before { transform: translateX(20px); }
        .toggle-switch input:focus + .slider { box-shadow: 0 0 0 3px rgba(16,185,129,0.2); }

        /* ── Theme Toggle Button ── */
        .theme-toggle { background: var(--surface-raised); border: 1px solid var(--border); border-radius: 10px; padding: 6px 8px; cursor: pointer; transition: all .15s; display: flex; align-items: center; justify-content: center; }
        .theme-toggle:hover { background: var(--accent-bg); border-color: var(--accent); }
        .theme-toggle svg { width: 18px; height: 18px; color: var(--text-secondary); }
        .dark .theme-toggle svg { color: #fbbf24; }

        /* ── Code badge ── */
        code.code-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: var(--surface-raised); color: var(--text-muted); border: 1px solid var(--border); font-family: 'SF Mono', 'Fira Code', monospace; }

        /* ── Inner card (for security settings) ── */
        .inner-card { background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; }

        /* ── Border color Utils ── */
        .border-theme { border-color: var(--border); }
        .border-subtle { border-color: var(--border-subtle); }

        /* ── Mobile sidebar ── */
        @media (max-width: 1023px) {
            .sidebar-overlay { position: fixed; inset: 0; background: var(--overlay); z-index: 40; opacity: 0; visibility: hidden; transition: all .3s; backdrop-filter: blur(4px); }
            .sidebar-overlay.open { opacity: 1; visibility: visible; }
            .sidebar { position: fixed; z-index: 50; transform: translateX(-100%); transition: transform .3s ease; height: 100%; }
            .sidebar.open { transform: translateX(0); }
        }
        /* ── Detail Drawer ── */
        .detail-drawer { position: fixed; inset: 0; z-index: 50; visibility: hidden; }
        .detail-drawer.open { visibility: visible; }
        .detail-drawer .drawer-overlay { position: absolute; inset: 0; background: var(--overlay); backdrop-filter: blur(4px); opacity: 0; transition: opacity .3s; }
        .detail-drawer.open .drawer-overlay { opacity: 1; }
        .detail-drawer .drawer-panel { position: absolute; right: 0; top: 0; bottom: 0; width: 380px; max-width: 90vw; background: var(--surface); box-shadow: var(--shadow-lg); transform: translateX(100%); transition: transform .3s ease; overflow-y: auto; }
        .detail-drawer.open .drawer-panel { transform: translateX(0); }
    </style>
    <script>
        // Theme initialization — light by default
        (function() {
            const saved = localStorage.getItem('adminTheme');
            if (saved === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="antialiased">

<!-- Mobile Header -->
<header class="lg:hidden fixed top-0 left-0 right-0 z-30 flex items-center justify-between px-4 py-3" style="background: var(--bg); border-bottom: 1px solid var(--border);">
    <button id="mobileMenuBtn" class="p-2 rounded-lg transition" style="color: var(--text-secondary);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <a href="/admin/dashboard" class="text-lg font-bold" style="color: var(--accent);">Quizly<span class="font-normal text-xs ml-1" style="color: var(--text-muted);">Admin</span></a>
    <div class="flex items-center gap-2">
        <button class="theme-toggle" onclick="toggleTheme()" id="mobileThemeBtn" title="Toggle theme">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </button>
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold" id="mobileAvatar">A</div>
    </div>
</header>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay lg:hidden" id="sidebarOverlay"></div>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="adminSidebar" class="sidebar flex flex-col fixed h-full lg:translate-x-0">
        <!-- Logo -->
        <div class="px-5 py-5 flex items-center gap-3" style="border-bottom: 1px solid var(--border);">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--accent);">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            <div>
                <span class="text-[15px] font-bold" style="color: var(--text-primary);">Quizly</span>
                <span class="block text-[10px] font-medium tracking-wider uppercase" style="color: var(--text-muted);">Admin Panel</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <p class="px-3 pt-2 pb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-muted);">Main</p>
            <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 12a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1v-7z"/></svg>
                Dashboard
            </a>
            <a href="/admin/users" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Users
            </a>
            <a href="/admin/assessments" class="nav-item {{ request()->is('admin/assessments*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Assessments
            </a>

            <p class="px-3 pt-5 pb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-muted);">Business</p>
            <a href="/admin/subscription-plans" class="nav-item {{ request()->is('admin/subscription-plans*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Plans & Billing
            </a>
            <a href="/admin/reports" class="nav-item {{ request()->is('admin/reports*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Reports
            </a>

            <p class="px-3 pt-5 pb-2 text-[10px] font-semibold uppercase tracking-wider" style="color: var(--text-muted);">System</p>
            <a href="/admin/feature-flags" class="nav-item {{ request()->is('admin/feature-flags*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                Feature Flags
            </a>
            <a href="/admin/settings" class="nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
        </nav>

        <!-- User Section -->
        <div class="px-3 py-4" style="border-top: 1px solid var(--border);">
            <div class="flex items-center gap-3 p-2.5 rounded-xl transition cursor-pointer mb-2" style="color: var(--text-primary);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold" id="adminAvatar">A</div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold truncate" style="color: var(--text-primary);" id="adminName">Admin</p>
                    <p class="text-[11px] truncate" style="color: var(--text-muted);" id="adminEmail">admin@quizly.com</p>
                </div>
                <svg class="w-4 h-4" style="color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
            </div>
            <button id="logoutBtn" class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-[12px] font-medium text-red-500 hover:bg-red-500/10 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign out
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-[260px] pt-14 lg:pt-0 min-h-screen">
        <!-- Top Bar (Desktop) -->
        <header class="topbar px-5 lg:px-8 py-3.5 sticky top-0 z-10 hidden lg:flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h1 class="text-[15px] font-semibold" style="color: var(--text-primary);">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2">
                <button class="theme-toggle" onclick="toggleTheme()" id="desktopThemeBtn" title="Toggle theme">
                    <svg id="themeIconLight" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="themeIconDark" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                @yield('header-actions')
            </div>
        </header>

        <!-- Mobile Page Title -->
        <div class="lg:hidden px-4 py-3" style="border-bottom: 1px solid var(--border);">
            <div class="flex items-center justify-between">
                <h1 class="text-[15px] font-semibold" style="color: var(--text-primary);">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-2">
                    @yield('header-actions')
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4 lg:p-6 xl:p-8">
            @yield('content')
        </div>
    </main>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-16 lg:top-4 right-4 z-[100] flex flex-col gap-2"></div>

<script>
    // Theme toggle
    function toggleTheme() {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('adminTheme', isDark ? 'dark' : 'light');
        updateThemeIcons();
        // Dispatch event for charts to re-render
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { dark: isDark } }));
    }
    function updateThemeIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const light = document.getElementById('themeIconLight');
        const dark = document.getElementById('themeIconDark');
        if (light && dark) {
            light.style.display = isDark ? 'none' : 'block';
            dark.style.display = isDark ? 'block' : 'none';
        }
    }
    updateThemeIcons();

    // Theme-aware color helpers for JS
    function isDarkMode() { return document.documentElement.classList.contains('dark'); }
    function themeColor(light, dark) { return isDarkMode() ? dark : light; }

    // Mobile menu
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            adminSidebar.classList.toggle('open');
            sidebarOverlay.classList.toggle('open');
        });
    }
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            adminSidebar.classList.remove('open');
            sidebarOverlay.classList.remove('open');
        });
    }

    // Auth
    const adminToken = localStorage.getItem('adminToken');
    const admin = JSON.parse(localStorage.getItem('admin') || 'null');
    if (!adminToken) window.location.href = '/admin/login';

    if (admin) {
        document.getElementById('adminName').textContent = admin.name || 'Admin';
        document.getElementById('adminEmail').textContent = admin.email || '';
        const initial = (admin.name || 'A').charAt(0).toUpperCase();
        document.getElementById('adminAvatar').textContent = initial;
        const mob = document.getElementById('mobileAvatar');
        if (mob) mob.textContent = initial;
    }

    // Logout
    document.getElementById('logoutBtn').addEventListener('click', async () => {
        try {
            await fetch('/api/admin/auth/logout', {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }
            });
        } catch (err) {}
        localStorage.removeItem('adminToken');
        localStorage.removeItem('admin');
        window.location.href = '/admin/login';
    });

    // Toast System
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#6366f1' };
        const color = colors[type] || colors.info;

        toast.style.cssText = `display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:12px;background:var(--toast-bg);border:1px solid var(--border);box-shadow:var(--shadow-lg);max-width:360px;transform:translateX(120%);opacity:0;transition:all .3s cubic-bezier(0.4,0,0.2,1);color:var(--text-primary);`;
        toast.innerHTML = `
            <div style="width:4px;height:28px;border-radius:2px;background:${color};flex-shrink:0;"></div>
            <p style="flex:1;font-size:13px;font-weight:500;margin:0;">${message}</p>
            <button onclick="this.parentElement.style.transform='translateX(120%)';this.parentElement.style.opacity='0';setTimeout(()=>this.parentElement.remove(),300)" style="background:none;border:none;color:var(--text-muted);cursor:pointer;padding:2px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        container.appendChild(toast);
        requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; });
        setTimeout(() => { toast.style.transform = 'translateX(120%)'; toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, duration);
    }

    function toastSuccess(msg) { showToast(msg, 'success'); }
    function toastError(msg) { showToast(msg, 'error'); }
    function toastWarning(msg) { showToast(msg, 'warning'); }
    function toastInfo(msg) { showToast(msg, 'info'); }

    // Confirm Modal
    let confirmResolve = null;
    function showConfirm(title, message, confirmText = 'Confirm', type = 'danger') {
        return new Promise((resolve) => {
            confirmResolve = resolve;
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            const btn = document.getElementById('confirmBtn');
            btn.textContent = confirmText;
            btn.style.background = type === 'danger' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#6366f1';
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        });
    }
    function closeConfirmModal(result) {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('confirmModal').classList.remove('flex');
        if (confirmResolve) { confirmResolve(result); confirmResolve = null; }
    }
</script>

<!-- Confirm Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-[110] items-center justify-center p-4" style="background: var(--overlay); backdrop-filter: blur(8px);" onclick="if(event.target===this) closeConfirmModal(false)">
    <div style="background: var(--surface); border: 1px solid var(--border); border-radius: 16px; max-width: 400px; width: 100%; box-shadow: var(--shadow-lg);">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(239,68,68,0.1);">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 id="confirmTitle" class="text-[15px] font-semibold" style="color: var(--text-primary);">Confirm</h3>
                    <p id="confirmMessage" class="text-[13px] mt-0.5" style="color: var(--text-secondary);">Are you sure?</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closeConfirmModal(false)" class="btn-ghost flex-1">Cancel</button>
            <button id="confirmBtn" onclick="closeConfirmModal(true)" class="flex-1 px-5 py-2 rounded-lg font-semibold text-[13px] text-white transition hover:opacity-90" style="background: #ef4444;">Confirm</button>
        </div>
    </div>
</div>

@yield('scripts')
</body>
</html>
