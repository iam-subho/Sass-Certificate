<?php

namespace App\Http\Controllers;

use App\Enums\AttributeType;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * Display a listing of global attributes (Super Admin only).
     */
    public function index()
    {
        $attributes = Attribute::global()
            ->with(['creator:id,name', 'options'])
            ->withCount('studentAttributes')
            ->latest()
            ->paginate(config('pagination.default', 15));

        return view('attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new global attribute.
     */
    public function create()
    {
        $types = AttributeType::cases();

        return view('attributes.create', compact('types'));
    }

    /**
     * Store a newly created global attribute in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['required', Rule::in(AttributeType::values())],
            'is_active' => 'boolean',
        ];

        // Add type-specific validation rules
        if ($request->type === 'numeric') {
            $rules['min_value'] = 'required|numeric';
            $rules['max_value'] = 'required|numeric|gte:min_value';
        } else {
            $rules['options'] = 'required|array|min:1';
            $rules['options.*.label'] = 'required|string|max:255';
            $rules['options.*.numeric_value'] = 'required|numeric';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $attribute = Attribute::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'school_id' => null, // Global attribute
                'min_value' => $validated['type'] === 'numeric' ? $validated['min_value'] : null,
                'max_value' => $validated['type'] === 'numeric' ? $validated['max_value'] : null,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id(),
            ]);

            // Create options for enum type
            if ($validated['type'] === 'enum' && !empty($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    $attribute->options()->create([
                        'label' => $option['label'],
                        'numeric_value' => $option['numeric_value'],
                        'display_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('attributes.index')->with('success', 'Attribute created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create attribute: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified global attribute.
     */
    public function show(Attribute $attribute)
    {
        if (!$attribute->isGlobal()) {
            abort(403, 'This is not a global attribute.');
        }

        $attribute->load(['creator:id,name', 'options']);

        // Get usage statistics
        $usageStats = $attribute->studentAttributes()
            ->selectRaw('COUNT(*) as total_assignments')
            ->selectRaw('COUNT(DISTINCT student_id) as unique_students')
            ->first();

        return view('attributes.show', compact('attribute', 'usageStats'));
    }

    /**
     * Show the form for editing the specified global attribute.
     */
    public function edit(Attribute $attribute)
    {
        if (!$attribute->isGlobal()) {
            abort(403, 'This is not a global attribute.');
        }

        $attribute->load('options');
        $types = AttributeType::cases();

        return view('attributes.edit', compact('attribute', 'types'));
    }

    /**
     * Update the specified global attribute in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        if (!$attribute->isGlobal()) {
            abort(403, 'This is not a global attribute.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];

        // Add type-specific validation rules based on attribute type
        if ($attribute->isNumericType()) {
            $rules['min_value'] = 'required|numeric';
            $rules['max_value'] = 'required|numeric|gte:min_value';
        } else {
            $rules['options'] = 'required|array|min:1';
            $rules['options.*.id'] = 'nullable|integer';
            $rules['options.*.label'] = 'required|string|max:255';
            $rules['options.*.numeric_value'] = 'required|numeric';
        }

        $validated = $request->validate($rules);

        try {
            DB::beginTransaction();

            $attribute->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'min_value' => $attribute->isNumericType() ? $validated['min_value'] : null,
                'max_value' => $attribute->isNumericType() ? $validated['max_value'] : null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Update options for enum type
            if ($attribute->isEnumType() && !empty($validated['options'])) {
                $existingOptionIds = [];

                foreach ($validated['options'] as $index => $optionData) {
                    if (!empty($optionData['id'])) {
                        // Update existing option
                        $option = $attribute->options()->find($optionData['id']);
                        if ($option) {
                            $option->update([
                                'label' => $optionData['label'],
                                'numeric_value' => $optionData['numeric_value'],
                                'display_order' => $index,
                            ]);
                            $existingOptionIds[] = $option->id;
                        }
                    } else {
                        // Create new option
                        $newOption = $attribute->options()->create([
                            'label' => $optionData['label'],
                            'numeric_value' => $optionData['numeric_value'],
                            'display_order' => $index,
                        ]);
                        $existingOptionIds[] = $newOption->id;
                    }
                }

                // Delete removed options (only if not used)
                $attribute->options()
                    ->whereNotIn('id', $existingOptionIds)
                    ->whereDoesntHave('studentAttributes')
                    ->delete();
            }

            DB::commit();

            return redirect()->route('attributes.index')->with('success', 'Attribute updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update attribute: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified global attribute from storage.
     */
    public function destroy(Attribute $attribute)
    {
        if (!$attribute->isGlobal()) {
            abort(403, 'This is not a global attribute.');
        }

        // Check if attribute has any assignments
        if ($attribute->studentAttributes()->exists()) {
            return back()->with('error', 'Cannot delete attribute that has student assignments.');
        }

        $attribute->delete();

        return redirect()->route('attributes.index')->with('success', 'Attribute deleted successfully.');
    }

    /**
     * Toggle attribute active status.
     */
    public function toggle(Attribute $attribute)
    {
        if (!$attribute->isGlobal()) {
            abort(403, 'This is not a global attribute.');
        }

        $attribute->update(['is_active' => !$attribute->is_active]);

        $status = $attribute->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Attribute {$status} successfully.");
    }
}
