@extends('layouts.admin')

@section('title', 'Users | Admin')
@section('page-title', 'User Management')

@section('header-actions')
<button onclick="openCreateModal()" class="btn-primary flex items-center gap-2">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    <span class="hidden sm:inline">Add User</span>
</button>
@endsection

@section('content')
<!-- Filters -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
    <div class="flex-1 w-full sm:w-auto relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchInput" placeholder="Search by name or email..." class="w-full pl-10 pr-4 py-2 text-[13px]">
    </div>
    <select id="statusFilter" class="w-full sm:w-auto px-3 py-2 text-[13px]">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

<!-- Users Table -->
<div class="panel">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom: 1px solid var(--border); background: var(--bg-alt);">
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">User</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden md:table-cell" style="color:var(--text-muted);">Email</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden lg:table-cell" style="color:var(--text-muted);">Company</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Plan</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">Status</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-8 h-8"></div><div class="skel h-3.5 w-24"></div></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-36"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel h-3.5 w-28"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-6 w-16 rounded-lg"></div></td></tr>
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-8 h-8"></div><div class="skel h-3.5 w-28"></div></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-40"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel h-3.5 w-24"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-12"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel skel-pill h-5 w-16"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-6 w-16 rounded-lg"></div></td></tr>
                <tr><td class="px-4 lg:px-5 py-3"><div class="flex items-center gap-3"><div class="skel skel-circle w-8 h-8"></div><div class="skel h-3.5 w-20"></div></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-32"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel h-3.5 w-32"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-6 w-16 rounded-lg"></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-[11px]" style="color:var(--text-muted);" id="paginationInfo">Loading users...</p>
    <div class="flex gap-2" id="paginationButtons"></div>
</div>

