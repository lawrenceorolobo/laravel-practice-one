@extends('layouts.user')
@section('title', 'Assessment Details | Quizly')

@section('content')
<div id="loading" class="text-center py-16">
    <div class="skeleton h-8 w-64 mx-auto mb-4"></div>
    <div class="skeleton h-4 w-48 mx-auto"></div>
</div>

<div id="content" class="hidden">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="/assessments" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <h2 class="text-2xl font-bold" id="title">Assessment</h2>
            <p class="text-slate-500" id="description"></p>
        </div>
        <div class="flex gap-3">
            <button onclick="editAssessment()" class="px-4 py-2 border rounded-lg hover:bg-slate-50">Edit</button>
            <button onclick="publishAssessment()" id="publishBtn" class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Publish</button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-4 gap-4 mb-8">
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-indigo-600" id="questionsCount">0</p>
            <p class="text-slate-500 text-sm">Questions</p>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-emerald-600" id="candidatesCount">0</p>
            <p class="text-slate-500 text-sm">Candidates</p>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-purple-600" id="completedCount">0</p>
            <p class="text-slate-500 text-sm">Completed</p>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-amber-600" id="avgScore">-</p>
            <p class="text-slate-500 text-sm">Avg Score</p>
        </div>
    </div>

    <!-- Public Link (shown after publish) -->
    <div id="publicLinkSection" class="hidden glass rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg mb-1">📎 Public Assessment Link</h3>
                <p class="text-sm text-slate-500">Share this link with anyone to let them join the assessment</p>
            </div>
            <button onclick="copyPublicLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Copy Link</button>
        </div>
        <div class="mt-3 bg-slate-50 border rounded-lg px-4 py-3">
            <code id="publicLinkUrl" class="text-sm text-indigo-600 break-all"></code>
        </div>
    </div>

    <!-- Questions -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Questions</h3>
            <div class="flex gap-2">
                <button onclick="openQuestionImportModal()" class="px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v12"/></svg>
                    Import CSV
                </button>
                <button onclick="openQuestionModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Question
                </button>
            </div>
        </div>
        <div id="questionsList"></div>
    </div>

    <!-- Invitees -->
    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Candidates</h3>
            <div class="flex gap-2">
                <button onclick="resendAll()" id="resendAllBtn" class="hidden px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Resend All
                </button>
                <button onclick="openInviteModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Invite Candidates
                </button>
            </div>
        </div>
        <div id="inviteesList"></div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="questionModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4" id="questionModalTitle">Add Question</h3>
        <form id="questionForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Type</label>
                <select name="type" onchange="onTypeChange(this.value)" class="w-full px-4 py-2 border rounded-lg">
                    <option value="single_choice">Single Choice</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="text_input">Text Input</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text *</label>
                <textarea name="text" required rows="2" class="w-full px-4 py-2 border rounded-lg" oninput="checkQuestionForm()"></textarea>
            </div>
            <!-- Options for single/multiple choice -->
            <div id="optionsContainer" class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <p id="optionsHint" class="text-xs text-slate-400 mb-2"></p>
                <div id="optionsList" class="space-y-2"></div>
                <button type="button" onclick="addOption()" class="mt-2 text-indigo-600 font-medium text-sm">+ Add Option</button>
            </div>
            <!-- Expected answer for text input -->
            <div id="textAnswerContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Expected Answer *</label>
                <textarea name="expected_answer" rows="2" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter the correct answer..."></textarea>
                <p class="text-xs text-slate-400 mt-1">Candidate answers will be matched case-insensitively (trimmed). For flexible matching, separate multiple accepted answers with <strong>||</strong> e.g. <em>Paris || paris france</em></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Points</label>
                <input type="number" name="points" value="1" min="1" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div id="questionError" class="hidden mb-3 p-3 bg-red-50 text-red-600 rounded-lg text-sm"></div>
            <div class="flex gap-3">
                <button type="submit" id="saveQuestionBtn" disabled class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Save Question</button>
                <button type="button" onclick="closeQuestionModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Invite Modal (Tabbed: Manual / CSV) -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Invite Candidates</h3>
        <!-- Tabs -->
        <div class="flex border-b mb-4">
            <button onclick="switchInviteTab('manual')" id="inviteTabManual" class="px-4 py-2 font-medium text-sm border-b-2 border-indigo-600 text-indigo-600">Manual Entry</button>
            <button onclick="switchInviteTab('csv')" id="inviteTabCsv" class="px-4 py-2 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">CSV Upload</button>
        </div>
        <!-- Manual Tab -->
        <div id="inviteManualTab">
            <form id="inviteForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="candidate@example.com" oninput="checkInviteForm()">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">First Name</label>
                        <input type="text" name="first_name" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Last Name</label>
                        <input type="text" name="last_name" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" id="addCandidateBtn" disabled class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Add Candidate</button>
                    <button type="button" onclick="closeInviteModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
                </div>
            </form>
        </div>
        <!-- CSV Tab -->
        <div id="inviteCsvTab" class="hidden">
            <div class="mb-3">
                <a href="#" onclick="downloadInviteTemplate(); return false;" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download template CSV
                </a>
            </div>
            <div id="inviteCsvDropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors" onclick="document.getElementById('inviteCsvInput').click()" ondragover="event.preventDefault(); this.classList.add('border-indigo-500','bg-indigo-50')" ondragleave="this.classList.remove('border-indigo-500','bg-indigo-50')" ondrop="handleInviteCsvDrop(event)">
                <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-sm text-gray-600">Drag & drop CSV here, or <span class="text-indigo-600 font-medium">browse</span></p>
                <p class="text-xs text-gray-400 mt-1">Columns: email, first_name (optional), last_name (optional)</p>
            </div>
            <input type="file" id="inviteCsvInput" accept=".csv,.txt" class="hidden" onchange="handleInviteCsvSelect(this)">
            <div id="inviteCsvPreview" class="hidden mt-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium"><span id="inviteCsvCount">0</span> invitees found</p>
                    <button onclick="clearInviteCsv()" class="text-xs text-red-500 hover:underline">Clear</button>
                </div>
                <div class="max-h-40 overflow-y-auto border rounded-lg">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0"><tr><th class="px-3 py-1 text-left">Email</th><th class="px-3 py-1 text-left">First Name</th><th class="px-3 py-1 text-left">Last Name</th></tr></thead>
                        <tbody id="inviteCsvRows"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" id="uploadInviteCsvBtn" disabled onclick="uploadInviteCsv()" class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Upload Invitees</button>
                <button type="button" onclick="closeInviteModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invitee Modal -->
