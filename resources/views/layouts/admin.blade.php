<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin | Quizly')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background: rgba(99, 102, 241, 0.2); color: #818cf8; }
        
        /* Glassmorphism Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, 
                rgba(255,255,255,0.05) 0%, 
                rgba(255,255,255,0.15) 50%, 
                rgba(255,255,255,0.05) 100%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s infinite ease-in-out;
            border-radius: 0.75rem;
        }
        .skeleton-glass {
            background: rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        .skeleton-glass::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(0,0,0,0.04) 50%, 
                transparent 100%);
            animation: skeleton-shimmer 1.5s infinite;
        }
        body.dark .skeleton-glass {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        body.dark .skeleton-glass::after {
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255,255,255,0.1) 50%, 
                transparent 100%);
        }
        @keyframes skeleton-shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton-text {
            height: 1em;
            margin: 0.25em 0;
            border-radius: 0.25rem;
        }
        .skeleton-circle {
            border-radius: 50%;
        }
        
        /* Mobile sidebar */
        @media (max-width: 1023px) {
            .sidebar-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }
            .sidebar-overlay.open {
                opacity: 1;
                visibility: visible;
            }
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .admin-sidebar.open {
                transform: translateX(0);
            }
        }
        
        /* Light Mode (default) */
        body { background: #f1f5f9; color: #1e293b; }
        .sidebar-bg { background: #ffffff; border-color: #e2e8f0; }
        .header-bg { background: #ffffff; border-color: #e2e8f0; }
        .card-bg { background: #ffffff; border-color: #e2e8f0; }
        .text-muted { color: #64748b; }
        .sidebar-link { color: #475569; }
        .sidebar-link:hover { background: #f1f5f9; color: #1e293b; }
        .sidebar-link.active { background: rgba(99, 102, 241, 0.1); color: #4f46e5; }
        
        /* Override Tailwind dark classes in light mode */
        .bg-slate-800 { background: #ffffff !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important; }
        .bg-slate-700 { background: #f8fafc !important; }
        .border-slate-700 { border-color: #e2e8f0 !important; }
        .border-slate-600 { border-color: #cbd5e1 !important; }
        .text-white { color: #1e293b !important; }
        .text-slate-300 { color: #475569 !important; }
        .text-slate-400 { color: #64748b !important; }
        .text-slate-500 { color: #94a3b8 !important; }
        .hover\:bg-slate-700:hover { background: #f1f5f9 !important; }
        
        /* Dark Mode - restore original dark colors */
        body.dark { background: #0f172a; color: #f1f5f9; }
        body.dark .sidebar-bg { background: #1e293b; border-color: #334155; }
        body.dark .header-bg { background: #1e293b; border-color: #334155; }
        body.dark .card-bg { background: #1e293b; border-color: #334155; }
        body.dark .text-muted { color: #94a3b8; }
        body.dark .sidebar-link { color: #cbd5e1; }
        body.dark .sidebar-link:hover { background: #334155; color: #f1f5f9; }
        
        body.dark .bg-slate-800 { background: #1e293b !important; }
        body.dark .bg-slate-700 { background: #334155 !important; }
        body.dark .border-slate-700 { border-color: #334155 !important; }
        body.dark .border-slate-600 { border-color: #475569 !important; }
        body.dark .text-white { color: #f1f5f9 !important; }
        body.dark .text-slate-300 { color: #cbd5e1 !important; }
        body.dark .text-slate-400 { color: #94a3b8 !important; }
        body.dark .text-slate-500 { color: #64748b !important; }
        body.dark .hover\:bg-slate-700:hover { background: #475569 !important; }
    </style>
    <script>
        // Apply saved theme immediately to prevent flash
        if (localStorage.getItem('adminTheme') === 'dark') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="antialiased">

<!-- Mobile Header -->
<header class="lg:hidden fixed top-0 left-0 right-0 bg-slate-800 border-b border-slate-700 px-4 py-3 z-30 flex items-center justify-between">
    <button id="mobileMenuBtn" class="p-2 hover:bg-slate-700 rounded-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
    <a href="/admin/dashboard" class="text-xl font-bold text-indigo-600">Quizly.</a>
    <button id="mobileThemeBtn" class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700">
        <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <svg class="w-5 h-5 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
    </button>
</header>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay lg:hidden" id="sidebarOverlay"></div>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="adminSidebar" class="admin-sidebar sidebar-bg w-64 border-r flex flex-col fixed h-full z-50 lg:translate-x-0">
        <!-- Logo -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <a href="/admin/dashboard" class="text-2xl font-bold text-indigo-600">Quizly.</a>
            <p class="text-xs text-muted mt-1">Admin Panel</p>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="/admin/dashboard" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <a href="/admin/users" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/users*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users
            </a>
            <a href="/admin/subscription-plans" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/subscription-plans*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Subscription Plans
            </a>
            <a href="/admin/assessments" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/assessments*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Assessments
            </a>
            <a href="/admin/reports" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/reports*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Reports
            </a>
            <a href="/admin/settings" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition-colors {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-slate-700">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center font-bold" id="adminAvatar">A</div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate" id="adminName">Admin</p>
                    <p class="text-xs text-slate-500 truncate" id="adminEmail">admin@quizly.com</p>
                </div>
            </div>
            <button id="logoutBtn" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-0">
        <!-- Top Bar (Desktop) -->
        <header class="header-bg border-b px-4 lg:px-8 py-4 sticky top-0 lg:top-0 z-10 hidden lg:flex items-center justify-between">
            <h1 class="text-xl font-bold">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-4">
                <button id="themeToggle" class="p-2 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700" title="Toggle theme">
                    <svg class="w-5 h-5 sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-5 h-5 moon-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                @yield('header-actions')
            </div>
        </header>
        
        <!-- Mobile Page Title -->
        <div class="lg:hidden bg-slate-800 border-b border-slate-700 px-4 py-3">
            <div class="flex items-center justify-between">
                <h1 class="text-lg font-bold">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-2">
                    @yield('header-actions')
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="p-4 lg:p-8">
            @yield('content')
        </div>
    </main>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-20 lg:top-6 right-6 z-[100] flex flex-col gap-3"></div>

<script>
    // Mobile menu toggle
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

    // Check admin authentication via cookie/session
    const adminToken = localStorage.getItem('adminToken');
    const admin = JSON.parse(localStorage.getItem('admin') || 'null');

    if (!adminToken) {
        window.location.href = '/admin/login';
    }

    if (admin) {
        document.getElementById('adminName').textContent = admin.name || 'Admin';
        document.getElementById('adminEmail').textContent = admin.email || '';
        document.getElementById('adminAvatar').textContent = (admin.name || 'A').charAt(0).toUpperCase();
    }

    // Logout handler
    document.getElementById('logoutBtn').addEventListener('click', async () => {
        try {
            await fetch('/api/admin/auth/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${adminToken}`,
                    'Accept': 'application/json'
                }
            });
        } catch (err) {}
        localStorage.removeItem('adminToken');
        localStorage.removeItem('admin');
        window.location.href = '/admin/login';
    });

    // Toast Notification System
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        
        const icons = {
            success: `<svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
            error: `<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
            warning: `<svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
            info: `<svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
        };
        
        const bgColors = {
            success: 'border-emerald-500/30 bg-slate-800/95',
            error: 'border-red-500/30 bg-slate-800/95',
            warning: 'border-amber-500/30 bg-slate-800/95',
            info: 'border-indigo-500/30 bg-slate-800/95'
        };
        
        toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border backdrop-blur-sm ${bgColors[type]} transform translate-x-full opacity-0 transition-all duration-300 max-w-sm`;
        toast.innerHTML = `
            <div class="flex-shrink-0">${icons[type]}</div>
            <p class="flex-1 text-sm font-medium text-white">${message}</p>
            <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-slate-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        `;
        
        container.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
        
        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // Convenience functions
    function toastSuccess(message) { showToast(message, 'success'); }
    function toastError(message) { showToast(message, 'error'); }
    function toastWarning(message) { showToast(message, 'warning'); }
    function toastInfo(message) { showToast(message, 'info'); }

    // Confirmation Modal System
    let confirmResolve = null;
    function showConfirm(title, message, confirmText = 'Confirm', type = 'danger') {
        return new Promise((resolve) => {
            confirmResolve = resolve;
            const modal = document.getElementById('confirmModal');
            const modalTitle = document.getElementById('confirmTitle');
            const modalMessage = document.getElementById('confirmMessage');
            const confirmBtn = document.getElementById('confirmBtn');
            
            modalTitle.textContent = title;
            modalMessage.textContent = message;
            confirmBtn.textContent = confirmText;
            
            // Set button color based on type
            confirmBtn.className = `flex-1 px-5 py-2.5 rounded-lg font-semibold transition-all ${
                type === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' :
                type === 'warning' ? 'bg-amber-500 hover:bg-amber-600 text-white' :
                'bg-indigo-600 hover:bg-indigo-700 text-white'
            }`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }

    function closeConfirmModal(result) {
        const modal = document.getElementById('confirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (confirmResolve) {
            confirmResolve(result);
            confirmResolve = null;
        }
    }
</script>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-[110] items-center justify-center p-4 bg-black/60 backdrop-blur-sm" onclick="if(event.target === this) closeConfirmModal(false)">
    <div class="bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full border border-slate-700">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="confirmTitle" class="text-lg font-bold text-white">Confirm Action</h3>
                    <p id="confirmMessage" class="text-sm text-slate-400">Are you sure you want to proceed?</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closeConfirmModal(false)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold border border-slate-600 text-slate-300 hover:bg-slate-700 transition-all">
                Cancel
            </button>
            <button id="confirmBtn" onclick="closeConfirmModal(true)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold bg-red-600 text-white hover:bg-red-700 transition-all">
                Confirm
            </button>
        </div>
    </div>
</div>

@yield('scripts')

<script>
// Theme toggle functionality
function updateThemeIcons() {
    const isDark = document.body.classList.contains('dark');
    document.querySelectorAll('.sun-icon').forEach(el => el.classList.toggle('hidden', isDark));
    document.querySelectorAll('.moon-icon').forEach(el => el.classList.toggle('hidden', !isDark));
}

function toggleTheme() {
    document.body.classList.toggle('dark');
    const isDark = document.body.classList.contains('dark');
    localStorage.setItem('adminTheme', isDark ? 'dark' : 'light');
    updateThemeIcons();
}

// Apply saved theme on load
if (localStorage.getItem('adminTheme') === 'dark') {
    document.body.classList.add('dark');
}
updateThemeIcons();

// Bind toggle buttons
document.getElementById('themeToggle')?.addEventListener('click', toggleTheme);
document.getElementById('mobileThemeBtn')?.addEventListener('click', toggleTheme);
</script>
</body>
</html>


