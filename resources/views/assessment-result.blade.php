<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Assessment Result</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-4xl bg-white rounded-xl shadow-lg border border-slate-200">

    <!-- Header -->
    <div class="px-8 py-6 border-b border-slate-200">
      <h1 class="text-2xl font-semibold text-slate-900">
        Frontend Engineering Assessment
      </h1>
      <p class="text-slate-500 mt-1">
        Candidate: <span class="font-medium text-slate-700">Alex Morgan</span>
      </p>
    </div>

    <!-- Result Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 px-8 py-10">

      <!-- Percentage -->
      <div class="flex flex-col items-center justify-center text-center">
        <div class="relative w-40 h-40 rounded-full bg-slate-100 flex items-center justify-center">
          <div class="absolute inset-3 rounded-full bg-white flex items-center justify-center">
            <span class="text-4xl font-bold text-indigo-600">
              75%
            </span>
          </div>
        </div>
        <p class="mt-4 text-sm text-slate-500">
          Correct Answers
        </p>
      </div>

      <!-- Breakdown -->
      <div class="space-y-6">

        <div class="flex items-center justify-between p-4 border rounded-lg">
          <span class="text-slate-600">Total Questions</span>
          <span class="font-semibold text-slate-900">20</span>
        </div>

        <div class="flex items-center justify-between p-4 border rounded-lg">
          <span class="text-slate-600">Answered</span>
          <span class="font-semibold text-slate-900">15</span>
        </div>

        <div class="flex items-center justify-between p-4 border rounded-lg">
          <span class="text-emerald-600">Correct</span>
          <span class="font-semibold text-emerald-600">15</span>
        </div>

        <div class="flex items-center justify-between p-4 border rounded-lg">
          <span class="text-rose-600">Incorrect</span>
          <span class="font-semibold text-rose-600">5</span>
        </div>

      </div>

      <!-- Summary -->
      <div class="flex flex-col justify-center space-y-6">

        <div class="p-5 bg-indigo-50 border border-indigo-200 rounded-lg">
          <p class="text-sm text-indigo-700 font-medium">
            Performance Summary
          </p>
          <p class="mt-2 text-slate-700 text-sm leading-relaxed">
            The candidate demonstrated a strong understanding of core frontend concepts,
            with opportunities to improve in advanced optimization topics.
          </p>
        </div>

        <div class="text-sm text-slate-500">
          Assessment by
          <span class="font-medium text-slate-700">
            OpenAI Learning Systems
          </span>
        </div>

      </div>

    </div>

    <!-- Footer -->
    <div class="px-8 py-5 border-t border-slate-200 flex justify-end gap-4">

      <button class="px-4 py-2 text-sm text-slate-600 hover:text-slate-900">
        Review Answers
      </button>

      <button class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
        Download Report
      </button>

    </div>

  </div>

</body>
</html>
