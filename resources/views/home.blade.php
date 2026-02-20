@extends('layouts.app')

@section('title', 'Quizly - Modern Assessment Platform')

@section('content')
<!-- Three.js Hero Canvas -->
<canvas id="hero-canvas" class="fixed inset-0 -z-10"></canvas>

<!-- Hero Section -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-50">
    <!-- Clean Background -->
    <div class="absolute inset-0 bg-slate-50"></div>
    
    <!-- Subtle Decorative Elements -->
    <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-indigo-100 rounded-full blur-3xl" data-speed="0.3"></div>
    <div class="absolute top-1/3 right-1/4 w-48 h-48 bg-slate-200 rounded-full blur-3xl"></div>
    <div class="absolute bottom-1/3 left-1/3 w-72 h-72 bg-slate-100 rounded-full blur-3xl"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-6 pt-32 pb-20 text-center">
        <!-- Animated Badge -->
        <div class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 px-5 py-2 rounded-full text-sm font-medium mb-8 shadow-sm animate-fade-in">
            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
            Trusted by 500+ companies worldwide
        </div>
        
        <!-- Main Heading with Gradient -->
        <h1 class="text-5xl md:text-7xl lg:text-8xl font-black mb-8 leading-tight">
            <span class="text-slate-900 animate-slide-up block">Assessments that</span>
            <span class="text-indigo-600 animate-slide-up-delay block">Actually Work</span>
        </h1>
        
        <!-- Subtitle -->
        <p class="text-xl md:text-2xl text-slate-600 max-w-3xl mx-auto mb-12 animate-fade-in-delay">
            Create stunning, fraud-proof assessments in minutes. Get real insights. Make better hiring decisions.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center animate-fade-in-delay-2">
            <a href="{{ route('register') }}" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg transition-all hover:bg-indigo-700 hover:scale-105 shadow-lg">
                Start Free Trial
            </a>
            <a href="#features" class="group flex items-center gap-2 px-8 py-4 text-slate-600 hover:text-indigo-600 font-medium transition-all">
                <span>See how it works</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
        
        <!-- Floating Stats -->
        <div class="grid grid-cols-3 gap-3 sm:gap-8 max-w-2xl mx-auto mt-20 animate-fade-in-delay-3 px-2">
            <div class="text-center bg-white rounded-2xl p-3 sm:p-4 shadow-sm">
                <div class="text-2xl sm:text-4xl font-bold text-slate-900">50K+</div>
                <div class="text-slate-500 text-xs sm:text-sm">Assessments</div>
            </div>
            <div class="text-center bg-white rounded-2xl p-3 sm:p-4 shadow-sm">
                <div class="text-2xl sm:text-4xl font-bold text-slate-900">1M+</div>
                <div class="text-slate-500 text-xs sm:text-sm">Test Takers</div>
            </div>
            <div class="text-center bg-white rounded-2xl p-3 sm:p-4 shadow-sm">
                <div class="text-2xl sm:text-4xl font-bold text-slate-900">99.9%</div>
                <div class="text-slate-500 text-xs sm:text-sm">Uptime</div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Logo Marquee -->
