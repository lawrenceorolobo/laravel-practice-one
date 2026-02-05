@extends('layouts.app')

@section('title', 'About | Quizly')

@section('content')

<!-- Hero Section -->
<section class="relative pt-32 pb-20 bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 overflow-hidden">
    <!-- Background Effects -->
    <div class="absolute inset-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-6 text-center">
        <span class="inline-block text-indigo-400 font-semibold text-sm uppercase tracking-widest mb-4">About Us</span>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6">
            We're building the<br />
            <span class="bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">future of hiring</span>
        </h1>
        <p class="text-xl text-indigo-200/70 max-w-2xl mx-auto">
            Quizly was founded to solve a simple problem: making assessments that are fair, fraud-proof, and actually useful.
        </p>
    </div>
</section>

<!-- Stats Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center reveal-on-scroll">
                <div class="text-5xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">500+</div>
                <div class="text-slate-500 font-medium">Companies Trust Us</div>
            </div>
            <div class="text-center reveal-on-scroll" style="animation-delay: 100ms">
                <div class="text-5xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">1M+</div>
                <div class="text-slate-500 font-medium">Tests Completed</div>
            </div>
            <div class="text-center reveal-on-scroll" style="animation-delay: 200ms">
                <div class="text-5xl font-black bg-gradient-to-r from-pink-600 to-rose-600 bg-clip-text text-transparent mb-2">99.9%</div>
                <div class="text-slate-500 font-medium">Uptime SLA</div>
            </div>
            <div class="text-center reveal-on-scroll" style="animation-delay: 300ms">
                <div class="text-5xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">24/7</div>
                <div class="text-slate-500 font-medium">Support Available</div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="reveal-on-scroll">
                <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Our Mission</span>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mt-4 mb-6">
                    Making hiring decisions based on skills, not resumes
                </h2>
                <p class="text-lg text-slate-500 mb-6">
                    We believe everyone deserves a fair chance to showcase their abilities. Traditional resumes often fail to capture true potential, and biased interview processes can overlook great candidates.
                </p>
                <p class="text-lg text-slate-500">
                    Our platform uses cutting-edge technology to create assessments that are fair, accurate, and impossible to cheat. We're leveling the playing field for candidates everywhere.
                </p>
            </div>
            <div class="reveal-on-scroll" style="animation-delay: 200ms">
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-3xl rotate-3"></div>
                    <div class="relative bg-white rounded-3xl p-10 shadow-xl">
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900">Fair Assessments</h3>
                                    <p class="text-slate-500 text-sm">Scientifically designed tests that measure actual skills</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900">Fraud Prevention</h3>
                                    <p class="text-slate-500 text-sm">AI-powered cheating detection and prevention</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900">Data-Driven Insights</h3>
                                    <p class="text-slate-500 text-sm">Comprehensive analytics for better decisions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16 reveal-on-scroll">
            <span class="text-indigo-600 font-semibold text-sm uppercase tracking-widest">Our Team</span>
            <h2 class="text-4xl md:text-5xl font-black text-slate-900 mt-4">
                Meet the leaders
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Team Member 1 -->
            <div class="group reveal-on-scroll">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-100 to-purple-100 aspect-square mb-6">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center">
                            <span class="text-5xl font-black text-white">L</span>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Lawrence</h3>
                <p class="text-indigo-600 font-medium mb-2">CEO & Co-founder</p>
                <p class="text-slate-500 text-sm">Former education tech lead at Google. 15+ years in EdTech.</p>
            </div>
            
            <!-- Team Member 2 -->
            <div class="group reveal-on-scroll" style="animation-delay: 100ms">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-100 to-pink-100 aspect-square mb-6">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                            <span class="text-5xl font-black text-white">A</span>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Amara Nwosu</h3>
                <p class="text-purple-600 font-medium mb-2">CTO & Co-founder</p>
                <p class="text-slate-500 text-sm">Security expert and former principal engineer at Stripe.</p>
            </div>
            
            <!-- Team Member 3 -->
            <div class="group reveal-on-scroll" style="animation-delay: 200ms">
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-pink-100 to-rose-100 aspect-square mb-6">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-pink-500 to-rose-500 rounded-full flex items-center justify-center">
                            <span class="text-5xl font-black text-white">C</span>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Chidi Eze</h3>
                <p class="text-pink-600 font-medium mb-2">Head of Product</p>
                <p class="text-slate-500 text-sm">Product leader with experience at Coursera and LinkedIn.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-indigo-600 to-purple-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative max-w-4xl mx-auto px-6 text-center reveal-on-scroll">
        <h2 class="text-3xl md:text-5xl font-black text-white mb-6">
            Join hundreds of companies already using Quizly
        </h2>
        <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto">
            Start your free trial today and transform your hiring process.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-indigo-600 rounded-2xl font-bold text-lg hover:shadow-2xl hover:shadow-black/20 hover:-translate-y-1 transition-all">
                Start Free Trial
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="#" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 text-white border border-white/30 rounded-2xl font-bold text-lg hover:bg-white/20 transition-all">
                Contact Sales
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
gsap.registerPlugin(ScrollTrigger);

gsap.utils.toArray('.reveal-on-scroll').forEach((el, i) => {
    gsap.from(el, {
        scrollTrigger: {
            trigger: el,
            start: 'top 85%',
            toggleActions: 'play none none reverse'
        },
        y: 50,
        opacity: 0,
        duration: 0.8,
        delay: i * 0.03,
        ease: 'power3.out'
    });
});
</script>
@endsection