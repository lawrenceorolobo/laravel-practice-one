@extends('layouts.user')
@section('title', 'Assessment Details | Quizly')

@section('content')
<style>
.builder-block {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    cursor: grab;
    user-select: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
@media (max-width: 639px) {
    .builder-block { padding: 4px 8px; font-size: 10px; gap: 4px; }
    .builder-block svg { width: 12px; height: 12px; }
}
.builder-block:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.builder-block:active {
    cursor: grabbing;
    transform: scale(0.95);
    opacity: 0.7;
}
#questionsDropZone.drag-over {
    outline: 2px solid rgb(99 102 241);
    outline-offset: -2px;
    background: rgba(99, 102, 241, 0.02);
}
</style>

<div id="loading" class="text-center py-16">
    <div class="skeleton h-8 w-64 mx-auto mb-4"></div>
    <div class="skeleton h-4 w-48 mx-auto"></div>
</div>

<div id="content" class="hidden">
<div class="max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <a href="/assessments" class="text-indigo-600 hover:text-indigo-700 font-medium flex items-center gap-2 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <h2 class="text-xl sm:text-2xl font-bold" id="title">Assessment</h2>
            <p class="text-slate-500 text-sm" id="description"></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="exportResultsCsv()" class="px-3 py-2 border rounded-lg hover:bg-slate-50 text-xs sm:text-sm flex items-center gap-1.5" title="Export results as CSV">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
            <button onclick="editAssessment()" class="px-3 py-2 border rounded-lg hover:bg-slate-50 text-xs sm:text-sm">Edit</button>
            <button onclick="duplicateAssessment()" class="px-3 py-2 border border-indigo-200 text-indigo-600 rounded-lg hover:bg-indigo-50 flex items-center gap-1.5 text-xs sm:text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                Duplicate
            </button>
            <button onclick="publishAssessment()" id="publishBtn" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-xs sm:text-sm">Publish</button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
        <div class="glass rounded-xl p-3 sm:p-4 text-center">
            <p class="text-2xl sm:text-3xl font-bold text-indigo-600" id="questionsCount">0</p>
            <p class="text-slate-500 text-xs sm:text-sm">Questions</p>
        </div>
        <div class="glass rounded-xl p-3 sm:p-4 text-center">
            <p class="text-2xl sm:text-3xl font-bold text-emerald-600" id="candidatesCount">0</p>
            <p class="text-slate-500 text-xs sm:text-sm">Candidates</p>
        </div>
        <div class="glass rounded-xl p-3 sm:p-4 text-center">
            <p class="text-2xl sm:text-3xl font-bold text-purple-600" id="completedCount">0</p>
            <p class="text-slate-500 text-xs sm:text-sm">Completed</p>
        </div>
        <div class="glass rounded-xl p-3 sm:p-4 text-center">
            <p class="text-2xl sm:text-3xl font-bold text-amber-600" id="avgScore">-</p>
            <p class="text-slate-500 text-xs sm:text-sm">Avg Score</p>
        </div>
    </div>

    <!-- Public Link (shown after publish) -->
    <div id="publicLinkSection" class="hidden glass rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg mb-1">📎 Public Assessment Link</h3>
                <p class="text-sm text-slate-500">Share this link with anyone to let them join the assessment</p>
            </div>
            <button onclick="copyPublicLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">Copy Link</button>
        </div>
        <div class="mt-3 bg-slate-50 border rounded-lg px-4 py-3">
            <code id="publicLinkUrl" class="text-sm text-indigo-600 break-all"></code>
        </div>
    </div>

    <!-- Drag & Drop Question Builder -->
    <div class="glass rounded-2xl p-4 sm:p-6 mb-6">
        <div class="flex justify-between items-center mb-3">
            <div>
                <h3 class="font-bold text-base sm:text-lg">Question Builder</h3>
                <p class="text-[10px] text-slate-400 hidden sm:block">Drag a type to add (desktop) or tap to add instantly (mobile)</p>
            </div>
            <button type="button" onclick="document.getElementById('builderPalette').classList.toggle('hidden')" class="text-sm text-indigo-600 font-medium hover:text-indigo-700">
                Show/Hide
            </button>
        </div>
        <div id="builderPalette" class="space-y-3">
            <!-- Standard -->
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1.5">Standard</p>
                <div class="flex flex-wrap gap-2">
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="single_choice">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>
                        Single Choice
                    </div>
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="multiple_choice">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="3" stroke-width="2"/><path d="M9 12l2 2 4-4" stroke-width="2" stroke-linecap="round"/></svg>
                        Multiple Choice
                    </div>
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="true_false">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 12l5 5L20 7" stroke-width="2" stroke-linecap="round"/></svg>
                        True / False
                    </div>
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="text_input">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 7h16M4 12h10M4 17h14" stroke-width="2" stroke-linecap="round"/></svg>
                        Text Input
                    </div>
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="fill_blank">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 17h6m4 0h6M4 7h16M4 12h16" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 3"/></svg>
                        Fill Blank
                    </div>
                    <div class="builder-block bg-blue-50 border border-blue-200 text-blue-700" draggable="true" data-qtype="numeric">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 20V4m5 16V4m5 16V4" stroke-width="2" stroke-linecap="round"/></svg>
                        Numeric
                    </div>
                </div>
            </div>
            <!-- Sorting & Matching -->
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1.5">Sorting & Matching</p>
                <div class="flex flex-wrap gap-2">
                    <div class="builder-block bg-amber-50 border border-amber-200 text-amber-700" draggable="true" data-qtype="ordering">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 7h4m-4 5h4m-4 5h4m6-10h8m-8 5h8m-8 5h8" stroke-width="2" stroke-linecap="round"/></svg>
                        Ordering
                    </div>
                    <div class="builder-block bg-amber-50 border border-amber-200 text-amber-700" draggable="true" data-qtype="drag_drop_sort">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" stroke-width="2" stroke-linecap="round"/></svg>
                        Drag & Drop Sort
                    </div>
                    <div class="builder-block bg-amber-50 border border-amber-200 text-amber-700" draggable="true" data-qtype="matching">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h4m8 0h4M4 12h4m8 0h4M4 18h4m8 0h4M8 6l8 12M8 12h8M8 18l8-12" stroke-width="1.5" stroke-linecap="round"/></svg>
                        Matching Pairs
                    </div>
                </div>
            </div>
            <!-- Psychometric / Reasoning -->
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1.5">Psychometric / Reasoning</p>
                <div class="flex flex-wrap gap-2">
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="sequence_pattern">🔢 Sequence</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="matrix_pattern">🔲 Matrix</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="odd_one_out">🎯 Odd One Out</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="spatial_rotation">🔄 Spatial</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="shape_assembly">🧩 Shape Assembly</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="shape_puzzle">🧱 Shape Puzzle</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="analogy">🔗 Analogy</div>
                    <div class="builder-block bg-purple-50 border border-purple-200 text-purple-700" draggable="true" data-qtype="pattern_recognition">👁️ Pattern</div>
                </div>
            </div>
            <!-- Interactive -->
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold mb-1.5">Interactive</p>
                <div class="flex flex-wrap gap-2">
                    <div class="builder-block bg-emerald-50 border border-emerald-200 text-emerald-700" draggable="true" data-qtype="hotspot">📍 Hotspot</div>
                    <div class="builder-block bg-emerald-50 border border-emerald-200 text-emerald-700" draggable="true" data-qtype="code_snippet">💻 Code</div>
                    <div class="builder-block bg-emerald-50 border border-emerald-200 text-emerald-700" draggable="true" data-qtype="likert_scale">📊 Likert Scale</div>
                    <div class="builder-block bg-emerald-50 border border-emerald-200 text-emerald-700" draggable="true" data-qtype="word_problem">📝 Word Problem</div>
                    <div class="builder-block bg-emerald-50 border border-emerald-200 text-emerald-700" draggable="true" data-qtype="mental_maths">🧮 Mental Maths</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions -->
    <div class="glass rounded-2xl p-4 sm:p-6 mb-6" id="questionsDropZone">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-4">
            <h3 class="font-bold text-base sm:text-lg">Questions</h3>
            <div class="flex gap-2 flex-wrap">
                <button onclick="openQuestionBank()" class="px-3 py-1.5 sm:px-4 sm:py-2 border border-purple-500 text-purple-600 rounded-lg hover:bg-purple-50 flex items-center gap-1.5 text-xs sm:text-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="hidden xs:inline">Question</span> Bank
                </button>
                <button onclick="openQuestionImportModal()" class="px-3 py-1.5 sm:px-4 sm:py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 flex items-center gap-1.5 text-xs sm:text-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v12"/></svg>
                    <span class="hidden xs:inline">Import</span> CSV
                </button>
                <button onclick="openQuestionModal()" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-1.5 text-xs sm:text-sm">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Question
                </button>
            </div>
        </div>
        <!-- Drop zone hint (shown during drag) -->
        <div id="dropHint" class="hidden border-2 border-dashed border-indigo-300 rounded-xl p-6 text-center text-indigo-400 mb-4 bg-indigo-50/50 transition-all">
            <svg class="w-8 h-8 mx-auto mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            Drop here to add question
        </div>
        <div id="questionsList"></div>
    </div>

    <!-- Invitees -->
    <div class="glass rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg">Candidates</h3>
            <div class="flex gap-2">
                <button onclick="resendAll()" id="resendAllBtn" class="hidden px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Resend All
                </button>
                <button onclick="openInviteModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Invite Candidates
                </button>
            </div>
        </div>
        <div id="inviteesList"></div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="questionModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4" id="questionModalTitle">Add Question</h3>
        <form id="questionForm">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Type</label>
                <select name="type" onchange="onTypeChange(this.value)" class="w-full px-4 py-2 border rounded-lg">
                    <optgroup label="Standard">
                        <option value="single_choice">Single Choice</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True / False</option>
                        <option value="text_input">Text Input</option>
                        <option value="fill_blank">Fill in the Blank</option>
                        <option value="numeric">Numeric Answer</option>
                    </optgroup>
                    <optgroup label="Sorting & Matching">
                        <option value="ordering">Ordering</option>
                        <option value="drag_drop_sort">Drag & Drop Sort</option>
                        <option value="matching">Matching Pairs</option>
                    </optgroup>
                    <optgroup label="Psychometric / Reasoning">
                        <option value="sequence_pattern">Sequence Pattern</option>
                        <option value="matrix_pattern">Matrix Pattern</option>
                        <option value="odd_one_out">Odd One Out</option>
                        <option value="spatial_rotation">Spatial Rotation</option>
                        <option value="shape_assembly">Shape Assembly</option>
                        <option value="shape_puzzle">Shape Puzzle (Fit the Pieces)</option>
                        <option value="analogy">Analogy</option>
                        <option value="pattern_recognition">Pattern Recognition</option>
                    </optgroup>
                    <optgroup label="Interactive">
                        <option value="hotspot">Hotspot (Image Click)</option>
                        <option value="code_snippet">Code Snippet</option>
                        <option value="likert_scale">Likert Scale</option>
                        <option value="word_problem">Word Problem</option>
                        <option value="mental_maths">Mental Maths</option>
                    </optgroup>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Question Text *</label>
                <textarea name="text" required rows="2" class="w-full px-4 py-2 border rounded-lg" oninput="checkQuestionForm()"></textarea>
            </div>

            <!-- Options for choice-based types -->
            <div id="optionsContainer" class="mb-4">
                <label class="block text-sm font-medium mb-2">Options</label>
                <p id="optionsHint" class="text-xs text-slate-400 mb-2"></p>
                <div id="optionsList" class="space-y-2"></div>
                <button type="button" onclick="addOption()" class="mt-2 text-indigo-600 font-medium text-sm">+ Add Option</button>
            </div>

            <!-- Expected answer for text/fill_blank types -->
            <div id="textAnswerContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Expected Answer *</label>
                <textarea name="expected_answer" rows="2" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter the correct answer..."></textarea>
                <p class="text-xs text-slate-400 mt-1">For flexible matching, separate with <strong>||</strong> e.g. <em>Paris || paris france</em></p>
            </div>

            <!-- Numeric config -->
            <div id="numericContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Correct Numeric Answer *</label>
                <input type="number" name="numeric_answer" step="any" class="w-full px-4 py-2 border rounded-lg mb-2" placeholder="e.g. 42">
                <label class="block text-sm font-medium mb-2">Tolerance (±)</label>
                <input type="number" name="numeric_tolerance" step="any" value="0" min="0" class="w-full px-4 py-2 border rounded-lg">
                <p class="text-xs text-slate-400 mt-1">Set to 0 for exact match, or allow margin (e.g. 0.5)</p>
            </div>

            <!-- Ordering items -->
            <div id="orderingContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Items (in CORRECT order)</label>
                <p class="text-xs text-slate-400 mb-2">Add items top-to-bottom in the correct sequence. They'll be shuffled during the test.</p>
                <div id="orderingList" class="space-y-2"></div>
                <button type="button" onclick="addOrderingItem()" class="mt-2 text-indigo-600 font-medium text-sm">+ Add Item</button>
            </div>

            <!-- Matching pairs -->
            <div id="matchingContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Matching Pairs</label>
                <p class="text-xs text-slate-400 mb-2">Define left → right correct pairings</p>
                <div id="matchingList" class="space-y-2"></div>
                <button type="button" onclick="addMatchingPair()" class="mt-2 text-indigo-600 font-medium text-sm">+ Add Pair</button>
            </div>

            <!-- Likert config -->
            <div id="likertContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Scale Labels</label>
                <p class="text-xs text-slate-400 mb-2">Customize the 5-point scale labels (or keep defaults)</p>
                <div class="grid grid-cols-5 gap-2">
                    <input type="text" name="likert_1" value="Strongly Disagree" class="px-2 py-1 border rounded text-xs text-center">
                    <input type="text" name="likert_2" value="Disagree" class="px-2 py-1 border rounded text-xs text-center">
                    <input type="text" name="likert_3" value="Neutral" class="px-2 py-1 border rounded text-xs text-center">
                    <input type="text" name="likert_4" value="Agree" class="px-2 py-1 border rounded text-xs text-center">
                    <input type="text" name="likert_5" value="Strongly Agree" class="px-2 py-1 border rounded text-xs text-center">
                </div>
            </div>

            <!-- Media URL for pattern/image-based types -->
            <div id="mediaContainer" class="mb-4 hidden">
                <label class="block text-sm font-medium mb-2">Question Image URL (optional)</label>
                <input type="url" name="media_url" class="w-full px-4 py-2 border rounded-lg" placeholder="https://example.com/pattern.png">
                <p class="text-xs text-slate-400 mt-1">Image displayed above the question for visual/pattern types</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Points</label>
                <input type="number" name="points" value="1" min="1" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div id="questionError" class="hidden mb-3 p-3 bg-red-50 text-red-600 rounded-lg text-sm"></div>
            <div class="flex gap-3">
                <button type="submit" id="saveQuestionBtn" disabled class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Save Question</button>
                <button type="button" onclick="closeQuestionModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Invite Modal (Tabbed: Manual / CSV) -->