<section class="py-16 bg-white border-y border-slate-100 overflow-hidden">
    <p class="text-center text-sm text-slate-400 uppercase tracking-widest mb-10">Trusted by leading organizations worldwide</p>
    <div class="relative">
        <div class="flex animate-marquee whitespace-nowrap">
            <div class="flex items-center gap-20 mx-10">
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Microsoft</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Google</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Amazon</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Meta</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Netflix</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Spotify</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Stripe</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Airbnb</span>
            </div>
            <div class="flex items-center gap-20 mx-10">
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Microsoft</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Google</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Amazon</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Meta</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Netflix</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Spotify</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Stripe</span>
                <span class="text-3xl font-bold text-slate-300 hover:text-indigo-500 transition-colors cursor-default">Airbnb</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-32 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20 reveal-on-scroll">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Features</span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 mt-4 mb-6">
                Everything you need
            </h2>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto">
                Powerful features that make assessment creation effortless and analysis insightful.
            </p>
        </div>
        
        <!-- Feature Cards Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="tilt-card group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll">
                <div class="absolute inset-0 bg-indigo-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Lightning Fast Creation</h3>
                    <p class="text-slate-500">Build complete assessments in minutes with our intuitive drag-and-drop builder.</p>
                </div>
            </div>
            
            <!-- Card 2 -->
            <div class="group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll" style="animation-delay: 100ms">
                <div class="absolute inset-0 bg-purple-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Fraud Detection AI</h3>
                    <p class="text-slate-500">Advanced algorithms detect cheating, tab-switching, and proxy test-taking in real-time.</p>
                </div>
            </div>
            
            <!-- Card 3 -->
            <div class="group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll" style="animation-delay: 200ms">
                <div class="absolute inset-0 bg-emerald-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Real-time Analytics</h3>
                    <p class="text-slate-500">Get instant insights with comprehensive dashboards and performance metrics.</p>
                </div>
            </div>
            
            <!-- Card 4 -->
            <div class="group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll" style="animation-delay: 300ms">
                <div class="absolute inset-0 bg-amber-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Timed Assessments</h3>
                    <p class="text-slate-500">Set custom time limits per question or entire assessment with auto-submit.</p>
                </div>
            </div>
            
            <!-- Card 5 -->
            <div class="group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll" style="animation-delay: 400ms">
                <div class="absolute inset-0 bg-rose-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-rose-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Bulk Invitations</h3>
                    <p class="text-slate-500">Invite hundreds of candidates at once with personalized email invitations.</p>
                </div>
            </div>
            
            <!-- Card 6 -->
            <div class="group relative bg-white rounded-3xl p-8 shadow-sm hover:shadow-2xl hover:shadow-sky-500/10 transition-all duration-500 hover:-translate-y-2 reveal-on-scroll" style="animation-delay: 500ms">
                <div class="absolute inset-0 bg-sky-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="w-14 h-14 bg-sky-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Export & Reports</h3>
                    <p class="text-slate-500">Download detailed PDF reports and export results to CSV, Excel, or integrate via API.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-20 reveal-on-scroll">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">How It Works</span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 mt-4">
                Three simple steps
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-12">
            <div class="text-center reveal-on-scroll">
                <div class="relative inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-3xl mb-8 shadow-lg shadow-indigo-500/30">
                    <span class="text-3xl font-black text-white">1</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Create</h3>
                <p class="text-slate-500">Build your assessment using our intuitive question builder with multiple question types.</p>
            </div>
            
            <div class="text-center reveal-on-scroll" style="animation-delay: 150ms">
                <div class="relative inline-flex items-center justify-center w-20 h-20 bg-purple-600 rounded-3xl mb-8 shadow-lg shadow-purple-500/30">
                    <span class="text-3xl font-black text-white">2</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Invite</h3>
                <p class="text-slate-500">Send personalized invitations to candidates via email with unique access links.</p>
            </div>
            
            <div class="text-center reveal-on-scroll" style="animation-delay: 300ms">
                <div class="relative inline-flex items-center justify-center w-20 h-20 bg-pink-600 rounded-3xl mb-8 shadow-lg shadow-pink-500/30">
                    <span class="text-3xl font-black text-white">3</span>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-4">Analyze</h3>
                <p class="text-slate-500">Review results with detailed analytics, fraud detection alerts, and performance insights.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-32 bg-white relative overflow-hidden">
    <!-- Clean Background -->
    <div class="absolute inset-0 bg-slate-50"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-indigo-50 rounded-full blur-3xl"></div>
    
    <div class="relative max-w-7xl mx-auto px-6">
        <div class="text-center mb-20 reveal-on-scroll">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Pricing</span>
            <h2 class="text-4xl md:text-6xl font-black text-slate-900 mt-4 mb-6">
                Simple, transparent pricing
            </h2>
            <p class="text-xl text-slate-500 max-w-2xl mx-auto">
                Start free, upgrade when you need more power.
            </p>
        </div>
        
        <div id="pricingGrid" class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Loading skeleton -->
            <div class="bg-white border border-slate-200 rounded-3xl p-8 animate-pulse">
                <div class="h-6 bg-slate-200 rounded w-1/2 mb-4"></div>
                <div class="h-4 bg-slate-200 rounded w-3/4 mb-8"></div>
                <div class="h-12 bg-slate-200 rounded w-1/2 mb-8"></div>
                <div class="space-y-3 mb-8">
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
                <div class="h-12 bg-slate-200 rounded"></div>
            </div>
            <div class="bg-indigo-600 rounded-3xl p-8 animate-pulse scale-105">
                <div class="h-6 bg-indigo-400 rounded w-1/2 mb-4"></div>
                <div class="h-4 bg-indigo-400 rounded w-3/4 mb-8"></div>
                <div class="h-12 bg-indigo-400 rounded w-1/2 mb-8"></div>
                <div class="space-y-3 mb-8">
                    <div class="h-4 bg-indigo-400 rounded"></div>
                    <div class="h-4 bg-indigo-400 rounded"></div>
                    <div class="h-4 bg-indigo-400 rounded"></div>
                </div>
                <div class="h-12 bg-white rounded"></div>
            </div>
            <div class="bg-white border border-slate-200 rounded-3xl p-8 animate-pulse">
                <div class="h-6 bg-slate-200 rounded w-1/2 mb-4"></div>
                <div class="h-4 bg-slate-200 rounded w-3/4 mb-8"></div>
                <div class="h-12 bg-slate-200 rounded w-1/2 mb-8"></div>
                <div class="space-y-3 mb-8">
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                    <div class="h-4 bg-slate-200 rounded"></div>
                </div>
                <div class="h-12 bg-slate-200 rounded"></div>
            </div>
        </div>
    </div>
