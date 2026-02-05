@extends('layouts.admin')

@section('title', 'Reports | Admin')
@section('page-title', 'Reports & Analytics')

@section('header-actions')
<button onclick="exportReport()" id="exportBtn" class="px-3 lg:px-4 py-2 bg-emerald-600 hover:bg-emerald-700 rounded-lg font-medium text-xs lg:text-sm transition-colors flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span class="hidden sm:inline">Export Report</span>
</button>
@endsection

@section('content')
<!-- Date Range Filter -->
<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:gap-4 mb-6 lg:mb-8">
    <div class="flex gap-3 w-full sm:w-auto">
        <div class="flex-1 sm:flex-initial">
            <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">From</label>
            <input type="date" id="dateFrom" class="w-full px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex-1 sm:flex-initial">
            <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">To</label>
            <input type="date" id="dateTo" class="w-full px-3 lg:px-4 py-2 bg-slate-800 border border-slate-700 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>
    <div class="self-end w-full sm:w-auto">
        <button onclick="loadReports()" class="w-full sm:w-auto px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-sm transition-colors">Apply</button>
    </div>
</div>

<!-- Stats Overview with Skeleton -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-8">
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Total Revenue</p>
        <div id="totalRevenueWrap">
            <div class="skeleton skeleton-glass h-8 w-24 rounded-lg mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-32 rounded"></div>
        </div>
    </div>
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">New Users</p>
        <div id="newUsersWrap">
            <div class="skeleton skeleton-glass h-8 w-16 rounded-lg mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-28 rounded"></div>
        </div>
    </div>
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Assessments Created</p>
        <div id="assessmentsCreatedWrap">
            <div class="skeleton skeleton-glass h-8 w-12 rounded-lg mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-30 rounded"></div>
        </div>
    </div>
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <p class="text-xs lg:text-sm text-slate-400 mb-1">Tests Completed</p>
        <div id="testsCompletedWrap">
            <div class="skeleton skeleton-glass h-8 w-12 rounded-lg mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-28 rounded"></div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid lg:grid-cols-2 gap-4 lg:gap-8 mb-6 lg:mb-8">
    <!-- Revenue Chart -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <h3 class="text-base lg:text-lg font-bold mb-4">Revenue Trend</h3>
        <div class="h-48 lg:h-64 flex items-end gap-1 lg:gap-2" id="revenueChart">
            <!-- Skeleton Chart -->
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 40%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 55%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 45%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 70%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 60%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 85%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 75%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 90%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 80%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 65%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 88%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 100%"></div>
        </div>
        <div class="flex justify-between mt-4 text-[10px] lg:text-xs text-slate-500" id="revenueLabels"></div>
    </div>

    <!-- User Growth Chart -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 p-4 lg:p-6">
        <h3 class="text-base lg:text-lg font-bold mb-4">User Growth</h3>
        <div class="h-48 lg:h-64 flex items-end gap-1 lg:gap-2" id="userChart">
            <!-- Skeleton Chart -->
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 30%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 35%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 40%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 50%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 55%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 60%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 70%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 75%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 82%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 88%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 92%"></div>
            <div class="skeleton skeleton-glass flex-1 rounded-t" style="height: 100%"></div>
        </div>
        <div class="flex justify-between mt-4 text-[10px] lg:text-xs text-slate-500" id="userLabels"></div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-slate-800 rounded-xl border border-slate-700">
    <div class="p-4 lg:p-6 border-b border-slate-700 flex items-center justify-between">
        <h3 class="text-base lg:text-lg font-bold">Recent Transactions</h3>
        <span class="text-xs lg:text-sm text-slate-400" id="transactionCount">Loading...</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-700">
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400">Transaction ID</th>
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400 hidden sm:table-cell">User</th>
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400 hidden md:table-cell">Plan</th>
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400">Amount</th>
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400 hidden lg:table-cell">Status</th>
                    <th class="text-left px-4 lg:px-6 py-3 text-xs font-medium text-slate-400 hidden sm:table-cell">Date</th>
                </tr>
            </thead>
            <tbody id="transactionsTable">
                <!-- Skeleton rows -->
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-32 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-20 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-20 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-6 w-20 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                </tr>
                <tr class="border-b border-slate-700/50">
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-28 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-16 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-16 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-6 w-16 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-20 rounded"></div></td>
                </tr>
                <tr>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-20 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-36 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden md:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><div class="skeleton skeleton-glass h-6 w-20 rounded-full"></div></td>
                    <td class="px-4 lg:px-6 py-4 hidden sm:table-cell"><div class="skeleton skeleton-glass h-5 w-24 rounded"></div></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- html2pdf for PDF generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    const token = localStorage.getItem('adminToken');
    let reportData = null;

    // Set default date range (last month)
    const today = new Date();
    const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    document.getElementById('dateTo').value = today.toISOString().split('T')[0];
    document.getElementById('dateFrom').value = monthAgo.toISOString().split('T')[0];

    async function loadReports() {
        const from = document.getElementById('dateFrom').value;
        const to = document.getElementById('dateTo').value;
        
        try {
            const res = await fetch(`/api/admin/reports?from=${from}&to=${to}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`, 
                    'Accept': 'application/json' 
                }
            });
            
            if (!res.ok) throw new Error('Failed to load reports');
            
            reportData = await res.json();
            
            // Update stats with change indicators
            updateStatCard('totalRevenueWrap', '₦' + (reportData.stats.total_revenue || 0).toLocaleString(), reportData.stats.revenue_change, 'text-emerald-400');
            updateStatCard('newUsersWrap', (reportData.stats.new_users || 0).toLocaleString(), reportData.stats.users_change, 'text-white');
            updateStatCard('assessmentsCreatedWrap', (reportData.stats.assessments_created || 0).toLocaleString(), reportData.stats.assessments_change, 'text-purple-400');
            updateStatCard('testsCompletedWrap', (reportData.stats.tests_completed || 0).toLocaleString(), reportData.stats.tests_change, 'text-amber-400');
            
            // Render charts
            renderRevenueChart(reportData.charts.revenue_trend);
            renderUserChart(reportData.charts.user_growth);
            
            // Render transactions
            renderTransactions(reportData.transactions);
            
        } catch (err) {
            console.error('Failed to load reports:', err);
            // Show error state
            ['totalRevenueWrap', 'newUsersWrap', 'assessmentsCreatedWrap', 'testsCompletedWrap'].forEach(id => {
                document.getElementById(id).innerHTML = `
                    <p class="text-xl lg:text-3xl font-bold text-slate-500">--</p>
                    <p class="text-xs mt-2 text-slate-500">Failed to load</p>
                `;
            });
        }
    }

    function updateStatCard(elementId, value, change, colorClass) {
        const changeClass = change > 0 ? 'text-emerald-400' : change < 0 ? 'text-red-400' : 'text-slate-500';
        const changeText = change > 0 ? `+${change}%` : change < 0 ? `${change}%` : '0%';
        
        document.getElementById(elementId).innerHTML = `
            <p class="text-xl lg:text-3xl font-bold ${colorClass}">${value}</p>
            <p class="text-xs mt-2 ${changeClass}">${changeText} from last period</p>
        `;
    }

    function renderRevenueChart(data) {
        const container = document.getElementById('revenueChart');
        const labelsContainer = document.getElementById('revenueLabels');
        
        if (!data || data.length === 0) {
            container.innerHTML = '<div class="text-center w-full text-slate-500 text-sm">No revenue data</div>';
            labelsContainer.innerHTML = '';
            return;
        }
        
        const maxRevenue = Math.max(...data.map(d => parseFloat(d.revenue) || 0));
        
        container.innerHTML = data.map(d => {
            const height = maxRevenue > 0 ? (parseFloat(d.revenue) / maxRevenue * 100) : 0;
            return `<div class="flex-1 bg-indigo-600 hover:bg-indigo-500 rounded-t transition-colors cursor-pointer relative group" style="height: ${Math.max(height, 2)}%">
                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-10">
                    ₦${parseFloat(d.revenue).toLocaleString()}
                </div>
            </div>`;
        }).join('');
        
        // Show every 2nd or 3rd label on mobile
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        labelsContainer.innerHTML = data.map((d, i) => {
            const [year, month] = d.month.split('-');
            const showLabel = window.innerWidth > 768 || i % 2 === 0;
            return `<span class="${showLabel ? '' : 'hidden sm:inline'}">${monthNames[parseInt(month) - 1]}</span>`;
        }).join('');
    }

    function renderUserChart(data) {
        const container = document.getElementById('userChart');
        const labelsContainer = document.getElementById('userLabels');
        
        if (!data || data.length === 0) {
            container.innerHTML = '<div class="text-center w-full text-slate-500 text-sm">No user data</div>';
            labelsContainer.innerHTML = '';
            return;
        }
        
        const maxUsers = Math.max(...data.map(d => parseInt(d.users) || 0));
        
        container.innerHTML = data.map(d => {
            const height = maxUsers > 0 ? (parseInt(d.users) / maxUsers * 100) : 0;
            return `<div class="flex-1 bg-emerald-600 hover:bg-emerald-500 rounded-t transition-colors cursor-pointer relative group" style="height: ${Math.max(height, 2)}%">
                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-slate-700 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-10">
                    ${d.users} users
                </div>
            </div>`;
        }).join('');
        
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        labelsContainer.innerHTML = data.map((d, i) => {
            const [year, month] = d.month.split('-');
            const showLabel = window.innerWidth > 768 || i % 2 === 0;
            return `<span class="${showLabel ? '' : 'hidden sm:inline'}">${monthNames[parseInt(month) - 1]}</span>`;
        }).join('');
    }

    function renderTransactions(transactions) {
        const tbody = document.getElementById('transactionsTable');
        const countEl = document.getElementById('transactionCount');
        
        if (!transactions || transactions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-4 lg:px-6 py-8 text-center text-slate-500 text-sm">No transactions found for this period</td></tr>';
            countEl.textContent = '0 transactions';
            return;
        }
        
        countEl.textContent = `${transactions.length} transactions`;
        
        tbody.innerHTML = transactions.map(txn => {
            const statusClass = txn.status === 'success' 
                ? 'bg-emerald-600/20 text-emerald-400' 
                : txn.status === 'pending' 
                    ? 'bg-amber-600/20 text-amber-400' 
                    : 'bg-red-600/20 text-red-400';
            
            return `<tr class="border-b border-slate-700/50 hover:bg-slate-700/30">
                <td class="px-4 lg:px-6 py-4 font-mono text-xs lg:text-sm">#${txn.reference}</td>
                <td class="px-4 lg:px-6 py-4 text-sm hidden sm:table-cell truncate max-w-[150px]">${txn.user_name}</td>
                <td class="px-4 lg:px-6 py-4 text-slate-400 text-sm hidden md:table-cell">${txn.plan}</td>
                <td class="px-4 lg:px-6 py-4 font-medium text-emerald-400 text-sm">₦${txn.amount.toLocaleString()}</td>
                <td class="px-4 lg:px-6 py-4 hidden lg:table-cell"><span class="px-2 py-1 rounded-full text-xs font-medium ${statusClass}">${txn.status.charAt(0).toUpperCase() + txn.status.slice(1)}</span></td>
                <td class="px-4 lg:px-6 py-4 text-slate-400 text-xs lg:text-sm hidden sm:table-cell">${txn.date}</td>
            </tr>`;
        }).join('');
    }

    async function exportReport() {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        try {
            const from = document.getElementById('dateFrom').value;
            const to = document.getElementById('dateTo').value;
            
            const res = await fetch(`/api/admin/reports/export-pdf?from=${from}&to=${to}`, {
                headers: { 
                    'Authorization': `Bearer ${token}`, 
                    'Accept': 'application/json' 
                }
            });
            
            if (!res.ok) throw new Error('Failed to generate PDF');
            
            const data = await res.json();
            
            // Create a temporary container for the HTML
            const container = document.createElement('div');
            container.innerHTML = data.html;
            document.body.appendChild(container);
            
            // Generate PDF using html2pdf
            const opt = {
                margin: 10,
                filename: data.filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            await html2pdf().set(opt).from(container).save();
            
            // Clean up
            document.body.removeChild(container);
            
        } catch (err) {
            console.error('Export failed:', err);
            toastError('Failed to export report. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span class="hidden sm:inline ml-2">Export Report</span>';
        }
    }

    // Load reports on page load
    loadReports();
</script>
@endsection
