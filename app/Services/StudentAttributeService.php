<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Student;
use App\Models\StudentAttribute;
use Illuminate\Support\Collection;

class StudentAttributeService
{
    /**
     * Maximum data points to show in charts.
     */
    protected int $maxChartPoints = 15;

    /**
     * Chart color palette.
     */
    protected array $colors = [
        '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
        '#EC4899', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
    ];

    /**
     * Get attribute history for a student with optional filters.
     */
    public function getHistory(
        int $studentId,
        ?int $attributeId = null,
        ?string $fromDate = null,
        ?string $toDate = null,
        int $perPage = 20
    ) {
        $query = StudentAttribute::where('student_id', $studentId)
            ->with(['attribute:id,name,type', 'option:id,label,numeric_value', 'assignedByUser:id,name']);

        if ($attributeId) {
            $query->where('attribute_id', $attributeId);
        }

        if ($fromDate) {
            $query->whereDate('assigned_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('assigned_at', '<=', $toDate);
        }

        return $query->latest('assigned_at')->paginate($perPage);
    }

    /**
     * Get available attributes for a student's school.
     */
    public function getAvailableAttributes(int $schoolId): Collection
    {
        return Attribute::active()
            ->availableForSchool($schoolId)
            ->select('id', 'name', 'type', 'min_value', 'max_value')
            ->with('options:id,attribute_id,label,numeric_value')
            ->get();
    }

    /**
     * Prepare chart data for student attribute history.
     */
    public function getChartData(
        int $studentId,
        ?int $schoolId = null,
        ?int $attributeId = null,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $student = Student::findOrFail($studentId);
        $schoolId = $schoolId ?? $student->school_id;

        // Get available attributes
        $attributes = $this->getAvailableAttributes($schoolId);

        // Filter to specific attribute if provided
        if ($attributeId) {
            $attributes = $attributes->where('id', $attributeId);
        }

        $chartData = [
            'attributes' => [],
            'colors' => $this->colors,
            'hasMoreData' => false,
            'maxPoints' => $this->maxChartPoints,
        ];

        foreach ($attributes as $index => $attribute) {
            $attributeData = $this->prepareAttributeChartData(
                $student,
                $attribute,
                $fromDate,
                $toDate,
                $index
            );

            if ($attributeData) {
                if ($attributeData['truncated']) {
                    $chartData['hasMoreData'] = true;
                }
                $chartData['attributes'][] = $attributeData;
            }
        }

        return $chartData;
    }

    /**
     * Prepare chart data for a single attribute.
     */
    protected function prepareAttributeChartData(
        Student $student,
        Attribute $attribute,
        ?string $fromDate,
        ?string $toDate,
        int $colorIndex
    ): ?array {
        // Build query with filters
        $query = $student->studentAttributes()
            ->where('attribute_id', $attribute->id)
            ->with('option:id,label,numeric_value');

        if ($fromDate) {
            $query->whereDate('assigned_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('assigned_at', '<=', $toDate);
        }

        // Get total count to check if we have more data
        $totalCount = $query->count();

        if ($totalCount === 0) {
            return null;
        }

        $hasTruncated = $totalCount > $this->maxChartPoints;

        // Get latest points, ordered by date ASC for chart
        $assignments = $query
            ->orderBy('assigned_at', 'desc')
            ->take($this->maxChartPoints)
            ->get()
            ->reverse()
            ->values();

        $labels = [];
        $data = [];
        $tooltips = [];

        foreach ($assignments as $assignment) {
            $labels[] = $assignment->assigned_at->format('M d');
            $data[] = $assignment->numeric_score;
            $tooltips[] = $assignment->display_value;
        }

        $color = $this->colors[$colorIndex % count($this->colors)];

        return [
            'id' => $attribute->id,
            'name' => $attribute->name,
            'type' => $attribute->type->value,
            'labels' => $labels,
            'data' => $data,
            'tooltips' => $tooltips,
            'color' => $color,
            'min' => $attribute->isNumericType() ? $attribute->min_value : 0,
            'max' => $attribute->isNumericType() ? $attribute->max_value : ($attribute->options->max('numeric_value') ?? 100),
            'totalCount' => $totalCount,
            'truncated' => $hasTruncated,
        ];
    }

    /**
     * Get summary statistics for a student's attributes.
     */
    public function getAttributeSummary(int $studentId, ?int $attributeId = null): array
    {
        $query = StudentAttribute::where('student_id', $studentId)
            ->with(['attribute:id,name,type', 'option:id,label,numeric_value']);

        if ($attributeId) {
            $query->where('attribute_id', $attributeId);
        }

        $assignments = $query->get();

        $summary = [];

        foreach ($assignments->groupBy('attribute_id') as $attrId => $attrAssignments) {
            $attribute = $attrAssignments->first()->attribute;
            $scores = $attrAssignments->pluck('numeric_score')->filter()->values();

            $summary[] = [
                'attribute_id' => $attrId,
                'attribute_name' => $attribute->name,
                'attribute_type' => $attribute->type->value,
                'total_assignments' => $attrAssignments->count(),
                'latest_value' => $attrAssignments->sortByDesc('assigned_at')->first()->display_value,
                'latest_score' => $attrAssignments->sortByDesc('assigned_at')->first()->numeric_score,
                'latest_date' => $attrAssignments->sortByDesc('assigned_at')->first()->assigned_at->format('Y-m-d'),
                'min_score' => $scores->isNotEmpty() ? $scores->min() : null,
                'max_score' => $scores->isNotEmpty() ? $scores->max() : null,
                'avg_score' => $scores->isNotEmpty() ? round($scores->avg(), 2) : null,
            ];
        }

        return $summary;
    }

    /**
     * Set maximum chart points.
     */
    public function setMaxChartPoints(int $points): self
    {
        $this->maxChartPoints = $points;
        return $this;
    }

    /**
     * Set custom colors.
     */
    public function setColors(array $colors): self
    {
        $this->colors = $colors;
        return $this;
    }
}
