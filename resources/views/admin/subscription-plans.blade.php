@extends('layouts.admin')

@section('title', 'Subscription Plans | Admin')
@section('page-title', 'Subscription Plans')

@section('header-actions')
<button onclick="openCreateModal()" class="px-3 lg:px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-xs lg:text-sm transition-colors flex items-center gap-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    <span class="hidden sm:inline">Add Plan</span>
</button>
@endsection

@section('content')
<!-- Plans Grid with Skeleton Loading -->
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6" id="plansGrid">
    <!-- Skeleton Card 1 -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-slate-700">
            <div class="flex items-center justify-between mb-3">
                <div class="skeleton skeleton-glass h-6 w-28 rounded"></div>
                <div class="skeleton skeleton-glass h-5 w-14 rounded-full"></div>
            </div>
            <div class="skeleton skeleton-glass h-9 w-32 rounded mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-16 rounded"></div>
        </div>
        <div class="p-4 lg:p-6">
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-28 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-36 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-24 rounded"></div></li>
            </ul>
            <div class="flex gap-2">
                <div class="skeleton skeleton-glass h-10 flex-1 rounded-lg"></div>
                <div class="skeleton skeleton-glass h-10 w-16 rounded-lg"></div>
            </div>
        </div>
    </div>
    <!-- Skeleton Card 2 -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="p-4 lg:p-6 border-b border-slate-700">
            <div class="flex items-center justify-between mb-3">
                <div class="skeleton skeleton-glass h-6 w-24 rounded"></div>
                <div class="skeleton skeleton-glass h-5 w-16 rounded-full"></div>
            </div>
            <div class="skeleton skeleton-glass h-9 w-36 rounded mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-14 rounded"></div>
        </div>
        <div class="p-4 lg:p-6">
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-32 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-28 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-20 rounded"></div></li>
            </ul>
            <div class="flex gap-2">
                <div class="skeleton skeleton-glass h-10 flex-1 rounded-lg"></div>
                <div class="skeleton skeleton-glass h-10 w-16 rounded-lg"></div>
            </div>
        </div>
    </div>
    <!-- Skeleton Card 3 -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden hidden sm:block lg:block">
        <div class="p-4 lg:p-6 border-b border-slate-700">
            <div class="flex items-center justify-between mb-3">
                <div class="skeleton skeleton-glass h-6 w-32 rounded"></div>
                <div class="skeleton skeleton-glass h-5 w-12 rounded-full"></div>
            </div>
            <div class="skeleton skeleton-glass h-9 w-28 rounded mb-2"></div>
            <div class="skeleton skeleton-glass h-4 w-18 rounded"></div>
        </div>
        <div class="p-4 lg:p-6">
            <ul class="space-y-3 mb-6">
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-24 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-32 rounded"></div></li>
                <li class="flex items-center gap-2"><div class="skeleton skeleton-glass h-4 w-4 rounded-full"></div><div class="skeleton skeleton-glass h-4 w-28 rounded"></div></li>
            </ul>
            <div class="flex gap-2">
                <div class="skeleton skeleton-glass h-10 flex-1 rounded-lg"></div>
                <div class="skeleton skeleton-glass h-10 w-16 rounded-lg"></div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="planModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
    <div class="bg-slate-800 rounded-xl border border-slate-700 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="p-4 lg:p-6 border-b border-slate-700 flex items-center justify-between sticky top-0 bg-slate-800">
            <h3 class="text-base lg:text-lg font-bold" id="modalTitle">Add Plan</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="planForm" class="p-4 lg:p-6 space-y-4">
            <input type="hidden" id="planId">
            <div>
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Plan Name</label>
                <input type="text" id="planName" required placeholder="e.g., Professional" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="grid grid-cols-2 gap-3 lg:gap-4">
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Price (₦)</label>
                    <input type="number" id="planPrice" required min="0" step="100" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Duration (days)</label>
                    <input type="number" id="planDuration" required min="1" value="30" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:gap-4">
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Max Assessments</label>
                    <input type="number" id="maxAssessments" required min="1" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Max Invitees</label>
                    <input type="number" id="maxInvitees" required min="1" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-1">Description</label>
                <textarea id="planDescription" rows="2" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div>
                <label class="block text-xs lg:text-sm font-medium text-slate-400 mb-2">Features (one per line)</label>
                <textarea id="planFeatures" rows="4" placeholder="Unlimited assessments&#10;Priority support&#10;Custom branding" class="w-full px-3 lg:px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" id="planActive" checked class="w-4 h-4 rounded border-slate-600 text-indigo-600 focus:ring-indigo-500">
                <label for="planActive" class="text-xs lg:text-sm text-slate-300">Active</label>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg font-medium text-sm transition-colors">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-sm transition-colors">Save Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const token = localStorage.getItem('adminToken');
    let allPlans = [];

    async function loadPlans() {
        try {
            const res = await fetch('/api/admin/subscription-plans', {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            const data = await res.json();
            allPlans = data.plans || data.data || [];
            renderPlans();
        } catch (err) {
            console.error('Failed to load plans:', err);
            renderPlans();
        }
    }

    function renderPlans() {
        const grid = document.getElementById('plansGrid');
        
        if (allPlans.length === 0) {
            grid.innerHTML = `
                <div class="sm:col-span-2 lg:col-span-3 bg-slate-800 rounded-xl border border-slate-700 p-8 lg:p-12 text-center">
                    <svg class="w-12 h-12 lg:w-16 lg:h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p class="text-slate-400 mb-4 text-sm lg:text-base">No subscription plans yet</p>
                    <button onclick="openCreateModal()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg font-medium text-sm transition-colors">
                        Create First Plan
                    </button>
                </div>
            `;
            return;
        }

        grid.innerHTML = allPlans.map(plan => {
            const features = plan.features ? (typeof plan.features === 'string' ? JSON.parse(plan.features) : plan.features) : [];
            return `
                <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden ${!plan.is_active ? 'opacity-60' : ''}">
                    <div class="p-4 lg:p-6 border-b border-slate-700">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-lg lg:text-xl font-bold truncate">${plan.name}</h3>
                            <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0 ml-2 ${plan.is_active ? 'bg-emerald-600/20 text-emerald-400' : 'bg-red-600/20 text-red-400'}">
                                ${plan.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                        <p class="text-2xl lg:text-3xl font-bold text-indigo-400">₦${Number(plan.price).toLocaleString()}</p>
                        <p class="text-xs lg:text-sm text-slate-500">${plan.duration_days} days</p>
                    </div>
                    <div class="p-4 lg:p-6">
                        <ul class="space-y-2 mb-4 lg:mb-6">
                            <li class="flex items-center gap-2 text-xs lg:text-sm text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ${plan.max_assessments} assessments
                            </li>
                            <li class="flex items-center gap-2 text-xs lg:text-sm text-slate-300">
                                <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                ${plan.max_invitees_per_assessment} invitees/test
                            </li>
                            ${features.slice(0, 3).map(f => `
                                <li class="flex items-center gap-2 text-xs lg:text-sm text-slate-300">
                                    <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="truncate">${f}</span>
                                </li>
                            `).join('')}
                        </ul>
                        <div class="flex gap-2">
                            <button onclick="editPlan('${plan.id}')" class="flex-1 px-3 py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs lg:text-sm font-medium transition-colors">Edit</button>
                            <button onclick="deletePlan('${plan.id}')" class="px-3 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 rounded-lg text-xs lg:text-sm font-medium transition-colors">Delete</button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add Plan';
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = '';
        document.getElementById('planModal').classList.remove('hidden');
        document.getElementById('planModal').classList.add('flex');
    }

    function editPlan(id) {
        const plan = allPlans.find(p => p.id === id);
        if (!plan) return;

        document.getElementById('modalTitle').textContent = 'Edit Plan';
        document.getElementById('planId').value = plan.id;
        document.getElementById('planName').value = plan.name;
        document.getElementById('planPrice').value = plan.price;
        document.getElementById('planDuration').value = plan.duration_days;
        document.getElementById('maxAssessments').value = plan.max_assessments;
        document.getElementById('maxInvitees').value = plan.max_invitees_per_assessment;
        document.getElementById('planDescription').value = plan.description || '';
        document.getElementById('planActive').checked = plan.is_active;
        
        const features = plan.features ? (typeof plan.features === 'string' ? JSON.parse(plan.features) : plan.features) : [];
        document.getElementById('planFeatures').value = features.join('\n');
        
        document.getElementById('planModal').classList.remove('hidden');
        document.getElementById('planModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('planModal').classList.add('hidden');
        document.getElementById('planModal').classList.remove('flex');
    }

    async function deletePlan(id) {
        const confirmed = await showConfirm('Delete Plan', 'Are you sure you want to delete this subscription plan?', 'Delete', 'danger');
        if (!confirmed) return;
        try {
            await fetch(`/api/admin/subscription-plans/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });
            toastSuccess('Plan deleted successfully');
            loadPlans();
        } catch (err) {
            toastError('Failed to delete plan');
            console.error('Failed to delete plan:', err);
        }
    }

    document.getElementById('planForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('planId').value;
        const featuresText = document.getElementById('planFeatures').value;
        const features = featuresText.split('\n').map(f => f.trim()).filter(f => f);
        
        const data = {
            name: document.getElementById('planName').value,
            price: parseFloat(document.getElementById('planPrice').value),
            duration_days: parseInt(document.getElementById('planDuration').value),
            max_assessments: parseInt(document.getElementById('maxAssessments').value),
            max_invitees_per_assessment: parseInt(document.getElementById('maxInvitees').value),
            description: document.getElementById('planDescription').value,
            features: features,
            is_active: document.getElementById('planActive').checked
        };

        try {
            await fetch(id ? `/api/admin/subscription-plans/${id}` : '/api/admin/subscription-plans', {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            closeModal();
            loadPlans();
        } catch (err) {
            console.error('Failed to save plan:', err);
        }
    });

    loadPlans();
</script>
@endsection
