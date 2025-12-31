<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Student;
use App\Models\StudentAttribute;
use App\Services\StudentAttributeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentAttributeController extends Controller
{
    protected StudentAttributeService $attributeService;

    public function __construct(StudentAttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }
    /**
     * Display student attributes assignment page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->isSuperAdmin() ? $request->get('school_id') : $user->school_id;

        // Get available attributes for the school
        $attributes = Attribute::active()
            ->availableForSchool($schoolId)
            ->with('options')
            ->get();

        // Get students with their recent attribute assignments
        $studentsQuery = Student::where('school_id', $schoolId)
            ->with(['class:id,name', 'studentAttributes' => function ($q) {
                $q->with(['attribute:id,name,type', 'option:id,label'])
                  ->latest('assigned_at')
                  ->take(5);
            }]);

        if ($request->filled('class_id')) {
            $studentsQuery->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->paginate(config('pagination.students', 20));

        return view('student-attributes.index', compact('attributes', 'students', 'schoolId'));
    }

    /**
     * Show form to assign attribute to a student.
     */
    public function create(Student $student)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $attributes = Attribute::active()
            ->availableForSchool($student->school_id)
            ->with('options')
            ->get();

        return view('student-attributes.create', compact('student', 'attributes'));
    }

    /**
     * Store a new attribute assignment.
     */
    public function store(Request $request, Student $student)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required',
            'assigned_at' => 'required|date|before_or_equal:today',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $attribute = Attribute::with('options')->findOrFail($validated['attribute_id']);

        // Validate value based on attribute type
        if ($attribute->isEnumType()) {
            if (!$attribute->options->contains('id', $validated['value'])) {
                return back()->withInput()->with('error', 'Invalid option selected.');
            }
        } else {
            // Numeric validation
            if (!is_numeric($validated['value'])) {
                return back()->withInput()->with('error', 'Value must be numeric.');
            }
            if ($attribute->min_value !== null && $validated['value'] < $attribute->min_value) {
                return back()->withInput()->with('error', "Value must be at least {$attribute->min_value}.");
            }
            if ($attribute->max_value !== null && $validated['value'] > $attribute->max_value) {
                return back()->withInput()->with('error', "Value must be at most {$attribute->max_value}.");
            }
        }

        // Check for duplicate (same student, same attribute, same date)
        $exists = StudentAttribute::where('student_id', $student->id)
            ->where('attribute_id', $attribute->id)
            ->whereDate('assigned_at', $validated['assigned_at'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'An assignment for this attribute on this date already exists.');
        }

        StudentAttribute::create([
            'student_id' => $student->id,
            'attribute_id' => $attribute->id,
            'attribute_option_id' => $attribute->isEnumType() ? $validated['value'] : null,
            'numeric_value' => $attribute->isNumericType() ? $validated['value'] : null,
            'assigned_by' => auth()->id(),
            'assigned_at' => $validated['assigned_at'],
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('student-attributes.history', $student)
            ->with('success', 'Attribute assigned successfully.');
    }

    /**
     * View attribute history for a student.
     */
    public function history(Student $student, Request $request)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        // Get paginated history using service
        $assignments = $this->attributeService->getHistory(
            $student->id,
            $request->input('attribute_id'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        // Get available attributes for filter dropdown
        $attributes = $this->attributeService->getAvailableAttributes($student->school_id);

        // Get chart data using service
        $chartData = $this->attributeService->getChartData(
            $student->id,
            $student->school_id,
            $request->input('attribute_id'),
            $request->input('from_date'),
            $request->input('to_date')
        );

        return view('student-attributes.history', compact('student', 'assignments', 'attributes', 'chartData'));
    }

    /**
     * Edit an attribute assignment.
     */
    public function edit(StudentAttribute $studentAttribute)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $studentAttribute->student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $studentAttribute->load(['student', 'attribute.options']);

        return view('student-attributes.edit', compact('studentAttribute'));
    }

    /**
     * Update an attribute assignment.
     */
    public function update(Request $request, StudentAttribute $studentAttribute)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $studentAttribute->student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'value' => 'required',
            'assigned_at' => 'required|date|before_or_equal:today',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $attribute = $studentAttribute->attribute;

        // Validate value based on attribute type
        if ($attribute->isEnumType()) {
            if (!$attribute->options->contains('id', $validated['value'])) {
                return back()->withInput()->with('error', 'Invalid option selected.');
            }
        } else {
            if (!is_numeric($validated['value'])) {
                return back()->withInput()->with('error', 'Value must be numeric.');
            }
            if ($attribute->min_value !== null && $validated['value'] < $attribute->min_value) {
                return back()->withInput()->with('error', "Value must be at least {$attribute->min_value}.");
            }
            if ($attribute->max_value !== null && $validated['value'] > $attribute->max_value) {
                return back()->withInput()->with('error', "Value must be at most {$attribute->max_value}.");
            }
        }

        // Check for duplicate (same student, same attribute, same date) - excluding current
        $exists = StudentAttribute::where('student_id', $studentAttribute->student_id)
            ->where('attribute_id', $studentAttribute->attribute_id)
            ->whereDate('assigned_at', $validated['assigned_at'])
            ->where('id', '!=', $studentAttribute->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'An assignment for this attribute on this date already exists.');
        }

        $studentAttribute->update([
            'attribute_option_id' => $attribute->isEnumType() ? $validated['value'] : null,
            'numeric_value' => $attribute->isNumericType() ? $validated['value'] : null,
            'assigned_at' => $validated['assigned_at'],
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('student-attributes.history', $studentAttribute->student)
            ->with('success', 'Attribute assignment updated successfully.');
    }

    /**
     * Delete an attribute assignment.
     */
    public function destroy(StudentAttribute $studentAttribute)
    {
        $user = auth()->user();

        // Authorization check
        if (!$user->isSuperAdmin() && $studentAttribute->student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $student = $studentAttribute->student;
        $studentAttribute->delete();

        return redirect()->route('student-attributes.history', $student)
            ->with('success', 'Attribute assignment deleted successfully.');
    }

    /**
     * Download CSV template for bulk import.
     */
    public function downloadTemplate(Request $request)
    {
        $user = auth()->user();
        $schoolId = $user->isSuperAdmin() ? $request->get('school_id') : $user->school_id;

        if (!$schoolId) {
            return back()->with('error', 'Please select a school.');
        }

        // Get attributes for the school
        $attributes = Attribute::active()
            ->availableForSchool($schoolId)
            ->with('options')
            ->get();

        // Get students for the school
        $students = Student::where('school_id', $schoolId)
            ->where('is_active', true)
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('first_name')
            ->get();

        $response = new StreamedResponse(function () use ($students, $attributes) {
            $handle = fopen('php://output', 'w');

            // Header row
            $headers = ['student_id', 'student_name', 'student_email', 'assigned_at', 'remarks'];
            foreach ($attributes as $attr) {
                $columnName = $attr->name;
                if ($attr->isEnumType()) {
                    $options = $attr->options->pluck('label')->implode('/');
                    $columnName .= " ({$options})";
                } else {
                    $columnName .= " ({$attr->min_value}-{$attr->max_value})";
                }
                $headers[] = $columnName;
            }
            fputcsv($handle, $headers);

            // Data rows (one per student)
            foreach ($students as $student) {
                $row = [
                    $student->id,
                    $student->full_name,
                    $student->email,
                    date('Y-m-d'), // Default date
                    '', // Remarks
                ];
                foreach ($attributes as $attr) {
                    $row[] = ''; // Empty value for each attribute
                }
                fputcsv($handle, $row);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="student_attributes_template.csv"');

        return $response;
    }

    /**
     * Show bulk import form.
     */
    public function importForm()
    {
        $user = auth()->user();
        $schoolId = $user->school_id;

        $attributes = Attribute::active()
            ->availableForSchool($schoolId)
            ->with('options')
            ->get();

        return view('student-attributes.import', compact('attributes'));
    }

    /**
     * Process bulk import.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $user = auth()->user();
        $schoolId = $user->isSuperAdmin() ? $request->get('school_id') : $user->school_id;

        // Get attributes for mapping
        $attributes = Attribute::active()
            ->availableForSchool($schoolId)
            ->with('options')
            ->get()
            ->keyBy('id');

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), 'r');

        // Read header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            return back()->with('error', 'Invalid CSV file.');
        }

        // Find attribute columns (starts after 'remarks')
        $attributeColumns = [];
        $remarksIndex = array_search('remarks', $headers);

        if ($remarksIndex === false) {
            fclose($handle);
            return back()->with('error', 'Invalid template format. Missing "remarks" column.');
        }

        // Map attribute columns by matching names
        for ($i = $remarksIndex + 1; $i < count($headers); $i++) {
            $columnHeader = $headers[$i];
            // Extract attribute name (before parentheses)
            $attrName = trim(preg_replace('/\s*\(.*\)$/', '', $columnHeader));

            foreach ($attributes as $attr) {
                if (strcasecmp($attr->name, $attrName) === 0) {
                    $attributeColumns[$i] = $attr;
                    break;
                }
            }
        }

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;

                $studentId = $row[0] ?? null;
                $assignedAt = $row[3] ?? null;
                $remarks = $row[4] ?? null;

                if (empty($studentId)) {
                    continue; // Skip empty rows
                }

                // Verify student belongs to school
                $student = Student::where('id', $studentId)
                    ->where('school_id', $schoolId)
                    ->first();

                if (!$student) {
                    $errors[] = "Row {$rowNum}: Student ID {$studentId} not found or doesn't belong to this school.";
                    continue;
                }

                if (empty($assignedAt) || !strtotime($assignedAt)) {
                    $errors[] = "Row {$rowNum}: Invalid date format.";
                    continue;
                }

                // Process each attribute column
                foreach ($attributeColumns as $colIndex => $attribute) {
                    $value = $row[$colIndex] ?? null;

                    if (empty($value)) {
                        continue; // Skip empty values
                    }

                    // Validate and prepare data
                    $optionId = null;
                    $numericValue = null;

                    if ($attribute->isEnumType()) {
                        // Find matching option by label
                        $option = $attribute->options->first(function ($opt) use ($value) {
                            return strcasecmp($opt->label, trim($value)) === 0;
                        });

                        if (!$option) {
                            $errors[] = "Row {$rowNum}: Invalid option '{$value}' for attribute '{$attribute->name}'.";
                            continue;
                        }
                        $optionId = $option->id;
                    } else {
                        if (!is_numeric($value)) {
                            $errors[] = "Row {$rowNum}: Value for '{$attribute->name}' must be numeric.";
                            continue;
                        }
                        $numericValue = (float) $value;

                        if ($attribute->min_value !== null && $numericValue < $attribute->min_value) {
                            $errors[] = "Row {$rowNum}: Value for '{$attribute->name}' must be at least {$attribute->min_value}.";
                            continue;
                        }
                        if ($attribute->max_value !== null && $numericValue > $attribute->max_value) {
                            $errors[] = "Row {$rowNum}: Value for '{$attribute->name}' must be at most {$attribute->max_value}.";
                            continue;
                        }
                    }

                    // Check for existing assignment
                    $exists = StudentAttribute::where('student_id', $studentId)
                        ->where('attribute_id', $attribute->id)
                        ->whereDate('assigned_at', $assignedAt)
                        ->exists();

                    if ($exists) {
                        $errors[] = "Row {$rowNum}: Duplicate entry for '{$attribute->name}' on {$assignedAt}.";
                        continue;
                    }

                    // Create assignment
                    StudentAttribute::create([
                        'student_id' => $studentId,
                        'attribute_id' => $attribute->id,
                        'attribute_option_id' => $optionId,
                        'numeric_value' => $numericValue,
                        'assigned_by' => auth()->id(),
                        'assigned_at' => $assignedAt,
                        'remarks' => $remarks,
                    ]);

                    $imported++;
                }
            }

            fclose($handle);
            DB::commit();

            $message = "{$imported} attribute(s) imported successfully.";
            if (!empty($errors)) {
                $message .= ' Some rows had errors.';
                session()->flash('import_errors', $errors);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
