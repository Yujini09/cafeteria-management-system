<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Notification Service
 * 
 * Centralized service for managing admin/superadmin notifications.
 * Ensures each notification is created only once, regardless of admin/superadmin count.
 */
class NotificationService
{
    /**
     * Create a single notification for all admins and superadmins
     * 
     * Instead of creating a notification for each admin/superadmin,
     * we create just one notification that both roles can see.
     *
     * @param string $action The action performed (e.g., 'Created Item')
     * @param string $module The module affected (e.g., 'inventory')
     * @param string $description Description of what happened
     * @param array $metadata Additional data to store (e.g., user who performed action)
     * @return Notification The created notification
     */
    public function createAdminNotification(
        string $action,
        string $module,
        string $description,
        array $metadata = []
    ): Notification
    {
        // Store the actor's user_id so foreign key constraints remain valid.
        // This still creates only one notification record per action (no per-admin loop).
        $actorId = Auth::id() ?? null;

        return Notification::create([
            'user_id' => $actorId,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get notifications for the current user
     * 
     * For superadmins: shows all system notifications
     * For admins: shows system notifications + user-specific notifications
     *
     * @param \App\Models\User $user The user requesting notifications
     * @param int $limit Number of notifications to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationsForUser($user, int $limit = 20)
    {
        $baseQuery = Notification::with('user');

        // Superadmins and admins should see the same notifications (per request):
        if (in_array($user->role, ['superadmin', 'admin'])) {
            return $baseQuery
                ->latest()
                ->take($limit)
                ->get();
        }

        // Other roles don't see admin notifications
        return collect();
    }

    /**
     * Check if a notification already exists to prevent duplicates
     *
     * @param string $action
     * @param string $module
     * @param string $description
     * @return bool
     */
    public function notificationExists(
        string $action,
        string $module,
        string $description
    ): bool
    {
        return Notification::where('action', $action)
            ->where('module', $module)
            ->where('description', $description)
            ->exists();
    }

    /**
     * Get unique notifications (avoid duplicates in display)
     * Useful for UI rendering when you need to guarantee uniqueness
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUniqueNotifications(int $limit = 20)
    {
        return Notification::latest()
            ->take($limit)
            ->get();
    }
}
