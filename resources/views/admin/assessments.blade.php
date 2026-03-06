@extends('layouts.admin')
@section('title', 'Assessments | Admin')
@section('page-title', 'Assessment Management')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-4">
    <div class="panel p-4" style="border-top:3px solid #6366f1;" id="stat-total">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(99,102,241,0.1);"><svg class="w-3.5 h-3.5" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
            <p class="text-[11px] font-medium" style="color:var(--text-secondary);">Total</p>
        </div><div class="skel h-7 w-12"></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #10b981;" id="stat-active">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,0.1);"><svg class="w-3.5 h-3.5" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <p class="text-[11px] font-medium" style="color:var(--text-secondary);">Active</p>
        </div><div class="skel h-7 w-10"></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #a855f7;" id="stat-completed">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(168,85,247,0.1);"><svg class="w-3.5 h-3.5" style="color:#c084fc" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
            <p class="text-[11px] font-medium" style="color:var(--text-secondary);">Completed</p>
        </div><div class="skel h-7 w-10"></div>
    </div>
    <div class="panel p-4" style="border-top:3px solid #f59e0b;" id="stat-score">
        <div class="flex items-center gap-2.5 mb-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:rgba(245,158,11,0.1);"><svg class="w-3.5 h-3.5" style="color:#fbbf24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
            <p class="text-[11px] font-medium" style="color:var(--text-secondary);">Avg Score</p>
        </div><div class="skel h-7 w-14"></div>
    </div>
</div>

<div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-4">
    <div class="flex-1 w-full sm:w-auto relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--text-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchInput" placeholder="Search assessments..." class="w-full pl-10 pr-4 py-2 text-[13px]">
    </div>
    <select id="statusFilter" class="w-full sm:w-auto px-3 py-2 text-[13px]">
        <option value="">All Status</option><option value="draft">Draft</option><option value="active">Active</option><option value="completed">Completed</option>
    </select>
</div>

<div class="panel">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr style="border-bottom:1px solid var(--border);background:var(--bg-alt);">
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Assessment</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden md:table-cell" style="color:var(--text-muted);">Created By</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">Invitees</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden sm:table-cell" style="color:var(--text-muted);">Done</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Status</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider hidden lg:table-cell" style="color:var(--text-muted);">Created</th>
                <th class="text-left px-4 lg:px-5 py-2.5 text-[10px] font-semibold uppercase tracking-wider" style="color:var(--text-muted);">Actions</th>
            </tr></thead>
            <tbody id="assessmentsTable">
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-32 mb-1.5"></div><div class="skel h-3 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-24"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-6"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-6"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-14"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel h-3.5 w-20"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-6 w-14 rounded-lg"></div></td></tr>
                <tr><td class="px-4 lg:px-5 py-3"><div class="skel h-3.5 w-36 mb-1.5"></div><div class="skel h-3 w-20"></div></td><td class="px-4 lg:px-5 py-3 hidden md:table-cell"><div class="skel h-3.5 w-28"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-8"></div></td><td class="px-4 lg:px-5 py-3 hidden sm:table-cell"><div class="skel h-3.5 w-6"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel skel-pill h-5 w-16"></div></td><td class="px-4 lg:px-5 py-3 hidden lg:table-cell"><div class="skel h-3.5 w-18"></div></td><td class="px-4 lg:px-5 py-3"><div class="skel h-6 w-14 rounded-lg"></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="assessmentModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);" onclick="if(event.target===this)closeAssessmentModal()">
    <div class="panel w-full max-w-lg max-h-[90vh] overflow-y-auto" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between sticky top-0 z-10" style="background:var(--surface);border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="assessmentModalTitle">Assessment Details</h3>
            <button onclick="closeAssessmentModal()" class="p-1 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5 space-y-4" id="assessmentModalBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const token=localStorage.getItem('adminToken');let allAssessments=[];
async function loadAssessments(){try{const r=await fetch('/api/admin/assessments',{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});const d=await r.json();allAssessments=d.assessments||d.data||[];updateStats();renderAssessments(allAssessments)}catch(e){updateStatsError();renderAssessments([])}}

