<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesSchoolResources
{
    /**
     * Authorize that the user can access a school-specific resource
     *
     * @param Model $resource
     * @param User|null $user
     * @param string $message
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeSchoolResource(
        Model $resource,
        ?User $user = null,
        string $message = 'Unauthorized action.'
    ): void {
        $user = $user ?? auth()->user();

        // Super admins can access everything
        if ($user->isSuperAdmin()) {
            return;
        }

        // School admins and issuers can only access their school's resources
        if (($user->isSchoolAdmin() || $user->isIssuer()) && $resource->school_id != $user->school_id) {
            abort(403, $message);
        }
    }

    /**
     * Authorize that the user belongs to the same school as the resource
     *
     * @param int|null $schoolId
     * @param User|null $user
     * @param string $message
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeSchoolId(
        ?int $schoolId,
        ?User $user = null,
        string $message = 'Unauthorized action.'
    ): void {
        $user = $user ?? auth()->user();

        // Super admins can access everything
        if ($user->isSuperAdmin()) {
            return;
        }

        // School admins and issuers can only access their school's resources
        if (($user->isSchoolAdmin() || $user->isIssuer()) && $schoolId != $user->school_id) {
            abort(403, $message);
        }
    }

    /**
     * Get the appropriate query scope based on user role
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $schoolIdColumn
     * @param User|null $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeByUserRole($query, ?string $schoolIdColumn = 'school_id', ?User $user = null)
    {
        $user = $user ?? auth()->user();

        // Super admins see everything
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // School admins and issuers see only their school's data
        if ($user->isSchoolAdmin() || $user->isIssuer()) {
            return $query->where($schoolIdColumn, $user->school_id);
        }

        return $query;
    }

    /**
     * Check if current user can perform super admin actions
     *
     * @param string $message
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeSuperAdmin(string $message = 'This action requires super admin privileges.')
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, $message);
        }
    }

    /**
     * Check if current user can perform school admin actions
     *
     * @param string $message
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeSchoolAdmin(string $message = 'This action requires school admin privileges.')
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->isSchoolAdmin()) {
            abort(403, $message);
        }
    }
}
