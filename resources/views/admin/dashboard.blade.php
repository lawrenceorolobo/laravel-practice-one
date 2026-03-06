@extends('layouts.admin')

@section('title', 'Dashboard | Admin')
@section('page-title', 'Dashboard')

@section('content')
<style>
    .kpi-card { transition: all 0.2s; border-top: 3px solid transparent; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
    .stat-value { font-size: 1.75rem; font-weight: 800; line-height: 1; letter-spacing: -0.03em; color: var(--text-primary); }
    .mini-stat { padding: 14px; border-radius: 12px; background: var(--bg); border: 1px solid var(--border); }
    .mini-stat-value { font-size: 1.25rem; font-weight: 700; line-height: 1.2; }
    .action-link { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border-radius: 12px; background: var(--bg); border: 1px solid var(--border); transition: all .2s; text-decoration: none; color: var(--text-primary); }
    .action-link:hover { border-color: var(--text-faint); transform: translateX(4px); background: var(--surface-raised); }
</style>

<!-- KPI Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6">
    <!-- Total Users -->
    <div class="kpi-card panel p-4 lg:p-5" style="border-top-color: #6366f1;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(99,102,241,0.1);">
                <svg class="w-4 h-4" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(99,102,241,0.1);color:#818cf8;" id="usersTrend">—</span>
        </div>
        <p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary);">Total Users</p>
        <div id="totalUsersWrap"><div class="skel h-7 w-16"></div></div>
        <p class="text-[11px] mt-2" style="color:var(--text-muted);" id="usersSubtext">—</p>
    </div>

    <!-- Active Subscriptions -->
    <div class="kpi-card panel p-4 lg:p-5" style="border-top-color: #10b981;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(16,185,129,0.1);">
                <svg class="w-4 h-4" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,0.1);color:#34d399;">Active</span>
        </div>
        <p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary);">Active Subscriptions</p>
        <div id="activeSubscriptionsWrap"><div class="skel h-7 w-12"></div></div>
        <p class="text-[11px] mt-2" style="color:var(--text-muted);" id="subsSubtext">—</p>
    </div>

    <!-- Total Assessments -->
    <div class="kpi-card panel p-4 lg:p-5" style="border-top-color: #a855f7;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(168,85,247,0.1);">
                <svg class="w-4 h-4" style="color:#c084fc" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(168,85,247,0.1);color:#c084fc;" id="assessmentsTrend">—</span>
        </div>
        <p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary);">Total Assessments</p>
        <div id="totalAssessmentsWrap"><div class="skel h-7 w-12"></div></div>
        <p class="text-[11px] mt-2" style="color:var(--text-muted);" id="assessSubtext">—</p>
    </div>

    <!-- Monthly Revenue -->
    <div class="kpi-card panel p-4 lg:p-5" style="border-top-color: #f59e0b;">
        <div class="flex items-center justify-between mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(245,158,11,0.1);">
                <svg class="w-4 h-4" style="color:#fbbf24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary);">Monthly Revenue</p>
        <div id="monthlyRevenueWrap"><div class="skel h-7 w-20"></div></div>
        <p class="text-[11px] mt-2" style="color:var(--text-muted);" id="revenueSubtext">—</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-3 gap-3 lg:gap-4 mb-6">
    <!-- Growth Chart -->
    <div class="lg:col-span-2 panel p-4 lg:p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Growth Overview</h3>
                <p class="text-[11px] mt-0.5" style="color:var(--text-muted);">Last 6 months</p>
            </div>
            <div class="flex gap-4 text-[11px]" style="color:var(--text-secondary);">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#818cf8"></span>Users</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#c084fc"></span>Assessments</span>
            </div>
        </div>
        <div id="growthChart" style="height: 260px; min-height: 260px;"><div class="skel w-full h-full"></div></div>
    </div>

    <!-- Plan Distribution -->
    <div class="panel p-4 lg:p-5">
        <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Plan Distribution</h3>
        <p class="text-[11px] mb-4" style="color:var(--text-muted);">Subscription breakdown</p>
        <div id="planChart" style="height: 220px; min-height: 220px;"><div class="skel w-full h-full"></div></div>
        <div id="planLegend" class="mt-3 space-y-2"></div>
    </div>
</div>

