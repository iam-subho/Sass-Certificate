@extends('layouts.app')

@section('title', 'Issue Certificates - Step 3')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-green-600">Step 1</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-green-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-semibold">✓</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-green-600">Step 2</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-blue-600"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold">3</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-blue-600">Step 3</div>
                    <div class="text-xs text-gray-500">Assign Ranks</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">4</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 4</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Step 3: Select Students & Assign Ranks</h1>
        <p class="text-sm text-gray-600 mb-6">Event: <span class="font-semibold">{{ $event->name }}</span></p>

        <form action="{{ route('issue.step4') }}" method="POST" id="step3-form">
            @csrf

            <!-- Certificate Template Selection -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <label for="certificate_template_id" class="block text-sm font-medium text-gray-700 mb-3">
                    Select Certificate Template <span class="text-red-500">*</span>
                </label>
                <select name="certificate_template_id" id="certificate_template_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Select Template --</option>
                    @foreach($availableTemplates as $template)
                        <option value="{{ $template->id }}" @if($event->certificate_template_id == $template->id) selected @endif>
                            {{ $template->name }}
                            @if($template->description)
                                - {{ $template->description }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-2 text-xs text-blue-700">Choose which template to use for these certificates</p>
            </div>

            <!-- Certificate Type Selection -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <label class="block text-sm font-medium text-gray-700 mb-3">Certificate Type</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="certificate_type" value="participation" class="text-blue-600 focus:ring-blue-500" checked onchange="toggleRankFields()">
                        <span class="ml-3 text-sm font-medium text-gray-700">Participation Certificates (All students receive "Participation")</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="certificate_type" value="rank" class="text-blue-600 focus:ring-blue-500" onchange="toggleRankFields()">
                        <span class="ml-3 text-sm font-medium text-gray-700">Rank-based Certificates (Assign ranks: 1st, 2nd, 3rd, etc.)</span>
                    </label>
                </div>
            </div>

            <!-- Students Selection with Ranks -->
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Select Students & Assign Ranks</h3>
                    <label class="flex items-center">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
                    </label>
                </div>

                <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                    @foreach($students as $student)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center flex-1 cursor-pointer">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500" onchange="updateSelectedCount()">
                                    <div class="ml-4 flex-1">
                                        <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                        <div class="text-sm text-gray-500">
                                            DOB: {{ $student->dob->format('M d, Y') }}
                                            @if($student->section)
                                                • Section: {{ $student->section }}
                                            @endif
                                        </div>
                                    </div>
                                </label>
                                <div class="ml-4">
                                    <select name="ranks[{{ $student->id }}]" class="rank-select rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" style="display: none;">
                                        <option value="Participation">Participation</option>
                                        <option value="1st Place">1st Place</option>
                                        <option value="2nd Place">2nd Place</option>
                                        <option value="3rd Place">3rd Place</option>
                                        <option value="Winner">Winner</option>
                                        <option value="Runner Up">Runner Up</option>
                                        <option value="Excellence">Excellence</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p class="text-sm text-gray-600">Selected: <span id="selected-count">0</span> students</p>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ url()->previous() }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    ← Back
                </a>
                <button type="submit" id="next-btn" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                    Next Step →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const nextBtn = document.getElementById('next-btn');

    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    function toggleRankFields() {
        const rankType = document.querySelector('input[name="certificate_type"]:checked').value;
        const rankSelects = document.querySelectorAll('.rank-select');

        rankSelects.forEach(select => {
            select.style.display = rankType === 'rank' ? 'block' : 'none';
        });
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        document.getElementById('selected-count').textContent = checked.length;
        nextBtn.disabled = checked.length === 0;
    }

    window.updateSelectedCount = updateSelectedCount;
    window.toggleRankFields = toggleRankFields;
});
</script>
@endsection
