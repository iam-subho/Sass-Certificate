<?php

namespace App\Http\Controllers;

use App\Http\Requests\Verification\VerificationSearchRequest;
use App\Models\Certificate;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Verify certificate by certificate_id.
     */
    public function verify($certificateId)
    {
        $certificate = Certificate::where('certificate_id', $certificateId)
            ->with([
                'student:id,first_name,last_name,dob,father_name,mother_name,mobile,email',
                'school:id,name,email,phone,logo',
                'event:id,name,event_date,event_type,description'
            ])
            ->first();

        if (!$certificate) {
            return view('verification.result', [
                'found' => false,
                'certificate_id' => $certificateId,
            ]);
        }

        return view('verification.result', [
            'found' => true,
            'certificate' => $certificate,
            'student' => $certificate->student,
            'school' => $certificate->school,
        ]);
    }

    /**
     * Show verification form.
     */
    public function showForm()
    {
        return view('verification.form');
    }

    /**
     * Search certificate by ID from form.
     */
    public function search(VerificationSearchRequest $request)
    {
        return redirect()->route('verify', $request->validated('certificate_id'));
    }
}