<!-- Create/Edit Modal -->
<div id="userModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);">
    <div class="panel w-full max-w-md max-h-[90vh] overflow-y-auto" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="modalTitle">Add User</h3>
            <button onclick="closeModal()" class="p-1 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm" class="p-5 space-y-4">
            <input type="hidden" id="userId">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">First Name</label>
                    <input type="text" id="firstName" required class="w-full">
                </div>
                <div>
                    <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Last Name</label>
                    <input type="text" id="lastName" required class="w-full">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Email</label>
                <input type="email" id="email" required class="w-full">
            </div>
            <div>
                <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Company Name</label>
                <input type="text" id="companyName" class="w-full">
            </div>
            <div id="passwordField">
                <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Password</label>
                <input type="password" id="password" class="w-full">
            </div>
            <div class="flex items-center gap-3">
                <label class="toggle-switch">
                    <input type="checkbox" id="isActive" checked>
                    <span class="slider"></span>
                </label>
                <label for="isActive" class="text-[12px] cursor-pointer" style="color:var(--text-secondary);">Active</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="btn-ghost flex-1">Cancel</button>
                <button type="submit" class="btn-primary flex-1">Save</button>
            </div>
        </form>
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
    let allUsers = [];
    const avatarColors = ['#6366f1','#10b981','#f59e0b','#a855f7','#ef4444','#06b6d4'];
    const avatarGrads = ['#818cf8','#34d399','#fbbf24','#c084fc','#f87171','#22d3ee'];

    async function loadUsers() {
        try {
            const res = await fetch('/api/admin/users', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            allUsers = data.users || data.data || [];
            renderUsers(allUsers);
        } catch (err) {
            document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="6" class="px-5 py-8 text-center text-[12px]" style="color:#f87171">Failed to load users</td></tr>';
            document.getElementById('paginationInfo').textContent = 'Error loading users';
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        document.getElementById('paginationInfo').textContent = `Showing ${users.length} users`;

        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-10 text-center text-[12px]" style="color:var(--text-muted)">No users found</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => {
            const ci = Math.abs((user.first_name || 'U').charCodeAt(0)) % 6;
            return `
            <tr class="tr-click" onclick="viewUser('${user.id}')" style="border-bottom:1px solid var(--border-subtle);">
                <td class="px-4 lg:px-5 py-2.5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0" style="background:linear-gradient(135deg,${avatarColors[ci]},${avatarGrads[ci]});">
                            ${(user.first_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <span class="text-[13px] font-medium truncate block" style="color:var(--text-primary);">${user.first_name} ${user.last_name}</span>
                            <span class="text-[11px] md:hidden truncate block" style="color:var(--text-muted);">${user.email}</span>
                        </div>
                    </div>
                </td>
                <td class="px-4 lg:px-5 py-2.5 text-[12px] hidden md:table-cell truncate max-w-[200px]" style="color:var(--text-secondary);">${user.email}</td>
                <td class="px-4 lg:px-5 py-2.5 text-[12px] hidden lg:table-cell truncate max-w-[150px]" style="color:var(--text-secondary);">${user.company_name || '—'}</td>
                <td class="px-4 lg:px-5 py-2.5">
                    <span class="badge ${user.subscription_status === 'active' ? 'badge-success' : 'badge-neutral'}">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:${user.subscription_status === 'active' ? '#10b981' : 'var(--text-muted)'}"></span>
                        ${user.subscription_status || 'free'}
                    </span>
                </td>
                <td class="px-4 lg:px-5 py-2.5 hidden sm:table-cell">
                    <span class="badge ${user.is_active ? 'badge-success' : 'badge-danger'}">
                        <span class="w-1.5 h-1.5 rounded-full" style="background:${user.is_active ? '#10b981' : '#ef4444'}"></span>
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-4 lg:px-5 py-2.5">
                    <div class="flex items-center gap-0.5">
                        <button onclick="editUser('${user.id}')" class="p-1.5 rounded-lg transition" style="color:var(--text-secondary);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'" title="Edit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button onclick="toggleUser('${user.id}')" class="p-1.5 rounded-lg transition" style="color:var(--text-secondary);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'" title="Toggle">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        </button>
                        <button onclick="deleteUser('${user.id}')" class="p-1.5 rounded-lg hover:bg-red-500/10 transition hidden sm:block" title="Delete">
                            <svg class="w-3.5 h-3.5" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add User';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('password').required = true;
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    }

    function editUser(id) {
        const user = allUsers.find(u => u.id === id);
        if (!user) return;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = user.id;
        document.getElementById('firstName').value = user.first_name;
        document.getElementById('lastName').value = user.last_name;
        document.getElementById('email').value = user.email;
        document.getElementById('companyName').value = user.company_name || '';
        document.getElementById('isActive').checked = user.is_active;
        document.getElementById('passwordField').style.display = 'none';
        document.getElementById('password').required = false;
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userModal').classList.remove('flex');
    }

    async function toggleUser(id) {
        try {
            await fetch(`/api/admin/users/${id}/toggle`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            loadUsers();
        } catch (err) { console.error('Toggle failed:', err); }
    }

    async function deleteUser(id) {
        const confirmed = await showConfirm('Delete User', 'This will permanently remove the user and all their data.', 'Delete', 'danger');
        if (!confirmed) return;
        try {
            await fetch(`/api/admin/users/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            toastSuccess('User deleted');
            loadUsers();
        } catch (err) { toastError('Failed to delete user'); }
    }

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const data = {
            first_name: document.getElementById('firstName').value,
            last_name: document.getElementById('lastName').value,
            email: document.getElementById('email').value,
            company_name: document.getElementById('companyName').value,
            is_active: document.getElementById('isActive').checked
        };
        if (!id) data.password = document.getElementById('password').value;
        try {
            await fetch(id ? `/api/admin/users/${id}` : '/api/admin/users', {
                method: id ? 'PUT' : 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            closeModal();
            toastSuccess(id ? 'User updated' : 'User created');
            loadUsers();
        } catch (err) { toastError('Failed to save user'); }
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
        const q = e.target.value.toLowerCase();
        renderUsers(allUsers.filter(u => u.first_name.toLowerCase().includes(q) || u.last_name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)));
    });

    document.getElementById('statusFilter').addEventListener('change', (e) => {
        const s = e.target.value;
        renderUsers(!s ? allUsers : allUsers.filter(u => s === 'active' ? u.is_active : !u.is_active));
    });

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
                <div class="space-y-3 py-5" style="border-bottom:1px solid var(--border);">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Company</span>
                        <span class="text-[12px] font-medium" style="color:var(--text-primary);">${user.company_name || '—'}</span>
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
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-medium" style="color:var(--text-muted);">Email Verified</span>
                        <span class="badge ${user.email_verified_at ? 'badge-success' : 'badge-warning'}">${user.email_verified_at ? 'Verified' : 'Unverified'}</span>
                    </div>
                </div>
                ${user.payments && user.payments.length > 0 ? `
                <div class="pt-5">
                    <h4 class="text-[12px] font-semibold mb-3" style="color:var(--text-primary);">Recent Payments</h4>
                    <div class="space-y-2">
                        ${user.payments.map(p => `
                            <div class="inner-card flex items-center justify-between">
                                <div>
                                    <p class="text-[12px] font-medium" style="color:var(--text-primary);">₦${Number(p.amount).toLocaleString()}</p>
                                    <p class="text-[10px]" style="color:var(--text-muted);">${new Date(p.paid_at || p.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'})}</p>
                                </div>
                                <span class="badge ${p.status === 'success' ? 'badge-success' : 'badge-warning'}">${p.status}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>` : ''}
                <div class="flex gap-3 mt-6">
                    <button onclick="closeDrawer();editUser('${user.id}')" class="btn-primary flex-1 text-center">Edit User</button>
                    <button onclick="closeDrawer();toggleUser('${user.id}')" class="btn-ghost flex-1 text-center">${user.is_active ? 'Disable' : 'Enable'}</button>
                </div>
            `;
        } catch (err) {
            document.getElementById('drawerContent').innerHTML = '<p class="text-center py-10 text-[12px]" style="color:#f87171;">Failed to load user details</p>';
        }
    }

    function closeDrawer() {
        document.getElementById('userDrawer').classList.remove('open');
    }

    loadUsers();
</script>
@endsection
