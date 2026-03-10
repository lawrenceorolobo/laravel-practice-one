@extends('layouts.admin')
@section('title', 'Assessments | Admin')
@section('page-title', 'Assessment Management')

@section('header-actions')
<button onclick="openCreateTemplate()" class="btn-primary flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Create Template
</button>
@endsection

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
        <option value="">All Status</option><option value="draft">Draft</option><option value="active">Active</option><option value="completed">Completed</option><option value="template">Templates</option><option value="user">User Created</option>
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
                <tr style="border-bottom:1px solid var(--border-subtle);"><td class="px-4 lg:px-5 py-3" colspan="7"><div class="skel h-3.5 w-32 mb-1.5"></div><div class="skel h-3 w-16"></div></td></tr>
                <tr><td class="px-4 lg:px-5 py-3" colspan="7"><div class="skel h-3.5 w-36 mb-1.5"></div><div class="skel h-3 w-20"></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Assessment Detail Modal -->
<div id="assessmentModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);" onclick="if(event.target===this)closeAssessmentModal()">
    <div class="panel w-full max-w-lg max-h-[90vh] overflow-y-auto" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between sticky top-0 z-10" style="background:var(--surface);border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="assessmentModalTitle">Assessment Details</h3>
            <button onclick="closeAssessmentModal()" class="p-1 rounded-lg transition" style="color:var(--text-muted);"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5 space-y-4" id="assessmentModalBody"></div>
    </div>
</div>

