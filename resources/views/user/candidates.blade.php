@extends('layouts.user')
@section('title', 'Candidates | Quizly')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-bold">Candidates</h2>
        <p class="text-slate-500">All candidates across your assessments</p>
    </div>
</div>

<div class="glass rounded-xl p-4 mb-6 flex gap-4 flex-wrap">
    <input type="text" id="searchInput" placeholder="Search by name or email..." class="flex-1 min-w-64 px-4 py-2 border rounded-lg">
    <select id="statusFilter" class="px-4 py-2 border rounded-lg">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="started">Started</option>
        <option value="completed">Completed</option>
    </select>
</div>

<div class="glass rounded-2xl overflow-hidden">
    <table class="w-full">
        <thead class="bg-slate-50">
            <tr class="text-left text-sm text-slate-600">
                <th class="px-6 py-4 font-semibold">Candidate</th>
                <th class="px-6 py-4 font-semibold">Assessment</th>
                <th class="px-6 py-4 font-semibold">Status</th>
                <th class="px-6 py-4 font-semibold">Score</th>
                <th class="px-6 py-4 font-semibold">Date</th>
            </tr>
        </thead>
        <tbody id="candidatesList">
            <tr><td colspan="5" class="px-6 py-8 text-center"><div class="skeleton h-4 w-48 mx-auto"></div></td></tr>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
async function loadCandidates() {
    try {
        // First get all assessments
        const assessRes = await fetch('/api/assessments', { headers: { 'Authorization': `Bearer ${token}` } });
        if (!assessRes.ok) return;
        const { data: assessments } = await assessRes.json();
        
        let allCandidates = [];
        for (const a of assessments) {
            const res = await fetch(`/api/assessments/${a.id}/invitees`, { headers: { 'Authorization': `Bearer ${token}` } });
            if (res.ok) {
                const { data } = await res.json();
                data.forEach(c => allCandidates.push({ ...c, assessment: a }));
            }
        }
        
        const tbody = document.getElementById('candidatesList');
        if (!allCandidates.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">No candidates yet</td></tr>';
            return;
        }
        
        tbody.innerHTML = allCandidates.map(c => {
            const statusColors = { pending: 'bg-slate-100 text-slate-600', started: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700' };
            return `<tr class="border-t">
                <td class="px-6 py-4">
                    <p class="font-medium">${c.first_name || ''} ${c.last_name || ''}</p>
                    <p class="text-sm text-slate-500">${c.email}</p>
                </td>
                <td class="px-6 py-4">${c.assessment?.title || '-'}</td>
                <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[c.status] || statusColors.pending}">${c.status}</span></td>
                <td class="px-6 py-4">${c.score ? c.score + '%' : '-'}</td>
                <td class="px-6 py-4 text-sm text-slate-500">${new Date(c.created_at).toLocaleDateString()}</td>
            </tr>`;
        }).join('');
    } catch (err) {
        document.getElementById('candidatesList').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-500">Error loading candidates</td></tr>';
    }
}
loadCandidates();
</script>
@endsection
