@extends('layouts.user')
@section('title', 'Assessments | Quizly')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold">Assessments</h2>
        <p class="text-slate-500">Manage your assessments and tests</p>
    </div>
    <a href="/assessments/create" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Assessment
    </a>
</div>

<!-- Filters -->
<div class="glass rounded-xl p-4 mb-6 flex gap-4 flex-wrap">
    <input type="text" id="searchInput" placeholder="Search assessments..." class="flex-1 min-w-64 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
    <select id="statusFilter" class="px-4 py-2 border rounded-lg">
        <option value="">All Status</option>
        <option value="draft">Draft</option>
        <option value="published">Published</option>
        <option value="closed">Closed</option>
    </select>
</div>

<!-- Assessments Grid -->
<div id="assessmentsGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Skeleton -->
    <div class="glass rounded-2xl p-6"><div class="skeleton h-6 w-3/4 mb-4"></div><div class="skeleton h-4 w-1/2 mb-2"></div><div class="skeleton h-4 w-1/3"></div></div>
    <div class="glass rounded-2xl p-6"><div class="skeleton h-6 w-3/4 mb-4"></div><div class="skeleton h-4 w-1/2 mb-2"></div><div class="skeleton h-4 w-1/3"></div></div>
    <div class="glass rounded-2xl p-6"><div class="skeleton h-6 w-3/4 mb-4"></div><div class="skeleton h-4 w-1/2 mb-2"></div><div class="skeleton h-4 w-1/3"></div></div>
</div>
@endsection

@section('scripts')
<script>
async function loadAssessments() {
    try {
        const res = await fetch('/api/assessments', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const grid = document.getElementById('assessmentsGrid');
        
        if (res.ok) {
            const data = await res.json();
            const list = data.data || [];
            
            if (list.length === 0) {
                grid.innerHTML = `<div class="col-span-full text-center py-16 glass rounded-2xl">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">No assessments yet</h3>
                    <p class="text-slate-500 mb-4">Create your first assessment to get started</p>
                    <a href="/assessments/create" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Assessment
                    </a>
                </div>`;
            } else {
                grid.innerHTML = list.map(a => {
                    const statusColors = { draft: 'bg-slate-100 text-slate-600', published: 'bg-emerald-100 text-emerald-700', closed: 'bg-red-100 text-red-700' };
                    return `<div class="glass rounded-2xl p-6 hover-lift cursor-pointer" onclick="window.location='/assessments/${a.id}'">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-bold text-lg text-slate-900">${a.title}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[a.status] || statusColors.draft}">${a.status || 'Draft'}</span>
                        </div>
                        <p class="text-slate-500 text-sm mb-4 line-clamp-2">${a.description || 'No description'}</p>
                        <div class="flex justify-between text-sm text-slate-600">
                            <span>${a.questions_count || 0} questions</span>
                            <span>${a.invitees_count || 0} candidates</span>
                        </div>
                    </div>`;
                }).join('');
            }
        } else {
            grid.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Failed to load assessments</div>';
        }
    } catch (err) {
        document.getElementById('assessmentsGrid').innerHTML = '<div class="col-span-full text-center py-8 text-red-500">Error loading assessments</div>';
    }
}

document.getElementById('searchInput').addEventListener('input', loadAssessments);
document.getElementById('statusFilter').addEventListener('change', loadAssessments);
loadAssessments();
</script>
@endsection