function updateStats(){const t=allAssessments.length,a=allAssessments.filter(x=>x.status==='active').length,c=allAssessments.reduce((s,x)=>s+(x.completed_count||0),0),sc=allAssessments.filter(x=>x.avg_score!=null),avg=sc.length>0?Math.round(sc.reduce((s,x)=>s+parseFloat(x.avg_score),0)/sc.length):0;
const stat=(id,l,v,cl)=>{const el=document.getElementById(id);if(el.querySelector('.skel')){el.innerHTML=`<p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary)">${l}</p><p class="text-xl font-bold" style="color:${cl}">${v}</p>`}};
stat('stat-total','Total',t,'var(--text-primary)');stat('stat-active','Active',a,'#34d399');stat('stat-completed','Completed',c,'#c084fc');stat('stat-score','Avg Score',avg+'%','#fbbf24')}

function updateStatsError(){['stat-total','stat-active','stat-completed','stat-score'].forEach(id=>{document.getElementById(id).innerHTML=`<p class="text-[11px] mb-1" style="color:var(--text-secondary)">—</p><p class="text-[12px]" style="color:#f87171">Failed</p>`})}

function renderAssessments(list){const tb=document.getElementById('assessmentsTable');if(!list.length){tb.innerHTML='<tr><td colspan="7" class="px-5 py-10 text-center text-[12px]" style="color:var(--text-muted)">No assessments found</td></tr>';return}
const sb=s=>s==='active'?'badge-success':s==='completed'?'badge-neutral':'badge-warning';
tb.innerHTML=list.map(a=>`<tr class="tr-click" style="border-bottom:1px solid var(--border-subtle);cursor:pointer;" onclick="viewAssessment('${a.id}')">
<td class="px-4 lg:px-5 py-2.5"><div class="min-w-0"><p class="text-[13px] font-medium truncate" style="color:var(--text-primary)">${a.title}</p><p class="text-[11px]" style="color:var(--text-muted)">${a.questions_count||0} questions</p></div></td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden md:table-cell truncate" style="color:var(--text-secondary)">${a.user?.first_name||'Unknown'} ${a.user?.last_name||''}</td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden sm:table-cell" style="color:var(--text-secondary)">${a.invitees_count||0}</td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden sm:table-cell" style="color:var(--text-secondary)">${a.completed_count||0}</td>
<td class="px-4 lg:px-5 py-2.5"><span class="badge ${sb(a.status)}">${a.status||'draft'}</span></td>
<td class="px-4 lg:px-5 py-2.5 text-[11px] hidden lg:table-cell" style="color:var(--text-muted)">${new Date(a.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric'})}</td>
<td class="px-4 lg:px-5 py-2.5"><div class="flex items-center gap-0.5">
<button onclick="event.stopPropagation();deleteAssessment('${a.id}')" class="p-1.5 rounded-lg hover:bg-red-500/10 transition" title="Delete"><svg class="w-3.5 h-3.5" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
</div></td></tr>`).join('')}

