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
    </div>

    <div id="loading" class="glass rounded-2xl p-6"><div class="skeleton h-6 w-1/2 mb-4"></div><div class="skeleton h-4 w-1/3"></div></div>

    <form id="editForm" class="hidden space-y-6">
        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-3 border rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-xl"></textarea>
                </div>
            </div>
        </div>

        <div class="glass rounded-2xl p-6">
            <h3 class="font-bold text-lg mb-4">Settings</h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" min="5" class="w-full px-4 py-3 border rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Passing Score (%)</label>
                    <input type="number" name="passing_score" min="0" max="100" class="w-full px-4 py-3 border rounded-xl">
                </div>
            </div>
        </div>

        <div id="errorMsg" class="hidden p-4 bg-red-50 text-red-600 rounded-xl"></div>

        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold">Save Changes</button>
            <button type="button" onclick="deleteAssessment()" class="px-8 py-4 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100">Delete</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
const id = window.location.pathname.split('/')[2];

async function loadAssessment() {
    const res = await fetch(`/api/assessments/${id}`, { headers: { 'Authorization': `Bearer ${token}` } });
    if (res.ok) {
        const { data } = await res.json();
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('editForm').classList.remove('hidden');
        document.querySelector('[name="title"]').value = data.title;
        document.querySelector('[name="description"]').value = data.description || '';
        document.querySelector('[name="duration_minutes"]').value = data.duration_minutes;
        document.querySelector('[name="passing_score"]').value = data.passing_score;
    }
}

document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const res = await fetch(`/api/assessments/${id}`, {
        method: 'PUT',
        headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
        body: JSON.stringify({
            title: form.title.value,
            description: form.description.value,
            duration_minutes: parseInt(form.duration_minutes.value),
            passing_score: parseInt(form.passing_score.value)
        })
    });
    if (res.ok) window.location.href = `/assessments/${id}`;
    else document.getElementById('errorMsg').textContent = 'Failed to save', document.getElementById('errorMsg').classList.remove('hidden');
});

async function deleteAssessment() {
    const confirmed = await showConfirm('Delete Assessment', 'Are you sure you want to delete this assessment? All questions and candidate data will be permanently removed.', 'Delete', 'danger');
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