</section>

<script>
async function loadPricingPlans() {
    try {
        const res = await fetch('/api/subscription/plans');
        const data = await res.json();
        const plans = data.plans || [];
        
        if (!plans.length) return;
        
        const grid = document.getElementById('pricingGrid');
        grid.innerHTML = plans.map((plan, idx) => {
            const isPopular = idx === 1;
            const features = typeof plan.features === 'string' ? JSON.parse(plan.features) : (plan.features || []);
            const price = plan.monthly_price == 0 ? '₦0' : `₦${(plan.monthly_price / 1000).toFixed(0)}K`;
            
            if (isPopular) {
                return `
                <div class="relative bg-indigo-600 border border-indigo-500 rounded-3xl p-8 reveal-on-scroll scale-105 shadow-2xl">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-white text-indigo-600 text-sm font-bold px-4 py-1 rounded-full shadow">Most Popular</div>
                    <h3 class="text-xl font-bold text-white mb-2">${plan.name}</h3>
                    <p class="text-indigo-100 mb-6">Best value plan</p>
                    <div class="mb-8">
                        <span class="text-5xl font-black text-white">${price}</span>
                        <span class="text-indigo-100">/month</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        ${features.map(f => `<li class="flex items-center gap-3 text-white"><svg class="w-5 h-5 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>${f}</li>`).join('')}
                    </ul>
                    <a href="/register" class="block w-full py-4 text-center bg-white hover:bg-slate-100 text-indigo-600 rounded-xl font-bold transition-all shadow-lg">Start Free Trial</a>
                </div>`;
            }
            
            return `
            <div class="relative bg-white border border-slate-200 rounded-3xl p-8 reveal-on-scroll hover:shadow-lg transition-all">
                <h3 class="text-xl font-bold text-slate-900 mb-2">${plan.name}</h3>
                <p class="text-slate-500 mb-6">${plan.monthly_price == 0 ? 'Perfect for trying out' : 'For large teams'}</p>
                <div class="mb-8">
                    <span class="text-5xl font-black text-slate-900">${price}</span>
                    <span class="text-slate-500">/month</span>
                </div>
                <ul class="space-y-4 mb-8">
                    ${features.map(f => `<li class="flex items-center gap-3 text-slate-600"><svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>${f}</li>`).join('')}
                </ul>
                <a href="/register" class="block w-full py-4 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-all">${plan.monthly_price == 0 ? 'Get Started Free' : 'Choose Plan'}</a>
            </div>`;
        }).join('');
    } catch (e) {
        console.error('Failed to load pricing:', e);
        document.getElementById('pricingGrid').innerHTML = `
            <div class="col-span-3 text-center py-12">
                <p class="text-slate-500 text-lg">No plans available yet. Please check back later.</p>
            </div>`;
    }
}
loadPricingPlans();
</script>

<!-- CTA Section -->
<section class="py-32 bg-white relative overflow-hidden">
    <div class="absolute inset-0 bg-slate-50"></div>
    <div class="relative max-w-4xl mx-auto px-6 text-center reveal-on-scroll">
        <h2 class="text-4xl md:text-6xl font-black text-slate-900 mb-6">
            Ready to transform your<br />
            <span class="text-indigo-600">hiring process?</span>
        </h2>
        <p class="text-xl text-slate-500 mb-10 max-w-2xl mx-auto">
            Join hundreds of companies already using Quizly. Start your free trial today.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg hover:bg-indigo-700 hover:shadow-xl hover:-translate-y-1 transition-all">
                Start Free Trial
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-100 text-slate-700 rounded-2xl font-bold text-lg hover:bg-slate-200 transition-all">
                Schedule Demo
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<!-- Three.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<!-- GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<script>
// Three.js Particle Background
const canvas = document.getElementById('hero-canvas');
const scene = new THREE.Scene();
const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
const renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true });
renderer.setSize(window.innerWidth, window.innerHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

// Particles
const particlesGeometry = new THREE.BufferGeometry();
const particlesCount = 3000;
const posArray = new Float32Array(particlesCount * 3);

for(let i = 0; i < particlesCount * 3; i++) {
    posArray[i] = (Math.random() - 0.5) * 10;
}

particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

const particlesMaterial = new THREE.PointsMaterial({
    size: 0.02,
    color: 0x6366f1,
    transparent: true,
    opacity: 0.8,
    blending: THREE.AdditiveBlending
});

const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
scene.add(particlesMesh);

camera.position.z = 3;

let mouseX = 0, mouseY = 0;

document.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX / window.innerWidth) * 2 - 1;
    mouseY = -(e.clientY / window.innerHeight) * 2 + 1;
});

function animate() {
    requestAnimationFrame(animate);
    particlesMesh.rotation.x += 0.0003;
    particlesMesh.rotation.y += 0.0005;
    particlesMesh.rotation.x += mouseY * 0.0003;
    particlesMesh.rotation.y += mouseX * 0.0003;
    renderer.render(scene, camera);
}
animate();

window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

// GSAP Scroll Animations
gsap.registerPlugin(ScrollTrigger);

// Reveal on scroll
gsap.utils.toArray('.reveal-on-scroll').forEach((el, i) => {
    gsap.from(el, {
        scrollTrigger: {
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
        },
        y: 60,
        opacity: 0,
        duration: 0.8,
        delay: i * 0.05,
        ease: 'power3.out'
    });
});
</script>

<style>
/* Animations */
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slide-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes marquee {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.animate-fade-in { animation: fade-in 1s ease-out forwards; }
.animate-fade-in-delay { animation: fade-in 1s ease-out 0.3s forwards; opacity: 0; }
.animate-fade-in-delay-2 { animation: fade-in 1s ease-out 0.6s forwards; opacity: 0; }
.animate-fade-in-delay-3 { animation: fade-in 1s ease-out 0.9s forwards; opacity: 0; }
.animate-slide-up { animation: slide-up 1s ease-out forwards; }
.animate-slide-up-delay { animation: slide-up 1s ease-out 0.2s forwards; opacity: 0; }
.animate-marquee { animation: marquee 30s linear infinite; }
</style>
@endsection