<div id="editInviteeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full">
        <h3 class="text-xl font-bold mb-4">Edit Candidate</h3>
        <form id="editInviteeForm">
            <input type="hidden" id="editInviteeId">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email *</label>
                <input type="email" id="editInvEmail" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">First Name</label>
                    <input type="text" id="editInvFirstName" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Last Name</label>
                    <input type="text" id="editInvLastName" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold">Save Changes</button>
                <button type="button" onclick="document.getElementById('editInviteeModal').classList.add('hidden')" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Question CSV Import Modal -->
<div id="questionImportModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Import Questions from CSV</h3>
        <div class="mb-3">
            <a href="#" onclick="downloadQuestionTemplate(); return false;" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download template CSV
            </a>
        </div>
        <div id="questionCsvDropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors" onclick="document.getElementById('questionCsvInput').click()" ondragover="event.preventDefault(); this.classList.add('border-indigo-500','bg-indigo-50')" ondragleave="this.classList.remove('border-indigo-500','bg-indigo-50')" ondrop="handleQuestionCsvDrop(event)">
            <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-sm text-gray-600">Drag & drop CSV here, or <span class="text-indigo-600 font-medium">browse</span></p>
            <p class="text-xs text-gray-400 mt-1">Columns: question, type, points, option_a-d, correct_answer, expected_answer</p>
        </div>
        <input type="file" id="questionCsvInput" accept=".csv,.txt" class="hidden" onchange="handleQuestionCsvSelect(this)">
        <div id="questionCsvPreview" class="hidden mt-4">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium"><span id="questionCsvCount">0</span> questions found</p>
                <button onclick="clearQuestionCsv()" class="text-xs text-red-500 hover:underline">Clear</button>
            </div>
            <div class="max-h-40 overflow-y-auto border rounded-lg">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 sticky top-0"><tr><th class="px-3 py-1 text-left">Question</th><th class="px-3 py-1 text-left">Type</th><th class="px-3 py-1 text-left">Points</th></tr></thead>
                    <tbody id="questionCsvRows"></tbody>
                </table>
            </div>
        </div>
        <div id="questionImportErrors" class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800"></div>
        <div class="flex gap-3 mt-4">
            <button type="button" id="uploadQuestionCsvBtn" disabled onclick="uploadQuestionCsv()" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Import Questions</button>
            <button type="button" onclick="closeQuestionImportModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const assessmentId = window.location.pathname.split('/')[2];