async function viewAssessment(id){const m=document.getElementById('assessmentModal'),b=document.getElementById('assessmentModalBody');document.getElementById('assessmentModalTitle').textContent='Loading...';b.innerHTML='<div class="text-center py-8"><div class="animate-spin w-6 h-6 border-2 border-t-transparent rounded-full mx-auto" style="border-color:#6366f1;border-top-color:transparent"></div></div>';m.classList.remove('hidden');m.classList.add('flex');
try{const r=await fetch(`/api/admin/assessments/${id}`,{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});const a=await r.json();document.getElementById('assessmentModalTitle').textContent=a.title;
let h=`<div class="inner-card space-y-2.5"><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Status</span><span class="badge ${a.status==='active'?'badge-success':a.status==='completed'?'badge-neutral':'badge-warning'}">${a.status||'draft'}</span></div><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Created By</span><span class="text-[12px]" style="color:var(--text-primary)">${a.user?.first_name||'Unknown'} ${a.user?.last_name||''}</span></div><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Q/Inv/Done</span><span class="text-[12px]" style="color:var(--text-primary)">${a.questions?.length||0}/${a.invitees_count||0}/${a.completed_count||0}</span></div>${a.avg_score!=null?`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Avg Score</span><span class="text-[12px] font-semibold" style="color:#34d399">${parseFloat(a.avg_score).toFixed(1)}%</span></div>`:''}</div>`;
if(a.questions?.length>0){h+=`<h4 class="text-[12px] font-semibold mt-4 mb-2" style="color:var(--text-secondary)">Questions</h4>`;a.questions.forEach((q,qi)=>{h+=`<div class="inner-card mb-2"><p class="text-[12px] font-medium mb-2" style="color:var(--text-primary)">${qi+1}. ${q.question_text}</p>`;if(q.options?.length>0){h+='<div class="space-y-1">';q.options.forEach(o=>{h+=`<div class="flex items-center gap-2 text-[11px] px-2 py-1 rounded-lg" style="color:${o.is_correct?'#10b981':'var(--text-muted)'};${o.is_correct?'background:rgba(16,185,129,0.08)':''}"><span class="font-medium">${o.option_label}.</span><span>${o.option_text}</span>${o.is_correct?'<span class="ml-auto">✓</span>':''}</div>`});h+='</div>'}if(q.expected_answer)h+=`<p class="text-[11px] mt-1" style="color:#10b981">Expected: ${q.expected_answer}</p>`;h+=`<p class="text-[10px] mt-1" style="color:var(--text-muted)">${q.points} pts · ${q.question_type}</p></div>`})}
const ci=(a.invitees||[]).filter(i=>i.test_session);if(ci.length>0){h+=`<h4 class="text-[12px] font-semibold mt-4 mb-2" style="color:var(--text-secondary)">Candidates (${ci.length})</h4>`;ci.forEach(inv=>{const ts=inv.test_session,nm=[inv.first_name,inv.last_name].filter(Boolean).join(' ')||inv.email,pct=ts.percentage!=null?parseFloat(ts.percentage).toFixed(1):'—',tm=ts.time_spent_seconds?`${Math.floor(ts.time_spent_seconds/60)}m ${ts.time_spent_seconds%60}s`:'—';h+=`<div class="inner-card mb-2"><div class="flex justify-between items-center mb-2"><div><p class="text-[12px] font-medium" style="color:var(--text-primary)">${nm}</p><p class="text-[11px]" style="color:var(--text-muted)">${inv.email}</p></div><div class="text-right"><p class="text-[13px] font-bold" style="color:${ts.passed?'#10b981':'#ef4444'}">${pct}%</p><p class="text-[10px]" style="color:var(--text-muted)">${ts.total_score}/${ts.max_score} · ${tm}</p></div></div>`;if(ts.answers?.length>0){h+='<div style="border-top:1px solid var(--border);padding-top:8px;margin-top:8px" class="space-y-1">';ts.answers.forEach(ans=>{const qq=(a.questions||[]).find(q=>q.id===ans.question_id),qt=qq?qq.question_text.substring(0,50)+(qq.question_text.length>50?'...':''):'Q',sel=Array.isArray(ans.selected_options)?ans.selected_options.join(', '):(ans.text_answer||'—');h+=`<div class="flex items-center gap-2 text-[11px]"><span style="color:${ans.is_correct?'#10b981':'#ef4444'}">${ans.is_correct?'✓':'✗'}</span><span style="color:var(--text-muted)" class="truncate flex-1">${qt}</span><span style="color:var(--text-secondary)">${sel}</span></div>`});h+='</div>'}h+='</div>'})}
b.innerHTML=h}catch(e){b.innerHTML='<p class="text-center py-4 text-[12px]" style="color:#f87171">Failed to load details</p>'}}

function closeAssessmentModal(){document.getElementById('assessmentModal').classList.add('hidden');document.getElementById('assessmentModal').classList.remove('flex')}
async function deleteAssessment(id){const c=await showConfirm('Delete Assessment','This action cannot be undone.','Delete','danger');if(!c)return;try{await fetch(`/api/admin/assessments/${id}`,{method:'DELETE',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});toastSuccess('Assessment deleted');loadAssessments()}catch(e){toastError('Failed to delete')}}
document.getElementById('searchInput').addEventListener('input',e=>{renderAssessments(allAssessments.filter(a=>a.title.toLowerCase().includes(e.target.value.toLowerCase())))});
document.getElementById('statusFilter').addEventListener('change',e=>{renderAssessments(!e.target.value?allAssessments:allAssessments.filter(a=>a.status===e.target.value))});
loadAssessments();
</script>
@endsection
