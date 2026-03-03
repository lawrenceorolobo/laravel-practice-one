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

                <button type="submit" id="startBtn" disabled
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Start Assessment
                </button>
            </form>

            <div id="regError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mt-4 hidden"></div>
        </div>
    </div>

    <!-- Quiz State -->
    <div id="quizState" class="hidden">
        <!-- Top Bar -->
        <div class="bg-white border-b sticky top-0 z-50 px-4 sm:px-6 py-3">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <div class="min-w-0">
                    <h1 class="font-semibold text-slate-900 truncate" id="quizTitle">Assessment</h1>
                    <p class="text-sm text-slate-500">Question <span id="currentQ">1</span> of <span id="totalQ">-</span></p>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <div id="timer" class="bg-red-100 text-red-700 px-3 py-2 rounded-lg font-mono font-medium text-sm">00:00</div>
                    <button onclick="submitTest()" class="bg-indigo-600 text-white px-3 sm:px-4 py-2 rounded-lg font-medium hover:bg-indigo-700 text-sm">
                        Submit
                    </button>
                </div>
            </div>
        </div>

        <!-- Auto-End Warning Banner -->
        <div id="autoEndBanner" class="hidden bg-red-600 text-white text-center py-2 text-sm font-medium">
            ⚠ Leaving this tab will automatically end your test
        </div>

        <!-- Webcam Overlay (shown when webcam_required) -->
        <div id="webcamOverlay" class="hidden fixed bottom-4 right-4 z-40 rounded-xl overflow-hidden shadow-2xl border-2 border-slate-700" style="width:180px;height:135px;">
            <video id="webcamVideo" autoplay muted playsinline class="w-full h-full object-cover bg-black"></video>
            <div class="absolute top-2 left-2 flex items-center gap-1">
                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                <span class="text-xs text-white font-medium drop-shadow">REC</span>
            </div>
        </div>

        <!-- Main Content: Sidebar + Question -->
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-4 p-4 sm:p-6">

            <!-- Question Navigation Panel -->
            <div class="lg:w-56 flex-shrink-0 order-2 lg:order-1">
                <div class="bg-white rounded-xl shadow-md border border-slate-200 p-4 lg:sticky lg:top-24">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3">Questions</h3>
                    <div id="questionNav" class="flex flex-wrap lg:flex-col gap-2">
                        <!-- Question number buttons rendered by JS -->
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 space-y-1">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Answered
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="w-3 h-3 rounded-full bg-slate-200 inline-block"></span> Unanswered
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 inline-block"></span> Current
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-slate-400 text-center">
                        <span id="answeredCount">0</span>/<span id="totalCount">0</span> answered
                    </div>
                </div>
            </div>

            <!-- Question Card -->
            <div class="flex-1 order-1 lg:order-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="bg-indigo-100 text-indigo-700 text-sm font-semibold px-3 py-1 rounded-full" id="questionBadge">Q1</span>
                        <span class="text-sm text-slate-400" id="questionType"></span>
                    </div>
                    <p class="text-lg font-medium text-slate-900 mb-6" id="questionText">Loading question...</p>

                    <div id="optionsContainer" class="space-y-3">
                        <!-- Options rendered here -->
                    </div>

                    <div id="textAnswerContainer" class="hidden">
                        <textarea id="textAnswer" rows="4" 
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"
                            placeholder="Type your answer here..."></textarea>
                    </div>

                    <!-- Numeric Input -->
                    <div id="numericContainer" class="hidden">
                        <input type="number" id="numericAnswer" step="any"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 text-lg"
                            placeholder="Enter your numeric answer...">
                    </div>

                    <!-- Fill in Blank -->
                    <div id="fillBlankContainer" class="hidden">
                        <input type="text" id="fillBlankAnswer"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500"
                            placeholder="Fill in the blank...">
                    </div>

                    <!-- Ordering / Drag Sort -->
                    <div id="orderingContainer" class="hidden">
                        <p class="text-sm text-slate-500 mb-3">Drag items to reorder, or use the arrows:</p>
                        <div id="orderingList" class="space-y-2"></div>
                    </div>

                    <!-- Matching -->
                    <div id="matchingContainer" class="hidden">
                        <p class="text-sm text-slate-500 mb-3">Match each item on the left with the correct item on the right:</p>
                        <div id="matchingPairs" class="space-y-3"></div>
                    </div>

                    <!-- Likert Scale -->
                    <div id="likertContainer" class="hidden">
                        <div id="likertScale" class="flex justify-between gap-2 mt-2"></div>
                    </div>

                    <!-- Pattern / Visual (shows image from metadata) -->
                    <div id="patternContainer" class="hidden">
                        <div id="patternVisual" class="flex justify-center mb-6"></div>
                        <div id="patternOptions" class="grid grid-cols-2 sm:grid-cols-3 gap-3"></div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between mt-6">
                    <button id="prevBtn" onclick="prevQuestion()" 
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        ← Previous
                    </button>
                    <button id="nextBtn" onclick="nextQuestion()" 
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700">
                        Next →
                    </button>
                </div>
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
let assessmentData = null;
let questions = [];
let currentIndex = 0;
let answers = {};
let timeRemaining = 0;
let timerInterval = null;
let allowBackNav = true;
let confirmResolve = null;
let autoEndOnLeave = false;
let webcamRequired = false;
let webcamStream = null;
let mediaRecorder = null;
let recordedChunks = [];
let isSubmitting = false;