<div id="inviteModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Invite Candidates</h3>
        <!-- Tabs -->
        <div class="flex border-b mb-4">
            <button onclick="switchInviteTab('manual')" id="inviteTabManual" class="px-4 py-2 font-medium text-sm border-b-2 border-indigo-600 text-indigo-600">Manual Entry</button>
            <button onclick="switchInviteTab('csv')" id="inviteTabCsv" class="px-4 py-2 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700">CSV Upload</button>
        </div>
        <!-- Manual Tab -->
        <div id="inviteManualTab">
            <form id="inviteForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Email *</label>
                    <input type="email" name="email" required class="w-full px-4 py-2 border rounded-lg" placeholder="candidate@example.com" oninput="checkInviteForm()">
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">First Name</label>
                        <input type="text" name="first_name" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Last Name</label>
                        <input type="text" name="last_name" class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" id="addCandidateBtn" disabled class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Add Candidate</button>
                    <button type="button" onclick="closeInviteModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
                </div>
            </form>
            <!-- Existing candidates from other assessments -->
            <div class="mt-5 pt-4 border-t">
                <p class="text-sm font-semibold text-slate-700 mb-2">Or add from previous assessments</p>
                <input type="text" id="reuseSearchInput" placeholder="Search by email or name..." class="w-full px-4 py-2 border rounded-lg text-sm mb-3" oninput="filterReuseCandidates()">
                <div id="reuseLoading" class="text-center py-4 text-sm text-slate-400">Loading candidates...</div>
                <div id="reuseEmpty" class="hidden text-center py-4 text-sm text-slate-400">No candidates from other assessments</div>
                <div id="reuseCandidatesList" class="hidden max-h-48 overflow-y-auto border rounded-lg">
                    <div class="sticky top-0 bg-slate-50 flex items-center gap-2 px-3 py-2 border-b">
                        <input type="checkbox" id="reuseSelectAll" onchange="toggleAllReuse(this.checked)" class="w-4 h-4">
                        <label for="reuseSelectAll" class="text-xs font-medium text-slate-600">Select All</label>
                        <span id="reuseSelectedCount" class="ml-auto text-xs text-slate-400">0 selected</span>
                    </div>
                    <div id="reuseRows"></div>
                </div>
                <button type="button" id="addReuseCandidatesBtn" disabled onclick="addReuseCandidates()" class="hidden w-full mt-3 bg-indigo-600 text-white py-2.5 rounded-lg font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">Add Selected Candidates</button>
            </div>
        </div>
        <!-- CSV Tab -->
        <div id="inviteCsvTab" class="hidden">
            <div class="mb-3">
                <a href="#" onclick="downloadInviteTemplate(); return false;" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download template CSV
                </a>
            </div>
            <div id="inviteCsvDropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors" onclick="document.getElementById('inviteCsvInput').click()" ondragover="event.preventDefault(); this.classList.add('border-indigo-500','bg-indigo-50')" ondragleave="this.classList.remove('border-indigo-500','bg-indigo-50')" ondrop="handleInviteCsvDrop(event)">
                <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <p class="text-sm text-gray-600">Drag & drop CSV here, or <span class="text-indigo-600 font-medium">browse</span></p>
                <p class="text-xs text-gray-400 mt-1">Columns: email, first_name (optional), last_name (optional)</p>
            </div>
            <input type="file" id="inviteCsvInput" accept=".csv,.txt" class="hidden" onchange="handleInviteCsvSelect(this)">
            <div id="inviteCsvPreview" class="hidden mt-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium"><span id="inviteCsvCount">0</span> invitees found</p>
                    <button onclick="clearInviteCsv()" class="text-xs text-red-500 hover:underline">Clear</button>
                </div>
                <div class="max-h-40 overflow-y-auto border rounded-lg">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 sticky top-0"><tr><th class="px-3 py-1 text-left">Email</th><th class="px-3 py-1 text-left">First Name</th><th class="px-3 py-1 text-left">Last Name</th></tr></thead>
                        <tbody id="inviteCsvRows"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="button" id="uploadInviteCsvBtn" disabled onclick="uploadInviteCsv()" class="flex-1 bg-emerald-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Upload Invitees</button>
                <button type="button" onclick="closeInviteModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Invitee Modal -->
<div id="editInviteeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full">
        <h3 class="text-xl font-bold mb-4">Edit Candidate</h3>
        <form id="editInviteeForm">
            <input type="hidden" id="editInviteeId">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Email *</label>
                <input type="email" id="editInvEmail" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">First Name</label>
                    <input type="text" id="editInvFirstName" class="w-full px-4 py-2 border rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Last Name</label>
                    <input type="text" id="editInvLastName" class="w-full px-4 py-2 border rounded-lg">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold">Save Changes</button>
                <button type="button" onclick="document.getElementById('editInviteeModal').classList.add('hidden')" class="px-6 py-3 border rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Question CSV Import Modal -->
<div id="questionImportModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4">Import Questions from CSV</h3>
        <div class="mb-3">
            <a href="#" onclick="downloadQuestionTemplate(); return false;" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download template CSV
            </a>
        </div>
        <div id="questionCsvDropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors" onclick="document.getElementById('questionCsvInput').click()" ondragover="event.preventDefault(); this.classList.add('border-indigo-500','bg-indigo-50')" ondragleave="this.classList.remove('border-indigo-500','bg-indigo-50')" ondrop="handleQuestionCsvDrop(event)">
            <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
            <p class="text-sm text-gray-600">Drag & drop CSV here, or <span class="text-indigo-600 font-medium">browse</span></p>
            <p class="text-xs text-gray-400 mt-1">Columns: question, type, points, option_a-d, correct_answer, expected_answer</p>
        </div>
        <input type="file" id="questionCsvInput" accept=".csv,.txt" class="hidden" onchange="handleQuestionCsvSelect(this)">
        <div id="questionCsvPreview" class="hidden mt-4">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-medium"><span id="questionCsvCount">0</span> questions found</p>
                <button onclick="clearQuestionCsv()" class="text-xs text-red-500 hover:underline">Clear</button>
            </div>
            <div class="max-h-40 overflow-y-auto border rounded-lg">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50 sticky top-0"><tr><th class="px-3 py-1 text-left">Question</th><th class="px-3 py-1 text-left">Type</th><th class="px-3 py-1 text-left">Points</th></tr></thead>
                    <tbody id="questionCsvRows"></tbody>
                </table>
            </div>
        </div>
        <div id="questionImportErrors" class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800"></div>
        <div class="flex gap-3 mt-4">
            <button type="button" id="uploadQuestionCsvBtn" disabled onclick="uploadQuestionCsv()" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed">Import Questions</button>
            <button type="button" onclick="closeQuestionImportModal()" class="px-6 py-3 border rounded-lg">Cancel</button>
        </div>
    </div>
</div>
</div>

<!-- Answers Modal -->
<div id="answersModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" onclick="if(event.target===this)closeAnswersModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-6 border-b shrink-0">
            <h2 class="text-xl font-bold">Candidate Answers</h2>
            <div id="answersModalActions" class="ml-auto mr-4 flex gap-2"></div>
            <button onclick="closeAnswersModal()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div id="answersModalBody" class="p-6 overflow-y-auto flex-1 space-y-4">
            <p class="text-center text-slate-500 py-8">Loading answers...</p>
        </div>
    </div>
</div>


