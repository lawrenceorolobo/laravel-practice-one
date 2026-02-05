@extends('layouts.admin')

@section('title', 'Users | Admin')
@section('page-title', 'User Management')

@section('header-actions')
<button onclick="openCreateModal()" class="px-3 lg:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-xs lg:text-sm transition-colors flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    <span class="hidden sm:inline">Add User</span>
</button>
@endsection

@section('content')
<!-- Filters -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 mb-4 lg:mb-6">
    <div class="flex-1 w-full sm:w-auto">
        <input type="text" id="searchInput" placeholder="Search users..." 
            class="w-full px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <select id="statusFilter" class="w-full sm:w-auto px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

<!-- Users Table -->
<div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-700 bg-slate-800/50">
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">User</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden md:table-cell">Email</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden lg:table-cell">Company</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">Subscription</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden sm:table-cell">Status</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody id="usersTableBody">
                <!-- Skeleton Loading Rows -->
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="skeleton skeleton-glass skeleton-circle w-8 h-8 lg:w-10 lg:h-10"></div>
                            <div class="skeleton skeleton-glass h-5 w-24 lg:w-32 rounded"></div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-36 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-28 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-20 rounded"></div></td>
                </tr>
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="skeleton skeleton-glass skeleton-circle w-8 h-8 lg:w-10 lg:h-10"></div>
                            <div class="skeleton skeleton-glass h-5 w-28 lg:w-36 rounded"></div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-40 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-14 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-20 rounded"></div></td>
                </tr>
                <tr>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="skeleton skeleton-glass skeleton-circle w-8 h-8 lg:w-10 lg:h-10"></div>
                            <div class="skeleton skeleton-glass h-5 w-20 lg:w-28 rounded"></div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-32 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-32 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-12 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-6 w-14 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-20 rounded"></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4 lg:mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
    <p class="text-xs lg:text-sm text-slate-400" id="paginationInfo">Loading users...</p>
    <div class="flex gap-2" id="paginationButtons"></div>
</div>

<!-- Create/Edit Modal -->
<div id="userModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-slate-800 rounded-xl border border-slate-700 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="p-4 lg:p-6 border-b border-slate-700 flex items-center justify-between sticky top-0 bg-slate-800">
            <h3 class="text-base lg:text-lg font-bold" id="modalTitle">Add User</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="userForm" class="p-4 lg:p-6 space-y-4">
            <input type="hidden" id="userId">
            <div class="grid grid-cols-2 gap-3 lg:gap-4">
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">First Name</label>
                    <input type="text" id="firstName" required class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Last Name</label>
                    <input type="text" id="lastName" required class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Email</label>
                <input type="email" id="email" required class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Company Name</label>
                <input type="text" id="companyName" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div id="passwordField">
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Password</label>
                <input type="password" id="password" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="isActive" checked class="w-4 h-4 rounded border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="isActive" class="text-xs lg:text-sm text-slate-300">Active</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-sm transition-colors">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');
    let allUsers = [];

    async function loadUsers() {
        try {
            const res = await fetch('/api/admin/users', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            allUsers = data.users || data.data || [];
            renderUsers(allUsers);
        } catch (err) {
            console.error('Failed to load users:', err);
            document.getElementById('usersTableBody').innerHTML = '<tr><td colspan="6" class="px-4 lg:px-6 py-8 text-center text-red-400 text-sm">Failed to load users</td></tr>';
            document.getElementById('paginationInfo').textContent = 'Error loading users';
        }
    }

    function renderUsers(users) {
        const tbody = document.getElementById('usersTableBody');
        document.getElementById('paginationInfo').textContent = `Showing ${users.length} users`;

        if (users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 lg:px-6 py-12 text-center text-slate-500 text-sm">No users found</td></tr>';
            return;
        }

        tbody.innerHTML = users.map(user => `
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <div class="flex items-center gap-2 lg:gap-3">
                        <div class="w-8 h-8 lg:w-10 lg:h-10 bg-indigo-600 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                            ${(user.first_name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <span class="font-medium text-sm truncate block">${user.first_name} ${user.last_name}</span>
                            <span class="text-xs text-slate-400 md:hidden truncate block">${user.email}</span>
                        </div>
                    </div>
                </td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-slate-400 text-sm hidden md:table-cell truncate max-w-[200px]">${user.email}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-slate-400 text-sm hidden lg:table-cell truncate max-w-[150px]">${user.company_name || '-'}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${user.subscription_status === 'active' ? 'bg-emerald-600/20 text-emerald-400' : 'bg-slate-600/20 text-slate-400'}">
                        ${user.subscription_status || 'free'}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 hidden sm:table-cell">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${user.is_active ? 'bg-emerald-600/20 text-emerald-400' : 'bg-red-600/20 text-red-400'}">
                        ${user.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <div class="flex items-center gap-1">
                        <button onclick="editUser('${user.id}')" class="p-1.5 lg:p-2 hover:bg-slate-700 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="toggleUser('${user.id}')" class="p-1.5 lg:p-2 hover:bg-slate-700 rounded-lg transition-colors" title="Toggle Status">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </button>
                        <button onclick="deleteUser('${user.id}')" class="p-1.5 lg:p-2 hover:bg-red-600/20 rounded-lg transition-colors hidden sm:block" title="Delete">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
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
            await fetch(`/api/admin/users/${id}/toggle`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            loadUsers();
        } catch (err) {
            console.error('Failed to toggle user:', err);
        }
    }

    async function deleteUser(id) {
        const confirmed = await showConfirm('Delete User', 'Are you sure you want to delete this user? All their data will be permanently removed.', 'Delete', 'danger');
        if (!confirmed) return;
        try {
            await fetch(`/api/admin/users/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            toastSuccess('User deleted successfully');
            loadUsers();
        } catch (err) {
            toastError('Failed to delete user');
            console.error('Failed to delete user:', err);
        }
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
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            closeModal();
            loadUsers();
        } catch (err) {
            console.error('Failed to save user:', err);
        }
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = allUsers.filter(u => 
            u.first_name.toLowerCase().includes(query) ||
            u.last_name.toLowerCase().includes(query) ||
            u.email.toLowerCase().includes(query)
        );
        renderUsers(filtered);
    });

    document.getElementById('statusFilter').addEventListener('change', (e) => {
        const status = e.target.value;
        if (!status) {
            renderUsers(allUsers);
        } else {
            const filtered = allUsers.filter(u => status === 'active' ? u.is_active : !u.is_active);
            renderUsers(filtered);
        }
    });

    loadUsers();
</script>
@endsection