<!-- Create/Edit Template Modal -->
<div id="createTemplateModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);" onclick="if(event.target===this)closeCreateTemplate()">
    <div class="panel w-full max-w-2xl max-h-[90vh] overflow-y-auto" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between sticky top-0 z-10" style="background:var(--surface);border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="createTemplateTitle">Create Assessment Template</h3>
            <button onclick="closeCreateTemplate()" class="p-1 rounded-lg transition" style="color:var(--text-muted);"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5 space-y-4" id="createTemplateBody">
            <!-- Step 1: Template Info -->
            <div id="templateStep1">
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Template Title *</label>
                        <input type="text" id="tmplTitle" placeholder="e.g. Software Engineering Assessment" class="w-full text-[13px]">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Description</label>
                        <textarea id="tmplDesc" placeholder="Brief description..." class="w-full text-[13px]" rows="2"></textarea>
                    </div>
                    <button onclick="createOrUpdateTemplate()" class="btn-primary w-full" id="tmplSubmitBtn">Create Template & Add Questions →</button>
                </div>
            </div>

            <!-- Step 2: Add/Edit Questions -->
            <div id="templateStep2" class="hidden">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-[13px] font-semibold" style="color:var(--text-primary);" id="tmplName">Template</h4>
                        <p class="text-[11px]" style="color:var(--text-muted);" id="tmplQCount">0 questions</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="showEditTitleForm()" class="btn-ghost text-[11px] flex items-center gap-1" title="Edit Title"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Edit Info</button>
                        <button onclick="closeCreateTemplate();loadAssessments()" class="btn-ghost text-[12px]">Done</button>
                    </div>
                </div>

                <!-- Inline edit title (hidden by default) -->
                <div id="editTitleSection" class="hidden inner-card mb-3 space-y-2">
                    <input type="text" id="editTmplTitle" class="w-full text-[13px]" placeholder="Title">
                    <textarea id="editTmplDesc" class="w-full text-[13px]" rows="2" placeholder="Description"></textarea>
                    <div class="flex gap-2">
                        <button onclick="saveTemplateInfo()" class="btn-primary text-[11px] flex-1">Save</button>
                        <button onclick="document.getElementById('editTitleSection').classList.add('hidden')" class="btn-ghost text-[11px]">Cancel</button>
                    </div>
                </div>

                <!-- Add question form -->
                <div class="inner-card mb-3 space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Question Type *</label>
                        <select id="qType" class="w-full text-[13px]" onchange="onTypeChange()">
                            <optgroup label="Standard">
                                <option value="single_choice">Single Choice</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True / False</option>
                            </optgroup>
                            <optgroup label="Text & Numeric">
                                <option value="fill_blank">Fill in the Blank</option>
                                <option value="numeric">Numeric Answer</option>
                                <option value="text_input">Text Input</option>
                                <option value="code_snippet">Code Snippet</option>
                            </optgroup>
                            <optgroup label="Ordering & Matching">
                                <option value="ordering">Ordering / Sequencing</option>
                                <option value="matching">Matching</option>
                                <option value="drag_drop_sort">Drag & Drop Sort</option>
                            </optgroup>
                            <optgroup label="Psychometric & Reasoning">
                                <option value="sequence_pattern">Sequence Pattern</option>
                                <option value="odd_one_out">Odd One Out</option>
                                <option value="analogy">Analogy</option>
                                <option value="pattern_recognition">Pattern Recognition</option>
                                <option value="mental_maths">Mental Maths</option>
                                <option value="word_problem">Word Problem</option>
                            </optgroup>
                            <optgroup label="Advanced">
                                <option value="matrix_pattern">Matrix Pattern</option>
                                <option value="spatial_rotation">Spatial Rotation</option>
                                <option value="shape_assembly">Shape Assembly</option>
                                <option value="shape_puzzle">Shape Puzzle</option>
                                <option value="hotspot">Hotspot</option>
                                <option value="likert_scale">Likert Scale</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Question Text *</label>
                        <textarea id="qText" placeholder="Enter the question..." class="w-full text-[13px]" rows="2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Points</label>
                            <input type="number" id="qPoints" value="1" min="1" max="100" class="w-full text-[13px]">
                        </div>
                        <div id="expectedAnswerWrap" class="hidden">
                            <label class="block text-[11px] font-semibold mb-1" style="color:var(--text-secondary);">Expected Answer</label>
                            <input type="text" id="qExpected" placeholder="e.g. 42 or text||alt" class="w-full text-[13px]">
                        </div>
                    </div>
                    <div id="optionsSection">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[11px] font-semibold" style="color:var(--text-secondary);">Options</label>
                            <button onclick="addOption()" class="text-[11px] font-medium" style="color:var(--accent);">+ Add Option</button>
                        </div>
                        <div id="optionsList" class="space-y-2"></div>
                    </div>
                    <button onclick="addQuestion()" class="btn-primary w-full" id="addQBtn">Add Question</button>
                </div>

                <!-- Existing questions list -->
                <h4 class="text-[12px] font-semibold mb-2" style="color:var(--text-secondary);">Questions</h4>
                <div id="addedQuestions" class="space-y-2"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const token=localStorage.getItem('adminToken');let allAssessments=[],currentTemplateId=null,questionCount=0,isEditMode=false;
const noOptionTypes=['text_input','fill_blank','numeric','mental_maths','word_problem','code_snippet'];

async function loadAssessments(){try{const r=await fetch('/api/admin/assessments',{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});const d=await r.json();allAssessments=d.assessments||d.data||[];updateStats();renderAssessments(allAssessments)}catch(e){updateStatsError();renderAssessments([])}}

function updateStats(){const userA=allAssessments.filter(x=>!x.is_template),t=userA.length,tmpl=allAssessments.filter(x=>x.is_template).length,a=userA.filter(x=>x.status==='active').length,c=userA.reduce((s,x)=>s+(x.completed_count||0),0),sc=userA.filter(x=>x.avg_score!=null),avg=sc.length>0?Math.round(sc.reduce((s,x)=>s+parseFloat(x.avg_score),0)/sc.length):0;
const stat=(id,l,v,cl)=>{const el=document.getElementById(id);if(el.querySelector('.skel')){el.innerHTML=`<p class="text-[11px] mb-1 font-medium" style="color:var(--text-secondary)">${l}</p><p class="text-xl font-bold" style="color:${cl}">${v}</p>`}};
stat('stat-total','Total',`${t} <span class="text-[11px] font-normal" style="color:var(--text-muted)">(+${tmpl} templates)</span>`,'var(--text-primary)');stat('stat-active','Active',a,'#34d399');stat('stat-completed','Completed',c,'#c084fc');stat('stat-score','Avg Score',avg+'%','#fbbf24')}