<!-- Revenue + Test Sessions -->
<div class="grid lg:grid-cols-2 gap-3 lg:gap-4 mb-6">
    <!-- Revenue Trend -->
    <div class="panel p-4 lg:p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Revenue Trend</h3>
                <p class="text-[11px] mt-0.5" style="color:var(--text-muted);">Monthly revenue (₦)</p>
            </div>
        </div>
        <div id="revenueChart" style="height: 220px; min-height: 220px;"><div class="skel w-full h-full"></div></div>
    </div>

    <!-- Test Sessions -->
    <div class="panel p-4 lg:p-5">
        <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Test Sessions</h3>
        <p class="text-[11px] mb-4" style="color:var(--text-muted);">Completion overview</p>
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="mini-stat">
                <p class="mini-stat-value" style="color: #34d399;" id="completedSessions">—</p>
                <p class="text-[11px] mt-1" style="color:var(--text-muted);">Completed</p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-value" style="color: #818cf8;" id="avgScore">—</p>
                <p class="text-[11px] mt-1" style="color:var(--text-muted);">Avg Score</p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-value" style="color: #fbbf24;" id="inProgressSessions">—</p>
                <p class="text-[11px] mt-1" style="color:var(--text-muted);">In Progress</p>
            </div>
            <div class="mini-stat">
                <p class="mini-stat-value" style="color: #f87171;" id="timedOutSessions">—</p>
                <p class="text-[11px] mt-1" style="color:var(--text-muted);">Timed Out</p>
            </div>
        </div>
        <div class="flex items-center gap-2 p-3 rounded-lg" style="background: var(--accent-bg); border: 1px solid rgba(99,102,241,0.15);">
            <svg class="w-3.5 h-3.5" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            <span class="text-[11px]" style="color:var(--text-secondary);"><span class="font-semibold" style="color:var(--text-primary);" id="totalSessions">0</span> total sessions</span>
        </div>
    </div>
</div>

<!-- Bottom: Recent Users + Quick Actions -->
<div class="grid lg:grid-cols-3 gap-3 lg:gap-4">
    <!-- Recent Users -->
    <div class="lg:col-span-2 panel">
        <div class="px-4 lg:px-5 py-3.5 flex items-center justify-between" style="border-bottom: 1px solid var(--border);">
            <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Recent Users</h3>
            <a href="/admin/users" class="text-[11px] font-medium" style="color:var(--accent-text);">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border);background:var(--bg-alt);">
                        <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Name</th>
                        <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">Email</th>
                        <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Status</th>
                        <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden md:table-cell" style="color:var(--text-muted);">Joined</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <tr style="border-bottom: 1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-7 h-7"></div><div class="skel h-3.5 w-24"></div></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-32"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-16"></div></td></tr>
                    <tr style="border-bottom: 1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-7 h-7"></div><div class="skel h-3.5 w-28"></div></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-36"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-16"></div></td></tr>
                    <tr><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-7 h-7"></div><div class="skel h-3.5 w-20"></div></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-40"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-16"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="panel">
        <div class="px-4 lg:px-5 py-3.5" style="border-bottom: 1px solid var(--border);">
            <h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Quick Actions</h3>
        </div>
        <div class="p-3 space-y-2">
            <a href="/admin/subscription-plans" class="action-link">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(99,102,241,0.1);"><svg class="w-3.5 h-3.5" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></div>
                <span class="text-[13px] font-medium">Add Plan</span>
                <svg class="w-3.5 h-3.5 ml-auto" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/admin/users" class="action-link">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,0.1);"><svg class="w-3.5 h-3.5" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></div>
                <span class="text-[13px] font-medium">Manage Users</span>
                <svg class="w-3.5 h-3.5 ml-auto" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/admin/feature-flags" class="action-link">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(168,85,247,0.1);"><svg class="w-3.5 h-3.5" style="color:#c084fc" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg></div>
                <span class="text-[13px] font-medium">Feature Flags</span>
                <svg class="w-3.5 h-3.5 ml-auto" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/admin/reports" class="action-link">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(245,158,11,0.1);"><svg class="w-3.5 h-3.5" style="color:#fbbf24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <span class="text-[13px] font-medium">View Reports</span>
                <svg class="w-3.5 h-3.5 ml-auto" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="/admin/settings" class="action-link">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(139,148,158,0.08);"><svg class="w-3.5 h-3.5" style="color:var(--text-secondary)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35"/></svg></div>
                <span class="text-[13px] font-medium">Settings</span>
                <svg class="w-3.5 h-3.5 ml-auto" style="color:var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</div>

