<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modern Landing Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased text-gray-900">

    <!-- NAVBAR -->
    <header class="fixed w-full z-50 bg-white/80 backdrop-blur border-b border-gray-200">
        <nav class="max-w-7xl mx-auto flex items-center justify-between py-4 px-6">
            <div class="text-2xl font-bold">Brand<span class="text-blue-600">.</span></div>

            <!-- Desktop menu -->
            <ul class="hidden md:flex gap-8 text-gray-700 font-medium">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li><a href="#" class="hover:text-blue-600">Features</a></li>
                <li><a href="#" class="hover:text-blue-600">Pricing</a></li>
                <li><a href="{{ route('about') }}" class="hover:text-blue-600">About</a></li>
            </ul>

            <button class="hidden md:block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                Get Started
            </button>

            <!-- Mobile menu icon -->
            <button class="md:hidden p-2 text-gray-700">
                <!-- Hamburger icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
    <!-- FOOTER -->
    {{-- <footer class="py-10 text-center text-gray-600">
        <p>© 2025 Brand. All rights reserved.</p>
    </footer> --}}

    <!-- FOOTER -->
    <footer class="bg-gray-900 text-gray-300 pt-16 pb-10">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-10">

            <!-- Logo + Newsletter -->
            <div>
                <h2 class="text-2xl font-bold text-white">Brand<span class="text-blue-500">.</span></h2>
                <p class="mt-4 text-gray-400">
                    Build modern experiences with beautifully crafted components.
                </p>

                <!-- Newsletter -->
                <form class="mt-6 flex">
                    <input type="email" placeholder="Your email"
                        class="w-full px-4 py-3 rounded-l-lg bg-gray-800 border border-gray-700 focus:outline-none focus:border-blue-500">
                    <button class="px-5 py-3 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700 transition">
                        Subscribe
                    </button>
                </form>
            </div>

            <!-- Links -->
            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Product</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="hover:text-white">Features</a></li>
                    <li><a href="#" class="hover:text-white">Pricing</a></li>
                    <li><a href="#" class="hover:text-white">Integrations</a></li>
                    <li><a href="#" class="hover:text-white">Updates</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Company</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="hover:text-white">About Us</a></li>
                    <li><a href="#" class="hover:text-white">Careers</a></li>
                    <li><a href="#" class="hover:text-white">Blog</a></li>
                    <li><a href="#" class="hover:text-white">Press</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-white mb-4">Support</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="hover:text-white">Help Center</a></li>
                    <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-white">Contact Us</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Footer -->
        <div class="border-t border-gray-700 mt-12 pt-6">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between">

                <p class="text-gray-400 text-sm">© 2025 Brand. All rights reserved.</p>

                <!-- Social Icons -->
                <div class="flex space-x-5 mt-4 md:mt-0">

                    <!-- Twitter -->
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 4.6c-.9.4-1.8.6-2.8.8..."></path>
                        </svg>
                    </a>

                    <!-- Instagram -->
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7 2C4.8 2 3 3.8 3 6v12c0..."></path>
                        </svg>
                    </a>

                    <!-- LinkedIn -->
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4.98 3.5A2.5 2.5 0 1 1..."></path>
                        </svg>
                    </a>

                </div>
            </div>
        </div>
    </footer>


</body>

</html>