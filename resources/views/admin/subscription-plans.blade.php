@extends('layouts.admin')
@section('title', 'Subscription Plans | Admin')
@section('page-title', 'Subscription Plans')

@section('header-actions')
<button onclick="openCreateModal()" class="btn-primary flex items-center gap-2">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    <span class="hidden sm:inline">Add Plan</span>
</button>
@endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4" id="plansGrid">
    <div class="panel overflow-hidden" style="border-top:3px solid #6366f1;">
        <div class="p-4 lg:p-5" style="border-bottom:1px solid var(--border);">
            <div class="flex items-center justify-between mb-2.5"><div class="skel h-4 w-24"></div><div class="skel skel-pill h-5 w-12"></div></div>
            <div class="skel h-7 w-28 mb-1.5"></div><div class="skel h-3 w-14"></div>
        </div>
        <div class="p-4 lg:p-5">
            <ul class="space-y-2.5 mb-4"><li class="flex items-center gap-2"><div class="skel skel-circle h-3.5 w-3.5"></div><div class="skel h-3 w-24"></div></li><li class="flex items-center gap-2"><div class="skel skel-circle h-3.5 w-3.5"></div><div class="skel h-3 w-32"></div></li><li class="flex items-center gap-2"><div class="skel skel-circle h-3.5 w-3.5"></div><div class="skel h-3 w-20"></div></li></ul>
            <div class="flex gap-2"><div class="skel h-8 flex-1 rounded-lg"></div><div class="skel h-8 w-14 rounded-lg"></div></div>
        </div>
    </div>
    <div class="panel overflow-hidden" style="border-top:3px solid #10b981;">
        <div class="p-4 lg:p-5" style="border-bottom:1px solid var(--border);">
            <div class="flex items-center justify-between mb-2.5"><div class="skel h-4 w-20"></div><div class="skel skel-pill h-5 w-14"></div></div>
            <div class="skel h-7 w-32 mb-1.5"></div><div class="skel h-3 w-12"></div>
        </div>
        <div class="p-4 lg:p-5">
            <ul class="space-y-2.5 mb-4"><li class="flex items-center gap-2"><div class="skel skel-circle h-3.5 w-3.5"></div><div class="skel h-3 w-28"></div></li><li class="flex items-center gap-2"><div class="skel skel-circle h-3.5 w-3.5"></div><div class="skel h-3 w-24"></div></li></ul>
            <div class="flex gap-2"><div class="skel h-8 flex-1 rounded-lg"></div><div class="skel h-8 w-14 rounded-lg"></div></div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="planModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);">
    <div class="panel w-full max-w-md max-h-[90vh] overflow-y-auto" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="modalTitle">Add Plan</h3>
            <button onclick="closeModal()" class="p-1 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <form id="planForm" class="p-5 space-y-4">
            <input type="hidden" id="planId">
            <div>
                <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Plan Name</label>
                <input type="text" id="planName" required placeholder="e.g., Professional" class="w-full">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Monthly Price (₦)</label><input type="number" id="planMonthlyPrice" required min="0" step="100" class="w-full"></div>
                <div><label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Annual Discount (%)</label><input type="number" id="planAnnualDiscount" min="0" max="100" step="1" value="15" class="w-full"></div>
            </div>
            <div>
                <label class="block text-[11px] font-medium mb-1.5" style="color:var(--text-secondary);">Features (one per line)</label>
                <textarea id="planFeatures" rows="4" placeholder="Unlimited assessments&#10;Priority support&#10;Custom branding" class="w-full"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <label class="toggle-switch"><input type="checkbox" id="planActive" checked><span class="slider"></span></label>
                <label for="planActive" class="text-[12px] cursor-pointer" style="color:var(--text-secondary);">Active</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="btn-ghost flex-1">Cancel</button>
                <button type="submit" class="btn-primary flex-1">Save Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Plan Preview Modal -->