<!-- Question Bank Modal -->
<div id="questionBankModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center sm:p-4">
    <div class="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-4xl h-[85vh] sm:h-auto sm:max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between p-4 border-b">
            <div>
                <h3 class="text-base sm:text-xl font-bold">Question Bank</h3>
                <p class="text-[10px] sm:text-xs text-slate-400">Pick questions from templates</p>
            </div>
            <button onclick="closeQuestionBank()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 text-xl">&times;</button>
        </div>
        <div class="flex flex-col sm:flex-row flex-1 overflow-hidden min-h-0">
            <!-- Template list -->
            <div class="sm:w-56 md:w-64 border-b sm:border-b-0 sm:border-r overflow-y-auto bg-slate-50 shrink-0 max-h-[30vh] sm:max-h-none" id="qbTemplateList">
                <p class="text-center text-slate-400 py-4 text-sm">Loading...</p>
            </div>
            <!-- Questions area -->
            <div class="flex-1 overflow-y-auto p-3 sm:p-4 min-h-0" id="qbQuestionsArea">
                <p class="text-center text-slate-400 py-8 text-sm">← Select a template</p>
            </div>
        </div>
        <div class="flex items-center justify-between p-3 sm:p-4 border-t bg-slate-50 rounded-b-2xl">
            <span id="qbSelectedCount" class="text-xs text-slate-500">0 selected</span>
            <div class="flex gap-2">
                <button onclick="closeQuestionBank()" class="px-3 py-2 border rounded-lg text-xs sm:text-sm">Cancel</button>
                <button onclick="importSelectedQuestions()" id="qbImportBtn" disabled class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs sm:text-sm font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                    Import Selected
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Builder palette drag-to-create engine (desktop + mobile touch)
(function() {
    let draggedType = null;

    // Attach drag events to all builder blocks
    document.querySelectorAll('.builder-block').forEach(block => {
        // Desktop drag
        block.addEventListener('dragstart', (e) => {
            draggedType = block.dataset.qtype;
            e.dataTransfer.setData('text/plain', draggedType);
            e.dataTransfer.effectAllowed = 'copy';
            setTimeout(() => document.getElementById('dropHint').classList.remove('hidden'), 0);
        });

        block.addEventListener('dragend', () => {
            draggedType = null;
            document.getElementById('dropHint').classList.add('hidden');
            document.getElementById('questionsDropZone').classList.remove('drag-over');
        });

        // Click/Tap to add instantly (works on both desktop and mobile)
        block.addEventListener('click', (e) => {
            if (block._wasDragging) { block._wasDragging = false; return; }
            openQuestionModal();
            setTimeout(() => {
                document.querySelector('[name="type"]').value = block.dataset.qtype;
                onTypeChange(block.dataset.qtype);
            }, 50);
        });

        // Mobile touch drag support
        let touchClone = null;
        let touchStartX, touchStartY, longPressTimer;

        block.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            block._wasDragging = false;

            longPressTimer = setTimeout(() => {
                block._wasDragging = true;
                draggedType = block.dataset.qtype;
                document.getElementById('dropHint').classList.remove('hidden');

                // Create floating clone
                touchClone = block.cloneNode(true);
                touchClone.style.cssText = 'position:fixed;z-index:9999;pointer-events:none;opacity:0.85;transform:scale(1.1);box-shadow:0 8px 30px rgba(0,0,0,0.2);';
                touchClone.style.left = (touchStartX - 50) + 'px';
                touchClone.style.top = (touchStartY - 20) + 'px';
                document.body.appendChild(touchClone);
                navigator.vibrate?.(30);
            }, 400);
        }, { passive: true });

        block.addEventListener('touchmove', (e) => {
            const t = e.touches[0];
            const dx = Math.abs(t.clientX - touchStartX);
            const dy = Math.abs(t.clientY - touchStartY);

            if (!block._wasDragging && (dx > 10 || dy > 10)) {
                clearTimeout(longPressTimer);
            }

            if (touchClone) {
                touchClone.style.left = (t.clientX - 50) + 'px';
                touchClone.style.top = (t.clientY - 20) + 'px';

                // Check if over drop zone
                const dropZone = document.getElementById('questionsDropZone');
                const rect = dropZone.getBoundingClientRect();
                if (t.clientX >= rect.left && t.clientX <= rect.right && t.clientY >= rect.top && t.clientY <= rect.bottom) {
                    dropZone.classList.add('drag-over');
                } else {
                    dropZone.classList.remove('drag-over');
                }
            }
        }, { passive: true });

        block.addEventListener('touchend', (e) => {
            clearTimeout(longPressTimer);

            if (touchClone) {
                const t = e.changedTouches[0];
                const dropZone = document.getElementById('questionsDropZone');
                const rect = dropZone.getBoundingClientRect();

                touchClone.remove();
                touchClone = null;
                dropZone.classList.remove('drag-over');
                document.getElementById('dropHint').classList.add('hidden');

                if (t.clientX >= rect.left && t.clientX <= rect.right && t.clientY >= rect.top && t.clientY <= rect.bottom) {
                    openQuestionModal();
                    setTimeout(() => {
                        document.querySelector('[name="type"]').value = draggedType;
                        onTypeChange(draggedType);
                    }, 50);
                }
                draggedType = null;
            }
        });

        block.addEventListener('touchcancel', () => {
            clearTimeout(longPressTimer);
            if (touchClone) { touchClone.remove(); touchClone = null; }
            document.getElementById('dropHint')?.classList.add('hidden');
            document.getElementById('questionsDropZone')?.classList.remove('drag-over');
            draggedType = null;
        });
    });

    // Desktop drop zone events
    const dropZone = document.getElementById('questionsDropZone');

    dropZone.addEventListener('dragover', (e) => {
        if (!draggedType) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', (e) => {
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const type = e.dataTransfer.getData('text/plain');
        dropZone.classList.remove('drag-over');
        document.getElementById('dropHint').classList.add('hidden');
        if (type) {
            openQuestionModal();
            setTimeout(() => {
                document.querySelector('[name="type"]').value = type;
                onTypeChange(type);
            }, 50);
        }
    });
})();

const assessmentId = window.location.pathname.split('/')[2];
let assessment = null;

async function loadAssessment() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}`, { 
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } 
        });
        if (res.ok) {
            const data = await res.json();
            // API returns assessment directly, or wrapped in data/assessment key
            assessment = data.assessment || data.data || data;
            renderAssessment();
        } else {
            console.error('Failed to load assessment:', res.status);
            document.getElementById('loading').innerHTML = '<p class="text-red-500">Failed to load assessment</p>';
        }
    } catch (err) { 
        console.error('Error loading assessment:', err); 
        document.getElementById('loading').innerHTML = '<p class="text-red-500">Network error</p>';
    }
}

function renderAssessment() {
    document.getElementById('loading').classList.add('hidden');
    document.getElementById('content').classList.remove('hidden');
    document.getElementById('title').textContent = assessment.title;
    document.getElementById('description').textContent = assessment.description || '';
    document.getElementById('questionsCount').textContent = assessment.questions?.length || 0;
    document.getElementById('candidatesCount').textContent = assessment.invitees_count || assessment.total_invites || 0;
    document.getElementById('completedCount').textContent = assessment.completed_count || 0;
    document.getElementById('avgScore').textContent = assessment.avg_score ? parseFloat(assessment.avg_score).toFixed(1) + '%' : '-';
    
    if (['active', 'scheduled', 'completed'].includes(assessment.status)) {
        document.getElementById('publishBtn').textContent = assessment.status.charAt(0).toUpperCase() + assessment.status.slice(1);
        document.getElementById('publishBtn').disabled = true;
        document.getElementById('publishBtn').classList.replace('bg-emerald-600', 'bg-slate-400');
    }
    
    // Show public link if access_code exists
    if (assessment.access_code) {
        const linkSection = document.getElementById('publicLinkSection');
        linkSection.classList.remove('hidden');
        document.getElementById('publicLinkUrl').textContent = window.location.origin + '/join/' + assessment.access_code;
    }
    
    renderQuestions();
    loadInvitees();

    // Real-time updates via WebSocket
    QuizlyEcho.private('assessment.' + assessmentId)
        .listen('InviteeUpdated', () => loadInvitees())
        .listen('TestCompleted', () => { loadInvitees(); loadAssessment(); });
}

async function duplicateAssessment() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/duplicate`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            toastSuccess(data.message);
            // Redirect to the new assessment
            setTimeout(() => { window.location.href = '/assessments/' + data.assessment.id; }, 1000);
        } else {
            toastError(data.message || 'Failed to duplicate');
        }
    } catch (err) {
        toastError('Network error');
    }
}

let selectedQuestions = new Set();

function renderQuestions() {
    const list = document.getElementById('questionsList');
    selectedQuestions.clear();
    updateBatchToolbar();
    if (!assessment.questions?.length) {
        list.innerHTML = '<p class="text-slate-500 text-center py-8">No questions yet. Add your first question.</p>';
        return;
    }
    const header = `
        <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 border-b">
            <input type="checkbox" id="selectAllQuestions" onchange="toggleAllQuestions(this.checked)" class="w-4 h-4 accent-indigo-600">
            <label for="selectAllQuestions" class="text-xs font-medium text-slate-500 select-none cursor-pointer">Select All</label>
            <span id="selectedQCount" class="ml-auto text-xs text-slate-400"></span>
        </div>`;
    const rows = assessment.questions.map((q, i) => `
        <div class="flex items-center justify-between p-4 border-b last:border-0 q-row" draggable="true" data-idx="${i}" data-id="${q.id}"
             ondragstart="qDragStart(event, ${i})" ondragover="qDragOver(event)" ondrop="qDrop(event, ${i})" ondragend="qDragEnd(event)">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <span class="cursor-grab active:cursor-grabbing text-slate-300 hover:text-slate-500 select-none" style="font-size:18px" title="Drag to reorder">⠿</span>
                <input type="checkbox" class="q-cb w-4 h-4 accent-indigo-600" data-id="${q.id}" onchange="onQuestionCheck()">
                <span class="font-bold text-indigo-600 shrink-0">Q${i + 1}.</span>
                <span class="truncate">${q.question_text}</span>
                <span class="ml-2 text-xs px-2 py-0.5 bg-slate-100 rounded text-slate-500 shrink-0">${q.question_type.replace('_', ' ')}</span>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <span class="text-sm text-slate-500">${q.points} pts</span>
                <button onclick='editQuestion(${JSON.stringify(q).replace(/'/g, "&apos;")})' class="text-indigo-500 hover:text-indigo-700" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button onclick="deleteQuestion('${q.id}')" class="text-red-500 hover:text-red-700" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    `).join('');
    list.innerHTML = header + rows;
}

// ─── Question Drag-Drop Reorder ───
let qDragIdx = null;

function qDragStart(e, idx) {
    qDragIdx = idx;
    e.currentTarget.style.opacity = '0.4';
    e.dataTransfer.effectAllowed = 'move';
}

function qDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    const row = e.target.closest('.q-row');
    document.querySelectorAll('.q-row').forEach(r => r.style.borderTopColor = '');
    if (row && parseInt(row.dataset.idx) !== qDragIdx) {
        row.style.borderTopColor = '#6366f1';
        row.style.borderTopWidth = '3px';
    }
}

function qDragEnd(e) {
    e.currentTarget.style.opacity = '1';
    document.querySelectorAll('.q-row').forEach(r => { r.style.borderTopColor = ''; r.style.borderTopWidth = ''; });
    qDragIdx = null;
}

async function qDrop(e, targetIdx) {
    e.preventDefault();
    document.querySelectorAll('.q-row').forEach(r => { r.style.borderTopColor = ''; r.style.borderTopWidth = ''; });
    if (qDragIdx === null || qDragIdx === targetIdx) return;

    // Reorder locally
    const moved = assessment.questions.splice(qDragIdx, 1)[0];
    assessment.questions.splice(targetIdx, 0, moved);
    renderQuestions();

    // Save order to API
    const order = assessment.questions.map(q => q.id);
    try {
        await fetch(`/api/assessments/${assessmentId}/questions/reorder`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ questions: order }),
        });
        toastSuccess('Questions reordered');
    } catch (err) {
        toastError('Failed to save order');
    }
    qDragIdx = null;
}

