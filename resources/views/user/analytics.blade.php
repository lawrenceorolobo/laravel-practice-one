@extends('layouts.user')
@section('title', 'Analytics | Quizly')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold">Analytics</h2>
    <p class="text-slate-500">Performance insights across assessments</p>
</div>

<!-- Overview -->
<div class="grid md:grid-cols-4 gap-6 mb-8">
    <div class="glass rounded-2xl p-6 hover-lift">
        <p class="text-slate-500 text-sm">Total Tests Taken</p>
        <p class="text-3xl font-bold mt-2" id="totalTests">
            <span class="skeleton skeleton-glass inline-block h-8 w-16 rounded"></span>
        </p>
    </div>
    <div class="glass rounded-2xl p-6 hover-lift">
        <p class="text-slate-500 text-sm">Average Score</p>
        <p class="text-3xl font-bold mt-2 text-indigo-600" id="avgScore">
            <span class="skeleton skeleton-glass inline-block h-8 w-16 rounded"></span>
        </p>
    </div>
    <div class="glass rounded-2xl p-6 hover-lift">
        <p class="text-slate-500 text-sm">Pass Rate</p>
        <p class="text-3xl font-bold mt-2 text-emerald-600" id="passRate">
            <span class="skeleton skeleton-glass inline-block h-8 w-16 rounded"></span>
        </p>
    </div>
    <div class="glass rounded-2xl p-6 hover-lift">
        <p class="text-slate-500 text-sm">Avg Completion Time</p>
        <p class="text-3xl font-bold mt-2 text-purple-600" id="avgTime">
            <span class="skeleton skeleton-glass inline-block h-8 w-16 rounded"></span>
        </p>
    </div>
</div>

<!-- Charts -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="font-bold text-lg mb-4">Score Distribution</h3>
        <div id="scoreChart" class="h-64 flex items-end justify-between gap-2 px-4">
            <!-- Loading skeleton -->
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:30%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:45%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:60%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:75%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:90%"></div>
        </div>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="font-bold text-lg mb-4">Tests Over Time</h3>
        <div id="timeChart" class="h-64 flex items-end justify-between gap-2 px-4">
            <!-- Loading skeleton -->
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:40%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:55%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:35%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:70%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:50%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:30%"></div>
            <div class="flex-1 skeleton skeleton-glass rounded-t" style="height:25%"></div>
        </div>
    </div>
</div>

<!-- Top Assessments -->
<div class="glass rounded-2xl p-6">
    <h3 class="font-bold text-lg mb-4">Assessment Performance</h3>
    <table class="w-full">
        <thead class="border-b">
            <tr class="text-left text-sm text-slate-500">
                <th class="pb-3">Assessment</th>
                <th class="pb-3">Candidates</th>
                <th class="pb-3">Completed</th>
                <th class="pb-3">Avg Score</th>
            </tr>
        </thead>
        <tbody id="assessmentsList"></tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
async function loadAnalytics() {
    try {
        // Fetch chart data from new analytics endpoint
        const analyticsRes = await fetch('/api/dashboard/analytics', { headers: { 'Authorization': `Bearer ${token}` } });
        if (analyticsRes.ok) {
            const data = await analyticsRes.json();
            
            // Update stats
            document.getElementById('totalTests').textContent = data.total_tests;
            document.getElementById('avgScore').textContent = data.avg_score + '%';
            document.getElementById('passRate').textContent = data.pass_rate + '%';
            document.getElementById('avgTime').textContent = data.avg_time_minutes > 0 ? data.avg_time_minutes + ' min' : '-';
            
            // Render Score Distribution Chart
            renderScoreChart(data.score_distribution);
            
            // Render Tests Over Time Chart
            renderTimeChart(data.tests_over_time);
        } else {
            showNoData();
        }
        
        // Fetch assessments for table
        const res = await fetch('/api/assessments', { headers: { 'Authorization': `Bearer ${token}` } });
        if (res.ok) {
            const { data } = await res.json();
            renderAssessmentTable(data || []);
        }
    } catch (err) { 
        console.error('Analytics error:', err);
        showNoData();
    }
}

function showNoData() {
    document.getElementById('totalTests').textContent = '0';
    document.getElementById('avgScore').textContent = '-';
    document.getElementById('passRate').textContent = '-';
    document.getElementById('avgTime').textContent = '-';
    document.getElementById('scoreChart').innerHTML = '<p class="text-slate-500 text-center w-full">No data available</p>';
    document.getElementById('timeChart').innerHTML = '<p class="text-slate-500 text-center w-full">No data available</p>';
}

function renderScoreChart(distribution) {
    const ranges = ['0-20', '21-40', '41-60', '61-80', '81-100'];
    const colors = ['bg-indigo-200', 'bg-indigo-300', 'bg-indigo-400', 'bg-indigo-500', 'bg-indigo-600'];
    const maxVal = Math.max(...Object.values(distribution), 1);
    
    const chart = document.getElementById('scoreChart');
    chart.innerHTML = ranges.map((range, i) => {
        const count = distribution[range] || 0;
        const height = count > 0 ? Math.max((count / maxVal) * 100, 10) : 5;
        return `
            <div class="flex-1 flex flex-col items-center justify-end h-full">
                <div class="${colors[i]} rounded-t w-full transition-all duration-500" style="height:${height}%">
                    <span class="text-xs text-center block mt-1 font-medium">${count > 0 ? count : ''}</span>
                </div>
                <p class="text-xs text-center mt-2 text-slate-500">${range}</p>
            </div>
        `;
    }).join('');
}

function renderTimeChart(testsOverTime) {
    const maxVal = Math.max(...testsOverTime.map(t => t.count), 1);
    
    const chart = document.getElementById('timeChart');
    chart.innerHTML = testsOverTime.map((item, i) => {
        const height = item.count > 0 ? Math.max((item.count / maxVal) * 100, 10) : 5;
        const intensity = Math.min(300 + (i * 50), 600);
        return `
            <div class="flex-1 flex flex-col items-center justify-end h-full">
                <div class="bg-emerald-${intensity < 400 ? '300' : intensity < 500 ? '400' : '500'} rounded-t w-full transition-all duration-500" style="height:${height}%">
                    <span class="text-xs text-center block mt-1 font-medium">${item.count > 0 ? item.count : ''}</span>
                </div>
                <p class="text-xs text-center mt-2 text-slate-500">${item.day}</p>
            </div>
        `;
    }).join('');
}

function renderAssessmentTable(assessments) {
    const tbody = document.getElementById('assessmentsList');
    
    if (!assessments.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-8 text-center text-slate-500">No assessments yet</td></tr>';
        return;
    }
    
    tbody.innerHTML = assessments.map(a => `
        <tr class="border-t">
            <td class="py-4 font-medium">${a.title}</td>
            <td class="py-4">${a.invitees_count || 0}</td>
            <td class="py-4">${a.completed_count || 0}</td>
            <td class="py-4">${a.avg_score ? Math.round(a.avg_score) + '%' : '-'}</td>
        </tr>
    `).join('');
}

loadAnalytics();
</script>
@endsection
