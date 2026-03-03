@extends('layouts.admin')
@section('title', 'Feature Flags | Admin')
@section('page-title', 'Feature Flags')

@section('content')
<!-- Loading -->
<div id="loading" class="space-y-3">
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="skel h-5 w-5 rounded"></div><div class="skel h-4 w-20"></div><div class="ml-auto skel h-3 w-12"></div>
        </div>
        <div class="px-4 lg:px-5 py-3 flex items-center justify-between"><div><div class="skel h-3.5 w-28 mb-1.5"></div><div class="skel h-3 w-40"></div></div><div class="skel h-6 w-11 rounded-full"></div></div>
        <div class="px-4 lg:px-5 py-3 flex items-center justify-between" style="border-top:1px solid var(--border-subtle);"><div><div class="skel h-3.5 w-24 mb-1.5"></div><div class="skel h-3 w-36"></div></div><div class="skel h-6 w-11 rounded-full"></div></div>
    </div>
    <div class="panel">
        <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
            <div class="skel h-5 w-5 rounded"></div><div class="skel h-4 w-24"></div><div class="ml-auto skel h-3 w-10"></div>
        </div>
        <div class="px-4 lg:px-5 py-3 flex items-center justify-between"><div><div class="skel h-3.5 w-32 mb-1.5"></div><div class="skel h-3 w-44"></div></div><div class="skel h-6 w-11 rounded-full"></div></div>
    </div>
</div>

<!-- Flags by Category -->
<div id="flagsContainer" class="hidden space-y-3"></div>
@endsection

@section('scripts')
<script>
async function loadFlags() {
    try {
        const res = await fetch('/api/admin/feature-flags', { headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' }});
        if (!res.ok) throw new Error('Failed');
        const data = await res.json();
        renderFlags(data.grouped);
    } catch (err) { console.error(err); }
}

function renderFlags(grouped) {
    const container = document.getElementById('flagsContainer');
    document.getElementById('loading').classList.add('hidden');
    container.classList.remove('hidden');

    const categoryIcons = { payments:'💳', proctoring:'🎥', assessments:'📝', communication:'📧', auth:'🔐', platform:'⚙️', general:'🏷️' };

    let html = '';
    for (const [category, flags] of Object.entries(grouped)) {
        html += `<div class="panel">
            <div class="px-4 lg:px-5 py-3 flex items-center gap-2.5" style="border-bottom:1px solid var(--border);">
                <span class="text-base">${categoryIcons[category]||'🏷️'}</span>
                <h3 class="text-[13px] font-semibold capitalize" style="color:var(--text-primary);">${category}</h3>
                <span class="ml-auto text-[10px]" style="color:var(--text-muted);">${flags.length} flags</span>
            </div>`;

        for (const flag of flags) {
            html += `<div class="px-4 lg:px-5 py-3.5 flex items-center justify-between transition" style="border-bottom:1px solid var(--border-subtle);" id="flag-${flag.id}" onmouseover="this.style.background='var(--surface-hover)'" onmouseout="this.style.background='transparent'">
                <div class="min-w-0 flex-1 pr-4">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[12.5px] font-semibold" style="color:var(--text-primary);">${flag.name}</span>
                        <code class="code-badge">${flag.key}</code>
                    </div>
                    <p class="text-[11px] mt-0.5 truncate" style="color:var(--text-muted);">${flag.description||''}</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" ${flag.enabled ? 'checked' : ''} onchange="toggleFlag(${flag.id}, this)">
                    <span class="slider"></span>
                </label>
            </div>`;
        }
        html += `</div>`;
    }
    container.innerHTML = html;
}

async function toggleFlag(id, el) {
    try {
        const res = await fetch(`/api/admin/feature-flags/${id}/toggle`, { method:'POST', headers: { 'Authorization': `Bearer ${adminToken}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message);
        el.checked = data.flag.enabled;
        toastSuccess(data.message);
    } catch (err) {
        el.checked = !el.checked;
        toastError(err.message || 'Toggle failed');
    }
}

loadFlags();
</script>
@endsection