// ─── Touch reorder for mobile ───
(function() {
    let tIdx = null, tClone = null;
    document.addEventListener('touchstart', function(e) {
        const grip = e.target.closest('.q-row [title="Drag to reorder"]');
        if (!grip) return;
        const row = grip.closest('.q-row');
        tIdx = parseInt(row.dataset.idx);
        tClone = row.cloneNode(true);
        tClone.style.cssText = 'position:fixed;pointer-events:none;opacity:0.85;z-index:9999;width:'+row.offsetWidth+'px;box-shadow:0 4px 20px rgba(0,0,0,0.15);background:#fff;border-radius:8px;';
        document.body.appendChild(tClone);
        row.style.opacity = '0.3';
    }, { passive: true });
    document.addEventListener('touchmove', function(e) {
        if (tIdx === null || !tClone) return;
        e.preventDefault();
        const t = e.touches[0];
        tClone.style.left = '16px'; tClone.style.top = (t.clientY - 25) + 'px';
    }, { passive: false });
    document.addEventListener('touchend', function(e) {
        if (tIdx === null) return;
        if (tClone) { tClone.remove(); tClone = null; }
        document.querySelectorAll('.q-row').forEach(r => r.style.opacity = '1');
        const t = e.changedTouches[0];
        const target = document.elementFromPoint(t.clientX, t.clientY)?.closest('.q-row');
        if (target) {
            const targetIdx = parseInt(target.dataset.idx);
            if (targetIdx !== tIdx) qDrop(e, targetIdx);
        }
        tIdx = null;
    }, { passive: true });
})();

function onQuestionCheck() {
    selectedQuestions.clear();
    document.querySelectorAll('.q-cb:checked').forEach(cb => selectedQuestions.add(cb.dataset.id));
    const allCbs = document.querySelectorAll('.q-cb');
    const selectAll = document.getElementById('selectAllQuestions');
    if (selectAll) selectAll.checked = allCbs.length > 0 && selectedQuestions.size === allCbs.length;
    document.getElementById('selectedQCount').textContent = selectedQuestions.size ? `${selectedQuestions.size} selected` : '';
    updateBatchToolbar();
}

function toggleAllQuestions(checked) {
    document.querySelectorAll('.q-cb').forEach(cb => { cb.checked = checked; });
    onQuestionCheck();
}

function updateBatchToolbar() {
    let bar = document.getElementById('batchQToolbar');
    if (selectedQuestions.size > 0) {
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'batchQToolbar';
            bar.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-4 z-50 animate-slide-up';
            bar.innerHTML = `
                <span id="batchQCount" class="text-sm font-medium"></span>
                <button onclick="batchDeleteQuestions()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">Delete Selected</button>
                <button onclick="toggleAllQuestions(false)" class="text-slate-300 hover:text-white text-sm">Cancel</button>
            `;
            document.body.appendChild(bar);
        }
        document.getElementById('batchQCount').textContent = `${selectedQuestions.size} question${selectedQuestions.size > 1 ? 's' : ''}`;
    } else if (bar) {
        bar.remove();
    }
}

async function batchDeleteQuestions() {
    if (!selectedQuestions.size) return;
    const count = selectedQuestions.size;
    const confirmed = await showConfirm('Delete Questions', `Are you sure you want to delete ${count} question${count > 1 ? 's' : ''}? This cannot be undone.`, 'Delete', 'danger');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/questions/batch-delete`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ question_ids: Array.from(selectedQuestions) })
        });
        const result = await res.json();
        if (res.ok) {
            toastSuccess(result.message || `Deleted ${count} question(s)`);
            selectedQuestions.clear();
            updateBatchToolbar();
            loadAssessment();
        } else {
            toastError(result.message || 'Failed to delete');
        }
    } catch (e) {
        toastError('Network error');
    }
}

async function loadInvitees() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, { headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            const list = document.getElementById('inviteesList');
            if (!data.data?.length) {
                list.innerHTML = '<p class="text-slate-500 text-center py-8">No candidates invited yet.</p>';
                document.getElementById('resendAllBtn').classList.add('hidden');
                return;
            }
            // Show resend all button if assessment is published
            if (['active', 'scheduled'].includes(assessment?.status)) {
                document.getElementById('resendAllBtn').classList.remove('hidden');
            }

            const statusColors = {
                pending: 'bg-slate-100 text-slate-600',
                sent: 'bg-blue-100 text-blue-700',
                opened: 'bg-purple-100 text-purple-700',
                started: 'bg-amber-100 text-amber-700',
                completed: 'bg-emerald-100 text-emerald-700',
                failed: 'bg-red-100 text-red-700',
            };

            // Build a single meaningful status label
            function inviteeStatusBadge(inv) {
                // If test started/completed, show that
                if (['started', 'completed'].includes(inv.status)) {
                    return `<span class="px-2 py-1 rounded text-xs font-medium ${statusColors[inv.status]}">${inv.status}</span>`;
                }
                // Otherwise show email delivery status
                const emailLabel = inv.email_status || 'pending';
                const emailColors = { pending: 'bg-slate-100 text-slate-500', queued: 'bg-yellow-100 text-yellow-700', sent: 'bg-blue-100 text-blue-700', failed: 'bg-red-100 text-red-600' };
                return `<span class="px-2 py-1 rounded text-xs font-medium ${emailColors[emailLabel] || 'bg-slate-100 text-slate-500'}">${emailLabel === 'sent' ? '✉ sent' : emailLabel}</span>`;
            }

            const header = `
                <div class="flex items-center gap-3 px-3 py-2 bg-slate-50 border-b">
                    <input type="checkbox" id="selectAllInvitees" onchange="toggleAllInvitees(this.checked)" class="w-4 h-4 accent-indigo-600">
                    <label for="selectAllInvitees" class="text-xs font-medium text-slate-500 select-none cursor-pointer">Select All</label>
                    <span id="selectedInvCount" class="ml-auto text-xs text-slate-400"></span>
                </div>`;
            const rows = data.data.map(inv => `
                <div class="flex items-center justify-between p-3 border-b last:border-0 group hover:bg-slate-50 ${inv.status === 'completed' && inv.test_session ? 'cursor-pointer border-l-3 border-l-emerald-400' : ''}" ${inv.status === 'completed' && inv.test_session ? `onclick="viewAnswers('${inv.test_session.id}')"` : ''}>
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <input type="checkbox" class="inv-cb w-4 h-4 accent-indigo-600" data-id="${inv.id}" onchange="onInviteeCheck()" onclick="event.stopPropagation()">
                        <div class="min-w-0">
                            <p class="font-medium truncate">${inv.first_name || inv.last_name ? `${inv.first_name || ''} ${inv.last_name || ''}`.trim() : ''}</p>
                            <p class="text-sm text-slate-500 truncate">${inv.email}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-3">
                        ${inviteeStatusBadge(inv)}
                        ${inv.test_session?.percentage != null ? `<span class="text-sm font-semibold ${inv.test_session.passed ? 'text-emerald-400' : 'text-red-400'}">${parseFloat(inv.test_session.percentage).toFixed(1)}%</span>` : ''}
                        ${inv.status === 'completed' && inv.test_session ? `<span class="text-xs text-emerald-500 opacity-0 group-hover:opacity-100 transition font-medium">View Answers →</span>` : ''}
                        ${!['started', 'completed'].includes(inv.status) ? `
                            <button onclick="event.stopPropagation(); openEditInvitee('${inv.id}', '${inv.email}', '${inv.first_name || ''}', '${inv.last_name || ''}')" class="text-indigo-500 hover:text-indigo-700 opacity-0 group-hover:opacity-100 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="event.stopPropagation(); resendInvite('${inv.id}')" class="text-blue-500 hover:text-blue-700 opacity-0 group-hover:opacity-100 transition" title="Resend">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </button>
                            <button onclick="event.stopPropagation(); deleteInvitee('${inv.id}')" class="text-red-500 hover:text-red-700 opacity-0 group-hover:opacity-100 transition" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `).join('');
            list.innerHTML = header + rows;
        }
    } catch (err) {}
}

let selectedInvitees = new Set();

function onInviteeCheck() {
    selectedInvitees.clear();
    document.querySelectorAll('.inv-cb:checked').forEach(cb => selectedInvitees.add(cb.dataset.id));
    const allCbs = document.querySelectorAll('.inv-cb');
    const selectAll = document.getElementById('selectAllInvitees');
    if (selectAll) selectAll.checked = allCbs.length > 0 && selectedInvitees.size === allCbs.length;
    const countEl = document.getElementById('selectedInvCount');
    if (countEl) countEl.textContent = selectedInvitees.size ? `${selectedInvitees.size} selected` : '';
    updateInvBatchToolbar();
}

function toggleAllInvitees(checked) {
    document.querySelectorAll('.inv-cb').forEach(cb => { cb.checked = checked; });
    onInviteeCheck();
}

function updateInvBatchToolbar() {
    let bar = document.getElementById('batchInvToolbar');
    if (selectedInvitees.size > 0) {
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'batchInvToolbar';
            bar.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-4 z-50';
            bar.innerHTML = `
                <span id="batchInvCount" class="text-sm font-medium"></span>
                <button onclick="batchResendInvitees()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">Resend Email</button>
                <button onclick="batchDeleteInvitees()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">Delete Selected</button>
                <button onclick="toggleAllInvitees(false)" class="text-slate-300 hover:text-white text-sm">Cancel</button>
            `;
            document.body.appendChild(bar);
        }
        document.getElementById('batchInvCount').textContent = `${selectedInvitees.size} candidate${selectedInvitees.size > 1 ? 's' : ''}`;
    } else if (bar) {
        bar.remove();
    }
}

async function batchDeleteInvitees() {
    if (!selectedInvitees.size) return;
    const count = selectedInvitees.size;
    const confirmed = await showConfirm('Delete Candidates', `Delete ${count} candidate${count > 1 ? 's' : ''}? This cannot be undone.`, 'Delete', 'danger');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/batch-delete`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ invitee_ids: Array.from(selectedInvitees) })
        });
        const result = await res.json();
        if (res.ok) {
            toastSuccess(result.message || `Deleted ${count} candidate(s)`);
            selectedInvitees.clear();
            updateInvBatchToolbar();
            loadInvitees();
            loadAssessment();
        } else toastError(result.message || 'Failed');
    } catch (e) { toastError('Network error'); }
}

async function batchResendInvitees() {
    if (!selectedInvitees.size) return;
    const count = selectedInvitees.size;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/batch-resend`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ invitee_ids: Array.from(selectedInvitees) })
        });
        const result = await res.json();
        if (res.ok) {
            toastSuccess(result.message || `Resending to ${count} candidate(s)`);
            selectedInvitees.clear();
            updateInvBatchToolbar();
            loadInvitees();
        } else toastError(result.message || 'Failed');
    } catch (e) { toastError('Network error'); }
}

let currentType = 'single_choice';
let orderingCount = 0;
let matchingCount = 0;

// Types grouped by form section needed
const CHOICE_TYPES = ['single_choice', 'multiple_choice', 'true_false', 'odd_one_out', 'analogy',
    'sequence_pattern', 'matrix_pattern', 'spatial_rotation', 'shape_assembly', 'pattern_recognition', 'hotspot'];
const TEXT_TYPES = ['text_input', 'fill_blank', 'code_snippet', 'word_problem', 'mental_maths'];
const ORDERING_TYPES = ['ordering', 'drag_drop_sort'];
const PATTERN_TYPES = ['sequence_pattern', 'matrix_pattern', 'spatial_rotation', 'shape_assembly',
    'pattern_recognition', 'hotspot', 'analogy', 'shape_puzzle'];

