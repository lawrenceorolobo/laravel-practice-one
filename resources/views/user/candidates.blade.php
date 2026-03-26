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
    <input type="text" id="searchInput" placeholder="Search by name or email..." class="flex-1 min-w-64 px-4 py-2 border rounded-lg" oninput="filterCandidates()">
    <select id="statusFilter" class="px-4 py-2 border rounded-lg" onchange="filterCandidates()">
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
                <th class="px-3 py-4 w-8"><input type="checkbox" id="selectAllCandidates" onchange="toggleAllCandidateCbs(this.checked)" class="w-4 h-4 accent-indigo-600 rounded"></th>
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

<!-- Candidate Detail Modal -->
<div id="candidateModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target===this)closeCandidateModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between rounded-t-2xl">
            <div>
                <h3 class="text-lg font-bold text-slate-900" id="modalCandidateName">Candidate</h3>
                <p class="text-sm text-slate-500" id="modalCandidateEmail">email</p>
            </div>
            <button onclick="closeCandidateModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Summary Stats -->
        <div class="px-6 py-4 grid grid-cols-3 gap-4 border-b">
            <div class="text-center">
                <p class="text-2xl font-bold text-slate-900" id="modalTotalTests">0</p>
                <p class="text-xs text-slate-500">Assessments</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-emerald-600" id="modalAvgScore">-</p>
                <p class="text-xs text-slate-500">Avg Score</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-indigo-600" id="modalPassRate">-</p>
                <p class="text-xs text-slate-500">Pass Rate</p>
            </div>
        </div>

        <!-- Assessment History -->
        <div class="px-6 py-4">
            <h4 class="text-sm font-semibold text-slate-600 uppercase tracking-wider mb-3">Assessment History</h4>
            <div id="modalAssessmentList" class="space-y-3">
                <p class="text-sm text-slate-400">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Compare Toolbar -->
<div id="compareCandidateBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50">
    <div class="bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-4">
        <span id="compareCandidateCount" class="text-sm font-medium"></span>
        <button onclick="openCandidateComparison()" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-1.5 rounded-lg text-sm font-bold transition">Compare</button>
        <button onclick="toggleAllCandidateCbs(false);document.getElementById('selectAllCandidates').checked=false" class="text-slate-300 hover:text-white text-sm">Cancel</button>
    </div>
</div>

<!-- Candidate Comparison Modal -->
<div id="candidateCompareModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this)closeCandidateComparison()">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h2 class="text-xl font-bold">Candidate Comparison</h2>
            <button onclick="closeCandidateComparison()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div id="candidateCompareBody" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>

<!-- Session Detail Modal -->
<div id="sessionDetailModal" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target===this)closeSessionDetail()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-5 border-b shrink-0">
            <div>
                <h2 class="text-lg font-bold" id="sdTitle">Session Detail</h2>
                <p class="text-xs text-slate-400" id="sdMeta"></p>
            </div>
            <button onclick="closeSessionDetail()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div id="sdBody" class="p-5 overflow-y-auto flex-1 space-y-5">
            <p class="text-center text-slate-400 py-10">Loading...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let allCandidates = [];

async function loadCandidates() {
    try {
        let page = 1, lastPage = 1;
        allCandidates = [];
        do {
            const res = await fetch(`/api/candidates?page=${page}`, { headers: { 'Authorization': `Bearer ${token}` } });
            if (!res.ok) {
                document.getElementById('candidatesList').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-500">Error loading candidates</td></tr>';
                return;
            }
            const json = await res.json();
            allCandidates = allCandidates.concat(json.data || []);
            lastPage = json.meta?.last_page ?? 1;
            page++;
        } while (page <= lastPage && page <= 10); // cap at 1000 records (10 pages × 100) for safety
        filterCandidates();
    } catch (err) {
        document.getElementById('candidatesList').innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-red-500">Error loading candidates</td></tr>';
    }
}