let assessment = null;

async function loadAssessment() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}`, { 
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } 
        });
        if (res.ok) {
            const data = await res.json();
            // API returns assessment directly, or wrapped in data/assessment key
            assessment = data.assessment || data.data || data;
            renderAssessment();
        } else {
            console.error('Failed to load assessment:', res.status);
            document.getElementById('loading').innerHTML = '<p class="text-red-500">Failed to load assessment</p>';
        }
    } catch (err) { 
        console.error('Error loading assessment:', err); 
        document.getElementById('loading').innerHTML = '<p class="text-red-500">Network error</p>';
    }
}

function renderAssessment() {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('content').classList.remove('hidden');
    document.getElementById('title').textContent = assessment.title;
    document.getElementById('description').textContent = assessment.description || '';
    document.getElementById('questionsCount').textContent = assessment.questions?.length || 0;
    document.getElementById('candidatesCount').textContent = assessment.invitees_count || assessment.total_invites || 0;
    document.getElementById('completedCount').textContent = assessment.completed_count || 0;
    document.getElementById('avgScore').textContent = assessment.avg_score ? parseFloat(assessment.avg_score).toFixed(1) + '%' : '-';
    
    if (['active', 'scheduled', 'completed'].includes(assessment.status)) {
        document.getElementById('publishBtn').textContent = assessment.status.charAt(0).toUpperCase() + assessment.status.slice(1);
        document.getElementById('publishBtn').disabled = true;
        document.getElementById('publishBtn').classList.replace('bg-emerald-600', 'bg-slate-400');
    }
    
    // Show public link if access_code exists
    if (assessment.access_code) {
        const linkSection = document.getElementById('publicLinkSection');
        linkSection.classList.remove('hidden');
        document.getElementById('publicLinkUrl').textContent = window.location.origin + '/join/' + assessment.access_code;
    }
    
    renderQuestions();
    loadInvitees();
}

function renderQuestions() {
    const list = document.getElementById('questionsList');
    if (!assessment.questions?.length) {
        list.innerHTML = '<p class="text-slate-500 text-center py-8">No questions yet. Add your first question.</p>';
        return;
    }
    list.innerHTML = assessment.questions.map((q, i) => `
        <div class="flex items-center justify-between p-4 border-b last:border-0">
            <div class="flex-1">
                <span class="font-bold text-indigo-600 mr-2">Q${i + 1}.</span>
                <span>${q.question_text}</span>
                <span class="ml-2 text-xs px-2 py-0.5 bg-slate-100 rounded text-slate-500">${q.question_type.replace('_', ' ')}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-slate-500">${q.points} pts</span>
                <button onclick='editQuestion(${JSON.stringify(q).replace(/'/g, "&apos;")})' class="text-indigo-500 hover:text-indigo-700" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button onclick="deleteQuestion('${q.id}')" class="text-red-500 hover:text-red-700" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    `).join('');
}

