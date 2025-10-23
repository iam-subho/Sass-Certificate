<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolProfile\UpdateSchoolProfileRequest;
use App\Http\Requests\SchoolProfile\DeleteSchoolImageRequest;
use App\Services\FileUploadHandler;
use App\Traits\AuthorizesSchoolResources;

class SchoolProfileController extends Controller
{
    use AuthorizesSchoolResources;

    protected FileUploadHandler $fileUploadHandler;

    public function __construct(FileUploadHandler $fileUploadHandler)
    {
        $this->fileUploadHandler = $fileUploadHandler;
    }

    /**
     * Display the school profile edit form.
     */
    public function edit()
    {
        $this->authorizeSchoolAdmin();

        $school = auth()->user()->school;

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

        // Start with non-file data
        $updateData = [
            'signature_left_title' => $validated['signature_left_title'],
            'signature_middle_title' => $validated['signature_middle_title'],
            'signature_right_title' => $validated['signature_right_title'],
        ];

        // Handle all file uploads using FileUploadHandler
        $uploadedFiles = $this->fileUploadHandler->handleSchoolUploads($request, $school);
        $updateData = array_merge($updateData, $uploadedFiles);

        $school->update($updateData);

        return redirect()->route('school.profile')->with('success', 'School profile updated successfully.');
    }

    /**
     * Delete a specific logo/signature.
     */
    public function deleteImage(DeleteSchoolImageRequest $request)
    {
        $school = auth()->user()->school;
        $validated = $request->validated();

        $field = $validated['field'];

        // Delete the file using FileUploadHandler
        $this->fileUploadHandler->deleteFile($school->{$field});

        // Update database
        $school->update([$field => null]);

        return back()->with('success', 'Image deleted successfully.');
    }
}