const CLOUDINARY_CLOUD = '{{ config("services.cloudinary.cloud_name", "") }}';
const CLOUDINARY_PRESET = '{{ config("services.cloudinary.upload_preset", "") }}';

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

// Proctoring — auto-end on tab switch
document.addEventListener('visibilitychange', () => {
    if (document.hidden && sessionId && !isSubmitting) {
        logProctoring('tab_switch');
        if (autoEndOnLeave) {
            // Immediately submit via sendBeacon (works even when tab is closing)
            const url = `/api/test/submit/${TOKEN}`;
            navigator.sendBeacon(url);
            isSubmitting = true;
            clearInterval(timerInterval);
            if (webcamStream) { webcamStream.getTracks().forEach(t => t.stop()); }
        }
    }
});

// Also handle tab/window close
window.addEventListener('beforeunload', (e) => {
    if (sessionId && autoEndOnLeave && !isSubmitting) {
        navigator.sendBeacon(`/api/test/submit/${TOKEN}`);
        isSubmitting = true;
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
            timeRemaining = Math.floor(data.time_remaining);
            assessmentData = data.assessment;
            showQuiz(data.assessment);
            loadQuestions();
        } else {
            assessmentData = data.assessment;
            showRegistration(data.assessment, data);
        }
    } catch (err) {
        showError('Network error. Please refresh.');
    }
}

function showRegistration(assessment, data) {
    hideAll();
    document.getElementById('registrationState').classList.remove('hidden');
    document.getElementById('assessmentTitle').textContent = assessment.title;
    document.getElementById('assessmentDesc').textContent = assessment.description || 'Please enter your details to begin.';
    document.getElementById('duration').textContent = assessment.duration_minutes + ' minutes';

    // Prefill name if invitee has name data — make readonly
    const fnInput = document.getElementById('firstName');
    const lnInput = document.getElementById('lastName');
    if (data?.first_name) {
        fnInput.value = data.first_name;
        fnInput.readOnly = true;
        fnInput.classList.add('bg-slate-100', 'cursor-not-allowed');
    }
    if (data?.last_name) {
        lnInput.value = data.last_name;
        lnInput.readOnly = true;
        lnInput.classList.add('bg-slate-100', 'cursor-not-allowed');
    }
    checkStartForm();
}

function showQuiz(assessment) {
    hideAll();
    document.getElementById('quizState').classList.remove('hidden');
    document.getElementById('quizTitle').textContent = assessment?.title || assessmentData?.title || 'Assessment';
    
    // Auto-end on leave
    autoEndOnLeave = assessment?.auto_end_on_leave || false;
    if (autoEndOnLeave) {
        document.getElementById('autoEndBanner').classList.remove('hidden');
    }
    
    // Webcam
    webcamRequired = assessment?.webcam_required || false;
    if (webcamRequired) { startWebcam(); }
    
    // Request fullscreen
    if (document.documentElement.requestFullscreen) {
        document.documentElement.requestFullscreen().catch(() => {});
    }
}