function onTypeChange(type) {
    currentType = type;
    const containers = ['optionsContainer', 'textAnswerContainer', 'numericContainer',
        'orderingContainer', 'matchingContainer', 'likertContainer', 'mediaContainer'];
    containers.forEach(id => document.getElementById(id).classList.add('hidden'));

    const hint = document.getElementById('optionsHint');

    if (CHOICE_TYPES.includes(type)) {
        document.getElementById('optionsContainer').classList.remove('hidden');
        hint.textContent = type === 'multiple_choice'
            ? 'Select one or more correct answers (but not all)'
            : 'Select the one correct answer';
        document.getElementById('optionsList').innerHTML = '';
        optionCount = 0;
        if (type === 'true_false') {
            addOptionWithText('True');
            addOptionWithText('False');
        } else {
            const defaults = type === 'multiple_choice' ? 3 : 2;
            for (let i = 0; i < defaults; i++) addOption();
        }
    } else if (TEXT_TYPES.includes(type)) {
        document.getElementById('textAnswerContainer').classList.remove('hidden');
    } else if (type === 'numeric') {
        document.getElementById('numericContainer').classList.remove('hidden');
    } else if (ORDERING_TYPES.includes(type) || type === 'shape_puzzle') {
        document.getElementById('orderingContainer').classList.remove('hidden');
        const label = document.querySelector('#orderingContainer > label');
        const hint = document.querySelector('#orderingContainer > p');
        if (type === 'shape_puzzle') {
            label.textContent = 'Puzzle Pieces (in correct slot order)';
            hint.textContent = 'Add the shape/piece names in the correct slot order. They will be shuffled for the candidate to drag and fit.';
        } else {
            label.textContent = 'Items (in CORRECT order)';
            hint.textContent = "Add items top-to-bottom in the correct sequence. They'll be shuffled during the test.";
        }
        document.getElementById('orderingList').innerHTML = '';
        orderingCount = 0;
        for (let i = 0; i < 3; i++) addOrderingItem();
    } else if (type === 'matching') {
        document.getElementById('matchingContainer').classList.remove('hidden');
        document.getElementById('matchingList').innerHTML = '';
        matchingCount = 0;
        for (let i = 0; i < 3; i++) addMatchingPair();
    } else if (type === 'likert_scale') {
        document.getElementById('likertContainer').classList.remove('hidden');
    }

    // Show media URL field for visual/pattern types
    if (PATTERN_TYPES.includes(type)) {
        document.getElementById('mediaContainer').classList.remove('hidden');
    }
}

function addOptionWithText(text) {
    addOption();
    document.querySelector(`[name="option_${optionCount - 1}"]`).value = text;
}

function addOrderingItem(value = '') {
    const list = document.getElementById('orderingList');
    list.insertAdjacentHTML('beforeend', `
        <div class="flex gap-2 items-center">
            <span class="text-xs text-slate-400 w-6">${orderingCount + 1}.</span>
            <input type="text" name="ordering_${orderingCount}" value="${value}" placeholder="Item ${orderingCount + 1}" class="flex-1 px-3 py-2 border rounded-lg">
            ${orderingCount >= 2 ? `<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-lg">&times;</button>` : ''}
        </div>
    `);
    orderingCount++;
}

function addMatchingPair(left = '', right = '') {
    const list = document.getElementById('matchingList');
    list.insertAdjacentHTML('beforeend', `
        <div class="flex gap-2 items-center">
            <input type="text" name="match_left_${matchingCount}" value="${left}" placeholder="Left item" class="flex-1 px-3 py-2 border rounded-lg">
            <span class="text-slate-400">→</span>
            <input type="text" name="match_right_${matchingCount}" value="${right}" placeholder="Right item" class="flex-1 px-3 py-2 border rounded-lg">
            ${matchingCount >= 2 ? `<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-lg">&times;</button>` : ''}
        </div>
    `);
    matchingCount++;
}

let editingQuestionId = null;

function openQuestionModal() {
    editingQuestionId = null;
    document.getElementById('questionModalTitle').textContent = 'Add Question';
    document.getElementById('questionModal').classList.remove('hidden');
    document.getElementById('questionForm').reset();
    document.getElementById('questionError').classList.add('hidden');
    currentType = 'single_choice';
    document.querySelector('[name="type"]').value = 'single_choice';
    onTypeChange('single_choice');
    checkQuestionForm();
}

function editQuestion(q) {
    editingQuestionId = q.id;
    document.getElementById('questionModalTitle').textContent = 'Edit Question';
    document.getElementById('questionModal').classList.remove('hidden');
    document.getElementById('questionError').classList.add('hidden');
    
    // Pre-populate
    document.querySelector('[name="text"]').value = q.question_text;
    document.querySelector('[name="points"]').value = q.points;
    document.querySelector('[name="type"]').value = q.question_type;
    currentType = q.question_type;
    onTypeChange(q.question_type);
    
    const meta = q.question_metadata || {};
    
    if (TEXT_TYPES.includes(q.question_type)) {
        document.querySelector('[name="expected_answer"]').value = q.expected_answer || '';
    } else if (q.question_type === 'numeric') {
        document.querySelector('[name="numeric_answer"]').value = q.expected_answer || '';
        document.querySelector('[name="numeric_tolerance"]').value = meta.tolerance || 0;
    } else if ((ORDERING_TYPES.includes(q.question_type) || q.question_type === 'shape_puzzle') && q.options) {
        document.getElementById('orderingList').innerHTML = '';
        orderingCount = 0;
        q.options.forEach(opt => addOrderingItem(opt.option_text || opt.text || ''));
    } else if (q.question_type === 'matching' && meta.correct_pairs) {
        document.getElementById('matchingList').innerHTML = '';
        matchingCount = 0;
        Object.entries(meta.correct_pairs).forEach(([left, right]) => addMatchingPair(left, right));
    } else if (q.question_type === 'likert_scale' && meta.scale_labels) {
        meta.scale_labels.forEach((label, i) => {
            const el = document.querySelector(`[name="likert_${i + 1}"]`);
            if (el) el.value = label;
        });
    } else if (CHOICE_TYPES.includes(q.question_type) && q.options) {
        document.getElementById('optionsList').innerHTML = '';
        optionCount = 0;
        q.options.forEach((opt, i) => {
            addOption();
            document.querySelector(`[name="option_${i}"]`).value = opt.option_text || opt.text || '';
            if (q.question_type === 'multiple_choice') {
                const cb = document.querySelector(`[name="correct_${i}"]`);
                if (cb) cb.checked = opt.is_correct;
            } else {
                if (opt.is_correct) {
                    const radio = document.querySelector(`[name="correct_option"][value="${i}"]`);
                    if (radio) radio.checked = true;
                }
            }
        });
    }
    
    // Restore media URL
    if (meta.media_url) {
        document.querySelector('[name="media_url"]').value = meta.media_url;
    }
    
    checkQuestionForm();
}

function closeQuestionModal() {
    editingQuestionId = null;
    document.getElementById('questionModal').classList.add('hidden');
    document.getElementById('optionsList').innerHTML = '';
    optionCount = 0;
}
function openInviteModal() {
    document.getElementById('inviteModal').classList.remove('hidden');
    document.getElementById('addCandidateBtn').disabled = true;
    switchInviteTab('manual');
    loadReuseCandidates();
}
function closeInviteModal() { document.getElementById('inviteModal').classList.add('hidden'); }

function switchInviteTab(tab) {
    ['manual', 'csv'].forEach(t => {
        const tabEl = document.getElementById('inviteTab' + t.charAt(0).toUpperCase() + t.slice(1));
        const panel = document.getElementById('invite' + t.charAt(0).toUpperCase() + t.slice(1) + 'Tab');
        if (tabEl && panel) {
            if (t === tab) {
                tabEl.classList.add('border-indigo-600', 'text-indigo-600');
                tabEl.classList.remove('border-transparent', 'text-gray-500');
                panel.classList.remove('hidden');
            } else {
                tabEl.classList.remove('border-indigo-600', 'text-indigo-600');
                tabEl.classList.add('border-transparent', 'text-gray-500');
                panel.classList.add('hidden');
            }
        }
    });
}

let reuseCandidates = [];
let reuseSelected = new Set();

async function loadReuseCandidates() {
    document.getElementById('reuseLoading').classList.remove('hidden');
    document.getElementById('reuseEmpty').classList.add('hidden');
    document.getElementById('reuseCandidatesList').classList.add('hidden');
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/previous-candidates`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        const { data } = await res.json();
        reuseCandidates = data || [];
        reuseSelected.clear();
        document.getElementById('reuseLoading').classList.add('hidden');
        if (!reuseCandidates.length) {
            document.getElementById('reuseEmpty').classList.remove('hidden');
            return;
        }
        document.getElementById('reuseCandidatesList').classList.remove('hidden');
        renderReuseCandidates(reuseCandidates);
    } catch (e) {
        document.getElementById('reuseLoading').innerHTML = '<p class="text-red-500 text-sm">Failed to load</p>';
    }
}

function renderReuseCandidates(list) {
    document.getElementById('reuseRows').innerHTML = list.map((c, i) => `
        <label class="flex items-center gap-3 px-3 py-2 hover:bg-slate-50 cursor-pointer border-b last:border-0">
            <input type="checkbox" class="reuse-cb w-4 h-4" data-idx="${i}" onchange="onReuseCheck()" ${reuseSelected.has(c.email) ? 'checked' : ''}>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">${c.first_name || c.last_name ? (c.first_name || '') + ' ' + (c.last_name || '') : c.email}</p>
                <p class="text-xs text-slate-400 truncate">${c.email} &middot; from "${c.from_assessment}"</p>
            </div>
        </label>
    `).join('');
}

function filterReuseCandidates() {
    const q = document.getElementById('reuseSearchInput').value.toLowerCase();
    const filtered = reuseCandidates.filter(c => 
        c.email.toLowerCase().includes(q) || 
        (c.first_name || '').toLowerCase().includes(q) || 
        (c.last_name || '').toLowerCase().includes(q)
    );
    renderReuseCandidates(filtered);
}

function onReuseCheck() {
    reuseSelected.clear();
    document.querySelectorAll('.reuse-cb:checked').forEach(cb => {
        const idx = parseInt(cb.dataset.idx);
        if (reuseCandidates[idx]) reuseSelected.add(reuseCandidates[idx].email);
    });
    document.getElementById('reuseSelectedCount').textContent = reuseSelected.size + ' selected';
    const btn = document.getElementById('addReuseCandidatesBtn');
    btn.disabled = reuseSelected.size === 0;
    if (reuseSelected.size > 0) btn.classList.remove('hidden');
    else btn.classList.add('hidden');
}

function toggleAllReuse(checked) {
    document.querySelectorAll('.reuse-cb').forEach(cb => { cb.checked = checked; });
    onReuseCheck();
}

async function addReuseCandidates() {
    if (!reuseSelected.size) return;
    // Build candidates with names from reuseCandidates array
    const candidates = reuseCandidates
        .filter(c => reuseSelected.has(c.email))
        .map(c => ({ email: c.email, first_name: c.first_name || null, last_name: c.last_name || null }));
    if (!candidates.length) return;
    const btn = document.getElementById('addReuseCandidatesBtn');
    btn.disabled = true;
    btn.textContent = 'Adding...';
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ candidates })
        });
        const result = await res.json();
        if (res.ok || res.status === 201) {
            toastSuccess(`Added ${result.created || 0} candidate(s)` + (result.skipped ? `, ${result.skipped} already invited` : ''));
            closeInviteModal();
            loadInvitees();
            loadAssessment();
        } else {
            toastError(result.message || 'Failed to add candidates');
        }
    } catch (e) {
        toastError('Network error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Add Selected Candidates';
    }
}

// Progressive form validation
function checkInviteForm() {
    const email = document.querySelector('#inviteForm [name="email"]').value.trim();
    document.getElementById('addCandidateBtn').disabled = !email || !/\S+@\S+\.\S+/.test(email);
}
function checkQuestionForm() {
    const text = document.querySelector('#questionForm [name="text"]').value.trim();
    document.getElementById('saveQuestionBtn').disabled = !text;
}

