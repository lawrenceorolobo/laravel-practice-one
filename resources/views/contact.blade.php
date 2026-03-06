@extends('layouts.app')

@section('title', 'Contact Us - Quizly')

@section('content')
<section class="relative min-h-screen flex items-center justify-center bg-slate-50 pt-28 pb-20">
    <div class="absolute inset-0 bg-slate-50"></div>
    <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-indigo-100 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/3 left-1/4 w-48 h-48 bg-slate-200 rounded-full blur-3xl"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-6 w-full">
        <div class="text-center mb-12">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Get in Touch</span>
            <h1 class="text-4xl md:text-6xl font-black text-slate-900 mt-4 mb-4">Contact Us</h1>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto">Have questions about Quizly? We'd love to hear from you.</p>
        </div>

        <div class="grid md:grid-cols-5 gap-8">
            <!-- Info -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Email</h3>
                    <p class="text-slate-500 text-sm">support@quizly.com</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Response Time</h3>
                    <p class="text-slate-500 text-sm">Within 24 hours</p>
                </div>
            </div>

            <!-- Form -->
            <div class="md:col-span-3 bg-white rounded-3xl p-8 shadow-sm">
                <form id="contactForm" onsubmit="submitContact(event)">
                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Name</label>
                            <input type="text" id="contactName" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition text-sm" placeholder="Your name">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                            <input type="email" id="contactEmail" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition text-sm" placeholder="you@email.com">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Subject</label>
                        <input type="text" id="contactSubject" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition text-sm" placeholder="How can we help?">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Message</label>
                        <textarea id="contactMessage" required rows="5" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition text-sm resize-none" placeholder="Tell us more..."></textarea>
                    </div>
                    <button type="submit" id="contactBtn" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all hover:shadow-lg">
                        Send Message
                    </button>
                    <p id="contactResult" class="text-center text-sm mt-4 hidden"></p>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
async function submitContact(e) {
    e.preventDefault();
    const btn = document.getElementById('contactBtn');
    const result = document.getElementById('contactResult');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    result.classList.add('hidden');

    try {
        const res = await fetch('/api/contact', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                name: document.getElementById('contactName').value,
                email: document.getElementById('contactEmail').value,
                subject: document.getElementById('contactSubject').value,
                message: document.getElementById('contactMessage').value,
            }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Failed');
        result.textContent = '✅ ' + data.message;
        result.className = 'text-center text-sm mt-4 text-emerald-600 font-medium';
        result.classList.remove('hidden');
        document.getElementById('contactForm').reset();
    } catch (err) {
        result.textContent = '❌ ' + (err.message || 'Something went wrong');
        result.className = 'text-center text-sm mt-4 text-red-500 font-medium';
        result.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Send Message';
    }
}
</script>
@endsection