async function startWebcam() {
    try {
        webcamStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        const video = document.getElementById('webcamVideo');
        video.srcObject = webcamStream;
        document.getElementById('webcamOverlay').classList.remove('hidden');

        // Start recording if Cloudinary is configured
        if (CLOUDINARY_CLOUD && CLOUDINARY_PRESET) {
            try {
                const mimeType = MediaRecorder.isTypeSupported('video/webm;codecs=vp9')
                    ? 'video/webm;codecs=vp9'
                    : MediaRecorder.isTypeSupported('video/webm')
                        ? 'video/webm'
                        : 'video/mp4';

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(webcamStream, { mimeType, videoBitsPerSecond: 500000 });

                mediaRecorder.ondataavailable = (e) => {
                    if (e.data && e.data.size > 0) recordedChunks.push(e.data);
                };

                // Record in 10-second chunks for reliability
                mediaRecorder.start(10000);
                console.log('Webcam recording started');
            } catch (recErr) {
                console.warn('MediaRecorder not available:', recErr);
            }
        }
    } catch (err) {
        if (webcamRequired) {
            toastError('Webcam access is required for this assessment. Please enable your camera and refresh.');
        }
    }
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

// Progressive form validation — enable Start button only when both names filled
function checkStartForm() {
    const fn = document.getElementById('firstName').value.trim();
    const ln = document.getElementById('lastName').value.trim();
    document.getElementById('startBtn').disabled = !(fn && ln);
}
document.getElementById('firstName').addEventListener('input', checkStartForm);
document.getElementById('lastName').addEventListener('input', checkStartForm);

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
        showQuiz(assessmentData);
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
        timeRemaining = Math.floor(data.time_remaining);
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
    document.getElementById('questionBadge').textContent = 'Q' + (currentIndex + 1);
    const typeLabels = {single_choice:'Single Choice',multiple_choice:'Multiple Choice',text_input:'Text Answer',true_false:'True/False',ordering:'Ordering',matching:'Matching',fill_blank:'Fill in Blank',numeric:'Numeric',sequence_pattern:'Pattern Sequence',matrix_pattern:'Matrix',odd_one_out:'Odd One Out',spatial_rotation:'Spatial',shape_assembly:'Shape Assembly',analogy:'Analogy',drag_drop_sort:'Drag & Drop',hotspot:'Hotspot',code_snippet:'Code',likert_scale:'Rating',pattern_recognition:'Pattern Recognition',mental_maths:'Mental Maths',word_problem:'Word Problem'};
    document.getElementById('questionType').textContent = typeLabels[q.question_type] || q.question_type;

    // Hide all containers
    const containers = ['optionsContainer','textAnswerContainer','numericContainer','fillBlankContainer','orderingContainer','matchingContainer','likertContainer','patternContainer'];
    containers.forEach(id => document.getElementById(id).classList.add('hidden'));

    const type = q.question_type;
    const textTypes = ['text_input', 'code_snippet', 'word_problem', 'mental_maths'];
    const choiceTypes = ['single_choice', 'multiple_choice', 'true_false', 'odd_one_out', 'analogy'];
    const patternTypes = ['sequence_pattern', 'matrix_pattern', 'spatial_rotation', 'shape_assembly', 'pattern_recognition', 'hotspot'];

    if (textTypes.includes(type)) {
        document.getElementById('textAnswerContainer').classList.remove('hidden');
        document.getElementById('textAnswer').value = answers[q.id]?.text || '';
        document.getElementById('textAnswer').placeholder = type === 'code_snippet' ? 'Write your code here...' : type === 'mental_maths' ? 'Enter your calculated answer...' : type === 'word_problem' ? 'Show your working and answer...' : 'Type your answer here...';
    } else if (type === 'numeric') {
        document.getElementById('numericContainer').classList.remove('hidden');
        document.getElementById('numericAnswer').value = answers[q.id]?.text || '';
    } else if (type === 'fill_blank') {
        document.getElementById('fillBlankContainer').classList.remove('hidden');
        document.getElementById('fillBlankAnswer').value = answers[q.id]?.text || '';
    } else if (type === 'ordering' || type === 'drag_drop_sort') {
        document.getElementById('orderingContainer').classList.remove('hidden');
        renderOrderingQuestion(q);
    } else if (type === 'matching') {
        document.getElementById('matchingContainer').classList.remove('hidden');
        renderMatchingQuestion(q);
    } else if (type === 'likert_scale') {
        document.getElementById('likertContainer').classList.remove('hidden');
        renderLikertQuestion(q);
    } else if (patternTypes.includes(type)) {
        document.getElementById('patternContainer').classList.remove('hidden');
        renderPatternQuestion(q);
    } else if (choiceTypes.includes(type)) {
        document.getElementById('optionsContainer').classList.remove('hidden');
        const selectedOpts = answers[q.id]?.options || [];
        const isMultiple = type === 'multiple_choice';

        // For true_false, override options if none exist
        let opts = q.options;
        if (type === 'true_false' && (!opts || opts.length === 0)) {
            opts = [{label: 'A', text: 'True'}, {label: 'B', text: 'False'}];
        }

        document.getElementById('optionsContainer').innerHTML = (opts || []).map(opt => `
            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 transition ${selectedOpts.includes(opt.label) ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200'}">
                <input type="${isMultiple ? 'checkbox' : 'radio'}" name="option" value="${opt.label}" ${selectedOpts.includes(opt.label) ? 'checked' : ''} onchange="selectOption('${opt.label}')" class="mr-3 text-indigo-600 focus:ring-indigo-500">
                <span class="font-medium mr-2">${opt.label}.</span>
                ${opt.media_url ? `<img src="${opt.media_url}" class="w-16 h-16 object-contain rounded mr-3" alt="">` : ''}
                <span>${opt.text}</span>
            </label>
        `).join('');
    } else {
        // Fallback: show as options if they exist, or text input
        if (q.options && q.options.length > 0) {
            document.getElementById('optionsContainer').classList.remove('hidden');
            const selectedOpts = answers[q.id]?.options || [];
            document.getElementById('optionsContainer').innerHTML = q.options.map(opt => `
                <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-slate-50 transition ${selectedOpts.includes(opt.label) ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200'}">
                    <input type="radio" name="option" value="${opt.label}" ${selectedOpts.includes(opt.label) ? 'checked' : ''} onchange="selectOption('${opt.label}')" class="mr-3 text-indigo-600 focus:ring-indigo-500">
                    <span class="font-medium mr-2">${opt.label}.</span>
                    <span>${opt.text}</span>
                </label>
            `).join('');
        } else {
            document.getElementById('textAnswerContainer').classList.remove('hidden');
            document.getElementById('textAnswer').value = answers[q.id]?.text || '';
        }
    }

    document.getElementById('prevBtn').disabled = !allowBackNav || currentIndex === 0;
    document.getElementById('nextBtn').textContent = currentIndex === questions.length - 1 ? 'Finish' : 'Next →';
    renderQuestionNav();
}

