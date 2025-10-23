<?php

namespace App\Http\Controllers;

use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index()
    {
        $templates = CertificateTemplate::withCount([
            'schools',
            'schools as active_schools_count' => function($query) {
                $query->where('is_active', true);
            }
        ])->paginate(15);
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created template.
     */
    public function store(StoreTemplateRequest $request)
    {
        $validated = $request->validated();

        CertificateTemplate::create($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Certificate template created successfully.');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(CertificateTemplate $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified template.
     */
    public function update(UpdateTemplateRequest $request, CertificateTemplate $template)
    {
        $validated = $request->validated();

        $template->update($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Certificate template updated successfully.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(CertificateTemplate $template)
    {
        if ($template->schools()->count() > 0) {
            return back()->with('error', 'Cannot delete template. It is assigned to ' . $template->schools()->count() . ' schools.');
        }

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Certificate template deleted successfully.');
    }

    /**
     * Preview template with sample data.
     */
    public function preview(CertificateTemplate $template)
    {
        $sampleData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'full_name' => 'John Doe',
            'dob' => '01 Jan 2000',
            'father_name' => 'Robert Doe',
            'mother_name' => 'Jane Doe',
            'mobile' => '+1234567890',
            'email' => 'john.doe@example.com',
            'school_name' => 'Sample School',
            'school_email' => 'school@example.com',
            'school_phone' => '+0987654321',
            'certificate_id' => 'CERT-SAMPLE123',
            'issued_date' => date('d M Y'),
            'qr_code' => asset('images/sample-qr.png'),
            'school_logo' => asset('images/sample-logo.png'),
            'certificate_left_logo' => asset('images/sample-logo-left.jpg'),
            'certificate_right_logo' => asset('images/sample-logo-right.jpg'),
            'signature_left' => asset('images/sample-signature-left.png'),
            'signature_middle' => asset('images/sample-signature-middle.png'),
            'signature_right' => asset('images/sample-signature-right.jpg'),
            'signature_left_title' => 'Principal',
            'signature_middle_title' => 'Director',
            'signature_right_title' => 'Chairman',
            'event_name' => 'Annual Science Competition',
            'event_type' => 'Competition',
            'event_date' => '15 Mar 2024',
            'event_description' => 'Annual inter-school science competition showcasing innovative projects',
            'rank' => '1st Position',
        ];

        $html = $template->render($sampleData);

        return view('templates.preview', compact('template', 'html'));
    }

    /**
     * Toggle template active status.
     */
    public function toggleStatus(CertificateTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        return redirect()->route('templates.index')
            ->with('success', 'Template status updated successfully.');
    }
}
