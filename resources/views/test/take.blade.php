<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $assessment->title ?? 'Assessment' }} | Quizly</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-100 min-h-screen">

<div id="app" class="min-h-screen">
    <!-- Loading State -->
    <div id="loadingState" class="min-h-screen flex items-center justify-center">
        <div class="text-center">
            <svg class="w-12 h-12 text-indigo-600 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-slate-600">Loading assessment...</p>
        </div>
    </div>

    <!-- Registration State -->
    <div id="registrationState" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-slate-900" id="assessmentTitle">Assessment</h1>
                <p class="text-slate-600 mt-2" id="assessmentDesc">Please enter your details to begin.</p>
            </div>

            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-600">Duration:</span>
                    <span class="font-medium text-slate-900" id="duration">-</span>
                </div>
            </div>

            <form id="startForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">First name</label>
                        <input type="text" id="firstName" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Last name</label>
                        <input type="text" id="lastName" required
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-sm text-amber-800">
                    <strong>Important:</strong> This test will monitor tab switching and fullscreen exits. Do not switch tabs or exit fullscreen during the assessment.
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition">
                    Start Assessment
                </button>
            </form>

            <div id="regError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mt-4 hidden"></div>
        </div>
    </div>

    <!-- Quiz State -->
    <div id="quizState" class="hidden">
        <!-- Top Bar -->
        <div class="bg-white border-b sticky top-0 z-50 px-6 py-4">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div>
                    <h1 class="font-semibold text-slate-900" id="quizTitle">Assessment</h1>
                    <p class="text-sm text-slate-500">Question <span id="currentQ">1</span> of <span id="totalQ">-</span></p>
                </div>
                <div class="flex items-center gap-4">
                    <div id="timer" class="bg-red-100 text-red-700 px-4 py-2 rounded-lg font-mono font-medium">00:00</div>
                    <button onclick="submitTest()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-indigo-700">
                        Submit Test
                    </button>
                </div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="max-w-4xl mx-auto p-6">
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <p class="text-lg font-medium text-slate-900 mb-6" id="questionText">Loading question...</p>

                <div id="optionsContainer" class="space-y-3">
                    <!-- Options rendered here -->
                </div>

                <div id="textAnswerContainer" class="hidden">
                    <textarea id="textAnswer" rows="4" 
                        class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"
                        placeholder="Type your answer here..."></textarea>
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex justify-between mt-6">
                <button id="prevBtn" onclick="prevQuestion()" 
                    class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    Previous
                </button>
                <button id="nextBtn" onclick="nextQuestion()" 
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700">
                    Next Question
                </button>
            </div>
        </div>
    </div>

    <!-- Completed State -->
    <div id="completedState" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Assessment Completed!</h2>
            <p class="text-slate-600 mb-6">Your responses have been submitted successfully.</p>
            
            <div id="scoreCard" class="hidden bg-indigo-50 rounded-lg p-6 mb-6">
                <p class="text-slate-600">Your Score</p>
                <p class="text-4xl font-bold text-indigo-600 mt-2" id="finalScore">-</p>
                <p id="passStatus" class="mt-2 font-medium"></p>
            </div>

            <a href="/" class="text-indigo-600 hover:text-indigo-700 font-medium">Return to homepage</a>
        </div>
    </div>

    <!-- Error State -->
    <div id="errorState" class="hidden min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Unable to Access Assessment</h2>
            <p class="text-slate-600" id="errorMessage">This assessment is not available.</p>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer" class="fixed top-6 right-6 z-[100] flex flex-col gap-3"></div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-[110] items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target === this) closeConfirmModal(false)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform scale-100 transition-all">
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 id="confirmTitle" class="text-lg font-bold text-slate-900">Confirm Action</h3>
                    <p id="confirmMessage" class="text-sm text-slate-600">Are you sure you want to proceed?</p>
                </div>
            </div>
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closeConfirmModal(false)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold border-2 border-slate-200 text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button id="confirmBtn" onclick="closeConfirmModal(true)" class="flex-1 px-5 py-2.5 rounded-lg font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition-all">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
const TOKEN = '{{ $token ?? "" }}';
let sessionId = null;
let questions = [];
let currentIndex = 0;
let answers = {};
let timeRemaining = 0;
let timerInterval = null;
let allowBackNav = true;
let confirmResolve = null;