function updateStatsError(){['stat-total','stat-active','stat-completed','stat-score'].forEach(id=>{document.getElementById(id).innerHTML=`<p class="text-[11px] mb-1" style="color:var(--text-secondary)">—</p><p class="text-[12px]" style="color:#f87171">Failed</p>`})}

function renderAssessments(list){const tb=document.getElementById('assessmentsTable');if(!list.length){tb.innerHTML='<tr><td colspan="7" class="px-5 py-10 text-center text-[12px]" style="color:var(--text-muted)">No assessments found</td></tr>';return}
const sb=s=>s==='active'?'badge-success':s==='completed'?'badge-neutral':'badge-warning';
tb.innerHTML=list.map(a=>{const isT=a.is_template;
const creator=isT?'<span style="color:#818cf8"><svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>System Template</span>':`${a.user?.first_name||'Unknown'} ${a.user?.last_name||''}`;
const safeTitle=a.title.replace(/'/g,"\\'").replace(/"/g,'&quot;');
return `<tr class="tr-click" style="border-bottom:1px solid var(--border-subtle);cursor:pointer;${isT?'background:var(--accent-bg);':''}" onclick="viewAssessment('${a.id}')">
<td class="px-4 lg:px-5 py-2.5"><div class="min-w-0"><p class="text-[13px] font-medium truncate" style="color:var(--text-primary)">${a.title}${isT?' <span class="badge badge-info" style="font-size:9px;padding:1px 6px;vertical-align:middle;">TEMPLATE</span>':''}</p><p class="text-[11px]" style="color:var(--text-muted)">${a.questions_count||0} questions</p></div></td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden md:table-cell truncate" style="color:var(--text-secondary)">${creator}</td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden sm:table-cell" style="color:${isT?'var(--text-muted)':'var(--text-secondary)'}">${isT?'—':a.invitees_count||0}</td>
<td class="px-4 lg:px-5 py-2.5 text-[12px] hidden sm:table-cell" style="color:${isT?'var(--text-muted)':'var(--text-secondary)'}">${isT?'—':a.completed_count||0}</td>
<td class="px-4 lg:px-5 py-2.5">${isT?'<span class="badge badge-info">template</span>':`<span class="badge ${sb(a.status)}">${a.status||'draft'}</span>`}</td>
<td class="px-4 lg:px-5 py-2.5 text-[11px] hidden lg:table-cell" style="color:var(--text-muted)">${new Date(a.created_at).toLocaleDateString('en-US',{month:'short',day:'numeric'})}</td>
<td class="px-4 lg:px-5 py-2.5"><div class="flex items-center gap-0.5">
${isT?`<button onclick="event.stopPropagation();openEditTemplate('${a.id}')" class="p-1.5 rounded-lg transition" style="color:#818cf8;" title="Edit Template" onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='transparent'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>`:''}
${!isT?`<button onclick="event.stopPropagation();toggleTemplate('${a.id}',false)" class="p-1.5 rounded-lg transition" style="color:var(--text-muted);" title="Make Template" onmouseover="this.style.background='var(--accent-bg)'" onmouseout="this.style.background='transparent'"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></button>`:''}<button onclick="event.stopPropagation();deleteAssessment('${a.id}')" class="p-1.5 rounded-lg hover:bg-red-500/10 transition" title="Delete"><svg class="w-3.5 h-3.5" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
</div></td></tr>`}).join('')}

