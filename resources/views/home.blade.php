@extends('layouts.app')

@section('content')
    <!-- HERO SECTION -->
    <section class="pt-32 pb-20 bg-gradient-to-b from-white to-blue-50">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 items-center gap-12">

            <!-- Left -->
            <div>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                    Build Better Experiences With
                    <span class="text-blue-600">Modern Design</span>
                </h1>
                <p class="mt-6 text-lg text-gray-600">
                    Increase conversion and boost user satisfaction with a beautifully optimized landing page
                    layout.
                </p>

                <div class="mt-8 flex gap-4">
                    <a href="#"
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-medium hover:bg-blue-700 transition">
                        Start For Free
                    </a>
                    <a href="#" class="px-6 py-3 rounded-lg text-lg font-medium bg-gray-200 hover:bg-gray-300 transition">
                        Learn More
                    </a>
                </div>

                <div class="mt-6 flex items-center gap-3 text-gray-500">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927C9.349 2.028 10.651 2.028 10.951 2.927l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.899-.755 1.64-1.54 1.138l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.502-1.84-.239-1.54-1.138l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z" />
                        </svg>
                    </span>
                    <span class="font-medium">Rated 4.9/5 by 12k+ customers</span>
                </div>
            </div>

            <!-- Right -->
            <div class="relative">
                <div class="rounded-xl shadow-xl bg-white p-6">
                    <img class="rounded-lg" src="https://picsum.photos/600/400" alt="Hero Preview" />
                </div>
            </div>

        </div>
    </section>

    <!-- FEATURES -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold">Powerful Features</h2>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                Designed to help you create, launch, and grow with ease using intuitive tools.
            </p>

            <div class="grid md:grid-cols-3 gap-10 mt-12">
                <div class="p-6 bg-white border rounded-xl shadow-sm hover:shadow-md transition">
                    <h3 class="text-xl font-semibold mb-3">Fast Performance</h3>
                    <p class="text-gray-600">Optimized to load quickly and enhance user satisfaction.</p>
                </div>
                <div class="p-6 bg-white border rounded-xl shadow-sm hover:shadow-md transition">
                    <h3 class="text-xl font-semibold mb-3">Responsive Layout</h3>
                    <p class="text-gray-600">Looks great on all devices from mobile to desktop.</p>
                </div>
                <div class="p-6 bg-white border rounded-xl shadow-sm hover:shadow-md transition">
                    <h3 class="text-xl font-semibold mb-3">Clean Aesthetic</h3>
                    <p class="text-gray-600">Modern minimalist design for maximum clarity.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-blue-600 text-white text-center">
        <h2 class="text-3xl md:text-4xl font-bold">Ready to Get Started?</h2>
        <p class="mt-4 text-lg">Build your next landing page in minutes—not hours.</p>
        <a href="#"
            class="mt-8 inline-block bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-medium hover:bg-gray-100 transition">
            Get Started Now
        </a>
    </section>
@endsection