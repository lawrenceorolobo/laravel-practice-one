@extends('layouts.admin')

@section('title', 'Settings | Admin')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-4xl space-y-4">
    <!-- General Settings -->
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(99,102,241,0.08);">
                <svg class="w-3.5 h-3.5" style="color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div><h2 class="text-[13px] font-semibold" style="color:var(--text-primary);">General Settings</h2><p class="text-[10px]" style="color:var(--text-muted);">Configure your platform</p></div>
        </div>
        <form id="generalForm" class="p-4 lg:p-5 space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Platform Name</label><input type="text" id="platformName" value="Quizly" class="w-full"></div>
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Support Email</label><input type="email" id="supportEmail" value="support@quizly.com" class="w-full"></div>
            </div>
            <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Platform Description</label><textarea id="platformDescription" rows="2" class="w-full" style="resize:vertical;">Enterprise assessment platform for modern businesses.</textarea></div>
            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>

    <!-- Email Settings -->
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(16,185,129,0.08);">
                <svg class="w-3.5 h-3.5" style="color:#10b981" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div><h2 class="text-[13px] font-semibold" style="color:var(--text-primary);">Email Configuration</h2><p class="text-[10px]" style="color:var(--text-muted);">SMTP and email notification settings</p></div>
        </div>
        <form id="emailForm" class="p-4 lg:p-5 space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">SMTP Host</label><input type="text" id="smtpHost" value="{{ env('MAIL_HOST', 'smtp.mailgun.org') }}" class="w-full"></div>
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">SMTP Port</label><input type="text" id="smtpPort" value="{{ env('MAIL_PORT', '587') }}" class="w-full"></div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">From Email</label><input type="email" id="fromEmail" value="{{ env('MAIL_FROM_ADDRESS', 'noreply@quizly.com') }}" class="w-full"></div>
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">From Name</label><input type="text" id="fromName" value="{{ env('MAIL_FROM_NAME', 'Quizly') }}" class="w-full"></div>
            </div>
            <button type="submit" class="btn-primary">Update Email Settings</button>
        </form>
    </div>

    <!-- Payment Settings -->
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(245,158,11,0.08);">
                <svg class="w-3.5 h-3.5" style="color:#f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div><h2 class="text-[13px] font-semibold" style="color:var(--text-primary);">Payment Gateway</h2><p class="text-[10px]" style="color:var(--text-muted);">Paystack integration settings</p></div>
        </div>
        <form id="paymentForm" class="p-4 lg:p-5 space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Public Key</label><input type="text" id="paystackPublic" value="pk_test_****" class="w-full"></div>
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Secret Key</label><input type="password" id="paystackSecret" value="sk_test_****" class="w-full"></div>
            </div>
            <div class="flex items-center gap-3">
                <label class="toggle-switch">
                    <input type="checkbox" id="testMode" checked>
                    <span class="slider"></span>
                </label>
                <label for="testMode" class="text-[12px] cursor-pointer" style="color:var(--text-secondary);">Test Mode Enabled</label>
            </div>
            <button type="submit" class="btn-primary">Save Payment Settings</button>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:rgba(239,68,68,0.08);">
                <svg class="w-3.5 h-3.5" style="color:#ef4444" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div><h2 class="text-[13px] font-semibold" style="color:var(--text-primary);">Security</h2><p class="text-[10px]" style="color:var(--text-muted);">Platform security configurations</p></div>
        </div>
        <div class="p-4 lg:p-5 space-y-3">
            <div class="inner-card flex items-center justify-between">
                <div>
                    <p class="text-[12.5px] font-medium" style="color:var(--text-primary);">Two-Factor Authentication</p>
                    <p class="text-[10.5px] mt-0.5" style="color:var(--text-muted);">Require 2FA for admin accounts</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggle2fa" onchange="saveSecurity()">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="inner-card flex items-center justify-between">
                <div>
                    <p class="text-[12.5px] font-medium" style="color:var(--text-primary);">Email Verification Required</p>
                    <p class="text-[10.5px] mt-0.5" style="color:var(--text-muted);">Users must verify email before accessing platform</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" id="toggleEmailVerify" checked onchange="saveSecurity()">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="inner-card flex items-center justify-between">
                <div>
                    <p class="text-[12.5px] font-medium" style="color:var(--text-primary);">Rate Limiting</p>
                    <p class="text-[10.5px] mt-0.5" style="color:var(--text-muted);">Limit API requests per minute</p>
                </div>
                <input type="number" id="rateLimitInput" value="60" min="1" class="w-20 text-center text-[12px]" onchange="saveSecurity()">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');

    async function loadSettings() {
        try {
            const res = await fetch('/api/admin/settings', { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
            if (res.status === 401) { localStorage.removeItem('adminToken'); window.location.href = '/admin/login'; return; }
            const data = await res.json();
            const s = data.settings || {};
            if (s.platform_name) document.getElementById('platformName').value = s.platform_name;
            if (s.support_email) document.getElementById('supportEmail').value = s.support_email;
            if (s.platform_description) document.getElementById('platformDescription').value = s.platform_description;
            // Security toggles — explicitly set checked state
            document.getElementById('toggle2fa').checked = s.require_2fa == 1 || s.require_2fa === true || s.require_2fa === '1';
            document.getElementById('toggleEmailVerify').checked = s.require_email_verification == 1 || s.require_email_verification === true || s.require_email_verification === '1';
            if (s.rate_limit_api) document.getElementById('rateLimitInput').value = s.rate_limit_api;
        } catch (err) { console.error('Failed to load settings:', err); }
    }

    document.getElementById('generalForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            const res = await fetch('/api/admin/settings', {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ settings: [
                    { key: 'platform_name', value: document.getElementById('platformName').value, type: 'string' },
                    { key: 'platform_description', value: document.getElementById('platformDescription').value, type: 'string' },
                    { key: 'support_email', value: document.getElementById('supportEmail').value, type: 'string' },
                ]})
            });
            if (res.ok) toastSuccess('Settings saved');
            else toastError('Failed to save settings');
        } catch (err) { toastError('Failed to save settings'); }
    });

    document.getElementById('emailForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toastInfo('Email settings are configured via environment variables (.env) for security.');
    });

    document.getElementById('paymentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toastInfo('Payment keys are configured via environment variables (.env) for security.');
    });

    async function saveSecurity() {
        try {
            const res = await fetch('/api/admin/settings', {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ settings: [
                    { key: 'require_2fa', value: document.getElementById('toggle2fa').checked ? '1' : '0', type: 'bool' },
                    { key: 'require_email_verification', value: document.getElementById('toggleEmailVerify').checked ? '1' : '0', type: 'bool' },
                    { key: 'rate_limit_api', value: document.getElementById('rateLimitInput').value, type: 'int' },
                ]})
            });
            if (res.ok) toastSuccess('Security settings updated');
            else toastError('Failed to update');
        } catch (err) { toastError('Failed to save'); }
    }

    loadSettings();
</script>
@endsection
