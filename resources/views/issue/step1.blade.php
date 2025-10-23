@extends('layouts.app')

@section('title', 'Issue Certificates - Step 1')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-semibold">1</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-blue-600">Step 1</div>
                    <div class="text-xs text-gray-500">Select Class</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">2</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 2</div>
                    <div class="text-xs text-gray-400">Select Event</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">3</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 3</div>
                    <div class="text-xs text-gray-400">Select Students</div>
                </div>
            </div>
            <div class="flex-1 h-1 mx-4 bg-gray-200"></div>
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-gray-200 text-gray-500 rounded-full font-semibold">4</div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Step 4</div>
                    <div class="text-xs text-gray-400">Delivery</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Step 1: Select Class & Students</h1>

        <form action="{{ route('issue.step2') }}" method="POST" id="step1-form">
            @csrf

            <div class="space-y-6">
                <!-- Class Selection -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id" id="class_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Section Selection -->
                <div>
                    <label for="section" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Section (Optional)
                    </label>
                    <select name="section" id="section" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- All Sections --</option>
                    </select>
                </div>

                <!-- Students List (loaded via AJAX) -->
                <div id="students-list" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Select Students <span class="text-red-500">*</span>
                    </label>
                    <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto bg-gray-50">
                        <div class="mb-2">
                            <label class="flex items-center">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Select All</span>
                            </label>
                        </div>
                        <hr class="my-2">
                        <div id="students-container" class="space-y-2">
                            <!-- Students will be loaded here -->
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Selected: <span id="selected-count">0</span> students</p>
                </div>

                <!-- Loading Indicator -->
                <div id="loading" style="display: none;" class="text-center py-4">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-sm text-gray-600">Loading students...</p>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
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
    const classSelect = document.getElementById('class_id');
    const sectionSelect = document.getElementById('section');
    const selectAllCheckbox = document.getElementById('select-all');
    const nextBtn = document.getElementById('next-btn');

    classSelect.addEventListener('change', loadStudents);
    sectionSelect.addEventListener('change', loadStudents);

    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('#students-container input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });

    function loadStudents() {
        const classId = classSelect.value;
        const section = sectionSelect.value;

        if (!classId) {
            document.getElementById('students-list').style.display = 'none';
            nextBtn.disabled = true;
            return;
        }

        document.getElementById('loading').style.display = 'block';
        document.getElementById('students-list').style.display = 'none';

        fetch(`{{ route('issue.load-students') }}?class_id=${classId}&section=${section}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('students-container');
                container.innerHTML = data.students.map(student => `
                    <label class="flex items-center p-2 hover:bg-white rounded cursor-pointer">
                        <input type="checkbox" name="student_ids[]" value="${student.id}"
                               class="student-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               onchange="updateSelectedCount()">
                        <span class="ml-3 text-sm text-gray-700">
                            ${student.first_name} ${student.last_name}
                            ${student.section ? `<span class="text-gray-500">(Section ${student.section})</span>` : ''}
                        </span>
                    </label>
                `).join('');

                // Load sections for selected class
                const selectedClass = @json($classes);
                const classData = selectedClass.find(c => c.id == classId);
                if (classData && classData.sections) {
                    const sections = classData.sections.split(',');
                    sectionSelect.innerHTML = '<option value="">-- All Sections --</option>' +
                        sections.map(s => `<option value="${s.trim()}">${s.trim()}</option>`).join('');
                }

                document.getElementById('loading').style.display = 'none';
                document.getElementById('students-list').style.display = 'block';
                updateSelectedCount();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('loading').style.display = 'none';
                alert('Failed to load students. Please try again.');
            });
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('#students-container input[type="checkbox"]:checked');
        document.getElementById('selected-count').textContent = checked.length;
        nextBtn.disabled = checked.length === 0;
    }

    window.updateSelectedCount = updateSelectedCount;
});
</script>
@endsection