function filterCandidates() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    let filtered = allCandidates;
    if (search) {
        filtered = filtered.filter(c =>
            (c.first_name || '').toLowerCase().includes(search) ||
            (c.last_name || '').toLowerCase().includes(search) ||
            c.email.toLowerCase().includes(search)
        );
    }
    if (status) {
        filtered = filtered.filter(c => c.status === status);
    }

    renderCandidates(filtered);
}

function renderCandidates(candidates) {
    const tbody = document.getElementById('candidatesList');
    if (!candidates.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">No candidates found</td></tr>';
        return;
    }

    const statusColors = { pending: 'bg-slate-100 text-slate-600', sent: 'bg-blue-100 text-blue-700', started: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700' };
    tbody.innerHTML = candidates.map((c, idx) => {
        const score = c.test_session?.percentage != null ? parseFloat(c.test_session.percentage).toFixed(1) + '%' : '-';
        const name = `${c.first_name || ''} ${c.last_name || ''}`.trim() || c.email;
        return `<tr class="border-t cursor-pointer hover:bg-slate-50 transition" onclick="openCandidateModal('${c.email}')">
            <td class="px-3 py-4" onclick="event.stopPropagation()"><input type="checkbox" class="cand-cb w-4 h-4 accent-indigo-600 rounded" data-email="${c.email}" data-name="${name.replace(/'/g, '\\&#39;')}" data-idx="${idx}" onchange="onCandidateCheck()"></td>
            <td class="px-6 py-4">
                <p class="font-medium">${name}</p>
                <p class="text-sm text-slate-500">${c.email}</p>
            </td>
            <td class="px-6 py-4">${c.assessment?.title || '-'}</td>
            <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[c.status] || statusColors.pending}">${c.status}</span></td>
            <td class="px-6 py-4 font-medium ${c.test_session?.passed ? 'text-emerald-600' : c.test_session?.percentage != null ? 'text-red-500' : 'text-slate-400'}">${score}</td>
            <td class="px-6 py-4 text-sm text-slate-500">${new Date(c.created_at).toLocaleDateString()}</td>
        </tr>`;
    }).join('');
}

function openCandidateModal(email) {
    // Group all entries for this email across assessments
    const entries = allCandidates.filter(c => c.email === email);
    if (!entries.length) return;

    const first = entries[0];
    const name = `${first.first_name || ''} ${first.last_name || ''}`.trim() || email;
    document.getElementById('modalCandidateName').textContent = name;
    document.getElementById('modalCandidateEmail').textContent = email;

    const completed = entries.filter(e => e.test_session?.percentage != null);
    document.getElementById('modalTotalTests').textContent = entries.length;

    if (completed.length > 0) {
        const avgScore = completed.reduce((sum, e) => sum + parseFloat(e.test_session.percentage), 0) / completed.length;
        const passCount = completed.filter(e => e.test_session?.passed).length;
        document.getElementById('modalAvgScore').textContent = avgScore.toFixed(1) + '%';
        document.getElementById('modalPassRate').textContent = Math.round((passCount / completed.length) * 100) + '%';
    } else {
        document.getElementById('modalAvgScore').textContent = '-';
        document.getElementById('modalPassRate').textContent = '-';
    }

    const statusColors = { pending: 'bg-slate-100 text-slate-600', sent: 'bg-blue-100 text-blue-700', started: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700' };
    document.getElementById('modalAssessmentList').innerHTML = entries.map(e => {
        const score = e.test_session?.percentage != null ? parseFloat(e.test_session.percentage).toFixed(1) + '%' : '-';
        const passed = e.test_session?.passed;
        const passLabel = e.test_session?.percentage != null ? (passed ? '<span class="text-emerald-600 text-xs font-semibold ml-2">Passed</span>' : '<span class="text-red-500 text-xs font-semibold ml-2">Failed</span>') : '';
        const clickable = e.test_session ? `onclick="openSessionDetail('${e.assessment_id}', '${e.test_session.id}', '${(e.assessment?.title || 'Assessment').replace(/'/g, '\\&apos;')}')" class="border rounded-xl p-4 hover:bg-indigo-50 hover:border-indigo-300 cursor-pointer transition group"` : `class="border rounded-xl p-4"`;
        return `<div ${clickable}>
            <div class="flex items-center justify-between mb-2">
                <p class="font-semibold text-slate-900 group-hover:text-indigo-700 transition">${e.assessment?.title || 'Assessment'}</p>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${statusColors[e.status] || ''}">${e.status}</span>
                    ${e.test_session ? '<span class="text-xs text-indigo-400 group-hover:text-indigo-600">View Details →</span>' : ''}
                </div>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-1 text-slate-500">
                    <span>Score: <strong class="text-slate-900">${score}</strong></span>${passLabel}
                </div>
                <span class="text-slate-400 text-xs">${new Date(e.created_at).toLocaleDateString()}</span>
            </div>
            ${e.test_session ? `<div class="mt-2 flex gap-3 text-xs text-slate-400">
                ${e.test_session.tab_switches > 0 ? `<span class="text-amber-600">⚠ ${e.test_session.tab_switches} tab switch(es)</span>` : ''}
                ${e.test_session.fullscreen_exits > 0 ? `<span class="text-amber-600">⚠ ${e.test_session.fullscreen_exits} fullscreen exit(s)</span>` : ''}
                ${e.test_session.webcam_recording_url ? '<span class="text-indigo-500">🎥 Recording available</span>' : ''}
            </div>` : ''}
        </div>`;
    }).join('');

    document.getElementById('candidateModal').classList.remove('hidden');
    document.getElementById('candidateModal').classList.add('flex');
}

function closeCandidateModal() {
    document.getElementById('candidateModal').classList.add('hidden');
    document.getElementById('candidateModal').classList.remove('flex');
}

// ---- Multi-select comparison ----
let selectedCandidateEmails = new Set();

function onCandidateCheck() {
    selectedCandidateEmails.clear();
    document.querySelectorAll('.cand-cb:checked').forEach(cb => selectedCandidateEmails.add(cb.dataset.email));
    const bar = document.getElementById('compareCandidateBar');
    if (selectedCandidateEmails.size >= 2) {
        bar.classList.remove('hidden');
        document.getElementById('compareCandidateCount').textContent = `${selectedCandidateEmails.size} candidates selected`;
    } else {
        bar.classList.add('hidden');
    }
}

function toggleAllCandidateCbs(checked) {
    document.querySelectorAll('.cand-cb').forEach(cb => { cb.checked = checked; });
    onCandidateCheck();
}

function openCandidateComparison() {
    const modal = document.getElementById('candidateCompareModal');
    const body = document.getElementById('candidateCompareBody');
    modal.classList.remove('hidden');
    const emails = Array.from(selectedCandidateEmails);
    const colors = ['#3C50E0', '#10B981', '#F59E0B', '#EF4444', '#7C3AED', '#EC4899'];

    // Gather data per unique candidate email
    const candidates = emails.map(email => {
        const entries = allCandidates.filter(c => c.email === email);
        const first = entries[0];
        const name = `${first.first_name || ''} ${first.last_name || ''}`.trim() || email;
        const completed = entries.filter(e => e.test_session?.percentage != null);
        const avgScore = completed.length > 0 ? completed.reduce((s, e) => s + parseFloat(e.test_session.percentage), 0) / completed.length : 0;
        const passCount = completed.filter(e => e.test_session?.passed).length;
        const passRate = completed.length > 0 ? Math.round((passCount / completed.length) * 100) : 0;
        const totalTime = completed.reduce((s, e) => s + (e.test_session?.time_spent_seconds || 0), 0);
        const tabSwitches = entries.reduce((s, e) => s + (e.test_session?.tab_switches || 0), 0);
        return { email, name, entries, completed, avgScore, passRate, totalTime, tabSwitches, totalAssessments: entries.length };
    });

    // Metrics table
    const metrics = [
        { label: 'Assessments', key: c => c.totalAssessments },
        { label: 'Completed', key: c => c.completed.length },
        { label: 'Avg Score', key: c => c.completed.length ? c.avgScore.toFixed(1) + '%' : '—' },
        { label: 'Pass Rate', key: c => c.completed.length ? c.passRate + '%' : '—' },
        { label: 'Total Time', key: c => c.totalTime > 0 ? Math.round(c.totalTime / 60) + ' min' : '—' },
        { label: 'Tab Switches', key: c => c.tabSwitches },
    ];

    let html = `<div class="overflow-x-auto"><table class="w-full text-sm">
        <thead class="border-b"><tr>
            <th class="py-3 text-left text-slate-500 font-semibold">Metric</th>
            ${candidates.map((c, i) => `<th class="py-3 text-center font-semibold" style="color:${colors[i % colors.length]}">${c.name.length > 20 ? c.name.substring(0, 18) + '…' : c.name}</th>`).join('')}
        </tr></thead>
        <tbody>${metrics.map(m => `<tr class="border-t border-slate-50">
            <td class="py-2.5 text-slate-600 font-medium">${m.label}</td>
            ${candidates.map(c => `<td class="py-2.5 text-center font-bold text-slate-800">${m.key(c)}</td>`).join('')}
        </tr>`).join('')}</tbody>
    </table></div>`;

    // Score comparison bars
    html += `<h3 class="font-bold text-lg mt-8 mb-4">Score Comparison</h3>
    <div class="space-y-3">${candidates.map((c, i) => {
        const score = c.avgScore;
        return `<div class="flex items-center gap-3">
            <span class="text-sm font-medium w-32 truncate" title="${c.name}">${c.name}</span>
            <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                <div class="h-full rounded-full flex items-center justify-end pr-2" style="width:${Math.max(score, 3)}%;background:${colors[i % colors.length]};transition:width .4s">
                    <span class="text-[10px] text-white font-bold">${score.toFixed(1)}%</span>
                </div>
            </div>
        </div>`;
    }).join('')}</div>`;

    // Per-assessment breakdown
    html += `<h3 class="font-bold text-lg mt-8 mb-4">Assessment Breakdown</h3><div class="overflow-x-auto"><table class="w-full text-xs">
        <thead class="border-b"><tr>
            <th class="py-2 text-left text-slate-500">Assessment</th>
            ${candidates.map((c, i) => `<th class="py-2 text-center" style="color:${colors[i % colors.length]}">${c.name.length > 15 ? c.name.substring(0, 13) + '…' : c.name}</th>`).join('')}
        </tr></thead><tbody>`;

    // Collect all unique assessments
    const allAssessmentIds = new Set();
    candidates.forEach(c => c.entries.forEach(e => { if (e.assessment?.id) allAssessmentIds.add(e.assessment.id); }));
    allAssessmentIds.forEach(aId => {
        const aTitle = candidates.flatMap(c => c.entries).find(e => e.assessment?.id === aId)?.assessment?.title || 'Assessment';
        html += `<tr class="border-t border-slate-50">
            <td class="py-2 text-slate-600 font-medium">${aTitle}</td>
            ${candidates.map(c => {
                const entry = c.entries.find(e => e.assessment?.id === aId);
                if (!entry) return `<td class="py-2 text-center text-slate-300">—</td>`;
                if (entry.test_session?.percentage != null) {
                    const pct = parseFloat(entry.test_session.percentage).toFixed(1);
                    const color = entry.test_session.passed ? '#10B981' : '#EF4444';
                    return `<td class="py-2 text-center font-bold" style="color:${color}">${pct}%</td>`;
                }
                return `<td class="py-2 text-center text-slate-400">${entry.status}</td>`;
            }).join('')}
        </tr>`;
    });
    html += `</tbody></table></div>`;

    body.innerHTML = html;
}

function closeCandidateComparison() {
    document.getElementById('candidateCompareModal').classList.add('hidden');
}

function buildAnswerHtml(answers) {
    return answers.map((a, i) => {
        const correctStyle = a.is_correct ? 'border-emerald-200 bg-emerald-50/50' : 'border-red-200 bg-red-50/50';
        const icon = a.is_correct ? '<span class="text-emerald-500 font-bold">✓</span>' : '<span class="text-red-500 font-bold">✗</span>';
        let answerDisplay = '';
        if (['text_input', 'fill_blank', 'numeric'].includes(a.question_type)) {
            answerDisplay = `<p class="text-sm mt-2"><b>Candidate:</b> ${a.text_answer || '<em class="text-slate-400">Blank</em>'}</p>
                             <p class="text-sm text-slate-500 mt-1"><b>Expected:</b> ${a.expected_answer || 'N/A'}</p>`;
        } else if (['ordering', 'drag_drop_sort', 'matching'].includes(a.question_type)) {
            answerDisplay = `<p class="text-sm mt-2"><b>Candidate:</b> ${a.text_answer || JSON.stringify(a.selected_options) || '<em class="text-slate-400">Blank</em>'}</p>`;
        } else {
            answerDisplay = `<div class="mt-3 space-y-1">${a.options.map((opt, oIdx) => {
                const isSel = (a.selected_options || []).includes(oIdx.toString());
                const isExp = opt.is_correct;
                let style = 'text-slate-600', prefix = '<span class="text-slate-300 w-4 inline-block text-center rounded-full text-[10px] border mr-1">○</span>';
                if (isSel && isExp) { style = 'text-emerald-700 font-medium'; prefix = '<span class="text-emerald-600 w-4 inline-block text-center rounded-full text-[10px] border border-emerald-500 bg-emerald-50 mr-1">✓</span>'; }
                else if (isSel && !isExp) { style = 'text-red-600'; prefix = '<span class="text-red-500 w-4 inline-block text-center rounded-full text-[10px] border border-red-500 bg-red-50 mr-1">✗</span>'; }
                else if (!isSel && isExp) { style = 'text-emerald-600 font-medium'; prefix = '<span class="text-emerald-500 w-4 inline-block text-center rounded-full text-[10px] border border-emerald-500 border-dashed mr-1">✓</span>'; }
                return `<p class="text-sm ${style}">${prefix} ${opt.text}</p>`;
            }).join('')}</div>`;
        }
        return `<div class="border rounded-xl p-4 ${correctStyle}"><div class="flex gap-3">
            <span class="font-bold text-slate-700 border-r pr-3">${i + 1}.</span>
            <div class="flex-1 min-w-0"><p class="font-medium text-slate-900 break-words">${a.question_text}</p>${answerDisplay}</div>
            <div class="text-right shrink-0">${icon}<p class="text-xs text-slate-500 mt-1 shadow-sm px-2 py-0.5 rounded-full bg-white">${a.points_earned} / ${a.max_points}</p></div>
        </div></div>`;
    }).join('');
}

async function openSessionDetail(assessmentId, sessionId, title) {
    const modal = document.getElementById('sessionDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('sdTitle').textContent = title;
    document.getElementById('sdMeta').textContent = 'Loading session data...';
    document.getElementById('sdBody').innerHTML = '<p class="text-center text-slate-400 py-10">Loading...</p>';

    try {
        const res = await fetch(`/api/assessments/${assessmentId}/sessions/${sessionId}/answers`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error();
        const data = await res.json();
        const s = data.session || {};

        document.getElementById('sdMeta').textContent = s.email || '';

        // --- Video Section ---
        const videoHtml = s.webcam_recording_url ? `
        <div class="rounded-xl overflow-hidden bg-black">
            <video controls class="w-full max-h-64" src="${s.webcam_recording_url}"></video>
        </div>
        <div class="flex gap-2">
            <a href="${s.webcam_recording_url}" download target="_blank" class="flex-1 text-center py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">⬇ Download Recording</a>
            <a href="${s.webcam_recording_url}" target="_blank" class="flex-1 text-center py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-lg hover:bg-slate-200 transition">↗ Open in New Tab</a>
        </div>` : `<div class="rounded-xl border-2 border-dashed border-slate-200 p-6 text-center text-slate-400 text-sm">No video recording available</div>`;

        // --- Proctoring Section ---
        const tabSw = s.tab_switches || 0;
        const fsEx = s.fullscreen_exits || 0;
        const mins = s.time_spent_seconds ? Math.round(s.time_spent_seconds / 60) : '-';
        const riskLevel = (tabSw + fsEx) === 0 ? {label:'Low Risk', cls:'text-emerald-600 bg-emerald-50'} : (tabSw + fsEx) <= 3 ? {label:'Medium Risk', cls:'text-amber-600 bg-amber-50'} : {label:'High Risk', cls:'text-red-600 bg-red-50'};
        const procHtml = `
        <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-slate-800">🔍 Proctoring Report</h3>
                <span class="text-xs font-bold px-2 py-1 rounded-full ${riskLevel.cls}">${riskLevel.label}</span>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-slate-50 rounded-lg p-3"><p class="text-xl font-bold ${tabSw > 0 ? 'text-amber-600' : 'text-slate-700'}">${tabSw}</p><p class="text-xs text-slate-500">Tab Switches</p></div>
                <div class="bg-slate-50 rounded-lg p-3"><p class="text-xl font-bold ${fsEx > 0 ? 'text-amber-600' : 'text-slate-700'}">${fsEx}</p><p class="text-xs text-slate-500">Fullscreen Exits</p></div>
                <div class="bg-slate-50 rounded-lg p-3"><p class="text-xl font-bold text-slate-700">${mins}</p><p class="text-xs text-slate-500">Minutes Spent</p></div>
            </div>
            ${s.ip_address ? `<p class="text-xs text-slate-400 mt-3">IP: ${s.ip_address} &nbsp;|&nbsp; Browser: ${(s.user_agent||'').substring(0,60)}...</p>` : ''}
        </div>`;

        // --- Score Section ---
        const pct = s.percentage != null ? parseFloat(s.percentage).toFixed(1) : null;
        const scoreHtml = pct !== null ? `
        <div class="rounded-xl border p-4 flex items-center justify-between">
            <div><h3 class="font-semibold text-slate-800">📊 Score</h3><p class="text-xs text-slate-400">${s.total_score ?? '-'} / ${s.max_score ?? '-'} points</p></div>
            <div class="text-right">
                <p class="text-3xl font-extrabold ${s.passed ? 'text-emerald-600' : 'text-red-500'}">${pct}%</p>
                <p class="text-xs font-semibold ${s.passed ? 'text-emerald-600' : 'text-red-500'}">${s.passed ? '✓ Passed' : '✗ Failed'}</p>
            </div>
        </div>` : '';

        // --- Answers Section ---
        const answersHtml = data.answers?.length ? `
        <div>
            <h3 class="font-semibold text-slate-800 mb-3">📝 Answers (${data.answers.length})</h3>
            <div class="space-y-3">${buildAnswerHtml(data.answers)}</div>
        </div>` : '';

        document.getElementById('sdBody').innerHTML = videoHtml + procHtml + scoreHtml + answersHtml;
    } catch (e) {
        document.getElementById('sdBody').innerHTML = '<p class="text-center text-red-500 py-10">Failed to load session data.</p>';
    }
}

function closeSessionDetail() {
    document.getElementById('sessionDetailModal').classList.add('hidden');
    document.getElementById('sessionDetailModal').classList.remove('flex');
    // Pause any playing video to stop audio
    document.querySelectorAll('#sessionDetailModal video').forEach(v => v.pause());
}

loadCandidates();

// Real-time updates via WebSocket
if (typeof user !== 'undefined' && user?.id) {
    QuizlyEcho.private('user.' + user.id)
        .listen('TestCompleted', () => loadCandidates())
        .listen('InviteeUpdated', () => loadCandidates());
}
</script>
@endsection
