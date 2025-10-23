<?php

namespace App\Http\Controllers;

use App\Http\Requests\Package\StorePackageRequest;
use App\Http\Requests\Package\UpdatePackageRequest;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of packages.
     */
    public function index()
    {
        $packages = Package::withCount([
            'schools',
            'schools as active_schools_count' => function($query) {
                $query->where('is_active', true)->where('status', 'approved');
            }
        ])->latest()->paginate(15);
        return view('packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create()
    {
        return view('packages.create');
    }

    /**
     * Store a newly created package.
     */
    public function store(StorePackageRequest $request)
    {
        $validated = $request->validated();

        Package::create($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Display the specified package.
     */
    public function show(Package $package)
    {
        $package->load(['schools' => function($query) {
            $query->with(['admins:id,name,email,school_id'])
                  ->withCount('certificates')
                  ->latest();
        }]);

        $stats = [
            'total_schools' => $package->schools()->count(),
            'active_schools' => $package->schools()->where('is_active', true)->count(),
            'total_certificates' => $package->schools->sum('certificates_count'),
        ];

        return view('packages.show', compact('package', 'stats'));
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    /**
     * Update the specified package.
     */
    public function update(UpdatePackageRequest $request, Package $package)
    {
        $validated = $request->validated();

        $package->update($validated);

        return redirect()->route('packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified package.
     */
    public function destroy(Package $package)
    {
        // Check if package has schools
        if ($package->schools()->count() > 0) {
            return back()->with('error', 'Cannot delete package with associated schools.');
        }

        $package->delete();

        return redirect()->route('packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    /**
     * Toggle package active status.
     */
    public function toggleStatus(Package $package)
    {
        $package->update(['is_active' => !$package->is_active]);

        return back()->with('success', 'Package status updated successfully.');
    }
}