async function loadInvitees() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            const list = document.getElementById('inviteesList');
            if (!data.data?.length) {
                list.innerHTML = '<p class="text-slate-500 text-center py-8">No candidates invited yet.</p>';
                document.getElementById('resendAllBtn').classList.add('hidden');
                return;
            }
            // Show resend all button if assessment is published
            if (['active', 'scheduled'].includes(assessment?.status)) {
                document.getElementById('resendAllBtn').classList.remove('hidden');
            }

            const statusColors = {
                pending: 'bg-slate-100 text-slate-600',
                sent: 'bg-blue-100 text-blue-700',
                opened: 'bg-purple-100 text-purple-700',
                started: 'bg-amber-100 text-amber-700',
                completed: 'bg-emerald-100 text-emerald-700',
                failed: 'bg-red-100 text-red-700',
            };

            // Build a single meaningful status label
            function inviteeStatusBadge(inv) {
                // If test started/completed, show that
                if (['started', 'completed'].includes(inv.status)) {
                    return `<span class="px-2 py-1 rounded text-xs font-medium ${statusColors[inv.status]}">${inv.status}</span>`;
                }
                // Otherwise show email delivery status
                const emailLabel = inv.email_status || 'pending';
                const emailColors = { pending: 'bg-slate-100 text-slate-500', queued: 'bg-yellow-100 text-yellow-700', sent: 'bg-blue-100 text-blue-700', failed: 'bg-red-100 text-red-600' };
                return `<span class="px-2 py-1 rounded text-xs font-medium ${emailColors[emailLabel] || 'bg-slate-100 text-slate-500'}">${emailLabel === 'sent' ? '✉ sent' : emailLabel}</span>`;
            }

            list.innerHTML = data.data.map(inv => `
                <div class="flex items-center justify-between p-3 border-b last:border-0 group hover:bg-slate-50">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium truncate">${inv.first_name || inv.last_name ? `${inv.first_name || ''} ${inv.last_name || ''}`.trim() : ''}</p>
                        <p class="text-sm text-slate-500 truncate">${inv.email}</p>
                    </div>
                    <div class="flex items-center gap-2 ml-3">
                        ${inviteeStatusBadge(inv)}
                        ${inv.test_session?.percentage != null ? `<span class="text-sm font-semibold ${inv.test_session.passed ? 'text-emerald-400' : 'text-red-400'}">${parseFloat(inv.test_session.percentage).toFixed(1)}%</span>` : ''}
                        ${!['started', 'completed'].includes(inv.status) ? `
                            <button onclick="openEditInvitee('${inv.id}', '${inv.email}', '${inv.first_name || ''}', '${inv.last_name || ''}')" class="text-indigo-500 hover:text-indigo-700 opacity-0 group-hover:opacity-100 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="resendInvite('${inv.id}')" class="text-blue-500 hover:text-blue-700 opacity-0 group-hover:opacity-100 transition" title="Resend">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </button>
                            <button onclick="deleteInvitee('${inv.id}')" class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        }
    } catch (err) {}
}

let currentType = 'single_choice';

function onTypeChange(type) {
    currentType = type;
    const optionsContainer = document.getElementById('optionsContainer');
    const textContainer = document.getElementById('textAnswerContainer');
    const hint = document.getElementById('optionsHint');

    if (type === 'text_input') {
        optionsContainer.classList.add('hidden');
        textContainer.classList.remove('hidden');
    } else {
        optionsContainer.classList.remove('hidden');
        textContainer.classList.add('hidden');
        hint.textContent = type === 'multiple_choice'
            ? 'Select one or more correct answers (but not all)'
            : 'Select the one correct answer';
        // Reset and add default options
        document.getElementById('optionsList').innerHTML = '';
        optionCount = 0;
        const defaults = type === 'multiple_choice' ? 3 : 2;
        for (let i = 0; i < defaults; i++) addOption();
    }
}

let editingQuestionId = null;

function openQuestionModal() {
    editingQuestionId = null;
    document.getElementById('questionModalTitle').textContent = 'Add Question';
    document.getElementById('questionModal').classList.remove('hidden');
    document.getElementById('questionForm').reset();
    document.getElementById('questionError').classList.add('hidden');
    currentType = 'single_choice';
    document.querySelector('[name="type"]').value = 'single_choice';
    onTypeChange('single_choice');
    checkQuestionForm();
}