// Answers Modal Viewer
async function viewAnswers(sessionId) {
    document.getElementById('answersModal').classList.remove('hidden');
    document.getElementById('answersModalBody').innerHTML = '<p class="text-center text-slate-500 py-8">Loading answers...</p>';
    document.getElementById('answersModalActions').innerHTML = '';
    
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/sessions/${sessionId}/answers`, {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error('Failed to fetch');
        const data = await res.json();
        
        // Add Play Recording button if available
        if (data.session && data.session.webcam_recording_url) {
            document.getElementById('answersModalActions').innerHTML = `<button onclick="window.open('${data.session.webcam_recording_url}', '_blank')" class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-bold transition flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Play Video Recording</button>`;
        }
        
        const html = data.answers.map((a, i) => {
            const correctStyle = a.is_correct ? 'border-emerald-200 bg-emerald-50/50' : 'border-red-200 bg-red-50/50';
            const icon = a.is_correct ? '<span class="text-emerald-500 font-bold">✓</span>' : '<span class="text-red-500 font-bold">✗</span>';
            
            let answerDisplay = '';
            if (['text_input', 'fill_blank', 'numeric'].includes(a.question_type)) {
                answerDisplay = `<p class="text-sm mt-2"><b>Candidate:</b> ${a.text_answer || '<em class="text-slate-400">Blank</em>'}</p>
                                 <p class="text-sm text-slate-500 mt-1"><b>Expected:</b> ${a.expected_answer || 'N/A'}</p>`;
            } else if (['ordering', 'drag_drop_sort', 'matching'].includes(a.question_type)) {
                answerDisplay = `<p class="text-sm mt-2"><b>Candidate:</b> ${a.text_answer || JSON.stringify(a.selected_options) || '<em class="text-slate-400">Blank</em>'}</p>`;
            } else {
                // Choice types
                answerDisplay = `<div class="mt-3 space-y-1">
                    ${a.options.map((opt, oIdx) => {
                        const isSelected = (a.selected_options || []).includes(oIdx.toString());
                        const isExpected = opt.is_correct;
                        let style = 'text-slate-600';
                        let prefix = '<span class="text-slate-300 w-4 inline-block text-center rounded-full text-[10px] border mr-1">○</span>';
                        if (isSelected && isExpected) { style = 'text-emerald-700 font-medium'; prefix = '<span class="text-emerald-600 w-4 inline-block text-center rounded-full text-[10px] border border-emerald-500 bg-emerald-50 mr-1">✓</span>'; }
                        else if (isSelected && !isExpected) { style = 'text-red-600'; prefix = '<span class="text-red-500 w-4 inline-block text-center rounded-full text-[10px] border border-red-500 bg-red-50 mr-1">✗</span>'; }
                        else if (!isSelected && isExpected) { style = 'text-emerald-600 font-medium'; prefix = '<span class="text-emerald-500 w-4 inline-block text-center rounded-full text-[10px] border border-emerald-500 border-dashed mr-1">✓</span>'; }
                        return `<p class="text-sm ${style}">${prefix} ${opt.text}</p>`;
                    }).join('')}
                </div>`;
            }
            
            return `
            <div class="border rounded-xl p-4 ${correctStyle}">
                <div class="flex gap-3">
                    <span class="font-bold text-slate-700 border-r pr-3">${i + 1}.</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-slate-900 break-words">${a.question_text}</p>
                        ${answerDisplay}
                    </div>
                    <div class="text-right shrink-0">
                        ${icon}
                        <p class="text-xs text-slate-500 mt-1 shadow-sm px-2 py-0.5 rounded-full bg-white">${a.points_earned} / ${a.max_points}</p>
                    </div>
                </div>
            </div>`;
        }).join('');
        
        document.getElementById('answersModalBody').innerHTML = html || '<p class="text-center text-slate-500 py-8">No answers found</p>';
    } catch (e) {
        document.getElementById('answersModalBody').innerHTML = '<p class="text-center text-red-500 py-8">Failed to load answers.</p>';
    }
}

function closeAnswersModal() {
    document.getElementById('answersModal').classList.add('hidden');
}

// Progressive form validation
function checkInviteForm() {
    const email = document.querySelector('#inviteForm [name="email"]').value.trim();
    document.getElementById('addCandidateBtn').disabled = !email || !/\S+@\S+\.\S+/.test(email);
}
function checkQuestionForm() {
    const text = document.querySelector('#questionForm [name="text"]').value.trim();
    document.getElementById('saveQuestionBtn').disabled = !text;
}

let optionCount = 0;
function addOption() {
    const list = document.getElementById('optionsList');
    const inputType = currentType === 'multiple_choice' ? 'checkbox' : 'radio';
    const name = currentType === 'multiple_choice' ? `correct_${optionCount}` : 'correct_option';
    list.insertAdjacentHTML('beforeend', `
        <div class="flex gap-2 items-center">
            <input type="${inputType}" name="${name}" value="${optionCount}" class="correct-marker w-4 h-4 accent-indigo-600">
            <input type="text" name="option_${optionCount}" placeholder="Option ${optionCount + 1}" class="flex-1 px-3 py-2 border rounded-lg">
            ${optionCount >= 2 ? `<button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-lg">&times;</button>` : ''}
        </div>
    `);
    optionCount++;
}

document.getElementById('questionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const errEl = document.getElementById('questionError');
    errEl.classList.add('hidden');
    const type = form.type.value;

    let body = {
        question_type: type,
        question_text: form.text.value,
        points: parseInt(form.points.value),
    };

    let metadata = {};
    const mediaUrl = form.media_url?.value?.trim();
    if (mediaUrl) metadata.media_url = mediaUrl;

    if (TEXT_TYPES.includes(type)) {
        const expected = form.expected_answer.value.trim();
        if (!expected) {
            errEl.textContent = 'Please enter the expected answer.';
            errEl.classList.remove('hidden');
            return;
        }
        body.expected_answer = expected;
        body.options = [];
    } else if (type === 'numeric') {
        const numAnswer = form.numeric_answer.value;
        if (!numAnswer && numAnswer !== '0') {
            errEl.textContent = 'Please enter the correct numeric answer.';
            errEl.classList.remove('hidden');
            return;
        }
        body.expected_answer = numAnswer;
        body.options = [];
        metadata.tolerance = parseFloat(form.numeric_tolerance.value) || 0;
    } else if (ORDERING_TYPES.includes(type) || type === 'shape_puzzle') {
        const items = [];
        for (let i = 0; i < orderingCount; i++) {
            const val = form[`ordering_${i}`]?.value?.trim();
            if (val) items.push(val);
        }
        if (items.length < 2) {
            errEl.textContent = type === 'shape_puzzle' ? 'Please add at least 2 puzzle pieces.' : 'Please add at least 2 ordering items.';
            errEl.classList.remove('hidden');
            return;
        }
        // Store as options with correct order
        body.options = items.map((text, i) => ({ text, is_correct: true, option_order: i }));
    } else if (type === 'matching') {
        const pairs = {};
        for (let i = 0; i < matchingCount; i++) {
            const left = form[`match_left_${i}`]?.value?.trim();
            const right = form[`match_right_${i}`]?.value?.trim();
            if (left && right) pairs[left] = right;
        }
        if (Object.keys(pairs).length < 2) {
            errEl.textContent = 'Please add at least 2 matching pairs.';
            errEl.classList.remove('hidden');
            return;
        }
        metadata.correct_pairs = pairs;
        // Store left items as options for display
        body.options = Object.keys(pairs).map((text, i) => ({ text, is_correct: true, option_order: i }));
    } else if (type === 'likert_scale') {
        const labels = [];
        for (let i = 1; i <= 5; i++) labels.push(form[`likert_${i}`]?.value || '');
        metadata.scale_labels = labels;
        body.options = [];
    } else if (CHOICE_TYPES.includes(type)) {
        const options = [];
        for (let i = 0; i < optionCount; i++) {
            const opt = form[`option_${i}`]?.value;
            if (!opt) continue;
            let isCorrect;
            if (type === 'multiple_choice') {
                isCorrect = form[`correct_${i}`]?.checked || false;
            } else {
                isCorrect = form.correct_option.value == i;
            }
            options.push({ text: opt, is_correct: isCorrect });
        }
        const correctCount = options.filter(o => o.is_correct).length;
        if (correctCount === 0) {
            errEl.textContent = 'Please select at least one correct answer.';
            errEl.classList.remove('hidden');
            return;
        }
        if (type === 'multiple_choice' && correctCount === options.length) {
            errEl.textContent = 'You cannot mark all options as correct.';
            errEl.classList.remove('hidden');
            return;
        }
        body.options = options;
    }

    if (Object.keys(metadata).length > 0) {
        body.question_metadata = metadata;
    }

    try {
        const url = editingQuestionId 
            ? `/api/assessments/${assessmentId}/questions/${editingQuestionId}`
            : `/api/assessments/${assessmentId}/questions`;
        const method = editingQuestionId ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        if (res.ok) { 
            closeQuestionModal(); 
            toastSuccess(editingQuestionId ? 'Question updated!' : 'Question added!');
            loadAssessment(); 
        } else {
            const data = await res.json();
            errEl.textContent = data.message || 'Failed to save question.';
            errEl.classList.remove('hidden');
        }
    } catch (err) {
        errEl.textContent = 'Network error.';
        errEl.classList.remove('hidden');
    }
});

document.getElementById('inviteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ 
                emails: [form.email.value],
                first_name: form.first_name?.value || null,
                last_name: form.last_name?.value || null,
            })
        });
        const result = await res.json();
        if (res.ok) { 
            closeInviteModal(); 
            loadInvitees(); 
            loadAssessment();
            form.reset();
            const msg = `Added ${result.created} invitee(s)${result.skipped > 0 ? `, ${result.skipped} already existed` : ''}`;
            if (result.skipped > 0) { toastError(msg); } else { toastSuccess(msg); }
        } else {
            toastError(result.message || 'Failed to add invitee');
        }
    } catch (err) {
        console.error('Error adding invitee:', err);
        toastError('Network error. Please try again.');
    }
});

// === CSV Upload Functions ===

// Invite CSV Tab Switching
function switchInviteTab(tab) {
    const manualTab = document.getElementById('inviteManualTab');
    const csvTab = document.getElementById('inviteCsvTab');
    const tabManual = document.getElementById('inviteTabManual');
    const tabCsv = document.getElementById('inviteTabCsv');
    if (tab === 'manual') {
        manualTab.classList.remove('hidden'); csvTab.classList.add('hidden');
        tabManual.classList.add('border-indigo-600','text-indigo-600'); tabManual.classList.remove('border-transparent','text-gray-500');
        tabCsv.classList.remove('border-indigo-600','text-indigo-600'); tabCsv.classList.add('border-transparent','text-gray-500');
    } else {
        csvTab.classList.remove('hidden'); manualTab.classList.add('hidden');
        tabCsv.classList.add('border-indigo-600','text-indigo-600'); tabCsv.classList.remove('border-transparent','text-gray-500');
        tabManual.classList.remove('border-indigo-600','text-indigo-600'); tabManual.classList.add('border-transparent','text-gray-500');
    }
}

let inviteCsvFile = null;

function handleInviteCsvDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-indigo-500','bg-indigo-50');
    const file = e.dataTransfer.files[0];
    if (file) previewInviteCsv(file);
}

function handleInviteCsvSelect(input) {
    if (input.files[0]) previewInviteCsv(input.files[0]);
}

function previewInviteCsv(file) {
    inviteCsvFile = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        const lines = e.target.result.split('\n').filter(l => l.trim());
        const rows = [];
        let startIdx = 0;
        // Check for header
        const firstCell = lines[0]?.split(',')[0]?.trim().toLowerCase();
        if (['email','email_address','e-mail','mail'].includes(firstCell)) startIdx = 1;
        for (let i = startIdx; i < lines.length && rows.length < 100; i++) {
            const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
            if (cols[0] && cols[0].includes('@')) {
                rows.push({ email: cols[0], first_name: cols[1] || '', last_name: cols[2] || '' });
            }
        }
        const tbody = document.getElementById('inviteCsvRows');
        tbody.innerHTML = rows.map(r => `<tr class="border-t"><td class="px-3 py-1">${r.email}</td><td class="px-3 py-1">${r.first_name}</td><td class="px-3 py-1">${r.last_name}</td></tr>`).join('');
        document.getElementById('inviteCsvCount').textContent = rows.length;
        document.getElementById('inviteCsvPreview').classList.remove('hidden');
        document.getElementById('inviteCsvDropzone').classList.add('hidden');
        document.getElementById('uploadInviteCsvBtn').disabled = rows.length === 0;
    };
    reader.readAsText(file);
}

function clearInviteCsv() {
    inviteCsvFile = null;
    document.getElementById('inviteCsvInput').value = '';
    document.getElementById('inviteCsvPreview').classList.add('hidden');
    document.getElementById('inviteCsvDropzone').classList.remove('hidden');
    document.getElementById('uploadInviteCsvBtn').disabled = true;
}

async function uploadInviteCsv() {
    if (!inviteCsvFile) return;
    const btn = document.getElementById('uploadInviteCsvBtn');
    btn.disabled = true; btn.textContent = 'Uploading...';
    try {
        const formData = new FormData();
        formData.append('csv', inviteCsvFile);
        const res = await fetch(`/api/assessments/${assessmentId}/invitees`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            body: formData
        });
        const result = await res.json();
        if (res.ok) {
            closeInviteModal();
            clearInviteCsv();
            loadInvitees();
            loadAssessment();
            toastSuccess(`Added ${result.created} invitee(s)${result.skipped > 0 ? `, ${result.skipped} duplicates skipped` : ''}`);
        } else {
            toastError(result.message || 'CSV upload failed');
        }
    } catch (err) {
        toastError('Network error. Please try again.');
    }
    btn.disabled = false; btn.textContent = 'Upload Invitees';
}

function downloadInviteTemplate() {
    const csv = 'email,first_name,last_name\njohn@example.com,John,Doe\njane@example.com,Jane,Smith\n';
    downloadCsvBlob(csv, 'invitee_template.csv');
}

// Question CSV Import
function openQuestionImportModal() {
    document.getElementById('questionImportModal').classList.remove('hidden');
}
function closeQuestionImportModal() {
    document.getElementById('questionImportModal').classList.add('hidden');
    clearQuestionCsv();
}

let questionCsvFile = null;

function handleQuestionCsvDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('border-indigo-500','bg-indigo-50');
    const file = e.dataTransfer.files[0];
    if (file) previewQuestionCsv(file);
}

function handleQuestionCsvSelect(input) {
    if (input.files[0]) previewQuestionCsv(input.files[0]);
}

function previewQuestionCsv(file) {
    questionCsvFile = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        const lines = e.target.result.split('\n').filter(l => l.trim());
        const rows = [];
        let startIdx = 0;
        const firstCell = lines[0]?.split(',')[0]?.trim().toLowerCase();
        if (['question','question_text','text','q'].includes(firstCell)) startIdx = 1;
        for (let i = startIdx; i < lines.length && rows.length < 50; i++) {
            const cols = lines[i].split(',').map(c => c.trim().replace(/^"|"$/g, ''));
            if (cols[0]) {
                rows.push({ text: cols[0].substring(0, 60) + (cols[0].length > 60 ? '...' : ''), type: cols[1] || 'single_choice', points: cols[2] || '1' });
            }
        }
        const tbody = document.getElementById('questionCsvRows');
        tbody.innerHTML = rows.map(r => `<tr class="border-t"><td class="px-3 py-1">${r.text}</td><td class="px-3 py-1">${r.type}</td><td class="px-3 py-1">${r.points}</td></tr>`).join('');
        document.getElementById('questionCsvCount').textContent = rows.length;
        document.getElementById('questionCsvPreview').classList.remove('hidden');
        document.getElementById('questionCsvDropzone').classList.add('hidden');
        document.getElementById('uploadQuestionCsvBtn').disabled = rows.length === 0;
    };
    reader.readAsText(file);
}

function clearQuestionCsv() {
    questionCsvFile = null;
    document.getElementById('questionCsvInput').value = '';
    document.getElementById('questionCsvPreview').classList.add('hidden');
    document.getElementById('questionCsvDropzone').classList.remove('hidden');
    document.getElementById('uploadQuestionCsvBtn').disabled = true;
    document.getElementById('questionImportErrors').classList.add('hidden');
}

async function uploadQuestionCsv() {
    if (!questionCsvFile) return;
    const btn = document.getElementById('uploadQuestionCsvBtn');
    btn.disabled = true; btn.textContent = 'Importing...';
    try {
        const formData = new FormData();
        formData.append('csv', questionCsvFile);
        const res = await fetch(`/api/assessments/${assessmentId}/questions/import`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
            body: formData
        });
        const result = await res.json();
        if (res.ok) {
            if (result.errors && result.errors.length > 0) {
                const errEl = document.getElementById('questionImportErrors');
                errEl.innerHTML = `<p class="font-medium text-amber-700 mb-1">${result.created} imported, ${result.errors.length} skipped:</p><ul class="list-disc list-inside text-xs">${result.errors.map(e => `<li>${e}</li>`).join('')}</ul>`;
                errEl.classList.remove('hidden');
            }
            if (result.created > 0) {
                toastSuccess(`Imported ${result.created} question(s)`);
                loadAssessment();
            }
            if (!result.errors || result.errors.length === 0) {
                closeQuestionImportModal();
            }
        } else {
            toastError(result.message || 'CSV import failed');
        }
    } catch (err) {
        toastError('Network error. Please try again.');
    }
    btn.disabled = false; btn.textContent = 'Import Questions';
}

function downloadQuestionTemplate() {
    const csv = [
        'question_text,question_type,points,option_a,option_b,option_c,option_d,correct_answer,expected_answer',
        'What is 2+2?,single_choice,1,3,4,5,6,B,',
        'Select all prime numbers,multiple_choice,2,2,4,5,6,"A,C",',
        'The sun revolves around the earth,true_false,1,,,,,FALSE,',
        'What is the capital of France?,text_input,1,,,,,,Paris',
        'The chemical symbol for water is ___,fill_blank,1,,,,,,H2O',
        'What is 15 x 12?,numeric,2,,,,,,180',
        'Solve: If a train travels 60km/h for 3 hours how far does it go?,word_problem,2,,,,,,180',
        'What is 25% of 200?,mental_maths,1,,,,,,50',
        'What does console.log(typeof null) output?,code_snippet,2,,,,,,object',
        'Dog is to puppy as cat is to?,analogy,1,Kitten,Cub,Foal,Calf,A,',
        'Which does not belong: Apple Banana Carrot Orange?,odd_one_out,1,Apple,Banana,Carrot,Orange,C,',
        'What comes next: 2 4 8 16 ?,pattern_recognition,1,24,32,30,20,B,',
        'I enjoy working in teams,likert_scale,1,Strongly Agree,Agree,Disagree,Strongly Disagree,A,',
        '"Arrange alphabetically: Banana Apple Cherry",ordering,1,,,,,,Apple Banana Cherry',
        '"Match: H2O=Water CO2=Carbon Dioxide",matching,1,,,,,,H2O:Water|CO2:Carbon Dioxide',
    ].join('\n') + '\n';
    downloadCsvBlob(csv, 'question_template.csv');
}

function downloadCsvBlob(content, filename) {
    const blob = new Blob([content], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = filename;
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

async function deleteQuestion(qid) {
    const confirmed = await showConfirm('Delete Question', 'Are you sure you want to delete this question? This action cannot be undone.', 'Delete', 'danger');
    if (!confirmed) return;
    try {
        await fetch(`/api/assessments/${assessmentId}/questions/${qid}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}` } });
        toastSuccess('Question deleted successfully');
        loadAssessment();
    } catch (err) {
        toastError('Failed to delete question');
    }
}

function copyPublicLink() {
    const url = document.getElementById('publicLinkUrl').textContent;
    navigator.clipboard.writeText(url).then(() => toastSuccess('Link copied!'));
}

async function publishAssessment() {
    const confirmed = await showConfirm('Publish Assessment', 'Are you ready to publish? Invitation emails will be dispatched to all candidates.', 'Publish', 'primary');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/publish`, { 
            method: 'POST', 
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } 
        });
        const data = await res.json();
        if (res.ok) {
            toastSuccess(data.message || 'Assessment published!');
            loadAssessment();
        } else {
            // Show specific validation errors
            const errors = data.errors || {};
            const msg = Object.values(errors).flat().join(' ') || data.message || 'Failed to publish.';
            toastError(msg);
        }
    } catch (err) {
        toastError('Failed to publish assessment');
    }
}

