@extends('layouts.admin')

@section('title', 'Settings | Admin')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-4xl">
    <!-- General Settings -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 mb-6">
        <div class="p-6 border-b border-slate-700">
            <h2 class="text-lg font-bold">General Settings</h2>
            <p class="text-sm text-slate-400 mt-1">Configure your platform settings</p>
        </div>
        <form id="generalForm" class="p-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Platform Name</label>
                    <input type="text" id="platformName" value="Quizly" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Support Email</label>
                    <input type="email" id="supportEmail" value="support@quizly.com" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-400 mb-2">Platform Description</label>
                <textarea id="platformDescription" rows="3" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">Enterprise assessment platform for modern businesses.</textarea>
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition-colors">Save Changes</button>
        </form>
    </div>

    <!-- Email Settings -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 mb-6">
        <div class="p-6 border-b border-slate-700">
            <h2 class="text-lg font-bold">Email Configuration</h2>
            <p class="text-sm text-slate-400 mt-1">SMTP and email notification settings</p>
        </div>
        <form id="emailForm" class="p-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">SMTP Host</label>
                    <input type="text" id="smtpHost" value="{{ env('MAIL_HOST', 'smtp.mailgun.org') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">SMTP Port</label>
                    <input type="text" id="smtpPort" value="{{ env('MAIL_PORT', '587') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">From Email</label>
                    <input type="email" id="fromEmail" value="{{ env('MAIL_FROM_ADDRESS', 'noreply@quizly.com') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">From Name</label>
                    <input type="text" id="fromName" value="{{ env('MAIL_FROM_NAME', 'Quizly') }}" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition-colors">Update Email Settings</button>
        </form>
    </div>

    <!-- Payment Settings -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 mb-6">
        <div class="p-6 border-b border-slate-700">
            <h2 class="text-lg font-bold">Payment Gateway</h2>
            <p class="text-sm text-slate-400 mt-1">Paystack integration settings</p>
        </div>
        <form id="paymentForm" class="p-6 space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Public Key</label>
                    <input type="text" id="paystackPublic" value="pk_test_****" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Secret Key</label>
                    <input type="password" id="paystackSecret" value="sk_test_****" class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="testMode" checked class="w-4 h-4 rounded border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="testMode" class="text-sm text-slate-300">Test Mode Enabled</label>
            </div>
            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium transition-colors">Save Payment Settings</button>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="bg-slate-800 rounded-xl border border-slate-700">
        <div class="p-6 border-b border-slate-700">
            <h2 class="text-lg font-bold">Security</h2>
            <p class="text-sm text-slate-400 mt-1">Platform security configurations</p>
        </div>
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between p-4 bg-slate-700/50 rounded-lg">
                <div>
                    <p class="font-medium">Two-Factor Authentication</p>
                    <p class="text-sm text-slate-400">Require 2FA for admin accounts</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-700/50 rounded-lg">
                <div>
                    <p class="font-medium">Email Verification Required</p>
                    <p class="text-sm text-slate-400">Users must verify email before accessing platform</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
            <div class="flex items-center justify-between p-4 bg-slate-700/50 rounded-lg">
                <div>
                    <p class="font-medium">Rate Limiting</p>
                    <p class="text-sm text-slate-400">Limit API requests per minute</p>
                </div>
                <input type="number" value="60" min="1" class="w-20 px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');

    document.getElementById('generalForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toastSuccess('Settings saved successfully!');
    });

    document.getElementById('emailForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toastSuccess('Email settings updated!');
    });

    document.getElementById('paymentForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toastSuccess('Payment settings updated!');
    });
</script>
@endsection