// Toast Notification System
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    
    const icons = {
        success: `<svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
        error: `<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
        warning: `<svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`,
        info: `<svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
    };
    
    const bgColors = {
        success: 'border-emerald-200 bg-gradient-to-r from-emerald-50 to-white',
        error: 'border-red-200 bg-gradient-to-r from-red-50 to-white',
        warning: 'border-amber-200 bg-gradient-to-r from-amber-50 to-white',
        info: 'border-indigo-200 bg-gradient-to-r from-indigo-50 to-white'
    };
    
    toast.className = `flex items-center gap-3 px-5 py-4 rounded-xl shadow-lg border backdrop-blur-sm ${bgColors[type]} transform translate-x-full opacity-0 transition-all duration-300 max-w-sm`;
    toast.innerHTML = `
        <div class="flex-shrink-0">${icons[type]}</div>
        <p class="flex-1 text-sm font-medium text-slate-700">${message}</p>
        <button onclick="this.parentElement.remove()" class="flex-shrink-0 text-slate-400 hover:text-slate-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    `;
    
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.remove('translate-x-full', 'opacity-0'));
    setTimeout(() => { toast.classList.add('translate-x-full', 'opacity-0'); setTimeout(() => toast.remove(), 300); }, duration);
}

function toastSuccess(msg) { showToast(msg, 'success'); }
function toastError(msg) { showToast(msg, 'error'); }
function toastWarning(msg) { showToast(msg, 'warning'); }
function toastInfo(msg) { showToast(msg, 'info'); }

// Confirmation Modal
function showConfirm(title, message, confirmText = 'Confirm', type = 'primary') {
    return new Promise((resolve) => {
        confirmResolve = resolve;
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        const confirmBtn = document.getElementById('confirmBtn');
        confirmBtn.textContent = confirmText;
        confirmBtn.className = `flex-1 px-5 py-2.5 rounded-lg font-semibold transition-all ${
            type === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-indigo-600 hover:bg-indigo-700 text-white'
        }`;
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
    });
}

function closeConfirmModal(result) {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
    if (confirmResolve) { confirmResolve(result); confirmResolve = null; }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    if (!TOKEN) {
        showError('Invalid assessment link.');
        return;
    }
    validateToken();
});

// Proctoring
document.addEventListener('visibilitychange', () => {
    if (document.hidden && sessionId) {
        logProctoring('tab_switch');
    }
});

document.addEventListener('fullscreenchange', () => {
    if (!document.fullscreenElement && sessionId) {
        logProctoring('fullscreen_exit');
    }
});

async function validateToken() {
    try {
        const res = await fetch(`/api/test/validate/${TOKEN}`);
        const data = await res.json();
        
        if (!res.ok || !data.valid) {
            showError(data.message || 'Assessment not available.');
            return;
        }
        
        if (data.resume) {
            sessionId = data.session_id;
            timeRemaining = data.time_remaining;
            showQuiz(data.assessment);
            loadQuestions();
        } else {
            showRegistration(data.assessment);
        }
    } catch (err) {
        showError('Network error. Please refresh.');
    }
}

function showRegistration(assessment) {
    hideAll();
    document.getElementById('registrationState').classList.remove('hidden');
    document.getElementById('assessmentTitle').textContent = assessment.title;
    document.getElementById('assessmentDesc').textContent = assessment.description || 'Please enter your details to begin.';
    document.getElementById('duration').textContent = assessment.duration_minutes + ' minutes';
}

function showQuiz(assessment) {
    hideAll();
    document.getElementById('quizState').classList.remove('hidden');
    document.getElementById('quizTitle').textContent = assessment?.title || 'Assessment';
    
    // Request fullscreen
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(() => {});
    }
    
    startTimer();
}

function showError(message) {
    hideAll();
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;
}

function hideAll() {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('registrationState').classList.add('hidden');
    document.getElementById('quizState').classList.add('hidden');
    document.getElementById('completedState').classList.add('hidden');
    document.getElementById('errorState').classList.add('hidden');
}

