<?php

namespace App\Http\Controllers;

use App\Http\Requests\Issuer\StoreIssuerRequest;
use App\Http\Requests\Issuer\UpdateIssuerRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class IssuerController extends Controller
{
    /**
     * Display a listing of issuers.
     */
    public function index()
    {
        $user = auth()->user();

        // Get issuers for the school with certificate count
        $issuers = User::where('school_id', $user->school_id)
            ->where('role', 'issuer')
            ->withCount([
                'issuedCertificates',
                'issuedCertificates as pending_count' => function($query) {
                    $query->where('status', 'pending');
                },
                'issuedCertificates as approved_count' => function($query) {
                    $query->where('status', 'approved');
                }
            ])
            ->paginate(15);

        return view('issuers.index', compact('issuers'));
    }

    /**
     * Show the form for creating a new issuer.
     */
    public function create()
    {
        return view('issuers.create');
    }

    /**
     * Store a newly created issuer.
     */
    public function store(StoreIssuerRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'issuer',
            'school_id' => $user->school_id,
            'is_active' => true,
        ]);

        return redirect()->route('issuers.index')
            ->with('success', 'Issuer created successfully.');
    }

    /**
     * Display the specified issuer.
     */
    public function show(User $issuer)
    {
        $user = auth()->user();

        // Authorization check
        if ($issuer->school_id != $user->school_id || $issuer->role != 'issuer') {
            abort(403);
        }

        // Load recent certificates with eager loading
        $issuer->load(['issuedCertificates' => function ($query) {
            $query->with(['student:id,first_name,last_name', 'event:id,name'])
                  ->latest()
                  ->take(10);
        }]);

        // Get stats efficiently
        $stats = [
            'total_issued' => $issuer->issuedCertificates()->count(),
            'pending' => $issuer->issuedCertificates()->where('status', 'pending')->count(),
            'approved' => $issuer->issuedCertificates()->where('status', 'approved')->count(),
            'rejected' => $issuer->issuedCertificates()->where('status', 'rejected')->count(),
        ];

        return view('issuers.show', compact('issuer', 'stats'));
    }

    /**
     * Show the form for editing the specified issuer.
     */
    public function edit(User $issuer)
    {
        $user = auth()->user();

        // Authorization check
        if ($issuer->school_id != $user->school_id || $issuer->role != 'issuer') {
            abort(403);
        }

        return view('issuers.edit', compact('issuer'));
    }

    /**
     * Update the specified issuer.
     */
    public function update(UpdateIssuerRequest $request, User $issuer)
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $request->has('is_active') ? $validated['is_active'] : $issuer->is_active,
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $issuer->update($data);

        return redirect()->route('issuers.index')
            ->with('success', 'Issuer updated successfully.');
    }

    /**
     * Remove the specified issuer.
     */
    public function destroy(User $issuer)
    {
        $user = auth()->user();

        // Authorization check
        if ($issuer->school_id != $user->school_id || $issuer->role != 'issuer') {
            abort(403);
        }

        // Check if issuer has issued certificates
        if ($issuer->issuedCertificates()->count() > 0) {
            return back()->with('error', 'Cannot delete issuer who has issued certificates.');
        }

        $issuer->delete();

        return redirect()->route('issuers.index')
            ->with('success', 'Issuer deleted successfully.');
    }

    /**
     * Toggle issuer active status.
     */
    public function toggleStatus(User $issuer)
    {
        $user = auth()->user();

        // Authorization check
        if ($issuer->school_id != $user->school_id || $issuer->role != 'issuer') {
            abort(403);
        }

        $issuer->update(['is_active' => !$issuer->is_active]);

        return redirect()->route('issuers.index')
            ->with('success', 'Issuer status updated successfully.');
    }
}
