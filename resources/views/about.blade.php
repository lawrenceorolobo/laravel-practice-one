@extends('layouts.app')

@section('content')
    <section class="pt-32 pb-20">

        <!-- HERO SECTION -->
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900">
                About <span class="text-blue-600">Us</span>
            </h1>
            <p class="mt-6 text-lg text-gray-600 max-w-3xl mx-auto">
                We are dedicated to building modern digital experiences rooted in trust, clarity,
                and performance. Our team focuses on delivering solutions that make a meaningful
                impact on people and businesses worldwide.
            </p>
        </div>

        <!-- IMAGE + VALUES SECTION -->
        <div class="max-w-7xl mx-auto px-20 grid md:grid-cols-2 gap-10 mt-16 items-center">

            <!-- Image -->
            <div class="rounded-xl shadow-lg overflow-hidden">
                <img src="https://picsum.photos/800/600" alt="About us photo" class="w-full h-full object-cover">
            </div>

            <!-- Content -->
            <div>
                <h2 class="text-3xl font-bold text-center text-gray-900">
                    Who We Are
                </h2>
                <p class="mt-4 text-gray-600 leading-relaxed text-center">
                    Our mission is simple: build products that feel intuitive, reliable,
                    and genuinely helpful. With a team of designers, engineers, and thinkers,
                    we embrace user-first design principles to craft experiences that
                    inspire confidence and trust.
                </p>

                <div class="mt-8 space-y-6">

                    <div class="flex gap-4">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mt-2"></div>
                        <p class="text-gray-700"><strong>Trust-driven design</strong> that ensures clarity and transparency
                            in every interaction.</p>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mt-2"></div>
                        <p class="text-gray-700"><strong>Consistent quality</strong> using modern technology, research, and
                            UI/UX best practices.</p>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mt-2"></div>
                        <p class="text-gray-700"><strong>User-centered philosophy</strong> that guides every decision we
                            make.</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- TEAM SECTION -->
        <div class="max-w-7xl mx-auto px-20 mt-24 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Meet Our Team</h2>
            <p class="mt-4 text-gray-600 max-w-2xl mx-auto">
                A group of passionate individuals who believe in building trustworthy,
                meaningful digital experiences.
            </p>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-10 mt-12">

                <!-- Team Member -->
                <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                    <img src="https://picsum.photos/300/300" alt="Team Member" class="w-full h-48 object-cover rounded-lg">
                    <h3 class="text-xl font-semibold mt-4">Sarah Johnson</h3>
                    <p class="text-gray-600">Lead Designer</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                    <img src="https://picsum.photos/301/300" alt="Team Member" class="w-full h-48 object-cover rounded-lg">
                    <h3 class="text-xl font-semibold mt-4">Michael Lee</h3>
                    <p class="text-gray-600">Head of Engineering</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition">
                    <img src="https://picsum.photos/302/300" alt="Team Member" class="w-full h-48 object-cover rounded-lg">
                    <h3 class="text-xl font-semibold mt-4">Aisha Patel</h3>
                    <p class="text-gray-600">Product Strategist</p>
                </div>

            </div>
        </div>

        <!-- CTA -->
        <div class="max-w-7xl mx-auto px-6 mt-28 text-center">
            <div class="bg-blue-600 text-white p-12 rounded-2xl shadow-lg">
                <h2 class="text-3xl font-bold">Want to learn more about what we do?</h2>
                <p class="mt-3 text-lg text-blue-100 max-w-xl mx-auto">
                    Discover how we help businesses grow with modern, trusted digital solutions.
                </p>
                <a href="#"
                    class="mt-8 inline-block bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-medium hover:bg-gray-100 transition">
                    Contact Us
                </a>
            </div>
        </div>

    </section>
@endsection