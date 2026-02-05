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
            // Redirect to the assessment detail/edit page
            const assessmentId = result.assessment?.id || result.data?.id;
            window.location.href = `/assessments/${assessmentId}`;
        } else {
            // Handle validation errors
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
</script>
@endsection
