<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolProfile\UpdateSchoolProfileRequest;
use App\Http\Requests\SchoolProfile\DeleteSchoolImageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    /**
     * Display the school profile edit form.
     */
    public function edit()
    {
        $user = auth()->user();

        if (!$user->isSchoolAdmin()) {
            abort(403, 'Only school admins can access this page.');
        }

        $school = $user->school;

        return view('school.profile', compact('school'));
    }

    /**
     * Update the school profile.
     */
    public function update(UpdateSchoolProfileRequest $request)
    {
        $user = auth()->user();
        $school = $user->school;
        $validated = $request->validated();

        // Handle file uploads
        $updateData = [
            'signature_left_title' => $validated['signature_left_title'],
            'signature_middle_title' => $validated['signature_middle_title'],
            'signature_right_title' => $validated['signature_right_title'],
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }
            $updateData['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        // Handle certificate left logo upload
        if ($request->hasFile('certificate_left_logo')) {
            if ($school->certificate_left_logo) {
                Storage::disk('public')->delete($school->certificate_left_logo);
            }
            $updateData['certificate_left_logo'] = $request->file('certificate_left_logo')->store('schools/certificate_logos', 'public');
        }

        // Handle certificate right logo upload
        if ($request->hasFile('certificate_right_logo')) {
            if ($school->certificate_right_logo) {
                Storage::disk('public')->delete($school->certificate_right_logo);
            }
            $updateData['certificate_right_logo'] = $request->file('certificate_right_logo')->store('schools/certificate_logos', 'public');
        }

        // Handle signature left upload
        if ($request->hasFile('signature_left')) {
            if ($school->signature_left) {
                Storage::disk('public')->delete($school->signature_left);
            }
            $updateData['signature_left'] = $request->file('signature_left')->store('schools/signatures', 'public');
        }

        // Handle signature middle upload
        if ($request->hasFile('signature_middle')) {
            if ($school->signature_middle) {
                Storage::disk('public')->delete($school->signature_middle);
            }
            $updateData['signature_middle'] = $request->file('signature_middle')->store('schools/signatures', 'public');
        }

        // Handle signature right upload
        if ($request->hasFile('signature_right')) {
            if ($school->signature_right) {
                Storage::disk('public')->delete($school->signature_right);
            }
            $updateData['signature_right'] = $request->file('signature_right')->store('schools/signatures', 'public');
        }

        $school->update($updateData);

        return redirect()->route('school.profile')->with('success', 'School profile updated successfully.');
    }

    /**
     * Delete a specific logo/signature.
     */
    public function deleteImage(DeleteSchoolImageRequest $request)
    {
        $user = auth()->user();
        $school = $user->school;
        $validated = $request->validated();

        $field = $validated['field'];

        if ($school->{$field}) {
            Storage::disk('public')->delete($school->{$field});
            $school->update([$field => null]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }
}