function editQuestion(q) {
    editingQuestionId = q.id;
    document.getElementById('questionModalTitle').textContent = 'Edit Question';
    document.getElementById('questionModal').classList.remove('hidden');
    document.getElementById('questionError').classList.add('hidden');
    
    // Pre-populate
    document.querySelector('[name="text"]').value = q.question_text;
    document.querySelector('[name="points"]').value = q.points;
    document.querySelector('[name="type"]').value = q.question_type;
    currentType = q.question_type;
    onTypeChange(q.question_type);
    
    if (q.question_type === 'text_input') {
        document.querySelector('[name="expected_answer"]').value = q.expected_answer || '';
    } else if (q.options) {
        document.getElementById('optionsList').innerHTML = '';
        optionCount = 0;
        q.options.forEach((opt, i) => {
            addOption();
            document.querySelector(`[name="option_${i}"]`).value = opt.option_text || opt.text || '';
            if (q.question_type === 'multiple_choice') {
                const cb = document.querySelector(`[name="correct_${i}"]`);
                if (cb) cb.checked = opt.is_correct;
            } else {
                if (opt.is_correct) {
                    const radio = document.querySelector(`[name="correct_option"][value="${i}"]`);
                    if (radio) radio.checked = true;
                }
            }
        });
    }
    checkQuestionForm();
}

function closeQuestionModal() {
    editingQuestionId = null;
    document.getElementById('questionModal').classList.add('hidden');
    document.getElementById('optionsList').innerHTML = '';
    optionCount = 0;
}
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
    document.getElementById('addCandidateBtn').disabled = true;
}
function closeInviteModal() { document.getElementById('inviteModal').classList.add('hidden'); }

// Progressive form validation
function checkInviteForm() {
    const email = document.querySelector('#inviteForm [name="email"]').value.trim();
    document.getElementById('addCandidateBtn').disabled = !email || !/\S+@\S+\.\S+/.test(email);
}
function checkQuestionForm() {
    const text = document.querySelector('#questionForm [name="text"]').value.trim();
    document.getElementById('saveQuestionBtn').disabled = !text;
}