// ═══ View Assessment ═══
async function viewAssessment(id){const m=document.getElementById('assessmentModal'),b=document.getElementById('assessmentModalBody');document.getElementById('assessmentModalTitle').textContent='Loading...';b.innerHTML='<div class="text-center py-8"><div class="animate-spin w-6 h-6 border-2 border-t-transparent rounded-full mx-auto" style="border-color:#6366f1;border-top-color:transparent"></div></div>';m.classList.remove('hidden');m.classList.add('flex');
try{const r=await fetch(`/api/admin/assessments/${id}`,{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});const a=await r.json();document.getElementById('assessmentModalTitle').textContent=a.title;
let h=`<div class="inner-card space-y-2.5"><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Status</span><span class="badge ${a.status==='active'?'badge-success':a.status==='completed'?'badge-neutral':'badge-warning'}">${a.is_template?'template':a.status||'draft'}</span></div><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Created By</span><span class="text-[12px]" style="color:var(--text-primary)">${a.is_template?'System Template':`${a.user?.first_name||'Unknown'} ${a.user?.last_name||''}`}</span></div>${!a.is_template?`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Q/Inv/Done</span><span class="text-[12px]" style="color:var(--text-primary)">${a.questions?.length||0}/${a.invitees_count||0}/${a.completed_count||0}</span></div>`:`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Questions</span><span class="text-[12px]" style="color:var(--text-primary)">${a.questions?.length||0}</span></div>`}${a.avg_score!=null&&!a.is_template?`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Avg Score</span><span class="text-[12px] font-semibold" style="color:#34d399">${parseFloat(a.avg_score).toFixed(1)}%</span></div>`:''}</div>`;
if(a.questions?.length>0){h+=`<h4 class="text-[12px] font-semibold mt-4 mb-2" style="color:var(--text-secondary)">Questions</h4>`;a.questions.forEach((q,qi)=>{h+=`<div class="inner-card mb-2"><p class="text-[12px] font-medium mb-2" style="color:var(--text-primary)">${qi+1}. ${q.question_text}</p>`;if(q.options?.length>0){h+='<div class="space-y-1">';q.options.forEach(o=>{h+=`<div class="flex items-center gap-2 text-[11px] px-2 py-1 rounded-lg" style="color:${o.is_correct?'#10b981':'var(--text-muted)'};${o.is_correct?'background:rgba(16,185,129,0.08)':''}"><span class="font-medium">${o.option_label}.</span><span>${o.option_text}</span>${o.is_correct?'<span class="ml-auto">✓</span>':''}</div>`});h+='</div>'}if(q.expected_answer)h+=`<p class="text-[11px] mt-1" style="color:#10b981">Expected: ${q.expected_answer}</p>`;h+=`<p class="text-[10px] mt-1" style="color:var(--text-muted)">${q.points} pts · ${q.question_type}</p></div>`})}
if(!a.is_template){const ci=(a.invitees||[]).filter(i=>i.test_session);if(ci.length>0){h+=`<h4 class="text-[12px] font-semibold mt-4 mb-2" style="color:var(--text-secondary)">Candidates (${ci.length})</h4>`;ci.forEach(inv=>{const ts=inv.test_session,nm=[inv.first_name,inv.last_name].filter(Boolean).join(' ')||inv.email,pct=ts.percentage!=null?parseFloat(ts.percentage).toFixed(1):'—',tm=ts.time_spent_seconds?`${Math.floor(ts.time_spent_seconds/60)}m ${ts.time_spent_seconds%60}s`:'—';h+=`<div class="inner-card mb-2"><div class="flex justify-between items-center"><div><p class="text-[12px] font-medium" style="color:var(--text-primary)">${nm}</p><p class="text-[11px]" style="color:var(--text-muted)">${inv.email}</p></div><div class="text-right"><p class="text-[13px] font-bold" style="color:${ts.passed?'#10b981':'#ef4444'}">${pct}%</p><p class="text-[10px]" style="color:var(--text-muted)">${ts.total_score}/${ts.max_score} · ${tm}</p></div></div></div>`})}}
b.innerHTML=h}catch(e){b.innerHTML='<p class="text-center py-4 text-[12px]" style="color:#f87171">Failed to load details</p>'}}

