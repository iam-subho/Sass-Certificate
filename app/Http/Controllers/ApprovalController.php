<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Display pending certificates for approval.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Certificate::where('school_id', $user->school_id)
            ->where('status', 'pending')
            ->with(['student', 'event', 'issuer']);

        // Filter by certificate ID
        if ($request->filled('certificate_id')) {
            $query->where('certificate_id', 'like', '%' . $request->certificate_id . '%');
        }

        // Filter by issuer
        if ($request->filled('issuer_id')) {
            $query->where('issuer_id', $request->issuer_id);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $pendingCertificates = $query->latest()->paginate(20)->appends($request->query());

        // Get issuers for filter dropdown
        $issuers = \App\Models\User::where('school_id', $user->school_id)
            ->where('role', 'issuer')
            ->orderBy('name')
            ->get();

        // Get events for filter dropdown
        $events = \App\Models\Event::where('school_id', $user->school_id)
            ->orderBy('name')
            ->get();

        return view('approvals.index', compact('pendingCertificates', 'issuers', 'events'));
    }

    /**
     * Approve a certificate.
     */
    public function approveCertificate(Certificate $certificate)
    {
        $user = auth()->user();

        if ($certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $certificate->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $user->id,
        ]);

        return back()->with('success', 'Certificate approved successfully.');
    }

    /**
     * Reject a certificate.
     */
    public function rejectCertificate(Certificate $certificate)
    {
        $user = auth()->user();

        if ($certificate->school_id != $user->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $certificate->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Certificate rejected.');
    }

    /**
     * Bulk approve certificates.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:certificates,id',
        ]);

        $user = auth()->user();

        // Get certificates and verify authorization
        $certificates = Certificate::whereIn('id', $validated['certificate_ids'])
            ->where('school_id', $user->school_id)
            ->where('status', 'pending')
            ->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No valid certificates to approve.');
        }

        $count = 0;
        foreach ($certificates as $certificate) {
            $certificate->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $user->id,
            ]);
            $count++;
        }

        return back()->with('success', "Successfully approved {$count} certificate(s).");
    }

    /**
     * Bulk reject certificates.
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:certificates,id',
        ]);

        $user = auth()->user();

        // Get certificates and verify authorization
        $certificates = Certificate::whereIn('id', $validated['certificate_ids'])
            ->where('school_id', $user->school_id)
            ->where('status', 'pending')
            ->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No valid certificates to reject.');
        }

        $count = $certificates->count();
        Certificate::whereIn('id', $certificates->pluck('id'))->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', "Successfully rejected {$count} certificate(s).");
    }
}
