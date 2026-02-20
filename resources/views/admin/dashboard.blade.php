@extends('layouts.admin')

@section('title', 'Dashboard | Admin')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Grid with Skeleton Loading -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
    <!-- Total Users Card -->
    <div class="bg-slate-800 rounded-xl p-4 lg:p-6 border border-slate-700" id="cardUsers">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Total Users</p>
        <div id="totalUsersWrap">
            <div class="skeleton skeleton-glass h-8 w-20 rounded-lg"></div>
        </div>
    </div>

    <!-- Active Subscriptions Card -->
    <div class="bg-slate-800 rounded-xl p-4 lg:p-6 border border-slate-700" id="cardSubs">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Active Subscriptions</p>
        <div id="activeSubscriptionsWrap">
            <div class="skeleton skeleton-glass h-8 w-16 rounded-lg"></div>
        </div>
    </div>

    <!-- Total Assessments Card -->
    <div class="bg-slate-800 rounded-xl p-4 lg:p-6 border border-slate-700" id="cardAssess">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Total Assessments</p>
        <div id="totalAssessmentsWrap">
            <div class="skeleton skeleton-glass h-8 w-16 rounded-lg"></div>
        </div>
    </div>

    <!-- Monthly Revenue Card -->
    <div class="bg-slate-800 rounded-xl p-4 lg:p-6 border border-slate-700" id="cardRevenue">
        <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-amber-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Monthly Revenue</p>
        <div id="monthlyRevenueWrap">
            <div class="skeleton skeleton-glass h-8 w-24 rounded-lg"></div>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="grid lg:grid-cols-3 gap-4 lg:gap-8">
    <!-- Recent Users -->
    <div class="lg:col-span-2 bg-slate-800 rounded-xl border border-slate-700">
        <div class="p-4 lg:p-6 border-b border-slate-700 flex items-center justify-between">
            <h2 class="text-base lg:text-lg font-bold">Recent Users</h2>
            <a href="/admin/users" class="text-sm text-indigo-400 hover:text-indigo-300">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700">
                        <th class="text-left px-4 lg:px-6 py-3 text-xs lg:text-sm font-medium text-slate-400">Name</th>
                        <th class="text-left px-4 lg:px-6 py-3 text-xs lg:text-sm font-medium text-slate-400 hidden sm:table-cell">Email</th>
                        <th class="text-left px-4 lg:px-6 py-3 text-xs lg:text-sm font-medium text-slate-400">Status</th>
                        <th class="text-left px-4 lg:px-6 py-3 text-xs lg:text-sm font-medium text-slate-400 hidden md:table-cell">Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <!-- Skeleton rows -->
                    <tr class="border-b border-slate-700/50">
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-28 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-40 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    </tr>
                    <tr class="border-b border-slate-700/50">
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-32 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-36 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    </tr>
                    <tr class="border-b border-slate-700/50">
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-44 rounded"></div></td>
                        <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                        <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-800 rounded-xl border border-slate-700">
        <div class="p-4 lg:p-6 border-b border-slate-700">
            <h2 class="text-base lg:text-lg font-bold">Quick Actions</h2>
        </div>
        <div class="p-3 lg:p-4 space-y-2 lg:space-y-3">
            <a href="/admin/subscription-plans" class="flex items-center gap-3 p-3 lg:p-4 bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-medium text-sm lg:text-base">Add Subscription Plan</span>
            </a>
            <a href="/admin/users" class="flex items-center gap-3 p-3 lg:p-4 bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span class="font-medium text-sm lg:text-base">Manage Users</span>
            </a>
            <a href="/admin/settings" class="flex items-center gap-3 p-3 lg:p-4 bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35"/>
                </svg>
                <span class="font-medium text-sm lg:text-base">System Settings</span>
            </a>
            <a href="/admin/reports" class="flex items-center gap-3 p-3 lg:p-4 bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium text-sm lg:text-base">View Reports</span>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="mt-4 lg:mt-8 bg-slate-800 rounded-xl border border-slate-700">
    <div class="p-4 lg:p-6 border-b border-slate-700">
        <h2 class="text-base lg:text-lg font-bold">Recent Activity</h2>
    </div>
    <div class="p-4 lg:p-6" id="activityList">
        <!-- Skeleton Activity Items -->
        <div class="space-y-4">
            <div class="flex items-start gap-3 lg:gap-4 p-3 lg:p-4 bg-slate-700/50 rounded-lg">
                <div class="skeleton skeleton-glass skeleton-circle w-10 h-10 flex-shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="skeleton skeleton-glass h-4 w-32 rounded"></div>
                    <div class="skeleton skeleton-glass h-3 w-48 rounded"></div>
                    <div class="skeleton skeleton-glass h-3 w-16 rounded"></div>
                </div>
            </div>
            <div class="flex items-start gap-3 lg:gap-4 p-3 lg:p-4 bg-slate-700/50 rounded-lg">
                <div class="skeleton skeleton-glass skeleton-circle w-10 h-10 flex-shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="skeleton skeleton-glass h-4 w-40 rounded"></div>
                    <div class="skeleton skeleton-glass h-3 w-36 rounded"></div>
                    <div class="skeleton skeleton-glass h-3 w-20 rounded"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');

    async function loadDashboard() {
        try {
            const res = await fetch('/api/admin/dashboard', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                localStorage.removeItem('adminToken');
                localStorage.removeItem('admin');
                window.location.href = '/admin/login';
                return;
            }

            const data = await res.json();
            
            // Replace skeletons with actual data
            document.getElementById('totalUsersWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold">${data.users?.total || 0}</p>`;
            document.getElementById('activeSubscriptionsWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-emerald-400">${data.users?.active_subscriptions || 0}</p>`;
            document.getElementById('totalAssessmentsWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-purple-400">${data.assessments?.total || 0}</p>`;
            document.getElementById('monthlyRevenueWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-amber-400">₦${(data.revenue?.this_month || 0).toLocaleString()}</p>`;
        } catch (err) {
            console.error('Failed to load dashboard:', err);
            // Show error state
            document.getElementById('totalUsersWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-slate-500">--</p>`;
            document.getElementById('activeSubscriptionsWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-slate-500">--</p>`;
            document.getElementById('totalAssessmentsWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-slate-500">--</p>`;
            document.getElementById('monthlyRevenueWrap').innerHTML = `<p class="text-2xl lg:text-3xl font-bold text-slate-500">--</p>`;
        }
    }

    async function loadUsers() {
        try {
            const res = await fetch('/api/admin/users', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            const users = data.users || data.data || [];

            const tbody = document.getElementById('usersTable');
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="px-4 lg:px-6 py-8 text-center text-slate-500">No users found</td></tr>';
                return;
            }

            tbody.innerHTML = users.slice(0, 5).map(user => `
                <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                                ${(user.first_name || 'U').charAt(0).toUpperCase()}
                            </div>
                            <span class="font-medium text-sm lg:text-base truncate">${user.first_name} ${user.last_name}</span>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 text-slate-400 text-sm hidden sm:table-cell truncate max-w-[200px]">${user.email}</td>
                    <td class="px-4 lg:px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium ${user.is_active ? 'bg-emerald-600/20 text-emerald-400' : 'bg-red-600/20 text-red-400'}">
                            ${user.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell">
                        <button onclick="toggleUser('${user.id}')" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">
                            Toggle Status
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (err) {
            console.error('Failed to load users:', err);
            document.getElementById('usersTable').innerHTML = '<tr><td colspan="4" class="px-4 lg:px-6 py-8 text-center text-red-400">Failed to load users</td></tr>';
        }
    }

    async function loadActivity() {
        try {
            // Fetch recent activity (using users as proxy for now)
            const res = await fetch('/api/admin/users?limit=5', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            const users = (data.users || data.data || []).slice(0, 3);

            const activityList = document.getElementById('activityList');
            
            if (users.length === 0) {
                activityList.innerHTML = '<p class="text-center text-slate-500 py-8">No recent activity</p>';
                return;
            }

            activityList.innerHTML = `
                <div class="space-y-4">
                    ${users.map(user => `
                        <div class="flex items-start gap-3 lg:gap-4 p-3 lg:p-4 bg-slate-700/50 rounded-lg">
                            <div class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-sm lg:text-base">New user registered</p>
                                <p class="text-xs lg:text-sm text-slate-400">${user.first_name} ${user.last_name} joined the platform</p>
                                <p class="text-xs text-slate-500 mt-1">${new Date(user.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } catch (err) {
            console.error('Failed to load activity:', err);
        }
    }

    async function toggleUser(id) {
        try {
            await fetch(`/api/admin/users/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            loadUsers();
        } catch (err) {
            console.error('Failed to toggle user:', err);
        }
    }

    // Load all data
    loadDashboard();
    loadUsers();
    loadActivity();
</script>
@endsection