let optionCount = 0;
function addOption() {
    const list = document.getElementById('optionsList');
    const inputType = currentType === 'multiple_choice' ? 'checkbox' : 'radio';
    const name = currentType === 'multiple_choice' ? `correct_${optionCount}` : 'correct_option';
    list.insertAdjacentHTML('beforeend', `
        <div class="flex gap-2 items-center">
            <input type="${inputType}" name="${name}" value="${optionCount}" class="correct-marker w-4 h-4 accent-indigo-600">
            <input type="text" name="option_${optionCount}" placeholder="Option ${optionCount + 1}" class="flex-1 px-3 py-2 border rounded-lg">
            ${optionCount >= 2 ? `<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-lg">&times;</button>` : ''}
        </div>
    `);
    optionCount++;
}

document.getElementById('questionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const errEl = document.getElementById('questionError');
    errEl.classList.add('hidden');
    const type = form.type.value;

    let body = {
        question_type: type,
        question_text: form.text.value,
        points: parseInt(form.points.value),
    };

    if (type === 'text_input') {
        const expected = form.expected_answer.value.trim();
        if (!expected) {
            errEl.textContent = 'Please enter the expected answer.';
            errEl.classList.remove('hidden');
            return;
        }
        body.expected_answer = expected;
        body.options = [];
    } else {
        const options = [];
        for (let i = 0; i < optionCount; i++) {
            const opt = form[`option_${i}`]?.value;
            if (!opt) continue;
            let isCorrect;
            if (type === 'multiple_choice') {
                isCorrect = form[`correct_${i}`]?.checked || false;
            } else {
                isCorrect = form.correct_option.value == i;
            }
            options.push({ text: opt, is_correct: isCorrect });
        }
        const correctCount = options.filter(o => o.is_correct).length;
        if (correctCount === 0) {
            errEl.textContent = 'Please select at least one correct answer.';
            errEl.classList.remove('hidden');
            return;
        }
        if (type === 'multiple_choice' && correctCount === options.length) {
            errEl.textContent = 'You cannot mark all options as correct.';
            errEl.classList.remove('hidden');
            return;
        }
        body.options = options;
    }

    try {
        const url = editingQuestionId 
            ? `/api/assessments/${assessmentId}/questions/${editingQuestionId}`
            : `/api/assessments/${assessmentId}/questions`;
        const method = editingQuestionId ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        if (res.ok) { 
            closeQuestionModal(); 
            toastSuccess(editingQuestionId ? 'Question updated!' : 'Question added!');
            loadAssessment(); 
        } else {
            const data = await res.json();
            errEl.textContent = data.message || 'Failed to save question.';
            errEl.classList.remove('hidden');
        }
    } catch (err) {
        errEl.textContent = 'Network error.';
        errEl.classList.remove('hidden');
    }
});

document.getElementById('inviteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                emails: [form.email.value],
                first_name: form.first_name?.value || null,
                last_name: form.last_name?.value || null,
            })
        });
        const result = await res.json();
        if (res.ok) { 
            closeInviteModal(); 
            loadInvitees(); 
            loadAssessment();
            form.reset();
            const msg = `Added ${result.created} invitee(s)${result.skipped > 0 ? `, ${result.skipped} already existed` : ''}`;
            if (result.skipped > 0) { toastError(msg); } else { toastSuccess(msg); }
        } else {
            toastError(result.message || 'Failed to add invitee');
        }
    } catch (err) {
        console.error('Error adding invitee:', err);
        toastError('Network error. Please try again.');
    }
});

// === CSV Upload Functions ===

// Invite CSV Tab Switching
function switchInviteTab(tab) {
    const manualTab = document.getElementById('inviteManualTab');
    const csvTab = document.getElementById('inviteCsvTab');
    const tabManual = document.getElementById('inviteTabManual');
    const tabCsv = document.getElementById('inviteTabCsv');
    if (tab === 'manual') {
        manualTab.classList.remove('hidden'); csvTab.classList.add('hidden');
        tabManual.classList.add('border-indigo-600','text-indigo-600'); tabManual.classList.remove('border-transparent','text-gray-500');
        tabCsv.classList.remove('border-indigo-600','text-indigo-600'); tabCsv.classList.add('border-transparent','text-gray-500');
    } else {
        csvTab.classList.remove('hidden'); manualTab.classList.add('hidden');
        tabCsv.classList.add('border-indigo-600','text-indigo-600'); tabCsv.classList.remove('border-transparent','text-gray-500');
        tabManual.classList.remove('border-indigo-600','text-indigo-600'); tabManual.classList.add('border-transparent','text-gray-500');
    }
}

let inviteCsvFile = null;

function handleInviteCsvDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-indigo-500','bg-indigo-50');
    const file = e.dataTransfer.files[0];
    if (file) previewInviteCsv(file);
}

function handleInviteCsvSelect(input) {
    if (input.files[0]) previewInviteCsv(input.files[0]);
}

function previewInviteCsv(file) {
    inviteCsvFile = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        const lines = e.target.result.split('\n').filter(l => l.trim());
        const rows = [];
        let startIdx = 0;
        // Check for header
        const firstCell = lines[0]?.split(',')[0]?.trim().toLowerCase();
        if (['email','email_address','e-mail','mail'].includes(firstCell)) startIdx = 1;
        for (let i = startIdx; i < lines.length && rows.length < 100; i++) {
            const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
            if (cols[0] && cols[0].includes('@')) {
                rows.push({ email: cols[0], first_name: cols[1] || '', last_name: cols[2] || '' });
            }
        }
        const tbody = document.getElementById('inviteCsvRows');
        tbody.innerHTML = rows.map(r => `<tr class="border-t"><td class="px-3 py-1">${r.email}</td><td class="px-3 py-1">${r.first_name}</td><td class="px-3 py-1">${r.last_name}</td></tr>`).join('');
        document.getElementById('inviteCsvCount').textContent = rows.length;
        document.getElementById('inviteCsvPreview').classList.remove('hidden');
        document.getElementById('inviteCsvDropzone').classList.add('hidden');
        document.getElementById('uploadInviteCsvBtn').disabled = rows.length === 0;
    };
    reader.readAsText(file);
}

function clearInviteCsv() {
    inviteCsvFile = null;
    document.getElementById('inviteCsvInput').value = '';
    document.getElementById('inviteCsvPreview').classList.add('hidden');
    document.getElementById('inviteCsvDropzone').classList.remove('hidden');
    document.getElementById('uploadInviteCsvBtn').disabled = true;
}

async function uploadInviteCsv() {
    if (!inviteCsvFile) return;
    const btn = document.getElementById('uploadInviteCsvBtn');
    btn.disabled = true; btn.textContent = 'Uploading...';
    try {
        const formData = new FormData();
        formData.append('csv', inviteCsvFile);
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            body: formData
        });
        const result = await res.json();
        if (res.ok) {
            closeInviteModal();
            clearInviteCsv();
            loadInvitees();
            loadAssessment();
            toastSuccess(`Added ${result.created} invitee(s)${result.skipped > 0 ? `, ${result.skipped} duplicates skipped` : ''}`);
        } else {
            toastError(result.message || 'CSV upload failed');
        }
    } catch (err) {
        toastError('Network error. Please try again.');
    }
    btn.disabled = false; btn.textContent = 'Upload Invitees';
}

function downloadInviteTemplate() {
    const csv = 'email,first_name,last_name\njohn@example.com,John,Doe\njane@example.com,Jane,Smith\n';
    downloadCsvBlob(csv, 'invitee_template.csv');
}

// Question CSV Import
function openQuestionImportModal() {
    document.getElementById('questionImportModal').classList.remove('hidden');
}
function closeQuestionImportModal() {
    document.getElementById('questionImportModal').classList.add('hidden');
    clearQuestionCsv();
}

let questionCsvFile = null;

function handleQuestionCsvDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-indigo-500','bg-indigo-50');
    const file = e.dataTransfer.files[0];
    if (file) previewQuestionCsv(file);
}

function handleQuestionCsvSelect(input) {
    if (input.files[0]) previewQuestionCsv(input.files[0]);
}

function previewQuestionCsv(file) {
    questionCsvFile = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        const lines = e.target.result.split('\n').filter(l => l.trim());
        const rows = [];
        let startIdx = 0;
        const firstCell = lines[0]?.split(',')[0]?.trim().toLowerCase();
        if (['question','question_text','text','q'].includes(firstCell)) startIdx = 1;
        for (let i = startIdx; i < lines.length && rows.length < 50; i++) {
            const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
            if (cols[0]) {
                rows.push({ text: cols[0].substring(0, 60) + (cols[0].length > 60 ? '...' : ''), type: cols[1] || 'single_choice', points: cols[2] || '1' });
            }
        }
        const tbody = document.getElementById('questionCsvRows');
        tbody.innerHTML = rows.map(r => `<tr class="border-t"><td class="px-3 py-1">${r.text}</td><td class="px-3 py-1">${r.type}</td><td class="px-3 py-1">${r.points}</td></tr>`).join('');
        document.getElementById('questionCsvCount').textContent = rows.length;
        document.getElementById('questionCsvPreview').classList.remove('hidden');
        document.getElementById('questionCsvDropzone').classList.add('hidden');
        document.getElementById('uploadQuestionCsvBtn').disabled = rows.length === 0;
    };
    reader.readAsText(file);
}

function clearQuestionCsv() {
    questionCsvFile = null;
    document.getElementById('questionCsvInput').value = '';
    document.getElementById('questionCsvPreview').classList.add('hidden');
    document.getElementById('questionCsvDropzone').classList.remove('hidden');
    document.getElementById('uploadQuestionCsvBtn').disabled = true;
    document.getElementById('questionImportErrors').classList.add('hidden');
}

async function uploadQuestionCsv() {
    if (!questionCsvFile) return;
    const btn = document.getElementById('uploadQuestionCsvBtn');
    btn.disabled = true; btn.textContent = 'Importing...';
    try {
        const formData = new FormData();
        formData.append('csv', questionCsvFile);
        const res = await fetch(`/api/assessments/${assessmentId}/questions/import`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            body: formData
        });
        const result = await res.json();
        if (res.ok) {
            if (result.errors && result.errors.length > 0) {
                const errEl = document.getElementById('questionImportErrors');
                errEl.innerHTML = `<p class="font-medium text-amber-700 mb-1">${result.created} imported, ${result.errors.length} skipped:</p><ul class="list-disc list-inside text-xs">${result.errors.map(e => `<li>${e}</li>`).join('')}</ul>`;
                errEl.classList.remove('hidden');
            }
            if (result.created > 0) {
                toastSuccess(`Imported ${result.created} question(s)`);
                loadAssessment();
            }
            if (!result.errors || result.errors.length === 0) {
                closeQuestionImportModal();
            }
        } else {
            toastError(result.message || 'CSV import failed');
        }
    } catch (err) {
        toastError('Network error. Please try again.');
    }
    btn.disabled = false; btn.textContent = 'Import Questions';
}

function downloadQuestionTemplate() {
    const csv = 'question_text,question_type,points,option_a,option_b,option_c,option_d,correct_answer,expected_answer\nWhat is 2+2?,single_choice,1,3,4,5,6,B,\nSelect all prime numbers,multiple_choice,2,2,4,5,6,"A,C",\nWhat is the capital of France?,text_input,1,,,,,,,Paris\n';
    downloadCsvBlob(csv, 'question_template.csv');
}

function downloadCsvBlob(content, filename) {
    const blob = new Blob([content], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

async function deleteQuestion(qid) {
    const confirmed = await showConfirm('Delete Question', 'Are you sure you want to delete this question? This action cannot be undone.', 'Delete', 'danger');
    if (!confirmed) return;
    try {
        await fetch(`/api/assessments/${assessmentId}/questions/${qid}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        toastSuccess('Question deleted successfully');
        loadAssessment();
    } catch (err) {
        toastError('Failed to delete question');
    }
}

