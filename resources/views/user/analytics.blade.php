@extends('layouts.user')
@section('title', 'Analytics | Quizly')

@section('content')
<style>
.stat-card { border-radius: 14px; padding: 20px 24px; }
.stat-card .stat-label { font-size: 13px; font-weight: 500; color: #64748b; }
.stat-card .stat-value { font-size: 28px; font-weight: 800; margin-top: 4px; letter-spacing: -0.5px; }
.chart-card { border-radius: 14px; padding: 24px; }
.chart-title { font-size: 16px; font-weight: 700; color: #1e293b; }
.chart-subtitle { font-size: 13px; color: #94a3b8; margin-top: 2px; }
.assessment-row { cursor: pointer; transition: background .15s; }
.assessment-row:hover { background: #f8fafc; }
.compare-bar { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); z-index: 50; }
/* Skeleton */
.skel { background: linear-gradient(90deg, #e2e8f0 25%, #cbd5e1 50%, #e2e8f0 75%); background-size: 200% 100%; animation: skel-anim 1.5s infinite; border-radius: 8px; }
@keyframes skel-anim { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.chart-skel { display: flex; align-items: flex-end; gap: 8px; padding: 40px 16px 16px; }
.chart-skel .bar { flex:1; border-radius: 4px 4px 0 0; }
@media print {
    nav, .sidebar, header, .no-print, .export-btns, .compare-bar { display: none !important; }
    body { background: white !important; }
    .glass { background: white !important; border: 1px solid #e2e8f0 !important; }
}
/* Leaderboard */
.leader-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; }
.leader-row + .leader-row { border-top: 1px solid #f1f5f9; }
.leader-rank { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; }
/* Difficulty */
.diff-row { display: flex; align-items: center; gap: 12px; padding: 8px 0; }
.diff-row + .diff-row { border-top: 1px solid #f1f5f9; }
.diff-bar-track { flex: 1; height: 6px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
.diff-bar-fill { height: 100%; border-radius: 3px; transition: width .6s ease; }
</style>

<div id="analyticsExport">

<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-slate-900">Analytics</h2>
        <p class="text-slate-500 text-sm">Comprehensive performance insights</p>
    </div>
    <div class="flex gap-2 export-btns no-print">
        <button onclick="exportAnalyticsCsv()" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            CSV
        </button>
        <button onclick="exportPdf()" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export PDF
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="glass stat-card">
        <p class="stat-label">Tests Taken</p>
        <div id="kpiTestsSkel"><div class="skel" style="height:32px;width:60px;margin-top:6px"></div></div>
        <p class="stat-value text-slate-900 hidden" id="kpiTests">—</p>
    </div>
    <div class="glass stat-card">
        <p class="stat-label">Avg Score</p>
        <div id="kpiAvgScoreSkel"><div class="skel" style="height:32px;width:56px;margin-top:6px"></div></div>
        <p class="stat-value hidden" style="color:#3C50E0" id="kpiAvgScore">—</p>
    </div>
    <div class="glass stat-card">
        <p class="stat-label">Pass Rate</p>
        <div id="kpiPassRateSkel"><div class="skel" style="height:32px;width:52px;margin-top:6px"></div></div>
        <p class="stat-value hidden" style="color:#10B981" id="kpiPassRate">—</p>
    </div>
    <div class="glass stat-card">
        <p class="stat-label">Avg Time</p>
        <div id="kpiAvgTimeSkel"><div class="skel" style="height:32px;width:50px;margin-top:6px"></div></div>
        <p class="stat-value hidden" style="color:#7C3AED" id="kpiAvgTime">—</p>
    </div>
    <div class="glass stat-card">
        <p class="stat-label">Candidates</p>
        <div id="kpiCandidatesSkel"><div class="skel" style="height:32px;width:48px;margin-top:6px"></div></div>
        <p class="stat-value hidden" style="color:#F59E0B" id="kpiCandidates">—</p>
    </div>
    <div class="glass stat-card">
        <p class="stat-label">Completion</p>
        <div id="kpiCompletionSkel"><div class="skel" style="height:32px;width:54px;margin-top:6px"></div></div>
        <p class="stat-value hidden" style="color:#EF4444" id="kpiCompletion">—</p>
    </div>
</div>

<!-- Row 1: Area Chart (30-day trend) + Donut (Score Distribution) -->
<div class="grid lg:grid-cols-5 gap-6 mb-6">
    <div class="glass chart-card lg:col-span-3">
        <p class="chart-title">Test Activity</p>
        <p class="chart-subtitle">Tests completed over the last 30 days</p>
        <div id="trendAreaChart" style="min-height:300px">
            <div class="chart-skel" style="height:260px"><div class="bar skel" style="height:35%"></div><div class="bar skel" style="height:55%"></div><div class="bar skel" style="height:40%"></div><div class="bar skel" style="height:65%"></div><div class="bar skel" style="height:50%"></div><div class="bar skel" style="height:45%"></div><div class="bar skel" style="height:60%"></div><div class="bar skel" style="height:30%"></div></div>
        </div>
    </div>
    <div class="glass chart-card lg:col-span-2">
        <p class="chart-title">Score Distribution</p>
        <p class="chart-subtitle">Performance breakdown by range</p>
        <div id="scoreDonutChart" style="min-height:300px">
            <div style="display:flex;align-items:center;justify-content:center;height:260px"><div class="skel" style="width:180px;height:180px;border-radius:50%"></div></div>
        </div>
    </div>
</div>

<!-- Row 2: Bar Chart (Tests/Week) + Radial (Completion Funnel) -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="glass chart-card">
        <p class="chart-title">Weekly Overview</p>
        <p class="chart-subtitle">Tests completed in the last 7 days</p>
        <div id="weeklyBarChart" style="min-height:280px">
            <div class="chart-skel" style="height:240px"><div class="bar skel" style="height:45%"></div><div class="bar skel" style="height:60%"></div><div class="bar skel" style="height:35%"></div><div class="bar skel" style="height:70%"></div><div class="bar skel" style="height:55%"></div><div class="bar skel" style="height:40%"></div><div class="bar skel" style="height:50%"></div></div>
        </div>
    </div>
    <div class="glass chart-card">
        <p class="chart-title">Completion Funnel</p>
        <p class="chart-subtitle">Candidate progression through stages</p>
        <div id="funnelRadialChart" style="min-height:280px">
            <div style="display:flex;align-items:center;justify-content:center;height:240px"><div class="skel" style="width:200px;height:200px;border-radius:50%"></div></div>
        </div>
    </div>
</div>

<!-- Row 3: Time Distribution Bar + Pass/Fail Donut -->
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="glass chart-card">
        <p class="chart-title">Time Spent</p>
        <p class="chart-subtitle">How long candidates take on tests</p>
        <div id="timeBarChart" style="min-height:260px">
            <div class="chart-skel" style="height:220px"><div class="bar skel" style="height:50%"></div><div class="bar skel" style="height:65%"></div><div class="bar skel" style="height:40%"></div><div class="bar skel" style="height:55%"></div><div class="bar skel" style="height:35%"></div></div>
        </div>
    </div>
    <div class="glass chart-card">
        <div class="grid grid-cols-2 gap-6" style="min-height:260px">
            <!-- Top Scorers -->
            <div>
                <p class="chart-title mb-3">Top Performers</p>
                <div id="topScorersList">
                    <div class="space-y-3"><div class="flex items-center gap-3"><div class="skel" style="width:28px;height:28px;border-radius:8px"></div><div class="flex-1"><div class="skel" style="height:14px;width:80%"></div><div class="skel" style="height:10px;width:50%;margin-top:4px"></div></div></div><div class="flex items-center gap-3"><div class="skel" style="width:28px;height:28px;border-radius:8px"></div><div class="flex-1"><div class="skel" style="height:14px;width:70%"></div><div class="skel" style="height:10px;width:40%;margin-top:4px"></div></div></div><div class="flex items-center gap-3"><div class="skel" style="width:28px;height:28px;border-radius:8px"></div><div class="flex-1"><div class="skel" style="height:14px;width:60%"></div><div class="skel" style="height:10px;width:45%;margin-top:4px"></div></div></div></div>
                </div>
            </div>
            <!-- Hardest Questions -->
            <div>
                <p class="chart-title mb-3">Hardest Questions</p>
                <div id="difficultyList">
                    <div class="space-y-3"><div><div class="skel" style="height:12px;width:90%"></div><div class="skel" style="height:6px;width:100%;margin-top:6px"></div></div><div><div class="skel" style="height:12px;width:75%"></div><div class="skel" style="height:6px;width:100%;margin-top:6px"></div></div><div><div class="skel" style="height:12px;width:85%"></div><div class="skel" style="height:6px;width:100%;margin-top:6px"></div></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assessment Performance Table -->
<div class="glass chart-card">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="chart-title">Assessment Performance</p>
            <p class="chart-subtitle">Click to view details · Select to compare</p>
        </div>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-slate-500 border-b border-slate-100">
                <th class="pb-3 pl-2 w-8"><input type="checkbox" id="selectAllAssessments" onchange="toggleAllAssessmentCbs(this.checked)" class="w-4 h-4 accent-indigo-600 rounded"></th>
                <th class="pb-3 font-semibold">Assessment</th>
                <th class="pb-3 font-semibold">Status</th>
                <th class="pb-3 font-semibold">Questions</th>
                <th class="pb-3 font-semibold">Candidates</th>
                <th class="pb-3 font-semibold">Completed</th>
                <th class="pb-3 font-semibold">Avg Score</th>
            </tr>
        </thead>
        <tbody id="assessmentsList">
            <tr><td colspan="7" class="py-4"><div class="flex gap-4 items-center"><div class="skel" style="width:16px;height:16px"></div><div class="skel" style="height:14px;width:40%"></div><div class="skel" style="height:14px;width:12%"></div><div class="skel" style="height:14px;width:8%"></div></div></td></tr>
            <tr><td colspan="7" class="py-4"><div class="flex gap-4 items-center"><div class="skel" style="width:16px;height:16px"></div><div class="skel" style="height:14px;width:35%"></div><div class="skel" style="height:14px;width:10%"></div><div class="skel" style="height:14px;width:8%"></div></div></td></tr>
            <tr><td colspan="7" class="py-4"><div class="flex gap-4 items-center"><div class="skel" style="width:16px;height:16px"></div><div class="skel" style="height:14px;width:45%"></div><div class="skel" style="height:14px;width:11%"></div><div class="skel" style="height:14px;width:8%"></div></div></td></tr>
        </tbody>
    </table>
    </div>
</div>

</div><!-- end analyticsExport -->

<!-- Compare Toolbar -->
<div id="compareToolbar" class="hidden compare-bar">
    <div class="bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-4">
        <span id="compareCount" class="text-sm font-medium"></span>
        <button onclick="openComparison()" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-1.5 rounded-lg text-sm font-bold transition">Compare</button>
        <button onclick="toggleAllAssessmentCbs(false);document.getElementById('selectAllAssessments').checked=false" class="text-slate-300 hover:text-white text-sm">Cancel</button>
    </div>
</div>

<!-- Comparison Modal -->
<div id="compareModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this)closeComparison()">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col shadow-2xl mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h2 class="text-xl font-bold">Assessment Comparison</h2>
            <button onclick="closeComparison()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div id="compareBody" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
let analyticsData = null;
let assessmentsData = [];
let selectedAssessments = new Set();

async function loadAnalytics() {
    try {
        const [analyticsRes, assessmentsRes] = await Promise.all([
            fetch('/api/dashboard/analytics', { headers: { 'Authorization': `Bearer ${token}` } }),
            fetch('/api/assessments', { headers: { 'Authorization': `Bearer ${token}` } }),
        ]);

        if (analyticsRes.ok) {
            analyticsData = await analyticsRes.json();
            renderKPIs(analyticsData);
            renderTrendArea(analyticsData.monthly_trend, analyticsData.tests_over_time);
            renderScoreDonut(analyticsData.score_distribution);
            renderWeeklyBar(analyticsData.tests_over_time);
            renderFunnelRadial(analyticsData.completion_funnel);
            renderTimeBar(analyticsData.time_distribution);
            renderTopScorers(analyticsData.top_scorers);
            renderDifficulty(analyticsData.question_difficulty);
        }

        if (assessmentsRes.ok) {
            const json = await assessmentsRes.json();
            assessmentsData = (json.data || []).filter(a => !a.is_template);
            renderAssessmentTable(assessmentsData);
        }
    } catch (err) { console.error('Analytics error:', err); }
}

function renderKPIs(d) {
    const kpis = ['Tests', 'AvgScore', 'PassRate', 'AvgTime', 'Candidates', 'Completion'];
    kpis.forEach(k => {
        const skelEl = document.getElementById(`kpi${k}Skel`);
        const valEl = document.getElementById(`kpi${k}`);
        if (skelEl) skelEl.style.display = 'none';
        if (valEl) valEl.classList.remove('hidden');
    });
    document.getElementById('kpiTests').textContent = d.total_tests;
    document.getElementById('kpiAvgScore').textContent = d.avg_score + '%';
    document.getElementById('kpiPassRate').textContent = d.pass_rate + '%';
    document.getElementById('kpiAvgTime').textContent = d.avg_time_minutes > 0 ? d.avg_time_minutes + ' min' : '—';
    document.getElementById('kpiCandidates').textContent = d.total_candidates;
    document.getElementById('kpiCompletion').textContent = d.completion_rate + '%';
}

// --------- ApexCharts ---------

function renderTrendArea(trend, weekly) {
    const el = document.getElementById('trendAreaChart');
    if (!trend.length && !weekly.length) { el.innerHTML = '<p class="text-slate-400 text-center py-12">No data yet</p>'; return; }

    const data = trend.length ? trend : weekly.map(w => ({ date: w.day, tests: w.count, avg_score: 0 }));
    el.innerHTML = '';
    new ApexCharts(el, {
        chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', zoom: { enabled: false } },
        series: [
            { name: 'Tests', data: data.map(d => d.tests) },
            { name: 'Avg Score', data: data.map(d => d.avg_score || 0) },
        ],
        colors: ['#3C50E0', '#80CAEE'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 95, 100] } },
        stroke: { width: 2, curve: 'smooth' },
        xaxis: { categories: data.map(d => { const dt = new Date(d.date); return isNaN(dt) ? d.date : dt.getDate(); }), labels: { style: { colors: '#94a3b8', fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '12px' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', markers: { width: 8, height: 8, radius: 8 } },
        dataLabels: { enabled: false },
        tooltip: { theme: 'light' },
    }).render();
}

function renderScoreDonut(dist) {
    const labels = ['0-20%', '21-40%', '41-60%', '61-80%', '81-100%'];
    const values = Object.values(dist);
    const el = document.getElementById('scoreDonutChart');
    el.innerHTML = '';
    new ApexCharts(el, {
        chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
        series: values,
        labels: labels,
        colors: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981', '#6366F1'],
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '14px', fontWeight: 700, color: '#1e293b', formatter: () => values.reduce((a, b) => a + b, 0) } } } } },
        stroke: { width: 3, colors: ['#fff'] },
        legend: { position: 'bottom', fontSize: '12px', markers: { width: 8, height: 8, radius: 8 } },
        dataLabels: { enabled: false },
        tooltip: { y: { formatter: v => v + ' tests' } },
    }).render();
}

function renderWeeklyBar(data) {
    const el = document.getElementById('weeklyBarChart');
    el.innerHTML = '';
    new ApexCharts(el, {
        chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        series: [{ name: 'Tests', data: data.map(d => d.count) }],
        colors: ['#3C50E0'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
        xaxis: { categories: data.map(d => d.day), labels: { style: { colors: '#94a3b8', fontSize: '12px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '12px' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, xaxis: { lines: { show: false } } },
        dataLabels: { enabled: false },
        tooltip: { theme: 'light' },
    }).render();
}

function renderFunnelRadial(f) {
    const max = Math.max(f.invited, 1);
    const series = [
        Math.round((f.invited / max) * 100),
        Math.round((f.started / max) * 100),
        Math.round((f.completed / max) * 100),
        Math.round((f.passed / max) * 100),
    ];
    const el = document.getElementById('funnelRadialChart');
    el.innerHTML = '';
    new ApexCharts(el, {
        chart: { type: 'radialBar', height: 280, fontFamily: 'Inter, sans-serif' },
        series: series,
        labels: ['Invited', 'Started', 'Completed', 'Passed'],
        colors: ['#3C50E0', '#F59E0B', '#10B981', '#6366F1'],
        plotOptions: { radialBar: { hollow: { size: '30%' }, track: { background: '#f1f5f9' }, dataLabels: { name: { fontSize: '12px', color: '#64748b' }, value: { fontSize: '14px', fontWeight: 700, color: '#1e293b', formatter: (val, opts) => { const idx = opts.config.series.indexOf(parseInt(val)) >= 0 ? opts.config.series.indexOf(parseInt(val)) : 0; const counts = [f.invited, f.started, f.completed, f.passed]; return counts[opts.seriesIndex] || val + '%'; } }, total: { show: true, label: 'Invited', fontSize: '13px', color: '#94a3b8', formatter: () => f.invited } } } },
        stroke: { lineCap: 'round' },
    }).render();
}

function renderTimeBar(dist) {
    const labels = Object.keys(dist);
    const values = Object.values(dist);
    const el = document.getElementById('timeBarChart');
    el.innerHTML = '';
    new ApexCharts(el, {
        chart: { type: 'bar', height: 240, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        series: [{ name: 'Candidates', data: values }],
        colors: ['#7C3AED'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '50%', distributed: true } },
        xaxis: { categories: labels, labels: { style: { colors: '#94a3b8', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '12px' } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { show: false },
        dataLabels: { enabled: false },
        tooltip: { theme: 'light' },
    }).render();
}

function renderTopScorers(scorers) {
    const el = document.getElementById('topScorersList');
    if (!scorers.length) { el.innerHTML = '<p class="text-slate-400 text-xs">No data yet</p>'; return; }
    const rankStyles = [
        { bg: '#FEF3C7', color: '#D97706' },
        { bg: '#E0E7FF', color: '#4F46E5' },
        { bg: '#FEE2E2', color: '#EF4444' },
        { bg: '#F1F5F9', color: '#64748B' },
        { bg: '#F1F5F9', color: '#64748B' },
    ];
    el.innerHTML = scorers.map((s, i) => `
        <div class="leader-row">
            <div class="leader-rank" style="background:${rankStyles[i].bg};color:${rankStyles[i].color}">${i + 1}</div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate text-slate-800">${s.name || s.email}</p>
                <p class="text-[11px] text-slate-400 truncate">${s.time_minutes ? s.time_minutes + 'm' : ''}</p>
            </div>
            <span class="text-sm font-bold" style="color:${s.score >= 70 ? '#10B981' : s.score >= 50 ? '#F59E0B' : '#EF4444'}">${s.score.toFixed(0)}%</span>
        </div>
    `).join('');
}

function renderDifficulty(questions) {
    const el = document.getElementById('difficultyList');
    if (!questions.length) { el.innerHTML = '<p class="text-slate-400 text-xs">Not enough data</p>'; return; }
    el.innerHTML = questions.slice(0, 5).map(q => {
        const c = q.success_rate < 30 ? '#EF4444' : q.success_rate < 60 ? '#F59E0B' : '#10B981';
        return `<div class="diff-row">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-medium text-slate-700 truncate" title="${q.question}">${q.question}</p>
                <div class="diff-bar-track mt-1"><div class="diff-bar-fill" style="width:${q.success_rate}%;background:${c}"></div></div>
            </div>
            <span class="text-xs font-bold whitespace-nowrap" style="color:${c}">${q.success_rate}%</span>
        </div>`;
    }).join('');
}

// --------- Assessment Table ---------

function renderAssessmentTable(assessments) {
    const tbody = document.getElementById('assessmentsList');
    if (!assessments.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-slate-400">No assessments yet</td></tr>';
        return;
    }
    const sc = { draft: '#94A3B8', active: '#10B981', scheduled: '#3B82F6', completed: '#7C3AED', cancelled: '#EF4444' };
    tbody.innerHTML = assessments.map(a => `
        <tr class="assessment-row border-t border-slate-50" onclick="window.location.href='/assessments/${a.id}'">
            <td class="py-3 pl-2" onclick="event.stopPropagation()"><input type="checkbox" class="assess-cb w-4 h-4 accent-indigo-600 rounded" data-id="${a.id}" data-title="${a.title}" onchange="onAssessmentCheck()"></td>
            <td class="py-3 font-semibold text-slate-800">${a.title}</td>
            <td class="py-3"><span class="inline-flex items-center gap-1 text-xs font-medium"><span class="w-1.5 h-1.5 rounded-full" style="background:${sc[a.status] || '#94A3B8'}"></span>${a.status}</span></td>
            <td class="py-3 text-slate-500">${a.questions_count || 0}</td>
            <td class="py-3 text-slate-500">${a.invitees_count || 0}</td>
            <td class="py-3 text-slate-500">${a.completed_count || 0}</td>
            <td class="py-3 font-bold" style="color:${a.avg_score >= 70 ? '#10B981' : a.avg_score >= 50 ? '#F59E0B' : a.avg_score ? '#EF4444' : '#CBD5E1'}">${a.avg_score ? Math.round(a.avg_score) + '%' : '—'}</td>
        </tr>
    `).join('');
}

// --------- Multi-select Comparison ---------

function onAssessmentCheck() {
    selectedAssessments.clear();
    document.querySelectorAll('.assess-cb:checked').forEach(cb => selectedAssessments.add({ id: cb.dataset.id, title: cb.dataset.title }));
    const bar = document.getElementById('compareToolbar');
    if (selectedAssessments.size >= 2) { bar.classList.remove('hidden'); document.getElementById('compareCount').textContent = `${selectedAssessments.size} selected`; }
    else { bar.classList.add('hidden'); }
}
function toggleAllAssessmentCbs(checked) { document.querySelectorAll('.assess-cb').forEach(cb => { cb.checked = checked; }); onAssessmentCheck(); }

async function openComparison() {
    const modal = document.getElementById('compareModal');
    const body = document.getElementById('compareBody');
    modal.classList.remove('hidden');
    body.innerHTML = '<div class="text-center py-12"><p class="text-slate-400">Loading...</p></div>';
    const items = Array.from(selectedAssessments);
    const colors = ['#3C50E0', '#10B981', '#F59E0B', '#EF4444', '#7C3AED', '#EC4899'];
    try {
        const results = await Promise.all(items.map(a => fetch(`/api/assessments/${a.id}/analytics`, { headers: { 'Authorization': `Bearer ${token}` } }).then(r => r.json())));
        const metrics = [
            { label: 'Total Invites', key: s => s.total_invites },
            { label: 'Total Takers', key: s => s.total_takers },
            { label: 'Response Rate', key: s => s.response_rate + '%' },
            { label: 'Pass Rate', key: s => s.pass_rate + '%' },
            { label: 'Avg Score', key: s => s.average_score + '%' },
            { label: 'Avg Time', key: s => s.average_time_minutes + ' min' },
        ];
        let html = `<table class="w-full text-sm"><thead class="border-b"><tr><th class="py-2 text-left text-slate-500">Metric</th>${items.map((a, i) => `<th class="py-2 text-center font-semibold" style="color:${colors[i % colors.length]}">${a.title.length > 25 ? a.title.substring(0, 23) + '…' : a.title}</th>`).join('')}</tr></thead><tbody>${metrics.map(m => `<tr class="border-t border-slate-50"><td class="py-2.5 text-slate-600">${m.label}</td>${results.map(r => `<td class="py-2.5 text-center font-bold text-slate-800">${m.key(r.summary)}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
        html += `<div id="compareBarChart" class="mt-6" style="min-height:250px"></div>`;
        body.innerHTML = html;
        // Render grouped bar chart
        new ApexCharts(document.getElementById('compareBarChart'), {
            chart: { type: 'bar', height: 250, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
            series: items.map((a, i) => ({ name: a.title, data: [results[i].summary.average_score, results[i].summary.pass_rate, results[i].summary.response_rate] })),
            colors: colors.slice(0, items.length),
            plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
            xaxis: { categories: ['Avg Score %', 'Pass Rate %', 'Response Rate %'], labels: { style: { fontSize: '12px', colors: '#94a3b8' } } },
            yaxis: { max: 100, labels: { style: { fontSize: '12px', colors: '#94a3b8' } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            legend: { position: 'top', fontSize: '11px', markers: { width: 8, height: 8, radius: 8 } },
            dataLabels: { enabled: false },
            tooltip: { theme: 'light' },
        }).render();
    } catch (err) { body.innerHTML = `<p class="text-red-500 text-center py-8">Failed to load data</p>`; }
}
function closeComparison() { document.getElementById('compareModal').classList.add('hidden'); }

// --------- Exports ---------

async function exportPdf() {
    const el = document.getElementById('analyticsExport');
    const btns = document.querySelectorAll('.export-btns');
    btns.forEach(b => b.style.display = 'none');

    // Convert ApexCharts SVGs to canvas images for html2pdf
    const svgEls = el.querySelectorAll('.apexcharts-svg');
    const originals = [];
    for (const svg of svgEls) {
        const parent = svg.parentElement;
        const canvas = document.createElement('canvas');
        const svgData = new XMLSerializer().serializeToString(svg);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        const img = new Image();
        await new Promise(resolve => {
            img.onload = resolve;
            img.onerror = resolve;
            img.src = url;
        });
        canvas.width = img.width * 2;
        canvas.height = img.height * 2;
        const ctx = canvas.getContext('2d');
        ctx.scale(2, 2);
        ctx.drawImage(img, 0, 0);
        URL.revokeObjectURL(url);
        const imgEl = document.createElement('img');
        imgEl.src = canvas.toDataURL('image/png');
        imgEl.style.width = svg.getAttribute('width') + 'px';
        imgEl.style.maxWidth = '100%';
        originals.push({ parent, svg, imgEl });
        parent.replaceChild(imgEl, svg);
    }

    try {
        await html2pdf().set({
            margin: [8, 8, 8, 8],
            filename: `quizly-analytics-${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 1.5, useCORS: true, scrollY: -window.scrollY, windowWidth: el.scrollWidth },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' },
            pagebreak: { mode: ['avoid-all', 'css'] },
        }).from(el).save();
    } catch (err) {
        console.error('PDF export failed:', err);
        toastError('PDF export failed. Please try again.');
    }

    // Restore SVGs
    originals.forEach(({ parent, svg, imgEl }) => parent.replaceChild(svg, imgEl));
    btns.forEach(b => b.style.display = '');
}

function exportAnalyticsCsv() {
    if (!assessmentsData.length) return;
    const headers = ['Assessment', 'Status', 'Questions', 'Candidates', 'Completed', 'Avg Score (%)'];
    const rows = assessmentsData.map(a => [a.title, a.status, a.questions_count||0, a.invitees_count||0, a.completed_count||0, a.avg_score ? Math.round(a.avg_score) : '-']);
    if (analyticsData) { rows.push([]); rows.push(['Summary']); rows.push(['Total Tests', analyticsData.total_tests]); rows.push(['Average Score', analyticsData.avg_score + '%']); rows.push(['Pass Rate', analyticsData.pass_rate + '%']); }
    const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v}"`).join(','))].join('\n');
    const blob = new Blob([csv], { type: 'text/csv' }); const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = `quizly-analytics-${new Date().toISOString().slice(0,10)}.csv`;
    a.click(); URL.revokeObjectURL(url);
}

loadAnalytics();
</script>
@endsection