<div id="planPreviewModal" class="fixed inset-0 hidden items-center justify-center z-50 p-4" style="background:var(--overlay);backdrop-filter:blur(8px);" onclick="if(event.target===this) closePlanPreview()">
    <div class="panel w-full max-w-sm" style="box-shadow:var(--shadow-lg);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid var(--border);">
            <h3 class="text-[14px] font-semibold" style="color:var(--text-primary);" id="previewTitle">Plan Details</h3>
            <button onclick="closePlanPreview()" class="p-1 rounded-lg transition" style="color:var(--text-muted);" onmouseover="this.style.background='var(--surface-raised)'" onmouseout="this.style.background='transparent'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="p-5 space-y-3" id="previewBody"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const token=localStorage.getItem('adminToken');let allPlans=[];
const planAccents=['#6366f1','#10b981','#a855f7','#f59e0b','#ef4444','#06b6d4'];

async function loadPlans(){try{const r=await fetch('/api/admin/subscription-plans',{headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});if(r.status===401){localStorage.removeItem('adminToken');window.location.href='/admin/login';return}const d=await r.json();allPlans=d.data||d.plans||[];renderPlans()}catch(e){renderPlans()}}

function renderPlans(){const g=document.getElementById('plansGrid');if(!allPlans.length){g.innerHTML=`<div class="sm:col-span-2 lg:col-span-3 panel p-8 lg:p-10 text-center"><svg class="w-10 h-10 mx-auto mb-3" style="color:var(--text-faint)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg><p class="text-[12px] mb-3" style="color:var(--text-muted)">No subscription plans yet</p><button onclick="openCreateModal()" class="btn-primary">Create First Plan</button></div>`;return}
g.innerHTML=allPlans.map((p,i)=>{const f=p.features?(typeof p.features==='string'?JSON.parse(p.features):p.features):[];const mp=Number(p.monthly_price||0),ad=Number(p.annual_discount_percent||0),ap=Math.round(mp*12*(1-ad/100)),ac=planAccents[i%planAccents.length];
return `<div class="panel overflow-hidden ${!p.is_active?'opacity-50':''}" style="border-top:3px solid ${ac};">
<div class="p-4 lg:p-5" style="border-bottom:1px solid var(--border);">
<div class="flex items-center justify-between mb-1.5"><h3 class="text-[15px] font-bold truncate" style="color:var(--text-primary)">${p.name}</h3><span class="badge ${p.is_active?'badge-success':'badge-danger'} ml-2 flex-shrink-0">${p.is_active?'Active':'Off'}</span></div>
<p class="text-xl font-bold" style="color:${ac}">₦${mp.toLocaleString()}<span class="text-[11px] font-normal" style="color:var(--text-muted)">/mo</span></p>
${ad>0?`<p class="text-[11px] mt-0.5" style="color:#34d399">${ad}% off annually → ₦${ap.toLocaleString()}/yr</p>`:''}
${p.users_count!==undefined?`<p class="text-[10px] mt-0.5" style="color:var(--text-muted)">${p.users_count} subscriber${p.users_count!==1?'s':''}</p>`:''}</div>
<div class="p-4 lg:p-5"><ul class="space-y-1.5 mb-4">${f.map(ft=>`<li class="flex items-center gap-2 text-[12px]" style="color:var(--text-secondary)"><svg class="w-3.5 h-3.5 flex-shrink-0" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="truncate">${ft}</span></li>`).join('')}</ul>
<div class="flex gap-2"><button onclick="previewPlan('${p.id}')" class="btn-ghost flex-1 text-[12px] py-1.5">View</button><button onclick="editPlan('${p.id}')" class="btn-primary flex-1 text-[12px] py-1.5">Edit</button><button onclick="deletePlan('${p.id}')" class="p-1.5 rounded-lg hover:bg-red-500/10 transition" title="Delete"><svg class="w-3.5 h-3.5" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div></div></div>`}).join('')}

function openCreateModal(){document.getElementById('modalTitle').textContent='Add Plan';document.getElementById('planForm').reset();document.getElementById('planId').value='';document.getElementById('planAnnualDiscount').value='15';document.getElementById('planActive').checked=true;document.getElementById('planModal').classList.remove('hidden');document.getElementById('planModal').classList.add('flex')}