// Ordering / Drag-Drop Sort
function renderOrderingQuestion(q) {
    const saved = answers[q.id]?.ordering;
    let items = saved ? saved : q.options.map(o => ({label: o.label, text: o.text}));

    const list = document.getElementById('orderingList');
    list.innerHTML = items.map((item, i) => `
        <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg" data-idx="${i}">
            <span class="text-sm font-bold text-slate-400 w-6">${i + 1}.</span>
            <span class="flex-1 font-medium text-slate-700">${item.text}</span>
            <div class="flex flex-col gap-0.5">
                <button onclick="moveOrderItem(${i}, -1)" class="p-1 hover:bg-slate-200 rounded text-slate-500 ${i === 0 ? 'opacity-30' : ''}" ${i === 0 ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                </button>
                <button onclick="moveOrderItem(${i}, 1)" class="p-1 hover:bg-slate-200 rounded text-slate-500 ${i === items.length - 1 ? 'opacity-30' : ''}" ${i === items.length - 1 ? 'disabled' : ''}>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>
        </div>
    `).join('');
}

function moveOrderItem(idx, dir) {
    const q = questions[currentIndex];
    if (!answers[q.id]) answers[q.id] = {};
    let items = answers[q.id].ordering || q.options.map(o => ({label: o.label, text: o.text}));
    const newIdx = idx + dir;
    if (newIdx < 0 || newIdx >= items.length) return;
    [items[idx], items[newIdx]] = [items[newIdx], items[idx]];
    answers[q.id].ordering = items;
    renderOrderingQuestion(q);
    saveAnswer(q.id);
    renderQuestionNav();
}

// Matching
function renderMatchingQuestion(q) {
    const meta = q.question_metadata || {};
    const left = meta.left_items || q.options.map(o => o.text);
    const right = meta.right_items || q.options.map(o => o.label);
    const saved = answers[q.id]?.matching || {};

    document.getElementById('matchingPairs').innerHTML = left.map((item, i) => {
        const key = `item_${i}`;
        return `
            <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                <span class="flex-1 font-medium text-slate-700 text-sm">${item}</span>
                <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                <select onchange="matchItem('${key}', this.value)" class="px-3 py-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-indigo-500 min-w-[140px]">
                    <option value="">Select...</option>
                    ${right.map(r => `<option value="${r}" ${saved[key] === r ? 'selected' : ''}>${r}</option>`).join('')}
                </select>
            </div>
        `;
    }).join('');
}

