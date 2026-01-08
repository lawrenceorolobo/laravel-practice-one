<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Assessment Access</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-lg bg-white rounded-xl shadow-lg border border-slate-200">

    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-200 text-center">
      <h1 class="text-2xl font-semibold text-slate-900">
        Frontend Engineering Assessment
      </h1>
      <p class="mt-2 text-sm text-slate-500">
        Enter your details to begin the assessment
      </p>
    </div>

    <!-- Availability -->
    <div class="px-8 py-6 text-center space-y-2">
      <p class="text-xs text-slate-500 uppercase tracking-wide">
        Available Between
      </p>
      <p class="text-slate-900 font-medium">
        Jan 10, 2026 · 09:00 AM — Jan 12, 2026 · 06:00 PM
      </p>
    </div>

    <!-- Form -->
    <div class="px-8 py-6 space-y-5">

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Email Address
        </label>
        <input
          type="email"
          placeholder="you@example.com"
          class="w-full px-4 py-3 border border-slate-300 rounded-md text-slate-900
                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        />
      </div>

      <!-- Access Code -->
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          Exam Access Code
        </label>
        <input
          type="text"
          placeholder="XXXX-XXXX"
          class="w-full px-4 py-3 border border-slate-300 rounded-md text-slate-900
                 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                 tracking-widest text-center uppercase"
        />
        <p class="mt-1 text-xs text-slate-500">
          The access code was provided by the test administrator.
        </p>
      </div>

    </div>

    <!-- Action -->
    <div class="px-8 py-6 border-t border-slate-200">
      <button
        class="w-full py-3 bg-indigo-600 text-white rounded-md font-medium
               hover:bg-indigo-700 transition disabled:bg-slate-300 disabled:cursor-not-allowed"
      >
        Start Assessment
      </button>
    </div>

    <!--