function editPlan(id){const p=allPlans.find(x=>x.id===id);if(!p)return;document.getElementById('modalTitle').textContent='Edit Plan';document.getElementById('planId').value=p.id;document.getElementById('planName').value=p.name;document.getElementById('planMonthlyPrice').value=p.monthly_price;document.getElementById('planAnnualDiscount').value=p.annual_discount_percent||15;document.getElementById('planActive').checked=p.is_active;const f=p.features?(typeof p.features==='string'?JSON.parse(p.features):p.features):[];document.getElementById('planFeatures').value=f.join('\n');document.getElementById('planModal').classList.remove('hidden');document.getElementById('planModal').classList.add('flex')}

function closeModal(){document.getElementById('planModal').classList.add('hidden');document.getElementById('planModal').classList.remove('flex')}

async function deletePlan(id){const c=await showConfirm('Delete Plan','Are you sure you want to delete this plan?','Delete','danger');if(!c)return;try{const r=await fetch(`/api/admin/subscription-plans/${id}`,{method:'DELETE',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json'}});const d=await r.json();if(!r.ok){toastError(d.message||'Failed');return}toastSuccess('Plan deleted');loadPlans()}catch(e){toastError('Failed to delete plan')}}

document.getElementById('planForm').addEventListener('submit',async(e)=>{e.preventDefault();const id=document.getElementById('planId').value;const ft=document.getElementById('planFeatures').value.split('\n').map(f=>f.trim()).filter(f=>f);const d={name:document.getElementById('planName').value,monthly_price:parseFloat(document.getElementById('planMonthlyPrice').value),annual_discount_percent:parseFloat(document.getElementById('planAnnualDiscount').value||15),features:ft,is_active:document.getElementById('planActive').checked};
try{const r=await fetch(id?`/api/admin/subscription-plans/${id}`:'/api/admin/subscription-plans',{method:id?'PUT':'POST',headers:{'Authorization':`Bearer ${token}`,'Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify(d)});const res=await r.json();if(!r.ok){toastError(res.message||'Failed');return}toastSuccess(id?'Plan updated':'Plan created');closeModal();loadPlans()}catch(e){toastError('Failed to save plan')}});

function previewPlan(id){const p=allPlans.find(x=>x.id===id);if(!p)return;const f=p.features?(typeof p.features==='string'?JSON.parse(p.features):p.features):[];const mp=Number(p.monthly_price||0),ad=Number(p.annual_discount_percent||0),ap=Math.round(mp*12*(1-ad/100));
document.getElementById('previewTitle').textContent=p.name;
document.getElementById('previewBody').innerHTML=`<div class="inner-card space-y-2"><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Status</span><span class="badge ${p.is_active?'badge-success':'badge-danger'}">${p.is_active?'Active':'Inactive'}</span></div><div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Monthly</span><span class="text-[13px] font-bold" style="color:#818cf8">₦${mp.toLocaleString()}</span></div>${ad>0?`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Annual</span><span class="text-[12px]" style="color:#34d399">${ad}% off → ₦${ap.toLocaleString()}/yr</span></div>`:''}${p.users_count!==undefined?`<div class="flex justify-between"><span class="text-[11px]" style="color:var(--text-muted)">Subscribers</span><span class="text-[12px]" style="color:var(--text-primary)">${p.users_count}</span></div>`:''}</div>
<div><p class="text-[11px] mb-2" style="color:var(--text-muted)">Features</p>${f.length>0?`<ul class="space-y-1.5">${f.map(ft=>`<li class="flex items-center gap-2 text-[12px]" style="color:var(--text-secondary)"><svg class="w-3.5 h-3.5 flex-shrink-0" style="color:#34d399" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>${ft}</li>`).join('')}</ul>`:'<p class="text-[12px] italic" style="color:var(--text-muted)">No features listed</p>'}</div>
<div class="flex gap-2 pt-2"><button onclick="closePlanPreview();editPlan('${p.id}')" class="btn-primary flex-1 text-[12px]">Edit</button><button onclick="closePlanPreview();deletePlan('${p.id}')" class="btn-ghost flex-1 text-[12px]" style="color:#f87171;">Delete</button></div>`;
document.getElementById('planPreviewModal').classList.remove('hidden');document.getElementById('planPreviewModal').classList.add('flex')}

function closePlanPreview(){document.getElementById('planPreviewModal').classList.add('hidden');document.getElementById('planPreviewModal').classList.remove('flex')}
loadPlans();
</script>
@endsection
