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

    <!-- Questions -->
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Questions</h3>
            <button onclick="openQuestionModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Question
            </button>
        </div>
        <div id="questionsList"></div>
    </div>

    <!-- Invitees -->
    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Candidates</h3>
            <button onclick="openInviteModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Invite Candidates
            </button>
        </div>
        <div id="inviteesList"></div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="questionModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Add Question</h3>
        <form id="questionForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Type</label>
                <select name="type" class="w-full px-4 py-2 border rounded-lg">
                    <option value="single_choice">Single Choice</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="text_input">Text Input</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text *</label>
                <textarea name="text" required rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
            </div>
            <div id="optionsContainer" class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <div id="optionsList" class="space-y-2"></div>
                <button type="button" onclick="addOption()" class="mt-2 text-indigo-600 font-medium text-sm">+ Add Option</button>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Points</label>
                <input type="number" name="points" value="1" min="1" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold">Save Question</button>
                <button type="button" onclick="closeQuestionModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Invite Modal -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full">
        <h3 class="text-xl font-bold mb-4">Invite Candidates</h3>
        <form id="inviteForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email *</label>
                <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="candidate@example.com">
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
                <button type="submit" class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-bold">Add Candidate</button>
                <button type="button" onclick="closeInviteModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
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
    document.getElementById('candidatesCount').textContent = assessment.invitees_count || 0;
    document.getElementById('completedCount').textContent = assessment.completed_count || 0;
    document.getElementById('avgScore').textContent = assessment.avg_score ? assessment.avg_score + '%' : '-';
    
    if (assessment.status === 'published') {
        document.getElementById('publishBtn').textContent = 'Published';
        document.getElementById('publishBtn').disabled = true;
        document.getElementById('publishBtn').classList.replace('bg-emerald-600', 'bg-slate-400');
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
            <div>
                <span class="font-bold text-indigo-600 mr-2">Q${i + 1}.</span>
                <span>${q.question_text}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-slate-500">${q.points} pts</span>
                <button onclick="deleteQuestion('${q.id}')" class="text-red-500 hover:text-red-700">
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
                return;
            }
            list.innerHTML = data.data.map(inv => `
                <div class="flex items-center justify-between p-3 border-b last:border-0">
                    <div>
                        <p class="font-medium">${inv.first_name || ''} ${inv.last_name || ''}</p>
                        <p class="text-sm text-slate-500">${inv.email}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-xs font-medium ${inv.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'}">${inv.status}</span>
                </div>
            `).join('');
        }
    } catch (err) {}
}

function openQuestionModal() { document.getElementById('questionModal').classList.remove('hidden'); addOption(); addOption(); }
function closeQuestionModal() { document.getElementById('questionModal').classList.add('hidden'); document.getElementById('optionsList').innerHTML = ''; }
function openInviteModal() { document.getElementById('inviteModal').classList.remove('hidden'); }
function closeInviteModal() { document.getElementById('inviteModal').classList.add('hidden'); }

let optionCount = 0;
function addOption() {
    const list = document.getElementById('optionsList');
    list.insertAdjacentHTML('beforeend', `
        <div class="flex gap-2 items-center">
            <input type="radio" name="correct_option" value="${optionCount}">
            <input type="text" name="option_${optionCount}" placeholder="Option ${optionCount + 1}" class="flex-1 px-3 py-2 border rounded-lg">
        </div>
    `);
    optionCount++;
}

document.getElementById('questionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const options = [];
    for (let i = 0; i < optionCount; i++) {
        const opt = form[`option_${i}`]?.value;
        if (opt) options.push({ text: opt, is_correct: form.correct_option.value == i });
    }
    
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/questions`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                question_type: form.type.value, 
                question_text: form.text.value, 
                points: parseInt(form.points.value), 
                options 
            })
        });
        if (res.ok) { closeQuestionModal(); loadAssessment(); }
    } catch (err) {}
});

document.getElementById('inviteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                emails: [form.email.value] 
            })
        });
        const result = await res.json();
        if (res.ok) { 
            closeInviteModal(); 
            loadInvitees(); 
            form.reset();
            toastSuccess(`Added ${result.created} invitee(s)${result.skipped > 0 ? `, ${result.skipped} already existed` : ''}`);
        } else {
            toastError(result.message || 'Failed to add invitee');
        }
    } catch (err) {
        console.error('Error adding invitee:', err);
        toastError('Network error. Please try again.');
    }
});

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

async function publishAssessment() {
    const confirmed = await showConfirm('Publish Assessment', 'Are you ready to publish this assessment? Candidates will be able to take it once published.', 'Publish', 'primary');
    if (!confirmed) return;
    try {
        await fetch(`/api/assessments/${assessmentId}/publish`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}` } });
        toastSuccess('Assessment published successfully!');
        loadAssessment();
    } catch (err) {
        toastError('Failed to publish assessment');
    }
}

function editAssessment() { window.location.href = `/assessments/${assessmentId}/edit`; }

loadAssessment();
</script>
@endsection