function copyPublicLink() {
    const url = document.getElementById('publicLinkUrl').textContent;
    navigator.clipboard.writeText(url).then(() => toastSuccess('Link copied!'));
}

async function publishAssessment() {
    const confirmed = await showConfirm('Publish Assessment', 'Are you ready to publish? Invitation emails will be dispatched to all candidates.', 'Publish', 'primary');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/publish`, { 
            method: 'POST', 
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } 
        });
        const data = await res.json();
        if (res.ok) {
            toastSuccess(data.message || 'Assessment published!');
            loadAssessment();
        } else {
            // Show specific validation errors
            const errors = data.errors || {};
            const msg = Object.values(errors).flat().join(' ') || data.message || 'Failed to publish.';
            toastError(msg);
        }
    } catch (err) {
        toastError('Failed to publish assessment');
    }
}

function editAssessment() { window.location.href = `/assessments/${assessmentId}/edit`; }

// --- Invitee Actions ---
async function deleteInvitee(id) {
    const confirmed = await showConfirm('Remove Candidate', 'Remove this candidate from the assessment?', 'Remove', 'danger');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message); loadInvitees(); loadAssessment(); }
        else { toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed'); }
    } catch { toastError('Network error'); }
}

async function resendInvite(id) {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}/resend`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message); loadInvitees(); }
        else { toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed'); }
    } catch { toastError('Network error'); }
}

async function resendAll() {
    const confirmed = await showConfirm('Resend All Invitations', 'Resend invitation emails to all pending candidates?', 'Resend All', 'primary');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/send`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message || `${data.sent} invitations queued`); loadInvitees(); }
        else { toastError(data.message || 'Failed'); }
    } catch { toastError('Network error'); }
}

function openEditInvitee(id, email, firstName, lastName) {
    document.getElementById('editInviteeId').value = id;
    document.getElementById('editInvEmail').value = email;
    document.getElementById('editInvFirstName').value = firstName;
    document.getElementById('editInvLastName').value = lastName;
    document.getElementById('editInviteeModal').classList.remove('hidden');
}

document.getElementById('editInviteeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('editInviteeId').value;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('editInvEmail').value,
                first_name: document.getElementById('editInvFirstName').value || null,
                last_name: document.getElementById('editInvLastName').value || null,
            }),
        });
        const data = await res.json();
        if (res.ok) {
            document.getElementById('editInviteeModal').classList.add('hidden');
            toastSuccess(data.message);
            loadInvitees();
        } else {
            toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed');
        }
    } catch { toastError('Network error'); }
});

loadAssessment();
</script>
@endsection
