@extends('layouts.user')
@section('title', 'Edit Assessment | Quizly')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-8">
        <a href="javascript:history.back()" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <h2 class="text-2xl font-bold">Edit Assessment</h2>
        <p class="text-slate-500">Update assessment details and reschedule</p>
    </div>

    <div id="loading" class="glass rounded-2xl p-6"><div class="skeleton h-6 w-1/2 mb-4"></div><div class="skeleton h-4 w-1/3"></div></div>

    <form id="editForm" class="hidden space-y-6">
        <!-- Basic Info -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500"></textarea>
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
                    <input type="number" name="duration_minutes" required min="5" max="480" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Pass Percentage (%) *</label>
                    <input type="number" name="pass_percentage" required min="0" max="100" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="shuffle_questions" id="shuffle" class="w-5 h-5 rounded">
                    <label for="shuffle">Shuffle questions</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="show_result_to_taker" id="results" class="w-5 h-5 rounded">
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
                    <label for="sendanswers">Send detailed answers to candidates</label>
                </div>
            </div>
        </div>

        <!-- Proctoring -->
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Proctoring</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="proctoring_enabled" id="proctoring" class="w-5 h-5 rounded">
                    <label for="proctoring">Enable proctoring</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="webcam_required" id="webcam" class="w-5 h-5 rounded">
                    <label for="webcam">Require webcam</label>
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
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">Save Changes</button>
            <button type="button" onclick="deleteAssessment()" class="px-8 py-4 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100">Delete</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
const id = window.location.pathname.split('/')[2];

function toLocalDatetime(dt) {
    if (!dt) return '';
    const d = new Date(dt);
    const offset = d.getTimezoneOffset();
    const local = new Date(d.getTime() - offset * 60000);
    return local.toISOString().slice(0, 16);
}

async function loadAssessment() {
    try {
        const res = await fetch(`/api/assessments/${id}`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        if (!res.ok) { toastError('Failed to load assessment'); return; }
        const { data } = await res.json();
        
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('editForm').classList.remove('hidden');
        
        // Text fields
        document.querySelector('[name="title"]').value = data.title || '';
        document.querySelector('[name="description"]').value = data.description || '';
        document.querySelector('[name="duration_minutes"]').value = data.duration_minutes || 30;
        document.querySelector('[name="pass_percentage"]').value = data.pass_percentage || 70;
        
        // Datetime fields
        document.querySelector('[name="start_datetime"]').value = toLocalDatetime(data.start_datetime);
        document.querySelector('[name="end_datetime"]').value = toLocalDatetime(data.end_datetime);
        
        // Checkboxes
        const checks = {
            shuffle_questions: data.shuffle_questions,
            show_result_to_taker: data.show_result_to_taker,
            allow_back_navigation: data.allow_back_navigation,
            shuffle_options: data.shuffle_options,
            proctoring_enabled: data.proctoring_enabled,
            webcam_required: data.webcam_required,
            fullscreen_required: data.fullscreen_required,
            auto_end_on_leave: data.auto_end_on_leave,
            send_answers_to_taker: data.send_answers_to_taker,
        };
        Object.entries(checks).forEach(([name, val]) => {
            const el = document.querySelector(`[name="${name}"]`);
            if (el) el.checked = !!val;
        });
    } catch (err) {
        toastError('Network error loading assessment');
    }
}

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const errorMsg = document.getElementById('errorMsg');
    btn.disabled = true;
    btn.textContent = 'Saving...';
    errorMsg.classList.add('hidden');
    
    const payload = {
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
        const res = await fetch(`/api/assessments/${id}`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (res.ok) {
            toastSuccess('Assessment updated!');
            setTimeout(() => window.location.href = `/assessments/${id}`, 800);
        } else {
            if (result.errors) {
                errorMsg.textContent = Object.values(result.errors).flat().join(', ');
            } else {
                errorMsg.textContent = result.message || 'Failed to save';
            }
            errorMsg.classList.remove('hidden');
        }
    } catch (err) {
        errorMsg.textContent = 'Network error. Please try again.';
        errorMsg.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
});

async function deleteAssessment() {
    const confirmed = await showConfirm('Delete Assessment', 'This will permanently remove all questions and candidate data.', 'Delete', 'danger');
    if (!confirmed) return;
    try {
        await fetch(`/api/assessments/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        toastSuccess('Assessment deleted');
        window.location.href = '/assessments';
    } catch (err) {
        toastError('Failed to delete assessment');
    }
}

loadAssessment();
</script>
@endsection
