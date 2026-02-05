<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Quizly - Modern assessment platform for businesses. Create, manage, and analyze assessments with AI-powered fraud detection.">
    <title>@yield('title', 'Quizly - Modern Assessment Platform')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📝</text></svg>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Glassmorphism Navbar */
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        .glass-nav.scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        }
        
        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Glass Skeleton */
        .glass-skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            backdrop-filter: blur(10px);
        }
        
        /* Cursor Glow */
        .cursor-glow {
            position: fixed;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: opacity 0.3s;
        }
        
        /* Gradient Border */
        .gradient-border {
            position: relative;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #6366f1, #a855f7, #ec4899) border-box;
            border: 2px solid transparent;
        }
        
        /* Tilt Card Effect */
        .tilt-card {
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }
        .tilt-card:hover {
            transform: perspective(1000px) rotateX(5deg) rotateY(-5deg);
        }
        
        /* Text Shimmer */
        .text-shimmer {
            background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899, #6366f1);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: text-shimmer 3s linear infinite;
        }
        @keyframes text-shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }
        
        /* Glow Button */
        .glow-button {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
            transition: box-shadow 0.3s ease;
        }
        .glow-button:hover {
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.5), 0 0 60px rgba(99, 102, 241, 0.3);
        }
        
        /* Floating Animation */
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        /* Smooth transitions */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Parallax */
        .parallax {
            will-change: transform;
        }
    </style>
</head>
<body class="antialiased text-slate-900 bg-white">

    <!-- Navigation -->
    <nav id="navbar" class="glass-nav fixed top-0 left-0 right-0 z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="text-2xl font-black text-indigo-600 hover:scale-105 transition-transform">
                    Quizly.
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors relative group">
                        Home
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="{{ url('/') }}#features" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors relative group">
                        Features
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="{{ url('/') }}#pricing" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors relative group">
                        Pricing
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all"></span>
                    </a>
                    <a href="{{ route('about') }}" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors relative group">
                        About
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-indigo-600 group-hover:w-full transition-all"></span>
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-indigo-600 font-medium transition-colors">
                        Sign in
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold hover:shadow-lg hover:shadow-indigo-500/25 hover:-translate-y-0.5 transition-all">
                        Get Started Free
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-t">
            <div class="px-6 py-4 space-y-4">
                <a href="{{ url('/') }}" class="block text-slate-600 hover:text-indigo-600 font-medium py-2">Home</a>
                <a href="{{ url('/') }}#features" class="block text-slate-600 hover:text-indigo-600 font-medium py-2">Features</a>
                <a href="{{ url('/') }}#pricing" class="block text-slate-600 hover:text-indigo-600 font-medium py-2">Pricing</a>
                <a href="{{ route('about') }}" class="block text-slate-600 hover:text-indigo-600 font-medium py-2">About</a>
                <hr class="border-slate-200">
                <a href="{{ route('login') }}" class="block text-slate-600 hover:text-indigo-600 font-medium py-2">Sign in</a>
                <a href="{{ route('register') }}" class="block w-full text-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">
                    Get Started Free
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid md:grid-cols-5 gap-12 mb-16">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <a href="{{ url('/') }}" class="text-3xl font-black text-indigo-400">
                        Quizly.
                    </a>
                    <p class="text-slate-400 mt-4 max-w-sm">
                        The modern assessment platform trusted by leading companies worldwide.
                    </p>
                    <div class="flex gap-4 mt-6">
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-indigo-500 rounded-lg flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-indigo-500 rounded-lg flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 hover:bg-indigo-500 rounded-lg flex items-center justify-center transition-all">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-bold text-white mb-4">Product</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="#" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Integrations</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Changelog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Company</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Legal</h4>
                    <ul class="space-y-3 text-slate-400">
                        <li><a href="#" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Security</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-slate-500 text-sm">
                    © <span id="year"></span> Quizly. All rights reserved.
                </p>
                <p class="text-slate-500 text-sm">
                    Made with ❤️ for better hiring
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Year
        document.getElementById('year').textContent = new Date().getFullYear();

        // Mobile menu
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

    <!-- Cursor Glow -->
    <div id="cursorGlow" class="cursor-glow hidden md:block" style="opacity: 0;"></div>
    <script>
        const cursorGlow = document.getElementById('cursorGlow');
        if (cursorGlow) {
            document.addEventListener('mousemove', (e) => {
                cursorGlow.style.left = e.clientX + 'px';
                cursorGlow.style.top = e.clientY + 'px';
                cursorGlow.style.opacity = '1';
            });
            document.addEventListener('mouseleave', () => {
                cursorGlow.style.opacity = '0';
            });
        }

        // Parallax on scroll
        const parallaxElements = document.querySelectorAll('.parallax');
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            parallaxElements.forEach(el => {
                const speed = el.dataset.speed || 0.5;
                el.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });
    </script>

    <!-- Toast Notification -->
    <div id="notification-toast" class="fixed top-24 right-6 z-50 transform transition-all duration-300 translate-x-full opacity-0">
        <div class="bg-white/90 backdrop-blur-lg border border-indigo-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4 max-w-sm">
            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-green-600 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-slate-800" id="toast-title">Notification</h4>
                <p class="text-sm text-slate-500" id="toast-message">Message body</p>
            </div>
            <button onclick="hideToast()" class="ml-auto text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <script>
        function showToast(title, message) {
            const toast = document.getElementById('notification-toast');
            document.getElementById('toast-title').textContent = title;
            document.getElementById('toast-message').textContent = message;
            
            toast.classList.remove('translate-x-full', 'opacity-0');
            
            // Play notification sound
            const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
            audio.play().catch(() => {});

            setTimeout(hideToast, 5000);
        }

        function hideToast() {
            const toast = document.getElementById('notification-toast');
            toast.classList.add('translate-x-full', 'opacity-0');
        }

        // Initialize Echo Listener
        document.addEventListener('DOMContentLoaded', () => {
            const userId = "{{ auth()->id() }}";
            
            if (userId && window.Echo) {
                console.log('Listening for notifications on assessments.' + userId);
                
                window.Echo.private(`assessments.${userId}`)
                    .listen('AssessmentCompleted', (e) => {
                        console.log('Event received:', e);
                        showToast(
                            'Assessment Completed! 🎓', 
                            `${e.candidate_name} has completed "${e.title}" with a score of ${e.score}%`
                        );
                    });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>