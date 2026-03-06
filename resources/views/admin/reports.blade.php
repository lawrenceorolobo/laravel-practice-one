@extends('layouts.admin')

@section('title', 'Reports | Admin')
@section('page-title', 'Reports & Analytics')

@section('header-actions')
<button onclick="exportReport()" id="exportBtn" class="btn-primary flex items-center gap-2">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <span class="hidden sm:inline">Export</span>
</button>
@endsection

@section('content')
<!-- Date Range Filter -->
<div class="flex flex-col sm:flex-row items-end gap-3 mb-4">
    <div class="flex gap-3 w-full sm:w-auto">
        <div class="flex-1 sm:flex-initial">
            <label class="block text-[10px] font-medium mb-1" style="color:var(--text-muted);">From</label>
            <input type="date" id="dateFrom" class="w-full">
        </div>
        <div class="flex-1 sm:flex-initial">
            <label class="block text-[10px] font-medium mb-1" style="color:var(--text-muted);">To</label>
            <input type="date" id="dateTo" class="w-full">
        </div>
    </div>
    <button onclick="loadReports()" class="btn-primary w-full sm:w-auto">Apply</button>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4">
    <div class="panel p-4" style="border-top:3px solid #10b981;">
        <p class="text-[11px] font-medium mb-2" style="color:var(--text-secondary);">Total Revenue</p>
        <div id="totalRevenueWrap"><div class="skel h-7 w-20 mb-1.5"></div><div class="skel h-3 w-24"></div></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #6366f1;">
        <p class="text-[11px] font-medium mb-2" style="color:var(--text-secondary);">New Users</p>
        <div id="newUsersWrap"><div class="skel h-7 w-14 mb-1.5"></div><div class="skel h-3 w-20"></div></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #a855f7;">
        <p class="text-[11px] font-medium mb-2" style="color:var(--text-secondary);">Assessments</p>
        <div id="assessmentsCreatedWrap"><div class="skel h-7 w-10 mb-1.5"></div><div class="skel h-3 w-16"></div></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #f59e0b;">
        <p class="text-[11px] font-medium mb-2" style="color:var(--text-secondary);">Tests Done</p>
        <div id="testsCompletedWrap"><div class="skel h-7 w-10 mb-1.5"></div><div class="skel h-3 w-20"></div></div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-3 lg:gap-4 mb-4">
    <div class="panel p-4 lg:p-5">
        <h3 class="text-[13px] font-semibold mb-0.5" style="color:var(--text-primary);">Revenue Trend</h3>
        <p class="text-[11px] mb-3" style="color:var(--text-muted);">Monthly revenue over selected period</p>
        <div id="revenueChart" style="height:260px;min-height:260px;"><div class="skel w-full h-full rounded-lg"></div></div>
    </div>
    <div class="panel p-4 lg:p-5">
        <h3 class="text-[13px] font-semibold mb-0.5" style="color:var(--text-primary);">User Growth</h3>
        <p class="text-[11px] mb-3" style="color:var(--text-muted);">New registrations by month</p>
        <div id="userChart" style="height:260px;min-height:260px;"><div class="skel w-full h-full rounded-lg"></div></div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="panel">
    <div class="px-4 lg:px-5 py-3 flex items-center justify-between" style="border-bottom:1px solid var(--border);">
        <div><h3 class="text-[13px] font-semibold" style="color:var(--text-primary);">Recent Transactions</h3><p class="text-[10px] mt-0.5" style="color:var(--text-muted);">Select rows to export</p></div>
        <div class="flex items-center gap-2">
            <button id="exportSelectedBtn" onclick="exportSelectedCSV()" class="btn-primary text-[11px] px-3 py-1.5 hidden items-center gap-1.5" style="font-size:11px;">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Selected
            </button>
            <span class="text-[11px] font-medium" style="color:var(--text-muted);" id="transactionCount">Loading...</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="border-bottom:1px solid var(--border);background:var(--bg-alt);">
                    <th class="px-3 py-2.5 w-8"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="rounded" style="accent-color:#6366f1;"></th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Ref</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">User</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden md:table-cell" style="color:var(--text-muted);">Plan</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Amount</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden lg:table-cell" style="color:var(--text-muted);">Status</th>
                    <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">Date</th>
                </tr>
            </thead>
            <tbody id="transactionsTable">
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-20"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="flex items-center gap-2"><div class="skel skel-circle w-6 h-6"></div><div class="skel h-3.5 w-20"></div></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-16"></div></td></tr>
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="flex items-center gap-2"><div class="skel skel-circle w-6 h-6"></div><div class="skel h-3.5 w-24"></div></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel skel-pill h-5 w-16"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel skel-pill h-5 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-20"></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Transaction Detail Drawer -->
<div id="txnDrawer" class="detail-drawer">
    <div class="drawer-overlay" onclick="closeTxnDrawer()"></div>
    <div class="drawer-panel">
        <div class="sticky top-0 z-10 px-5 py-4 flex items-center justify-between" style="background:var(--surface);border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);">Transaction Details</h3>
            <button onclick="closeTxnDrawer()" class="p-1.5 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div id="txnDrawerBody" class="p-5"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    const token = localStorage.getItem('adminToken');
    let reportData = null, revenueChartInstance = null, userChartInstance = null;
    const chartFg = () => themeColor('#64748b','#484f58');
    const chartGrid = () => themeColor('#e2e8f0','#21262d');
    const chartTooltipTheme = () => isDarkMode() ? 'dark' : 'light';

    const today = new Date();
    const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
    document.getElementById('dateFrom').value = monthAgo.toISOString().split('T')[0];

    async function loadReports() {
        const from = document.getElementById('dateFrom').value, to = document.getElementById('dateTo').value;
        try {
            const res = await fetch(`/api/admin/reports?from=${from}&to=${to}`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed');
            reportData = await res.json();
            updateStatCard('totalRevenueWrap', '₦'+(reportData.stats.total_revenue||0).toLocaleString(), reportData.stats.revenue_change);
            updateStatCard('newUsersWrap', (reportData.stats.new_users||0).toLocaleString(), reportData.stats.users_change);
            updateStatCard('assessmentsCreatedWrap', (reportData.stats.assessments_created||0).toLocaleString(), reportData.stats.assessments_change);
            updateStatCard('testsCompletedWrap', (reportData.stats.tests_completed||0).toLocaleString(), reportData.stats.tests_change);
            renderRevenueChart(reportData.charts.revenue_trend);
            renderUserChart(reportData.charts.user_growth);
            renderTransactions(reportData.transactions);
        } catch (err) {
            ['totalRevenueWrap','newUsersWrap','assessmentsCreatedWrap','testsCompletedWrap'].forEach(id => {
                document.getElementById(id).innerHTML = `<p class="text-xl font-bold" style="color:var(--text-muted);">—</p><p class="text-[11px] mt-1" style="color:var(--text-muted);">Failed to load</p>`;
            });
        }
    }

    function updateStatCard(id, value, change) {
        const arrow = change > 0 ? '↑' : change < 0 ? '↓' : '';
        const cc = change > 0 ? '#10b981' : change < 0 ? '#ef4444' : 'var(--text-muted)';
        const ct = change > 0 ? `${arrow} +${change}%` : change < 0 ? `${arrow} ${change}%` : '0%';
        document.getElementById(id).innerHTML = `<p class="text-xl font-bold" style="color:var(--text-primary);">${value}</p><p class="text-[11px] font-medium mt-1" style="color:${cc}">${ct} vs last period</p>`;
    }

    function renderRevenueChart(data) {
        if (revenueChartInstance) revenueChartInstance.destroy();
        const el = document.getElementById('revenueChart');
        if (!data?.length) { el.innerHTML = '<div class="flex items-center justify-center h-full text-[12px]" style="color:var(--text-muted);">No revenue data</div>'; return; }
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        revenueChartInstance = new ApexCharts(el, {
            chart: { type:'area', height:260, toolbar:{show:false}, fontFamily:'Inter', background:'transparent' },
            series: [{ name:'Revenue', data:data.map(d=>parseFloat(d.revenue)||0) }],
            xaxis: { categories:data.map(d=>{const[y,m]=d.month.split('-');return months[parseInt(m)-1]}), labels:{style:{colors:chartFg(),fontSize:'10px'}}, axisBorder:{show:false}, axisTicks:{show:false} },
            yaxis: { labels:{style:{colors:chartFg(),fontSize:'10px'}, formatter:v=>'₦'+(v/1000).toFixed(0)+'k'} },
            stroke: { curve:'smooth', width:2.5 }, fill: { type:'gradient', gradient:{shadeIntensity:1,opacityFrom:0.35,opacityTo:0.02,stops:[0,100]} },
            colors: ['#6366f1'], grid: { borderColor:chartGrid(), strokeDashArray:4 },
            tooltip: { theme:chartTooltipTheme(), y:{formatter:v=>'₦'+v.toLocaleString()} }, dataLabels:{enabled:false}
        });
        revenueChartInstance.render();
    }

    function renderUserChart(data) {
        if (userChartInstance) userChartInstance.destroy();
        const el = document.getElementById('userChart');
        if (!data?.length) { el.innerHTML = '<div class="flex items-center justify-center h-full text-[12px]" style="color:var(--text-muted);">No user data</div>'; return; }
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        userChartInstance = new ApexCharts(el, {
            chart: { type:'bar', height:260, toolbar:{show:false}, fontFamily:'Inter', background:'transparent' },
            series: [{ name:'New Users', data:data.map(d=>parseInt(d.users)||0) }],
            xaxis: { categories:data.map(d=>{const[y,m]=d.month.split('-');return months[parseInt(m)-1]}), labels:{style:{colors:chartFg(),fontSize:'10px'}}, axisBorder:{show:false}, axisTicks:{show:false} },
            yaxis: { labels:{style:{colors:chartFg(),fontSize:'10px'}} },
            colors: ['#10b981'], plotOptions: { bar:{borderRadius:6,columnWidth:'50%'} },
            grid: { borderColor:chartGrid(), strokeDashArray:4 }, tooltip:{theme:chartTooltipTheme()}, dataLabels:{enabled:false}
        });
        userChartInstance.render();
    }

    function renderTransactions(transactions) {
        const tbody = document.getElementById('transactionsTable'), countEl = document.getElementById('transactionCount');
        if (!transactions?.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-5 py-10 text-center text-[12px]" style="color:var(--text-muted);">No transactions found</td></tr>';
            countEl.textContent = '0'; return;
        }
        countEl.textContent = `${transactions.length} txns`;
        const avatarColors = ['#6366f1','#10b981','#f59e0b','#a855f7','#ef4444','#06b6d4'];
        const avatarGrads = ['#818cf8','#34d399','#fbbf24','#c084fc','#f87171','#22d3ee'];
        tbody.innerHTML = transactions.map((txn, i) => {
            const ci = i % 6;
            const initial = (txn.user_name||'U').charAt(0).toUpperCase();
            return `<tr class="tr-click" style="border-bottom:1px solid var(--border-subtle);">
                <td class="px-3 py-2.5" onclick="event.stopPropagation()"><input type="checkbox" class="txn-check rounded" data-idx="${i}" onchange="updateExportBtn()" style="accent-color:#6366f1;"></td>
                <td class="px-4 lg:px-5 py-2.5 font-mono text-[11px] font-medium" style="color:var(--text-primary);" onclick='openTxnDrawer(${JSON.stringify(txn)})'>#${txn.reference}</td>
                <td class="px-4 lg:px-5 py-2.5 hidden sm:table-cell" onclick='openTxnDrawer(${JSON.stringify(txn)})'><div class="flex items-center gap-2"><div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-[9px] font-bold" style="background:linear-gradient(135deg,${avatarColors[ci]},${avatarGrads[ci]});">${initial}</div><span class="text-[12px] truncate max-w-[100px]" style="color:var(--text-secondary);">${txn.user_name}</span></div></td>
                <td class="px-4 lg:px-5 py-2.5 hidden md:table-cell" onclick='openTxnDrawer(${JSON.stringify(txn)})'><span class="badge badge-neutral">${txn.plan}</span></td>
                <td class="px-4 lg:px-5 py-2.5 font-semibold text-[12px]" style="color:var(--text-primary);" onclick='openTxnDrawer(${JSON.stringify(txn)})'>₦${txn.amount.toLocaleString()}</td>
                <td class="px-4 lg:px-5 py-2.5 hidden lg:table-cell" onclick='openTxnDrawer(${JSON.stringify(txn)})'><span class="badge ${txn.status==='success'?'badge-success':txn.status==='failed'?'badge-danger':'badge-warning'}"><span class="w-1.5 h-1.5 rounded-full" style="background:${txn.status==='success'?'#10b981':txn.status==='failed'?'#ef4444':'#f59e0b'}"></span>${txn.status}</span></td>
                <td class="px-4 lg:px-5 py-2.5 text-[11px] hidden sm:table-cell" style="color:var(--text-muted);" onclick='openTxnDrawer(${JSON.stringify(txn)})'>${txn.date}</td>
            </tr>`;
        }).join('');
    }

    function openTxnDrawer(txn) {
        const drawer = document.getElementById('txnDrawer'), body = document.getElementById('txnDrawerBody');
        body.innerHTML = `
            <div class="text-center mb-5">
                <span class="badge ${txn.status==='success'?'badge-success':txn.status==='failed'?'badge-danger':'badge-warning'} mb-2 inline-flex">${txn.status}</span>
                <p class="text-2xl font-bold mt-2" style="color:var(--text-primary);">₦${txn.amount.toLocaleString()}</p>
                <p class="text-[12px] mt-1" style="color:var(--text-muted);">${txn.plan} subscription</p>
            </div>
            <div class="space-y-0 divide-y rounded-xl overflow-hidden mb-5" style="background:var(--bg);border:1px solid var(--border);">
                <div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Transaction ID</span><span class="text-[12px] font-mono font-medium" style="color:var(--text-primary);">#${txn.reference}</span></div>
                <div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Customer</span><span class="text-[12px] font-medium" style="color:var(--text-primary);">${txn.user_name}</span></div>
                ${txn.user_email?`<div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Email</span><span class="text-[12px]" style="color:var(--text-secondary);">${txn.user_email}</span></div>`:''}
                <div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Plan</span><span class="text-[12px]" style="color:var(--text-primary);">${txn.plan}</span></div>
                <div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Amount</span><span class="text-[12px] font-bold" style="color:#10b981;">₦${txn.amount.toLocaleString()}</span></div>
                <div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Date</span><span class="text-[12px]" style="color:var(--text-primary);">${txn.date}</span></div>
                ${txn.channel?`<div class="flex justify-between items-center px-4 py-2.5" style="border-color:var(--border);"><span class="text-[11px]" style="color:var(--text-muted);">Channel</span><span class="text-[12px] capitalize" style="color:var(--text-primary);">${txn.channel}</span></div>`:''}
            </div>
            <h4 class="text-[12px] font-semibold mb-2" style="color:var(--text-secondary);">Payment Timeline</h4>
            <div class="space-y-2.5">
                <div class="flex items-start gap-2.5"><div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:#6366f1;"></div><div><p class="text-[12px] font-medium" style="color:var(--text-primary);">Payment initiated</p><p class="text-[10px]" style="color:var(--text-muted);">${txn.date}</p></div></div>
                ${txn.status==='success'?`<div class="flex items-start gap-2.5"><div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:#10b981;"></div><div><p class="text-[12px] font-medium" style="color:var(--text-primary);">Payment confirmed</p><p class="text-[10px]" style="color:var(--text-muted);">${txn.date}</p></div></div><div class="flex items-start gap-2.5"><div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:#10b981;"></div><div><p class="text-[12px] font-medium" style="color:var(--text-primary);">Subscription activated</p><p class="text-[10px]" style="color:var(--text-muted);">${txn.plan} plan</p></div></div>`:''}
                ${txn.status==='pending'?`<div class="flex items-start gap-2.5"><div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 animate-pulse" style="background:#f59e0b;"></div><div><p class="text-[12px] font-medium" style="color:var(--text-primary);">Awaiting confirmation</p><p class="text-[10px]" style="color:var(--text-muted);">Processing...</p></div></div>`:''}
                ${txn.status==='failed'?`<div class="flex items-start gap-2.5"><div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" style="background:#ef4444;"></div><div><p class="text-[12px] font-medium" style="color:var(--text-primary);">Payment failed</p><p class="text-[10px]" style="color:var(--text-muted);">Transaction declined</p></div></div>`:''}
            </div>`;
        drawer.classList.add('open');
        requestAnimationFrame(() => drawer.querySelector('.drawer-panel').style.transform = 'translateX(0)');
    }

    function closeTxnDrawer() { document.getElementById('txnDrawer').classList.remove('open'); }

    async function exportReport() {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        try {
            const from = document.getElementById('dateFrom').value, to = document.getElementById('dateTo').value;
            const res = await fetch(`/api/admin/reports/export-pdf?from=${from}&to=${to}`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            const container = document.createElement('div');
            container.innerHTML = data.html;
            document.body.appendChild(container);
            await html2pdf().set({ margin:10, filename:data.filename, image:{type:'jpeg',quality:0.98}, html2canvas:{scale:2}, jsPDF:{unit:'mm',format:'a4',orientation:'portrait'} }).from(container).save();
            document.body.removeChild(container);
        } catch (err) { toastError('Failed to export report'); }
        finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span class="hidden sm:inline ml-2">Export</span>';
        }
    }

    // Re-render charts on theme change
    window.addEventListener('themeChanged', () => {
        if (reportData) {
            renderRevenueChart(reportData.charts.revenue_trend);
            renderUserChart(reportData.charts.user_growth);
        }
    });

    function toggleSelectAll(el) {
        document.querySelectorAll('.txn-check').forEach(cb => cb.checked = el.checked);
        updateExportBtn();
    }

    function updateExportBtn() {
        const checked = document.querySelectorAll('.txn-check:checked').length;
        const btn = document.getElementById('exportSelectedBtn');
        if (checked > 0) {
            btn.classList.remove('hidden');
            btn.classList.add('flex');
            btn.querySelector('span') || (btn.innerHTML = btn.innerHTML.replace('Export Selected', `Export Selected (${checked})`));
            btn.textContent = '';
            btn.innerHTML = `<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> Export Selected (${checked})`;
        } else {
            btn.classList.add('hidden');
            btn.classList.remove('flex');
        }
    }

    function exportSelectedCSV() {
        const checks = document.querySelectorAll('.txn-check:checked');
        if (!checks.length || !reportData?.transactions) return;

        const indices = Array.from(checks).map(c => parseInt(c.dataset.idx));
        const selected = indices.map(i => reportData.transactions[i]).filter(Boolean);

        const header = 'Reference,User,Email,Plan,Amount,Status,Date';
        const rows = selected.map(t =>
            `"${t.reference}","${t.user_name}","${t.user_email||''}","${t.plan}",${t.amount},"${t.status}","${t.date}"`
        );
        const csv = [header, ...rows].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `transactions_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);
        toastSuccess('Exported ' + selected.length + ' transactions');
    }

    loadReports();
</script>
@endsection
