<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class FileUploadHandler
{
    /**
     * Upload disk
     */
    protected string $disk;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->disk = config('uploads.disk', 'public');
    }

    /**
     * Handle a single file upload
     *
     * @param UploadedFile|null $file
     * @param string|null $oldPath
     * @param string $storagePath
     * @return string|null
     */
    public function handleUpload(?UploadedFile $file, ?string $oldPath = null, string $storagePath = 'uploads'): ?string
    {
        if (!$file) {
            return null;
        }

        // Delete old file if exists
        if ($oldPath) {
            $this->deleteFile($oldPath);
        }

        // Store new file
        return $file->store($storagePath, $this->disk);
    }

    /**
     * Handle multiple file uploads from request
     *
     * @param Request $request
     * @param array $fields
     * @param Model|null $model
     * @return array
     */
    public function handleMultipleUploads(Request $request, array $fields, ?Model $model = null): array
    {
        $uploadedFiles = [];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $oldPath = $model?->$field ?? null;
                $storagePath = $this->getStoragePathForField($field);

                $uploadedFiles[$field] = $this->handleUpload(
                    $request->file($field),
                    $oldPath,
                    $storagePath
                );
            }
        }

        return $uploadedFiles;
    }

    /**
     * Handle school-specific file uploads
     *
     * @param Request $request
     * @param Model|null $school
     * @return array
     */
    public function handleSchoolUploads(Request $request, ?Model $school = null): array
    {
        $fields = config('uploads.school_fields', []);
        return $this->handleMultipleUploads($request, $fields, $school);
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @return bool
     */
    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    /**
     * Delete multiple files from storage
     *
     * @param array $paths
     * @return void
     */
    public function deleteMultipleFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }
    }

    /**
     * Delete school files
     *
     * @param Model $school
     * @return void
     */
    public function deleteSchoolFiles(Model $school): void
    {
        $fields = config('uploads.school_fields', []);
        $paths = [];

        foreach ($fields as $field) {
            if ($school->$field) {
                $paths[] = $school->$field;
            }
        }

        $this->deleteMultipleFiles($paths);
    }

    /**
     * Get storage path for a specific field
     *
     * @param string $field
     * @return string
     */
    protected function getStoragePathForField(string $field): string
    {
        $paths = config('uploads.paths', []);

        // Map fields to paths
        $fieldPathMap = [
            'logo' => $paths['school_logos'] ?? 'schools/logos',
            'certificate_left_logo' => $paths['certificate_logos'] ?? 'schools/certificate_logos',
            'certificate_right_logo' => $paths['certificate_logos'] ?? 'schools/certificate_logos',
            'signature_left' => $paths['signatures'] ?? 'schools/signatures',
            'signature_middle' => $paths['signatures'] ?? 'schools/signatures',
            'signature_right' => $paths['signatures'] ?? 'schools/signatures',
        ];

        return $fieldPathMap[$field] ?? $paths['schools'] ?? 'schools';
    }

    /**
     * Get file URL
     *
     * @param string|null $path
     * @return string|null
     */
    public function getFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Check if file exists
     *
     * @param string|null $path
     * @return bool
     */
    public function fileExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk($this->disk)->exists($path);
    }
}
