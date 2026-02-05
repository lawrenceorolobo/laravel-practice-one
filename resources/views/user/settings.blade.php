@extends('layouts.user')
@section('title', 'Settings | Quizly')

@section('content')
<div class="max-w-3xl">
    <div class="mb-8">
        <h2 class="text-2xl font-bold">Settings</h2>
        <p class="text-slate-500">Manage your account and preferences</p>
    </div>

    <!-- Profile -->
    <div class="glass rounded-2xl p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Profile</h3>
        <form id="profileForm" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">First Name</label>
                    <input type="text" name="first_name" id="firstName" class="w-full px-4 py-3 border rounded-xl">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Last Name</label>
                    <input type="text" name="last_name" id="lastName" class="w-full px-4 py-3 border rounded-xl">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" disabled class="w-full px-4 py-3 border rounded-xl bg-slate-50">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Company</label>
                <input type="text" name="company_name" id="company" class="w-full px-4 py-3 border rounded-xl">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700">Save Changes</button>
        </form>
    </div>

    <!-- Password -->
    <div class="glass rounded-2xl p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Change Password</h3>
        <form id="passwordForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Current Password</label>
                <input type="password" name="current_password" class="w-full px-4 py-3 border rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">New Password</label>
                <input type="password" name="new_password" class="w-full px-4 py-3 border rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" class="w-full px-4 py-3 border rounded-xl">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-3 rounded-xl font-semibold hover:bg-slate-900">Update Password</button>
        </form>
    </div>

    <!-- Subscription -->
    <div class="glass rounded-2xl p-6 mb-6">
        <h3 class="font-bold text-lg mb-4">Subscription</h3>
        <div id="subscriptionInfo" class="flex items-center justify-between">
            <div>
                <p class="font-medium" id="planName">Loading...</p>
                <p class="text-sm text-slate-500" id="planStatus"></p>
            </div>
            <a href="/pricing" class="text-indigo-600 font-medium hover:text-indigo-700">Upgrade Plan</a>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="border-2 border-red-200 rounded-2xl p-6">
        <h3 class="font-bold text-lg text-red-600 mb-4">Danger Zone</h3>
        <p class="text-slate-600 mb-4">Once you delete your account, there is no going back.</p>
        <button onclick="deleteAccount()" class="bg-red-50 text-red-600 px-6 py-3 rounded-xl font-semibold hover:bg-red-100">Delete Account</button>
    </div>
</div>

<div id="toast" class="hidden fixed bottom-4 right-4 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg">Saved!</div>
@endsection

@section('scripts')
<script>
// Load user data
document.getElementById('firstName').value = user.first_name || '';
document.getElementById('lastName').value = user.last_name || '';
document.getElementById('email').value = user.email || '';
document.getElementById('company').value = user.company_name || '';

// Load subscription
async function loadSubscription() {
    try {
        const res = await fetch('/api/subscription/status', { headers: { 'Authorization': `Bearer ${token}` } });
        if (res.ok) {
            const data = await res.json();
            document.getElementById('planName').textContent = data.plan?.name || 'Free Plan';
            document.getElementById('planStatus').textContent = data.status === 'active' ? 'Active' : 'Inactive';
        }
    } catch (err) {
        document.getElementById('planName').textContent = 'Free Plan';
    }
}
loadSubscription();

document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    try {
        const res = await fetch('/api/auth/me', {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                first_name: form.first_name.value,
                last_name: form.last_name.value,
                company_name: form.company_name.value
            })
        });
        if (res.ok) {
            const { data } = await res.json();
            localStorage.setItem('user', JSON.stringify(data));
            showToast('Profile updated!');
        }
    } catch (err) {}
});

document.getElementById('passwordForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    if (form.new_password.value !== form.confirm_password.value) {
        toastError('Passwords do not match');
        return;
    }
    try {
        const res = await fetch('/api/auth/password', {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
            body: JSON.stringify({
                current_password: form.current_password.value,
                new_password: form.new_password.value
            })
        });
        if (res.ok) {
            form.reset();
            showToast('Password updated!');
        } else {
            const data = await res.json();
            toastError(data.message || 'Failed to update password');
        }
    } catch (err) {}
});

function showToast(msg) {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

async function deleteAccount() {
    const confirmed = await showConfirm('Delete Account', 'Are you absolutely sure? This will permanently delete all your data including assessments, questions, and candidate records.', 'Delete My Account', 'danger');
    if (!confirmed) return;
    try {
        await fetch('/api/auth/me', { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        localStorage.clear();
        window.location.href = '/';
    } catch (err) {
        toastError('Failed to delete account');
    }
}
</script>
@endsection
