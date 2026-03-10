@extends('layouts.user')
@section('title', 'Create Assessment | Quizly')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="/assessments" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Assessments
        </a>
        <h2 class="text-2xl font-bold">Create Assessment</h2>
        <p class="text-slate-500">Set up a new assessment for candidates</p>
    </div>

    <form id="createForm" class="space-y-6">
        <!-- Start from Template -->
        <div class="glass rounded-2xl p-6" id="templateSection">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-lg">Start from Template</h3>
                    <p class="text-slate-500 text-sm">Clone a pre-built assessment with questions ready to go</p>
                </div>
                <button type="button" onclick="document.getElementById('templateGrid').classList.toggle('hidden')" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">
                    Show/Hide
                </button>
            </div>
            <div id="templateGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="col-span-full text-center text-slate-400 py-8">Loading templates...</div>
            </div>
        </div>

        <div class="relative flex items-center gap-4 py-2">
            <div class="flex-1 border-t border-slate-200"></div>
            <span class="text-sm text-slate-400 font-medium">or create from scratch</span>
            <div class="flex-1 border-t border-slate-200"></div>
        </div>

        <!-- Basic Info -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="e.g., Senior Developer Assessment">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500" placeholder="Describe what this assessment covers..."></textarea>
                </div>
            </div>
        </div>

        <!-- Schedule -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Schedule</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Start Date & Time *</label>
                    <input type="datetime-local" name="start_datetime" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">End Date & Time *</label>
                    <input type="datetime-local" name="end_datetime" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Settings -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Settings</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" required min="5" max="480" value="30" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Pass Percentage (%) *</label>
                    <input type="number" name="pass_percentage" required min="0" max="100" value="70" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="shuffle_questions" id="shuffle" checked class="w-5 h-5 rounded">
                    <label for="shuffle">Shuffle questions</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="show_result_to_taker" id="results" checked class="w-5 h-5 rounded">
                    <label for="results">Show results to candidates</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="allow_back_navigation" id="backnav" class="w-5 h-5 rounded">
                    <label for="backnav">Allow back navigation</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="shuffle_options" id="shuffleopts" class="w-5 h-5 rounded">
                    <label for="shuffleopts">Shuffle answer options</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="send_answers_to_taker" id="sendanswers" class="w-5 h-5 rounded">
                    <label for="sendanswers">Send detailed answers to candidates after test</label>
                </div>
            </div>
        </div>

        <!-- Proctoring -->
        <div class="glass rounded-2xl p-6" id="proctoringSection">
            <h3 class="font-bold text-lg mb-4">Proctoring</h3>
            <div id="proctoringDisabledMsg" class="hidden p-3 bg-amber-50 text-amber-700 rounded-xl text-sm mb-4">
                ⚠️ Proctoring features are disabled by admin. Contact your administrator to enable them.
            </div>
            <div class="space-y-3" id="proctoringCheckboxes">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="proctoring_enabled" id="proctoring" class="w-5 h-5 rounded">
                    <label for="proctoring">Enable proctoring</label>
                </div>
                <div class="flex items-center gap-3" id="webcamRow">
                    <input type="checkbox" name="webcam_required" id="webcam" class="w-5 h-5 rounded">
                    <label for="webcam">Require webcam</label>
                    <span id="webcamDisabledHint" class="text-xs text-amber-600 hidden">(disabled by admin)</span>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="fullscreen_required" id="fullscreen" class="w-5 h-5 rounded">
                    <label for="fullscreen">Require fullscreen</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="auto_end_on_leave" id="autoend" class="w-5 h-5 rounded">
                    <label for="autoend">Auto-end test if candidate switches tab</label>
                </div>
            </div>
        </div>

        <div id="errorMsg" class="hidden p-4 bg-red-50 text-red-600 rounded-xl"></div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">
                Create Assessment
            </button>
            <button type="button" onclick="window.location='/assessments'" class="px-8 py-4 border-2 border-slate-200 rounded-xl font-bold hover:bg-slate-50">
                Cancel
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Set default start date/time to 1 hour from now
const now = new Date();
now.setHours(now.getHours() + 1);
now.setMinutes(0, 0, 0);
const startDefault = now.toISOString().slice(0, 16);

// Set default end date to 7 days from now
const endDate = new Date(now);
endDate.setDate(endDate.getDate() + 7);
const endDefault = endDate.toISOString().slice(0, 16);

document.querySelector('[name="start_datetime"]').value = startDefault;
document.querySelector('[name="end_datetime"]').value = endDefault;

// Fetch feature flags and gate UI
(async function() {
    try {
        const res = await fetch('/api/feature-flags/public');
        const flags = await res.json();

        if (!flags.proctoring_enabled) {
            document.getElementById('proctoringSection').style.display = 'none';
        }

        if (!flags.webcam_recording) {
            document.getElementById('webcamRow').style.display = 'none';
        }

        if (!flags.send_answers_to_taker) {
            document.getElementById('sendanswers').parentElement.style.display = 'none';
        }
    } catch(e) { console.warn('Could not load feature flags'); }
})();