function closeAssessmentModal(){document.getElementById('assessmentModal').classList.add('hidden');document.getElementById('assessmentModal').classList.remove('flex')}
async function deleteAssessment(id){const c=await showConfirm('Delete Assessment','This action cannot be undone.','Delete','danger');if(!c)return;try{await fetch(`/api/admin/assessments/${id}`,{method:'DELETE',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});toastSuccess('Assessment deleted');loadAssessments()}catch(e){toastError('Failed to delete')}}
async function toggleTemplate(id,isTemplate){const msg='Make this assessment a template visible to all users?';const c=await showConfirm('Make Template',msg,'Make Template','info');if(!c)return;try{const r=await fetch(`/api/admin/assessments/${id}/toggle-template`,{method:'POST',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});if(r.ok){const d=await r.json();toastSuccess(d.message);loadAssessments()}else{toastError('Failed')}}catch(e){toastError('Network error')}}

// Filters
document.getElementById('searchInput').addEventListener('input',()=>applyFilters());
document.getElementById('statusFilter').addEventListener('change',()=>applyFilters());
function applyFilters(){const s=document.getElementById('searchInput').value.toLowerCase(),f=document.getElementById('statusFilter').value;let list=allAssessments;if(s)list=list.filter(a=>a.title.toLowerCase().includes(s));if(f==='template')list=list.filter(a=>a.is_template);else if(f==='user')list=list.filter(a=>!a.is_template);else if(f)list=list.filter(a=>a.status===f);renderAssessments(list)}

// ═══ Template CRUD ═══
function openCreateTemplate(){
    isEditMode=false;currentTemplateId=null;questionCount=0;
    document.getElementById('tmplTitle').value='';
    document.getElementById('tmplDesc').value='';
    document.getElementById('tmplSubmitBtn').textContent='Create Template & Add Questions →';
    document.getElementById('templateStep1').classList.remove('hidden');
    document.getElementById('templateStep2').classList.add('hidden');
    document.getElementById('createTemplateTitle').textContent='Create Assessment Template';
    document.getElementById('createTemplateModal').classList.remove('hidden');
    document.getElementById('createTemplateModal').classList.add('flex');
}
function closeCreateTemplate(){
    document.getElementById('createTemplateModal').classList.add('hidden');
    document.getElementById('createTemplateModal').classList.remove('flex');
}

async function openEditTemplate(id){
    isEditMode=true;currentTemplateId=id;
    document.getElementById('createTemplateTitle').textContent='Edit Template';
    document.getElementById('templateStep1').classList.add('hidden');
    document.getElementById('templateStep2').classList.remove('hidden');
    document.getElementById('editTitleSection').classList.add('hidden');
    document.getElementById('createTemplateModal').classList.remove('hidden');
    document.getElementById('createTemplateModal').classList.add('flex');
    // Load template details
    try{
        const r=await fetch(`/api/admin/assessments/${id}`,{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});
        const a=await r.json();
        document.getElementById('tmplName').textContent=a.title;
        questionCount=a.questions?.length||0;
        document.getElementById('tmplQCount').textContent=`${questionCount} questions`;
        document.getElementById('editTmplTitle').value=a.title;
        document.getElementById('editTmplDesc').value=a.description||'';
        renderExistingQuestions(a.questions||[]);
    }catch(e){toastError('Failed to load template')}
    resetQuestionForm();
}

function showEditTitleForm(){
    document.getElementById('editTitleSection').classList.remove('hidden');
}
async function saveTemplateInfo(){
    const title=document.getElementById('editTmplTitle').value.trim();
    if(!title){toastError('Title required');return}
    try{
        const r=await fetch(`/api/admin/templates/${currentTemplateId}`,{method:'PUT',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({title,description:document.getElementById('editTmplDesc').value.trim()||null})});
        if(r.ok){document.getElementById('tmplName').textContent=title;document.getElementById('editTitleSection').classList.add('hidden');toastSuccess('Template info updated')}else{toastError('Failed to update')}
    }catch(e){toastError('Network error')}
}