function editAssessment() { window.location.href = `/assessments/${assessmentId}/edit`; }

// --- Invitee Actions ---
async function deleteInvitee(id) {
    const confirmed = await showConfirm('Remove Candidate', 'Remove this candidate from the assessment?', 'Remove', 'danger');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}`, { method: 'DELETE', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message); loadInvitees(); loadAssessment(); }
        else { toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed'); }
    } catch { toastError('Network error'); }
}

async function resendInvite(id) {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}/resend`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message); loadInvitees(); }
        else { toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed'); }
    } catch { toastError('Network error'); }
}

async function resendAll() {
    const confirmed = await showConfirm('Resend All Invitations', 'Resend invitation emails to all pending candidates?', 'Resend All', 'primary');
    if (!confirmed) return;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/send`, { method: 'POST', headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' } });
        const data = await res.json();
        if (res.ok) { toastSuccess(data.message || `${data.sent} invitations queued`); loadInvitees(); }
        else { toastError(data.message || 'Failed'); }
    } catch { toastError('Network error'); }
}

function openEditInvitee(id, email, firstName, lastName) {
    document.getElementById('editInviteeId').value = id;
    document.getElementById('editInvEmail').value = email;
    document.getElementById('editInvFirstName').value = firstName;
    document.getElementById('editInvLastName').value = lastName;
    document.getElementById('editInviteeModal').classList.remove('hidden');
}

document.getElementById('editInviteeForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('editInviteeId').value;
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/invitees/${id}`, {
            method: 'PUT',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                email: document.getElementById('editInvEmail').value,
                first_name: document.getElementById('editInvFirstName').value || null,
                last_name: document.getElementById('editInvLastName').value || null,
            }),
        });
        const data = await res.json();
        if (res.ok) {
            document.getElementById('editInviteeModal').classList.add('hidden');
            toastSuccess(data.message);
            loadInvitees();
        } else {
            toastError(data.message || Object.values(data.errors||{}).flat()[0] || 'Failed');
        }
    } catch { toastError('Network error'); }
});

