@extends('layouts.admin')

@section('title', 'Assessments | Admin')
@section('page-title', 'Assessment Management')

@section('content')
<!-- Stats Grid with Skeleton Loading -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
    <!-- Total Assessments -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6" id="stat-total">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Total Assessments</p>
        <div class="skeleton skeleton-glass h-8 lg:h-10 w-16 rounded"></div>
    </div>
    <!-- Active -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6" id="stat-active">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Active</p>
        <div class="skeleton skeleton-glass h-8 lg:h-10 w-12 rounded"></div>
    </div>
    <!-- Completed -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6" id="stat-completed">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Completed</p>
        <div class="skeleton skeleton-glass h-8 lg:h-10 w-12 rounded"></div>
    </div>
    <!-- Avg Score -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6" id="stat-score">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Avg. Score</p>
        <div class="skeleton skeleton-glass h-8 lg:h-10 w-16 rounded"></div>
    </div>
</div>

<!-- Filters -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 mb-4 lg:mb-6">
    <div class="flex-1 w-full sm:w-auto">
        <input type="text" id="searchInput" placeholder="Search assessments..." 
            class="w-full px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <select id="statusFilter" class="w-full sm:w-auto px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="published">Published</option>
        <option value="closed">Closed</option>
    </select>
</div>

<!-- Assessments Table -->
<div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-700 bg-slate-800/50">
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">Assessment</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden md:table-cell">Created By</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden sm:table-cell">Invitees</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden sm:table-cell">Completed</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">Status</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400 hidden lg:table-cell">Created</th>
                    <th class="text-left px-4 lg:px-6 py-3 lg:py-4 text-xs lg:text-sm font-medium text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody id="assessmentsTable">
                <!-- Skeleton Loading Rows -->
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="skeleton skeleton-glass h-5 w-32 lg:w-40 rounded mb-2"></div>
                        <div class="skeleton skeleton-glass h-4 w-20 rounded"></div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-28 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-8 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-8 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-16 rounded"></div></td>
                </tr>
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4">
                        <div class="skeleton skeleton-glass h-5 w-36 lg:w-48 rounded mb-2"></div>
                        <div class="skeleton skeleton-glass h-4 w-24 rounded"></div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-32 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-10 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-6 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-20 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-20 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-16 rounded"></div></td>
                </tr>
                <tr>
                    <td class="px-4 lg:px-6 py-4">
                        <div class="skeleton skeleton-glass h-5 w-28 lg:w-36 rounded mb-2"></div>
                        <div class="skeleton skeleton-glass h-4 w-16 rounded"></div>
                    </td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-6 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-8 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-6 w-14 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-5 w-22 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-8 w-16 rounded"></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');
    let allAssessments = [];

    async function loadAssessments() {
        try {
            const res = await fetch('/api/admin/assessments', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            allAssessments = data.assessments || data.data || [];
            updateStats();
            renderAssessments(allAssessments);
        } catch (err) {
            console.error('Failed to load assessments:', err);
            updateStatsError();
            renderAssessments([]);
        }
    }

    function updateStats() {
        const total = allAssessments.length;
        const active = allAssessments.filter(a => a.status === 'published').length;
        const completed = allAssessments.filter(a => a.status === 'closed').length;
        
        // Calculate avg score from completed invitees
        let avgScore = 0;
        const allScores = allAssessments.flatMap(a => a.scores || []);
        if (allScores.length > 0) {
            avgScore = Math.round(allScores.reduce((a, b) => a + b, 0) / allScores.length);
        }

        document.getElementById('stat-total').innerHTML = `
            <p class="text-xs lg:text-sm text-slate-400 mb-1">Total Assessments</p>
            <p class="text-2xl lg:text-3xl font-bold">${total}</p>
        `;
        document.getElementById('stat-active').innerHTML = `
            <p class="text-xs lg:text-sm text-slate-400 mb-1">Active</p>
            <p class="text-2xl lg:text-3xl font-bold text-emerald-400">${active}</p>
        `;
        document.getElementById('stat-completed').innerHTML = `
            <p class="text-xs lg:text-sm text-slate-400 mb-1">Completed</p>
            <p class="text-2xl lg:text-3xl font-bold text-purple-400">${completed}</p>
        `;
        document.getElementById('stat-score').innerHTML = `
            <p class="text-xs lg:text-sm text-slate-400 mb-1">Avg. Score</p>
            <p class="text-2xl lg:text-3xl font-bold text-amber-400">${avgScore}%</p>
        `;
    }

    function updateStatsError() {
        const errorHtml = (label) => `
            <p class="text-xs lg:text-sm text-slate-400 mb-1">${label}</p>
            <p class="text-sm text-red-400">Failed to load</p>
        `;
        document.getElementById('stat-total').innerHTML = errorHtml('Total Assessments');
        document.getElementById('stat-active').innerHTML = errorHtml('Active');
        document.getElementById('stat-completed').innerHTML = errorHtml('Completed');
        document.getElementById('stat-score').innerHTML = errorHtml('Avg. Score');
    }

    function renderAssessments(assessments) {
        const tbody = document.getElementById('assessmentsTable');
        
        if (assessments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 lg:px-6 py-12 text-center text-slate-500 text-sm">No assessments found</td></tr>';
            return;
        }

        tbody.innerHTML = assessments.map(a => `
            <tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <div class="min-w-0">
                        <p class="font-medium text-sm truncate">${a.title}</p>
                        <p class="text-xs text-slate-500">${a.questions_count || 0} questions</p>
                    </div>
                </td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-slate-400 text-sm hidden md:table-cell truncate">${a.user?.first_name || 'Unknown'} ${a.user?.last_name || ''}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-sm hidden sm:table-cell">${a.invitees_count || 0}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-sm hidden sm:table-cell">${a.completed_count || 0}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${
                        a.status === 'published' ? 'bg-emerald-600/20 text-emerald-400' :
                        a.status === 'closed' ? 'bg-slate-600/20 text-slate-400' :
                        'bg-amber-600/20 text-amber-400'
                    }">
                        ${a.status || 'draft'}
                    </span>
                </td>
                <td class="px-4 lg:px-6 py-3 lg:py-4 text-slate-400 text-sm hidden lg:table-cell">${new Date(a.created_at).toLocaleDateString()}</td>
                <td class="px-4 lg:px-6 py-3 lg:py-4">
                    <div class="flex items-center gap-1">
                        <button onclick="viewAssessment('${a.id}')" class="p-1.5 lg:p-2 hover:bg-slate-700 rounded-lg transition-colors" title="View">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <button onclick="deleteAssessment('${a.id}')" class="p-1.5 lg:p-2 hover:bg-red-600/20 rounded-lg transition-colors" title="Delete">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function viewAssessment(id) {
        window.location.href = `/admin/assessments/${id}`;
    }

    async function deleteAssessment(id) {
        const confirmed = await showConfirm('Delete Assessment', 'Are you sure you want to delete this assessment? This action cannot be undone.', 'Delete', 'danger');
        if (!confirmed) return;
        try {
            await fetch(`/api/admin/assessments/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            toastSuccess('Assessment deleted successfully');
            loadAssessments();
        } catch (err) {
            toastError('Failed to delete assessment');
            console.error('Failed to delete assessment:', err);
        }
    }

    document.getElementById('searchInput').addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        const filtered = allAssessments.filter(a => a.title.toLowerCase().includes(query));
        renderAssessments(filtered);
    });

    document.getElementById('statusFilter').addEventListener('change', (e) => {
        const status = e.target.value;
        if (!status) {
            renderAssessments(allAssessments);
        } else {
            const filtered = allAssessments.filter(a => a.status === status);
            renderAssessments(filtered);
        }
    });

    loadAssessments();
</script>
@endsection