function matchItem(key, value) {
    const q = questions[currentIndex];
    if (!answers[q.id]) answers[q.id] = {};
    if (!answers[q.id].matching) answers[q.id].matching = {};
    answers[q.id].matching[key] = value;
    saveAnswer(q.id);
    renderQuestionNav();
}

// Likert Scale
function renderLikertQuestion(q) {
    const meta = q.question_metadata || {};
    const labels = meta.scale_labels || ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'];
    const saved = answers[q.id]?.text || '';

    document.getElementById('likertScale').innerHTML = labels.map((label, i) => {
        const val = String(i + 1);
        return `
            <label class="flex-1 cursor-pointer">
                <div class="flex flex-col items-center gap-2 p-3 border rounded-lg transition ${saved === val ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 hover:bg-slate-50'}">
                    <input type="radio" name="likert" value="${val}" ${saved === val ? 'checked' : ''} onchange="selectLikert('${val}')" class="text-indigo-600">
                    <span class="text-xs text-center text-slate-600 leading-tight">${label}</span>
                </div>
            </label>
        `;
    }).join('');
}

function selectLikert(val) {
    const q = questions[currentIndex];
    if (!answers[q.id]) answers[q.id] = {};
    answers[q.id].text = val;
    saveAnswer(q.id);
    renderLikertQuestion(q);
    renderQuestionNav();
}

// Pattern / Visual Types
function renderPatternQuestion(q) {
    const meta = q.question_metadata || {};
    const visual = document.getElementById('patternVisual');
    const optionsGrid = document.getElementById('patternOptions');

    // Show pattern image or grid
    if (meta.image_url) {
        visual.innerHTML = `<img src="${meta.image_url}" class="max-h-64 rounded-lg border border-slate-200 shadow-sm" alt="Pattern">`;
    } else if (meta.grid) {
        visual.innerHTML = renderPatternGrid(meta.grid);
    } else {
        visual.innerHTML = '<p class="text-slate-400 text-sm">Visual pattern displayed here</p>';
    }

    // Render option cards with images or text
    const selected = answers[q.id]?.options || [];
    optionsGrid.innerHTML = (q.options || []).map(opt => `
        <button onclick="selectOption('${opt.label}')" 
            class="p-4 border-2 rounded-xl text-center transition-all ${selected.includes(opt.label) ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-slate-200 hover:border-slate-300'}">
            ${opt.media_url ? `<img src="${opt.media_url}" class="w-full h-24 object-contain mb-2 rounded" alt="">` : ''}
            <span class="font-semibold text-sm">${opt.label}</span>
            ${opt.text ? `<span class="block text-xs text-slate-500 mt-1">${opt.text}</span>` : ''}
        </button>
    `).join('');
}

function renderPatternGrid(grid) {
    if (!Array.isArray(grid)) return '';
    const colors = { 0: '#f1f5f9', 1: '#3b82f6', 2: '#ef4444', 3: '#22c55e', 4: '#f59e0b', 5: '#8b5cf6', '?': '#e2e8f0' };
    return `<div class="inline-grid gap-1 p-3 bg-white border border-slate-200 rounded-lg shadow-sm" style="grid-template-columns: repeat(${grid[0]?.length || 3}, 2.5rem)">
        ${grid.flat().map(cell => `<div class="w-10 h-10 rounded flex items-center justify-center text-sm font-bold" style="background:${colors[cell] || '#f1f5f9'}">${cell === '?' ? '?' : ''}</div>`).join('')}
    </div>`;
}

function renderQuestionNav() {
    const nav = document.getElementById('questionNav');
    let cnt = 0;
    nav.innerHTML = questions.map((q, i) => {
        const ans = answers[q.id];
        const isAnswered = ans && (
            (ans.options && ans.options.length > 0) || 
            ans.text || 
            ans.ordering || 
            (ans.matching && Object.keys(ans.matching).length > 0)
        );
        if (isAnswered) cnt++;
        const isCur = i === currentIndex;
        let cls = 'w-9 h-9 rounded-lg text-sm font-semibold flex items-center justify-center cursor-pointer transition-all ';
        if (isCur) cls += 'bg-indigo-600 text-white ring-2 ring-indigo-300';
        else if (isAnswered) cls += 'bg-emerald-500 text-white';
        else cls += 'bg-slate-100 text-slate-600 hover:bg-slate-200';
        return `<button onclick="jumpToQuestion(${i})" class="${cls}">${i+1}</button>`;
    }).join('');
    document.getElementById('answeredCount').textContent = cnt;
    document.getElementById('totalCount').textContent = questions.length;
}