// ===== View Candidate Answers =====
async function viewAnswers(sessionId) {
    const modal = document.getElementById('answersModal');
    const body = document.getElementById('answersBody');
    modal.classList.remove('hidden');
    body.innerHTML = '<div class="text-center py-12"><div class="skeleton h-8 w-48 mx-auto mb-4"></div><p class="text-slate-400">Loading answers...</p></div>';

    try {
        const res = await fetch(`/api/assessments/${assessmentId}/sessions/${sessionId}/answers`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Failed to load answers');
        const data = await res.json();

        const s = data.session;
        const header = `
            <div class="flex items-center justify-between bg-slate-50 rounded-xl p-4 mb-6">
                <div>
                    <h3 class="font-bold text-lg">${s.candidate || 'Candidate'}</h3>
                    <p class="text-slate-500 text-sm">${s.email}</p>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    <span class="px-3 py-1 rounded-full font-bold ${s.passed ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'}">${s.score.toFixed(1)}% — ${s.passed ? 'Passed' : 'Failed'}</span>
                    <span class="text-slate-500">${data.correct_count}/${data.total_questions} correct</span>
                    ${s.time_spent ? `<span class="text-slate-500">${Math.round(s.time_spent / 60)} min</span>` : ''}
                    ${s.tab_switches > 0 ? `<span class="text-amber-600" title="Tab switches">⚠ ${s.tab_switches} tab switch${s.tab_switches > 1 ? 'es' : ''}</span>` : ''}
                    ${s.webcam_recording_url ? `<button onclick="document.getElementById('proctoringVideo').classList.toggle('hidden')" class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition font-medium">📹 View Recording</button>` : ''}
                </div>
            </div>
            ${s.webcam_recording_url ? `
            <div id="proctoringVideo" class="hidden mt-4 rounded-xl overflow-hidden border border-slate-200">
                <div class="bg-slate-50 px-4 py-2 border-b flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-600">📹 Proctoring Recording</span>
                    <button onclick="document.getElementById('proctoringVideo').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <video controls class="w-full max-h-96" src="${s.webcam_recording_url}"></video>
            </div>` : ''}`;

        const questions = data.answers.map((a, i) => {
            const optionsHtml = a.options.length > 0 ? a.options.map(o => {
                const selected = (a.selected_options || []).includes(o.label);
                const isCorrect = o.is_correct;
                let cls = 'border p-2 rounded text-sm flex items-center gap-2';
                if (selected && isCorrect) cls += ' bg-emerald-50 border-emerald-300';
                else if (selected && !isCorrect) cls += ' bg-red-50 border-red-300';
                else if (isCorrect) cls += ' bg-emerald-50 border-emerald-200 opacity-70';
                else cls += ' border-slate-200';
                return `<div class="${cls}">
                    ${selected ? (isCorrect ? '✅' : '❌') : (isCorrect ? '✓' : '○')}
                    <span class="font-medium">${o.label}.</span> ${o.text}
                </div>`;
            }).join('') : (a.text_answer ? `<div class="border p-3 rounded bg-slate-50 text-sm"><strong>Answer:</strong> ${a.text_answer}</div>` : '<p class="text-slate-400 text-sm">No answer provided</p>');

            return `
                <div class="border rounded-xl p-4 ${a.is_correct ? 'border-emerald-200' : 'border-red-200'}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <span class="text-xs font-bold ${a.is_correct ? 'text-emerald-600' : 'text-red-600'} uppercase">Q${i + 1} — ${a.is_correct ? 'Correct' : 'Incorrect'}</span>
                            <p class="font-medium mt-1">${a.question_text}</p>
                        </div>
                        <span class="text-sm font-bold ${a.is_correct ? 'text-emerald-600' : 'text-slate-400'}">${a.points_earned}/${a.max_points} pts</span>
                    </div>
                    <div class="space-y-2">${optionsHtml}</div>
                </div>`;
        }).join('');

        body.innerHTML = header + '<div class="space-y-4">' + questions + '</div>';
    } catch (err) {
        body.innerHTML = `<p class="text-red-500 text-center py-8">Failed to load answers. ${err.message}</p>`;
    }
}

function closeAnswersModal() {
    document.getElementById('answersModal').classList.add('hidden');
}

// ===== Export Results CSV =====
async function exportResultsCsv() {
    try {
        const res = await fetch(`/api/assessments/${assessmentId}/results?per_page=1000`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error();
        const { data } = await res.json();
        if (!data?.length) { toastError('No results to export'); return; }

        const headers = ['Name', 'Email', 'Score (%)', 'Passed', 'Time (min)', 'Tab Switches', 'Fullscreen Exits', 'Status', 'Submitted At'];
        const rows = data.map(s => [
            `${s.first_name || ''} ${s.last_name || ''}`.trim(),
            s.email,
            s.percentage != null ? parseFloat(s.percentage).toFixed(1) : '-',
            s.passed ? 'Yes' : 'No',
            s.time_spent_seconds ? Math.round(s.time_spent_seconds / 60) : '-',
            s.tab_switches || 0,
            s.fullscreen_exits || 0,
            s.status,
            s.submitted_at || '-',
        ]);

        const csv = [headers.join(','), ...rows.map(r => r.map(v => `"${v}"`).join(','))].join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${assessment?.title || 'assessment'}-results.csv`;
        a.click();
        URL.revokeObjectURL(url);
        toastSuccess('CSV downloaded');
    } catch { toastError('Failed to export'); }
}

loadAssessment();
// ═══════════════════════════════════════════════
// Question Bank
// ═══════════════════════════════════════════════
let qbSelectedIds = new Set();
let qbTemplatesCache = null;

function openQuestionBank() {
    document.getElementById('questionBankModal').classList.remove('hidden');
    qbSelectedIds.clear();
    updateQbCount();
    loadQbTemplates();
}

function closeQuestionBank() {
    document.getElementById('questionBankModal').classList.add('hidden');
}

async function loadQbTemplates() {
    const list = document.getElementById('qbTemplateList');
    if (qbTemplatesCache) {
        renderQbTemplates(qbTemplatesCache);
        return;
    }
    list.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">Loading...</p>';
    try {
        const res = await fetch('/api/templates', {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        const data = await res.json();
        qbTemplatesCache = data.templates || [];
        renderQbTemplates(qbTemplatesCache);
    } catch (e) {
        list.innerHTML = '<p class="text-center text-red-400 py-4 text-sm">Failed to load</p>';
    }
}

function renderQbTemplates(templates) {
    const list = document.getElementById('qbTemplateList');
    if (!templates.length) {
        list.innerHTML = '<p class="text-center text-slate-400 py-4 text-sm">No templates found</p>';
        return;
    }
    list.innerHTML = templates.map(t => `
        <button onclick="loadQbQuestions('${t.id}', this)" class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-white hover:shadow-sm transition text-sm qb-tmpl-btn">
            <div class="font-semibold text-slate-700 truncate text-xs sm:text-sm">${t.title}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">${t.questions_count} questions</div>
        </button>
    `).join('');
}

async function loadQbQuestions(templateId, btnEl) {
    // Highlight active template
    document.querySelectorAll('.qb-tmpl-btn').forEach(b => b.classList.remove('bg-white', 'shadow-sm', 'ring-2', 'ring-indigo-300'));
    if (btnEl) btnEl.classList.add('bg-white', 'shadow-sm', 'ring-2', 'ring-indigo-300');

    const area = document.getElementById('qbQuestionsArea');
    area.innerHTML = '<p class="text-center text-slate-400 py-8">Loading questions...</p>';

    try {
        const res = await fetch(`/api/templates/${templateId}/questions`, {
            headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' },
        });
        const data = await res.json();
        const questions = data.questions || [];

        if (!questions.length) {
            area.innerHTML = '<p class="text-center text-slate-400 py-8">No questions in this template</p>';
            return;
        }

        const typeBadge = (type) => {
            const colors = {
                single_choice: 'bg-blue-100 text-blue-700', multiple_choice: 'bg-blue-100 text-blue-700',
                true_false: 'bg-blue-100 text-blue-700', text_input: 'bg-blue-100 text-blue-700',
                fill_blank: 'bg-blue-100 text-blue-700', numeric: 'bg-blue-100 text-blue-700',
                ordering: 'bg-amber-100 text-amber-700', drag_drop_sort: 'bg-amber-100 text-amber-700',
                matching: 'bg-amber-100 text-amber-700',
                code_snippet: 'bg-emerald-100 text-emerald-700', likert_scale: 'bg-emerald-100 text-emerald-700',
                hotspot: 'bg-emerald-100 text-emerald-700', word_problem: 'bg-emerald-100 text-emerald-700',
                mental_maths: 'bg-emerald-100 text-emerald-700',
            };
            const c = colors[type] || 'bg-purple-100 text-purple-700';
            return `<span class="text-[10px] px-1.5 py-0.5 rounded ${c}">${type.replace(/_/g, ' ')}</span>`;
        };

        area.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-600">
                    <input type="checkbox" onchange="toggleQbAll(this, '${templateId}')" class="w-4 h-4 rounded">
                    Select All
                </label>
                <span class="text-xs text-slate-400">${data.template?.title || ''}</span>
            </div>
            <div class="space-y-2">
                ${questions.map((q, i) => `
                    <label class="flex items-start gap-3 p-3 border rounded-xl hover:bg-slate-50 cursor-pointer transition ${qbSelectedIds.has(q.id) ? 'bg-indigo-50 border-indigo-300' : ''}">
                        <input type="checkbox" value="${q.id}" onchange="toggleQbQuestion(this)" ${qbSelectedIds.has(q.id) ? 'checked' : ''} class="w-4 h-4 rounded mt-0.5 shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-slate-400">Q${i + 1}</span>
                                ${typeBadge(q.question_type)}
                                <span class="text-[10px] text-slate-400">${q.points} pts</span>
                            </div>
                            <p class="text-sm text-slate-700 line-clamp-2">${q.question_text}</p>
                        </div>
                    </label>
                `).join('')}
            </div>
        `;
    } catch (e) {
        area.innerHTML = '<p class="text-center text-red-400 py-8">Failed to load questions</p>';
    }
}

function toggleQbQuestion(cb) {
    if (cb.checked) {
        qbSelectedIds.add(cb.value);
        cb.closest('label').classList.add('bg-indigo-50', 'border-indigo-300');
    } else {
        qbSelectedIds.delete(cb.value);
        cb.closest('label').classList.remove('bg-indigo-50', 'border-indigo-300');
    }
    updateQbCount();
}

function toggleQbAll(cb) {
    const checkboxes = document.querySelectorAll('#qbQuestionsArea input[type="checkbox"][value]');
    checkboxes.forEach(c => {
        c.checked = cb.checked;
        if (cb.checked) {
            qbSelectedIds.add(c.value);
            c.closest('label')?.classList.add('bg-indigo-50', 'border-indigo-300');
        } else {
            qbSelectedIds.delete(c.value);
            c.closest('label')?.classList.remove('bg-indigo-50', 'border-indigo-300');
        }
    });
    updateQbCount();
}

function updateQbCount() {
    const count = qbSelectedIds.size;
    document.getElementById('qbSelectedCount').textContent = `${count} question${count !== 1 ? 's' : ''} selected`;
    document.getElementById('qbImportBtn').disabled = count === 0;
}

async function importSelectedQuestions() {
    if (qbSelectedIds.size === 0) return;
    const btn = document.getElementById('qbImportBtn');
    btn.disabled = true;
    btn.textContent = 'Importing...';

    try {
        const res = await fetch(`/api/assessments/${assessmentId}/import-questions`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ question_ids: Array.from(qbSelectedIds) }),
        });
        const data = await res.json();
        if (res.ok) {
            toastSuccess(data.message);
            closeQuestionBank();
            loadAssessment();
        } else {
            toastError(data.message || 'Import failed');
        }
    } catch (e) {
        toastError('Network error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Import Selected';
    }
}
</script>

<!-- Answers Modal -->
<div id="answersModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50" onclick="if(event.target===this) closeAnswersModal()">
    <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[85vh] flex flex-col shadow-2xl mx-4">
        <div class="flex items-center justify-between p-6 border-b">
            <h2 class="text-xl font-bold">Candidate Answers</h2>
            <button onclick="closeAnswersModal()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        <div id="answersBody" class="p-6 overflow-y-auto flex-1"></div>
    </div>
</div>
@endsection