function renderExistingQuestions(questions){
    const el=document.getElementById('addedQuestions');
    el.innerHTML=questions.map((q,i)=>`<div class="inner-card" id="q-${q.id}">
<div class="flex items-start justify-between gap-2 mb-1">
    <p class="text-[12px] font-medium flex-1" style="color:var(--text-primary)">${i+1}. ${q.question_text}</p>
    <div class="flex items-center gap-1 flex-shrink-0">
        <span class="badge badge-neutral text-[9px]">${q.question_type.replace(/_/g,' ')}</span>
        <button onclick="editQuestion('${q.id}','${currentTemplateId}')" class="p-1 rounded transition" style="color:#818cf8;" title="Edit"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
        <button onclick="deleteQuestion('${q.id}','${currentTemplateId}')" class="p-1 rounded transition hover:bg-red-500/10" title="Delete"><svg class="w-3 h-3" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
    </div>
</div>
${q.options?.length>0?'<div class="space-y-0.5">'+q.options.map(o=>`<div class="text-[11px] px-2 py-0.5 rounded" style="color:${o.is_correct?'#10b981':'var(--text-muted)'};${o.is_correct?'background:rgba(16,185,129,0.06)':''}"><span class="font-medium">${o.option_label}.</span> ${o.option_text}${o.is_correct?' ✓':''}</div>`).join('')+'</div>':''}
${q.expected_answer?`<p class="text-[10px] mt-1" style="color:#10b981">Expected: ${q.expected_answer}</p>`:''}
<p class="text-[10px] mt-1" style="color:var(--text-muted)">${q.points} pts</p>
</div>`).join('');
}

async function editQuestion(qId,tmplId){
    // Load question data and open inline editor
    try{
        const r=await fetch(`/api/admin/assessments/${tmplId}`,{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});
        const a=await r.json();
        const q=(a.questions||[]).find(x=>String(x.id)===String(qId));
        if(!q){toastError('Question not found');return}
        // Populate the form with existing data
        document.getElementById('qType').value=q.question_type;
        onTypeChange();
        document.getElementById('qText').value=q.question_text;
        document.getElementById('qPoints').value=q.points;
        document.getElementById('qExpected').value=q.expected_answer||'';
        // Load options
        if(q.options?.length>0&&!noOptionTypes.includes(q.question_type)){
            document.getElementById('optionsList').innerHTML='';
            q.options.forEach(o=>addOptionRow(o.option_text,o.is_correct));
        }
        // Change button to update mode
        const btn=document.getElementById('addQBtn');
        btn.textContent='Update Question';
        btn.setAttribute('data-edit-id',qId);
        // Scroll to form
        document.getElementById('qType').scrollIntoView({behavior:'smooth',block:'center'});
    }catch(e){toastError('Failed to load question')}
}

async function deleteQuestion(qId,tmplId){
    const c=await showConfirm('Delete Question','Remove this question?','Delete','danger');if(!c)return;
    try{
        const r=await fetch(`/api/admin/templates/${tmplId}/questions/${qId}`,{method:'DELETE',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});
        if(r.ok){document.getElementById('q-'+qId)?.remove();questionCount--;document.getElementById('tmplQCount').textContent=`${questionCount} questions`;toastSuccess('Question deleted')}
        else{toastError('Failed to delete')}
    }catch(e){toastError('Network error')}
}

async function createOrUpdateTemplate(){
    const title=document.getElementById('tmplTitle').value.trim();
    if(!title){toastError('Title is required');return}
    try{
        const r=await fetch('/api/admin/templates',{method:'POST',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({title,description:document.getElementById('tmplDesc').value.trim()||null})});
        if(!r.ok){const e=await r.json();toastError(e.message||'Failed');return}
        const d=await r.json();
        currentTemplateId=d.assessment.id;questionCount=0;
        document.getElementById('templateStep1').classList.add('hidden');
        document.getElementById('templateStep2').classList.remove('hidden');
        document.getElementById('tmplName').textContent=title;
        document.getElementById('tmplQCount').textContent='0 questions';
        document.getElementById('addedQuestions').innerHTML='';
        document.getElementById('editTmplTitle').value=title;
        document.getElementById('editTmplDesc').value=document.getElementById('tmplDesc').value.trim();
        document.getElementById('createTemplateTitle').textContent='Add Questions — '+title;
        toastSuccess('Template created! Add questions below.');
        resetQuestionForm();
    }catch(e){toastError('Network error')}
}