function jumpToQuestion(index) {
    saveCurrentAnswer();
    if (!allowBackNav && index < currentIndex) {
        toastWarning('Back navigation is disabled.');
        return;
    }
    currentIndex = index;
    renderQuestion();
}

function saveCurrentAnswer() {
    const q = questions[currentIndex];
    const type = q.question_type;
    const textTypes = ['text_input', 'code_snippet', 'word_problem', 'mental_maths'];

    if (textTypes.includes(type)) {
        if (!answers[q.id]) answers[q.id] = {};
        answers[q.id].text = document.getElementById('textAnswer').value;
        if (answers[q.id].text) saveAnswer(q.id);
    } else if (type === 'numeric') {
        if (!answers[q.id]) answers[q.id] = {};
        answers[q.id].text = document.getElementById('numericAnswer').value;
        if (answers[q.id].text) saveAnswer(q.id);
    } else if (type === 'fill_blank') {
        if (!answers[q.id]) answers[q.id] = {};
        answers[q.id].text = document.getElementById('fillBlankAnswer').value;
        if (answers[q.id].text) saveAnswer(q.id);
    }
    // ordering, matching, likert save immediately on interaction
}

function selectOption(label) {
    const q = questions[currentIndex];
    const isMultiple = q.question_type === 'multiple_choice';
    
    if (!answers[q.id]) answers[q.id] = { options: [] };
    if (!answers[q.id].options) answers[q.id].options = [];
    
    if (isMultiple) {
        const idx = answers[q.id].options.indexOf(label);
        if (idx > -1) answers[q.id].options.splice(idx, 1);
        else answers[q.id].options.push(label);
    } else {
        answers[q.id].options = [label];
    }
    
    saveAnswer(q.id);
    renderQuestion(); // Re-render for visual feedback
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
                ordering: answer.ordering || null,
                matching: answer.matching || null,
            }),
        });
    } catch (err) {
        console.error('Failed to save answer');
    }
}

function nextQuestion() {
    saveCurrentAnswer();
    
    if (currentIndex < questions.length - 1) {
        currentIndex++;
        renderQuestion();
    } else {
        submitTest();
    }
}

function prevQuestion() {
    if (allowBackNav && currentIndex > 0) {
        saveCurrentAnswer();
        currentIndex--;
        renderQuestion();
    }
}

async function submitTest() {
    const confirmed = await showConfirm('Submit Assessment', 'Are you sure you want to submit? You cannot change your answers after submission.', 'Submit', 'primary');
    if (!confirmed) return;
    
    try {
        // Stop webcam recording and upload
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            await stopAndUploadRecording();
        }

        const res = await fetch(`/api/test/submit/${TOKEN}`, {method: 'POST'});
        const data = await res.json();
        
        clearInterval(timerInterval);
        stopWebcam();
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

function stopWebcam() {
    if (webcamStream) {
        webcamStream.getTracks().forEach(t => t.stop());
        webcamStream = null;
    }
}

async function stopAndUploadRecording() {
    return new Promise((resolve) => {
        mediaRecorder.onstop = async () => {
            if (recordedChunks.length === 0) { resolve(); return; }

            const blob = new Blob(recordedChunks, { type: mediaRecorder.mimeType });
            recordedChunks = [];

            try {
                toastInfo('Uploading proctoring recording...');
                const formData = new FormData();
                formData.append('file', blob, `proctoring_${sessionId}.webm`);
                formData.append('upload_preset', CLOUDINARY_PRESET);
                formData.append('folder', 'quizly/proctoring');
                formData.append('resource_type', 'video');

                const uploadRes = await fetch(`https://api.cloudinary.com/v1_1/${CLOUDINARY_CLOUD}/video/upload`, {
                    method: 'POST',
                    body: formData,
                });

                if (uploadRes.ok) {
                    const uploadData = await uploadRes.json();
                    // Save to backend
                    await fetch(`/api/test/recording/${TOKEN}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            recording_url: uploadData.secure_url,
                            recording_id: uploadData.public_id,
                        }),
                    });
                    console.log('Recording uploaded:', uploadData.secure_url);
                } else {
                    console.error('Cloudinary upload failed');
                }
            } catch (err) {
                console.error('Recording upload error:', err);
            }
            resolve();
        };

        mediaRecorder.stop();
    });
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