document.getElementById('createForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const errorMsg = document.getElementById('errorMsg');
    
    btn.disabled = true;
    btn.textContent = 'Creating...';
    errorMsg.classList.add('hidden');
    
    const data = {
        title: form.title.value,
        description: form.description.value || null,
        duration_minutes: parseInt(form.duration_minutes.value),
        pass_percentage: parseFloat(form.pass_percentage.value),
        start_datetime: form.start_datetime.value,
        end_datetime: form.end_datetime.value,
        shuffle_questions: form.shuffle_questions.checked,
        shuffle_options: form.shuffle_options.checked,
        show_result_to_taker: form.show_result_to_taker.checked,
        allow_back_navigation: form.allow_back_navigation.checked,
        proctoring_enabled: form.proctoring_enabled.checked,
        webcam_required: form.webcam_required.checked,
        fullscreen_required: form.fullscreen_required.checked,
        auto_end_on_leave: form.auto_end_on_leave.checked,
        send_answers_to_taker: form.send_answers_to_taker.checked,
    };
    
    try {
        const res = await fetch('/api/assessments', {
            method: 'POST',
            headers: { 
                'Authorization': `Bearer ${token}`, 
                'Content-Type': 'application/json', 
                'Accept': 'application/json' 
            },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (res.ok) {
            const assessmentId = result.assessment?.id || result.data?.id;
            window.location.href = `/assessments/${assessmentId}`;
        } else {
            if (result.errors) {
                const errorList = Object.values(result.errors).flat().join(', ');
                errorMsg.textContent = result.message + ' (' + errorList + ')';
            } else {
                errorMsg.textContent = result.message || 'Failed to create assessment';
            }
            errorMsg.classList.remove('hidden');
        }
    } catch (err) {
        console.error('Create assessment error:', err);
        errorMsg.textContent = 'Network error. Please try again.';
        errorMsg.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Create Assessment';
    }
});

// Load templates
(async function loadTemplates() {
    try {
        const res = await fetch('/api/templates', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        const data = await res.json();
        const grid = document.getElementById('templateGrid');

        if (!data.templates || data.templates.length === 0) {
            document.getElementById('templateSection').style.display = 'none';
            return;
        }

        const typeLabels = {
            single_choice: 'Choice', multiple_choice: 'Multi', true_false: 'T/F',
            text_input: 'Text', fill_blank: 'Fill', numeric: 'Numeric',
            ordering: 'Order', drag_drop_sort: 'Drag', matching: 'Match',
            code_snippet: 'Code', likert_scale: 'Likert', sequence_pattern: 'Pattern',
            matrix_pattern: 'Matrix', odd_one_out: 'Odd Out', spatial_rotation: 'Spatial',
            shape_assembly: 'Shape', shape_puzzle: 'Puzzle', analogy: 'Analogy',
            pattern_recognition: 'Pattern', mental_maths: 'Maths', word_problem: 'Word',
            hotspot: 'Hotspot',
        };

        grid.innerHTML = data.templates.map(t => `
            <div class="border-2 border-slate-200 rounded-xl p-4 hover:border-indigo-400 hover:shadow-lg transition-all cursor-pointer group"
                 onclick="cloneTemplate('${t.id}', this)">
                <h4 class="font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition">${t.title}</h4>
                <p class="text-xs text-slate-400 mb-3 line-clamp-2">${t.description || ''}</p>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">${t.questions_count} Q</span>
                    <span class="text-xs text-slate-400">${t.duration_minutes} min</span>
                    <span class="text-xs text-slate-400">${t.pass_percentage}% pass</span>
                </div>
                <div class="flex flex-wrap gap-1">
                    ${t.question_types.slice(0, 5).map(qt => `<span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded">${typeLabels[qt] || qt}</span>`).join('')}
                    ${t.question_types.length > 5 ? `<span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded">+${t.question_types.length - 5}</span>` : ''}
                </div>
                <div class="mt-3 text-center">
                    <span class="text-xs font-bold text-indigo-600 group-hover:text-indigo-700">Use Template →</span>
                </div>
            </div>
        `).join('');
    } catch (e) {
        document.getElementById('templateSection').style.display = 'none';
    }
})();

async function cloneTemplate(templateId, el) {
    const origText = el.querySelector('span:last-child')?.textContent;
    const label = el.querySelector('span:last-child');
    if (label) label.textContent = 'Cloning...';

    try {
        const res = await fetch(`/api/templates/${templateId}/clone`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            window.location.href = `/assessments/${data.assessment.id}`;
        } else {
            toastError(data.message || 'Failed to clone template');
            if (label) label.textContent = origText;
        }
    } catch (err) {
        toastError('Network error');
        if (label) label.textContent = origText;
    }
}
</script>
@endsection