function onTypeChange(){
    const t=document.getElementById('qType').value;
    const isNoOpt=noOptionTypes.includes(t);
    document.getElementById('optionsSection').style.display=isNoOpt?'none':'block';
    document.getElementById('expectedAnswerWrap').classList.toggle('hidden',!isNoOpt);
    if(t==='true_false'){
        document.getElementById('optionsList').innerHTML='';
        addOptionRow('True',true);addOptionRow('False',false);
    } else if(!isNoOpt&&document.getElementById('optionsList').children.length===0){
        addOptionRow('',false);addOptionRow('',false);
    }
}

function addOption(){addOptionRow('',false)}
function addOptionRow(text,correct){
    const div=document.createElement('div');div.className='flex items-center gap-2';
    div.innerHTML=`<input type="checkbox" class="opt-correct accent-emerald-500 w-4 h-4" ${correct?'checked':''} title="Mark correct">
<input type="text" class="opt-text flex-1 text-[12px]" placeholder="Option text..." value="${text.replace(/"/g,'&quot;')}">
<button onclick="this.parentElement.remove()" class="p-1 rounded" style="color:var(--text-muted)"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
    document.getElementById('optionsList').appendChild(div);
}

function resetQuestionForm(){
    document.getElementById('qType').value='single_choice';
    document.getElementById('qText').value='';
    document.getElementById('qPoints').value='1';
    document.getElementById('qExpected').value='';
    document.getElementById('optionsList').innerHTML='';
    addOptionRow('',false);addOptionRow('',false);
    const btn=document.getElementById('addQBtn');btn.textContent='Add Question';btn.removeAttribute('data-edit-id');
    onTypeChange();
}

async function addQuestion(){
    const type=document.getElementById('qType').value;
    const text=document.getElementById('qText').value.trim();
    if(!text){toastError('Question text required');return}

    const isNoOpt=noOptionTypes.includes(type);
    const body={question_text:text,question_type:type,points:parseInt(document.getElementById('qPoints').value)||1};

    if(isNoOpt){
        const exp=document.getElementById('qExpected').value.trim();
        if(exp)body.expected_answer=exp;
    } else {
        const opts=[];
        document.querySelectorAll('#optionsList > div').forEach(row=>{
            const t=row.querySelector('.opt-text').value.trim();
            if(t)opts.push({text:t,is_correct:row.querySelector('.opt-correct').checked});
        });
        if(opts.length<2){toastError('Need at least 2 options');return}
        body.options=opts;
    }

    const btn=document.getElementById('addQBtn');
    const editId=btn.getAttribute('data-edit-id');
    btn.disabled=true;btn.textContent=editId?'Updating...':'Adding...';

    try{
        let url,method;
        if(editId){
            url=`/api/admin/templates/${currentTemplateId}/questions/${editId}`;method='PUT';
        } else {
            url=`/api/admin/templates/${currentTemplateId}/questions`;method='POST';
        }
        const r=await fetch(url,{method,headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify(body)});
        const d=await r.json();
        if(!r.ok){toastError(d.message||'Failed');btn.disabled=false;btn.textContent=editId?'Update Question':'Add Question';return}

        if(editId){
            toastSuccess('Question updated');
        } else {
            questionCount++;
            document.getElementById('tmplQCount').textContent=`${questionCount} questions`;
            toastSuccess('Question added');
        }
        // Reload questions list
        const rr=await fetch(`/api/admin/assessments/${currentTemplateId}`,{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});
        const aa=await rr.json();
        questionCount=aa.questions?.length||questionCount;
        document.getElementById('tmplQCount').textContent=`${questionCount} questions`;
        renderExistingQuestions(aa.questions||[]);
        resetQuestionForm();
    }catch(e){toastError('Network error')}
    btn.disabled=false;btn.textContent='Add Question';
}

// Init
onTypeChange();
loadAssessments();
</script>
@endsection
