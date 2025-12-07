<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 antialiased text-gray-900">

    <!-- MOBILE NAV -->
    <div class="md:hidden bg-white border-b py-4 px-6 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-bold text-blue-600">Dashboard</h1>
        <button>
            <svg class="w-7 h-7 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div class="flex">

        <!-- SIDEBAR (Desktop) -->
        <aside class="hidden md:flex flex-col w-64 h-screen sticky top-0 bg-white border-r">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-blue-600">Brand.</h1>
            </div>

            <nav class="flex-1 p-6 space-y-3">
                <a href="#" class="flex items-center gap-3 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                    </svg>
                    Overview
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2h-2M4 6a2 2 0 012-2h2m12 7V18a2 2 0 01-2 2h-2m-8 0H6a2 2 0 01-2-2v-5m16 0H4" />
                    </svg>
                    Analytics
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 8h10M7 12h8m-8 4h6" />
                    </svg>
                    Reports
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zM12 2v2m6 2l-1.41 1.41M2 12H4m2-6L4.59 7.41M12 20v2m6-6l1.41 1.41M4 16l1.41 1.41" />
                    </svg>
                    Settings
                </a>
            </nav>

            <div class="p-6 border-t">
                <button class="flex items-center gap-2 text-gray-600 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1m0-8V7a2 2 0 114 0v1" />
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6 md:p-10">

            <!-- Top Bar -->
            <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Overview</h2>

                <button class="bg-blue-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                    Download Report
                </button>
            </div>

            <!-- STATS GRID -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <p class="text-gray-600">Total Users</p>
                    <h3 class="text-2xl font-bold mt-2 text-gray-900">12,450</h3>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <p class="text-gray-600">Monthly Revenue</p>
                    <h3 class="text-2xl font-bold mt-2 text-gray-900">$84,710</h3>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <p class="text-gray-600">Conversions</p>
                    <h3 class="text-2xl font-bold mt-2 text-gray-900">4.9%</h3>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-sm border">
                    <p class="text-gray-600">Active Sessions</p>
                    <h3 class="text-2xl font-bold mt-2 text-gray-900">1,221</h3>
                </div>
            </div>

            <!-- CHART + RECENT ACTIVITY -->
            <div class="grid lg:grid-cols-3 gap-6">

                <!-- Chart Placeholder -->
                <div class="bg-white lg:col-span-2 p-6 rounded-xl shadow-sm border">
                    <h3 class="text-lg font-semibold">Performance Chart</h3>
                    <div class="mt-4 h-64 flex justify-center items-center bg-gray-100 rounded-lg">
                        <span class="text-gray-500">[Add Chart Here]</span>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white p-6 rounded-xl shadow-sm border">
                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>

                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-3 h-3 bg-blue-600 rounded-full mt-2"></div>
                            <p class="text-gray-700">
                                New user <strong>James Carter</strong> joined.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <div class="w-3 h-3 bg-green-600 rounded-full mt-2"></div>
                            <p class="text-gray-700">
                                Monthly revenue increased by <strong>12%</strong>.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mt-2"></div>
                            <p class="text-gray-700">
                                System performance warning detected.
                            </p>
                        </li>

                        <li class="flex items-start gap-3">
                            <div class="w-3 h-3 bg-purple-600 rounded-full mt-2"></div>
                            <p class="text-gray-700">
                                New integration added to the platform.
                            </p>
                        </li>
                    </ul>
                </div>

            </div>

        </main>

    </div>

</body>

</html>