// Start form
document.getElementById('startForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorDiv = document.getElementById('regError');
    errorDiv.classList.add('hidden');
    
    try {
        const res = await fetch(`/api/test/start/${TOKEN}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                first_name: document.getElementById('firstName').value,
                last_name: document.getElementById('lastName').value,
                device_fingerprint: getFingerprint(),
            }),
        });
        
        const data = await res.json();
        
        if (!res.ok) {
            errorDiv.textContent = data.errors?.fraud?.[0] || data.message || 'Cannot start assessment.';
            errorDiv.classList.remove('hidden');
            return;
        }
        
        sessionId = data.session_id;
        await loadQuestions();
        showQuiz();
    } catch (err) {
        errorDiv.textContent = 'Network error.';
        errorDiv.classList.remove('hidden');
    }
});

async function loadQuestions() {
    try {
        const res = await fetch(`/api/test/questions/${TOKEN}`);
        const data = await res.json();
        
        if (!res.ok) {
            showError(data.errors?.time?.[0] || data.message || 'Failed to load questions.');
            return;
        }
        
        questions = data.questions;
        timeRemaining = data.time_remaining;
        allowBackNav = data.allow_back_navigation;
        
        document.getElementById('totalQ').textContent = questions.length;
        renderQuestion();
        startTimer();
    } catch (err) {
        showError('Failed to load questions.');
    }
}

function renderQuestion() {
    const q = questions[currentIndex];
    document.getElementById('currentQ').textContent = currentIndex + 1;
    document.getElementById('questionText').textContent = q.question_text;
    
    const optionsContainer = document.getElementById('optionsContainer');
    const textContainer = document.getElementById('textAnswerContainer');
    
    if (q.question_type === 'text_input') {
        optionsContainer.classList.add('hidden');
        textContainer.classList.remove('hidden');
        document.getElementById('textAnswer').value = answers[q.id]?.text || '';
    } else {
        textContainer.classList.add('hidden');
        optionsContainer.classList.remove('hidden');
        
        const selectedOpts = answers[q.id]?.options || [];
        const isMultiple = q.question_type === 'multiple_choice';
        
        optionsContainer.innerHTML = q.options.map(opt => `
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 transition ${selectedOpts.includes(opt.label) ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200'}">
                <input type="${isMultiple ? 'checkbox' : 'radio'}" 
                    name="option" value="${opt.label}" 
                    ${selectedOpts.includes(opt.label) ? 'checked' : ''}
                    onchange="selectOption('${opt.label}')"
                    class="mr-3 text-indigo-600 focus:ring-indigo-500">
                <span class="font-medium mr-2">${opt.label}.</span>
                <span>${opt.text}</span>
            </label>
        `).join('');
    }
    
    // Navigation visibility
    document.getElementById('prevBtn').disabled = !allowBackNav || currentIndex === 0;
    document.getElementById('nextBtn').textContent = currentIndex === questions.length - 1 ? 'Finish' : 'Next Question';
}

function selectOption(label) {
    const q = questions[currentIndex];
    const isMultiple = q.question_type === 'multiple_choice';
    
    if (!answers[q.id]) answers[q.id] = { options: [] };
    
    if (isMultiple) {
        const idx = answers[q.id].options.indexOf(label);
        if (idx > -1) answers[q.id].options.splice(idx, 1);
        else answers[q.id].options.push(label);
    } else {
        answers[q.id].options = [label];
    }
    
    saveAnswer(q.id);
}

async function saveAnswer(questionId) {
    const q = questions.find(q => q.id === questionId);
    const answer = answers[questionId] || {};
    
    try {
        await fetch(`/api/test/answer/${TOKEN}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                question_id: questionId,
                selected_options: answer.options || null,
                text_answer: answer.text || null,
            }),
        });
    } catch (err) {
        console.error('Failed to save answer');
    }
}

function nextQuestion() {
    // Save text answer if applicable
    const q = questions[currentIndex];
    if (q.question_type === 'text_input') {
        if (!answers[q.id]) answers[q.id] = {};
        answers[q.id].text = document.getElementById('textAnswer').value;
        saveAnswer(q.id);
    }
    
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        renderQuestion();
    } else {
        submitTest();
    }
}

function prevQuestion() {
    if (allowBackNav && currentIndex > 0) {
        currentIndex--;
        renderQuestion();
    }
}

async function submitTest() {
    const confirmed = await showConfirm('Submit Assessment', 'Are you sure you want to submit? You cannot change your answers after submission.', 'Submit', 'primary');
    if (!confirmed) return;
    
    try {
        const res = await fetch(`/api/test/submit/${TOKEN}`, {method: 'POST'});
        const data = await res.json();
        
        clearInterval(timerInterval);
        hideAll();
        document.getElementById('completedState').classList.remove('hidden');
        
        if (data.score) {
            document.getElementById('scoreCard').classList.remove('hidden');
            document.getElementById('finalScore').textContent = data.score.percentage + '%';
            const passEl = document.getElementById('passStatus');
            passEl.textContent = data.score.passed ? '✓ Passed' : '✗ Did not pass';
            passEl.className = 'mt-2 font-medium ' + (data.score.passed ? 'text-green-600' : 'text-red-600');
        }
        
        // Exit fullscreen
        if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
    } catch (err) {
        toastError('Failed to submit. Please try again.');
    }
}

function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    
    timerInterval = setInterval(() => {
        timeRemaining--;
        const mins = Math.floor(timeRemaining / 60);
        const secs = timeRemaining % 60;
        document.getElementById('timer').textContent = `${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
        
        if (timeRemaining <= 60) {
            document.getElementById('timer').classList.add('animate-pulse');
        }
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            toastWarning('Time is up! Your answers are being submitted.');
            submitTest();
        }
    }, 1000);
}

async function logProctoring(eventType) {
    try {
        await fetch(`/api/test/proctoring/${TOKEN}`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({event_type: eventType}),
        });
    } catch (err) {}
}

function getFingerprint() {
    return btoa(navigator.userAgent + screen.width + screen.height + new Date().getTimezoneOffset());
}
</script>

</body>
</html>
