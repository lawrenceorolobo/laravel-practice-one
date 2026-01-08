<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>MCQ Exam</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-3xl bg-white rounded-xl shadow-lg border border-slate-200">

    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
      <div>
        <h1 class="text-lg font-semibold text-slate-900">
          Software Engineering Assessment
        </h1>
        <p class="text-sm text-slate-500">
          Question 3 of 20
        </p>
      </div>

      <div class="text-right">
        <p class="text-xs text-slate-400 uppercase tracking-wide">
          Test Started
        </p>
        <p class="text-sm font-medium text-slate-700">
          10:42 AM
        </p>
      </div>
    </div>

    <!-- Progress -->
    <div class="px-6 py-3">
      <div class="w-full bg-slate-200 rounded-full h-2">
        <div class="bg-indigo-600 h-2 rounded-full" style="width: 15%;"></div>
      </div>
    </div>

    <!-- Question -->
    <div class="px-6 py-6">
      <h2 class="text-lg font-medium text-slate-900 leading-relaxed">
        Which of the following best describes the purpose of a virtual DOM in modern front-end frameworks?
      </h2>
    </div>

    <!-- Options -->
    <div class="px-6 space-y-4">

      <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer hover:border-indigo-400 transition group">
        <input type="radio" name="option" class="mt-1 accent-indigo-600">
        <span class="text-slate-700 group-hover:text-slate-900">
          It directly manipulates the browser DOM for faster rendering.
        </span>
      </label>

      <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer hover:border-indigo-400 transition group">
        <input type="radio" name="option" class="mt-1 accent-indigo-600">
        <span class="text-slate-700 group-hover:text-slate-900">
          It acts as an abstraction layer to efficiently update only changed UI elements.
        </span>
      </label>

      <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer hover:border-indigo-400 transition group">
        <input type="radio" name="option" class="mt-1 accent-indigo-600">
        <span class="text-slate-700 group-hover:text-slate-900">
          It replaces CSS and improves styling performance.
        </span>
      </label>

      <label class="flex items-start gap-4 p-4 border rounded-lg cursor-pointer hover:border-indigo-400 transition group">
        <input type="radio" name="option" class="mt-1 accent-indigo-600">
        <span class="text-slate-700 group-hover:text-slate-900">
          It stores application data globally.
        </span>
      </label>

    </div>

    <!-- Footer Actions -->
    <div class="flex items-center justify-between px-6 py-5 mt-6 border-t border-slate-200">

      <button class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900">
        ← Previous
      </button>

      <button class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition">
        Next Question →
      </button>

    </div>

  </div>

</body>
</html>