<!-- User Detail Drawer -->
<div id="userDrawer" class="detail-drawer">
    <div class="drawer-overlay" onclick="closeDrawer()"></div>
    <div class="drawer-panel">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);">User Details</h3>
            <button onclick="closeDrawer()" class="p-1.5 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="drawerContent" class="p-5">
            <div class="flex flex-col items-center gap-2 py-6"><div class="skel skel-circle w-16 h-16"></div><div class="skel h-4 w-32 mt-2"></div><div class="skel h-3 w-40"></div></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');
    const textFg = () => themeColor('#64748b','#8b949e');
    const gridLine = () => themeColor('rgba(0,0,0,0.06)','rgba(255,255,255,0.04)');
    const tooltipTheme = () => isDarkMode() ? 'dark' : 'light';

    function chartDefaults() {
        return {
            chart: { toolbar: { show: false }, fontFamily: 'Inter, sans-serif', background: 'transparent' },
            grid: { borderColor: gridLine(), strokeDashArray: 3 },
            tooltip: { theme: tooltipTheme() },
        };
    }

    let growthChartInstance = null, revenueChartInstance = null, planChartInstance = null;

    async function loadDashboard() {
        try {
            const res = await fetch('/api/admin/dashboard', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            if (res.status === 401) { localStorage.removeItem('adminToken'); localStorage.removeItem('admin'); window.location.href = '/admin/login'; return; }
            const data = await res.json();

            function animateValue(el, end, prefix = '', suffix = '') {
                let start = 0; const duration = 800; const startTime = performance.now();
                function step(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.floor(eased * end);
                    el.innerHTML = `<p class="stat-value">${prefix}${current.toLocaleString()}${suffix}</p>`;
                    if (progress < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            animateValue(document.getElementById('totalUsersWrap'), data.users?.total || 0);
            animateValue(document.getElementById('activeSubscriptionsWrap'), data.users?.active_subscriptions || 0);
            animateValue(document.getElementById('totalAssessmentsWrap'), data.assessments?.total || 0);
            animateValue(document.getElementById('monthlyRevenueWrap'), data.revenue?.this_month || 0, '₦');

            document.getElementById('usersSubtext').textContent = `${data.users?.new_this_month || 0} new this month`;
            document.getElementById('subsSubtext').textContent = `${data.users?.active || 0} active users`;
            document.getElementById('assessSubtext').textContent = `${data.assessments?.this_month || 0} created this month`;
            document.getElementById('revenueSubtext').textContent = `₦${(data.revenue?.total || 0).toLocaleString()} lifetime`;

            if (data.users?.new_this_month > 0) {
                document.getElementById('usersTrend').innerHTML = `↑ ${data.users.new_this_month}`;
                document.getElementById('usersTrend').style.background = 'rgba(16,185,129,0.1)';
                document.getElementById('usersTrend').style.color = '#34d399';
            }
            if (data.assessments?.this_month > 0) {
                document.getElementById('assessmentsTrend').innerHTML = `↑ ${data.assessments.this_month}`;
                document.getElementById('assessmentsTrend').style.background = 'rgba(16,185,129,0.1)';
                document.getElementById('assessmentsTrend').style.color = '#34d399';
            }
        } catch (err) {
            console.error('Failed to load dashboard:', err);
            ['totalUsersWrap','activeSubscriptionsWrap','totalAssessmentsWrap','monthlyRevenueWrap'].forEach(id => {
                document.getElementById(id).innerHTML = `<p class="stat-value" style="color:var(--text-muted);">—</p>`;
            });
        }
    }

    async function loadAnalytics() {
        try {
            const res = await fetch('/api/admin/dashboard/analytics', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();

            try { renderGrowthChart(data); } catch(e) {
                console.error('Growth chart error:', e);
                document.getElementById('growthChart').innerHTML = '<p class="text-center py-10 text-[12px]" style="color:var(--text-muted);">Chart unavailable</p>';
            }
            try { renderRevenueChart(data); } catch(e) {
                console.error('Revenue chart error:', e);
                document.getElementById('revenueChart').innerHTML = '<p class="text-center py-10 text-[12px]" style="color:var(--text-muted);">Chart unavailable</p>';
            }
            try { renderPlanChart(data); } catch(e) {
                console.error('Plan chart error:', e);
                document.getElementById('planChart').innerHTML = '<p class="text-center py-10 text-[12px]" style="color:var(--text-muted);">Chart unavailable</p>';
            }

            const ts = data.test_sessions || {};
            document.getElementById('completedSessions').textContent = ts.completed || 0;
            document.getElementById('avgScore').textContent = ts.avg_score ? ts.avg_score + '%' : '—';
            document.getElementById('inProgressSessions').textContent = ts.in_progress || 0;
            document.getElementById('timedOutSessions').textContent = ts.timed_out || 0;
            document.getElementById('totalSessions').textContent = ts.total || 0;

        } catch (err) {
            console.error('Failed to load analytics:', err);
            ['growthChart', 'revenueChart', 'planChart'].forEach(id => {
                document.getElementById(id).innerHTML = '<p class="text-center py-10 text-[12px]" style="color:var(--text-muted);">Failed to load</p>';
            });
        }
    }

    function renderGrowthChart(data) {
        if (growthChartInstance) growthChartInstance.destroy();
        growthChartInstance = new ApexCharts(document.getElementById('growthChart'), {
            ...chartDefaults(),
            chart: { ...chartDefaults().chart, type: 'area', height: 260 },
            series: [
                { name: 'Users', data: data.users || [] },
                { name: 'Assessments', data: data.assessments || [] },
            ],
            xaxis: { categories: data.labels || [], labels: { style: { colors: textFg(), fontSize: '10px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: textFg(), fontSize: '10px' } } },
            colors: ['#818cf8', '#c084fc'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 95, 100] } },
            stroke: { curve: 'smooth', width: 2 },
            dataLabels: { enabled: false },
            legend: { show: false },
        });
        growthChartInstance.render();
    }

    function renderRevenueChart(data) {
        if (revenueChartInstance) revenueChartInstance.destroy();
        revenueChartInstance = new ApexCharts(document.getElementById('revenueChart'), {
            ...chartDefaults(),
            chart: { ...chartDefaults().chart, type: 'bar', height: 220 },
            series: [{ name: 'Revenue', data: data.revenue || [] }],
            xaxis: { categories: data.labels || [], labels: { style: { colors: textFg(), fontSize: '10px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: textFg(), fontSize: '10px' }, formatter: v => '₦' + v.toLocaleString() } },
            colors: ['#fbbf24'],
            fill: { type: 'gradient', gradient: { shade: 'dark', type: 'vertical', shadeIntensity: 0.3, opacityFrom: 1, opacityTo: 0.8 } },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
            dataLabels: { enabled: false },
        });
        revenueChartInstance.render();
    }

    function renderPlanChart(data) {
        if (planChartInstance) planChartInstance.destroy();
        const plans = data.plan_distribution || [];
        if (plans.length > 0) {
            const planColors = ['#818cf8', '#c084fc', '#34d399', '#fbbf24', '#f87171'];
            planChartInstance = new ApexCharts(document.getElementById('planChart'), {
                chart: { type: 'donut', height: 220, fontFamily: 'Inter, sans-serif', background: 'transparent' },
                series: plans.map(p => p.count),
                labels: plans.map(p => p.plan),
                colors: planColors.slice(0, plans.length),
                plotOptions: { pie: { donut: { size: '72%', labels: { show: true, total: { show: true, label: 'Total', color: textFg(), fontSize: '12px', fontWeight: 600, formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) } } } } },
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                legend: { show: false },
                tooltip: { theme: tooltipTheme() },
            });
            planChartInstance.render();

            document.getElementById('planLegend').innerHTML = plans.map((p, i) => `
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full" style="background:${planColors[i]}"></span>
                        <span class="text-[11px]" style="color:var(--text-secondary);">${p.plan}</span>
                    </div>
                    <span class="text-[11px] font-semibold" style="color:var(--text-primary);">${p.count}</span>
                </div>
            `).join('');
        } else {
            document.getElementById('planChart').innerHTML = '<p class="text-center py-8 text-[12px]" style="color:var(--text-muted);">No data</p>';
        }
    }

    async function loadUsers() {
        try {
            const res = await fetch('/api/admin/users', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            const users = data.users || data.data || [];
            const tbody = document.getElementById('usersTable');

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-5 py-8 text-center text-[12px]" style="color:var(--text-muted);">No users found</td></tr>';
                return;
            }

            const gradients = ['from-indigo-500 to-purple-500','from-emerald-500 to-cyan-500','from-amber-500 to-orange-500','from-pink-500 to-rose-500','from-blue-500 to-indigo-500'];

            tbody.innerHTML = users.slice(0, 5).map((user, i) => `
                <tr class="tr-click" onclick="viewUser('${user.id}')" style="border-bottom: 1px solid var(--border-subtle);">
                    <td class="px-4 lg:px-5 py-2.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br ${gradients[i % gradients.length]} flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                ${(user.first_name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <span class="text-[13px] font-medium truncate" style="color:var(--text-primary);">${user.first_name} ${user.last_name}</span>
                        </div>
                    </td>
                    <td class="px-4 lg:px-5 py-2.5 text-[12px] hidden sm:table-cell truncate max-w-[200px]" style="color:var(--text-secondary);">${user.email}</td>
                    <td class="px-4 lg:px-5 py-2.5">
                        <span class="badge ${user.is_active ? 'badge-success' : 'badge-danger'}">
                            <span class="w-1.5 h-1.5 rounded-full" style="background: ${user.is_active ? '#10b981' : '#ef4444'}"></span>
                            ${user.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-4 lg:px-5 py-2.5 text-[11px] hidden md:table-cell" style="color:var(--text-muted);">${new Date(user.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric'})}</td>
                </tr>
            `).join('');
        } catch (err) {
            document.getElementById('usersTable').innerHTML = '<tr><td colspan="4" class="px-5 py-8 text-center text-[11px]" style="color:#f87171">Failed to load</td></tr>';
        }
    }

    // Re-render charts on theme change
    window.addEventListener('themeChanged', loadAnalytics);

    loadDashboard();
    loadUsers();
    loadAnalytics();

    // Real-time updates via WebSocket
    QuizlyEcho.private('admin')
        .listen('AssessmentUpdated', () => { loadDashboard(); })
        .listen('TestCompleted', () => { loadDashboard(); loadUsers(); })
        .listen('InviteeUpdated', () => { loadDashboard(); });

    const avatarColors = ['#6366f1','#10b981','#f59e0b','#a855f7','#ef4444','#06b6d4'];
    const avatarGrads = ['#818cf8','#34d399','#fbbf24','#c084fc','#f87171','#22d3ee'];

    async function viewUser(id) {
        const drawer = document.getElementById('userDrawer');
        drawer.classList.add('open');
        document.getElementById('drawerContent').innerHTML = '<div class="flex flex-col items-center gap-2 py-6"><div class="skel skel-circle w-16 h-16"></div><div class="skel h-4 w-32 mt-2"></div><div class="skel h-3 w-40"></div></div>';
        try {
            const res = await fetch(`/api/admin/users/${id}`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const user = await res.json();
            const ci = Math.abs((user.first_name || 'U').charCodeAt(0)) % 6;
            document.getElementById('drawerContent').innerHTML = `
                <div class="flex flex-col items-center text-center pb-5" style="border-bottom:1px solid var(--border);">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-xl font-bold" style="background:linear-gradient(135deg,${avatarColors[ci]},${avatarGrads[ci]});">
                        ${(user.first_name || 'U').charAt(0).toUpperCase()}
                    </div>
                    <h3 class="text-[15px] font-semibold mt-3" style="color:var(--text-primary);">${user.first_name} ${user.last_name}</h3>
                    <p class="text-[12px]" style="color:var(--text-secondary);">${user.email}</p>
                    <span class="badge mt-2 ${user.is_active ? 'badge-success' : 'badge-danger'}">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:${user.is_active ? '#10b981':'#ef4444'}"></span>
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </div>
                <div class="space-y-3 py-5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Company</span>
                        <span class="text-[12px] font-medium" style="color:var(--text-primary);">${user.company_name || '\u2014'}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Plan</span>
                        <span class="badge ${user.subscription_status === 'active' ? 'badge-success' : 'badge-neutral'}">${user.subscription_plan || 'Free'}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Assessments</span>
                        <span class="text-[12px] font-semibold" style="color:var(--text-primary);">${user.assessments_count || 0}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Joined</span>
                        <span class="text-[12px]" style="color:var(--text-primary);">${new Date(user.created_at).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'})}</span>
                    </div>
                </div>
            `;
        } catch (err) {
            document.getElementById('drawerContent').innerHTML = '<p class="text-center py-10 text-[12px]" style="color:#f87171;">Failed to load user details</p>';
        }
    }

    function closeDrawer() {
        document.getElementById('userDrawer').classList.remove('open');
    }
</script>
@endsection
