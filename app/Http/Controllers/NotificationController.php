<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, $notificationId = null)
    {
        $user = auth()->user();

        if ($notificationId) {
            // Find and mark a single notification as read
            $notification = $user->notifications()->findOrFail($notificationId);
            $notification->markAsRead();
        } else {
            // Mark all notifications as read
            $user->unreadNotifications->markAsRead();
        }

        // If using AJAX, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $notificationId ? 'Notification marked as read.' : 'All notifications marked as read.',
            ]);
        }

        return redirect($request->input('redirect_to', route('dashboard')));
    }

    public function markSpecificNotificationAsRead($notificationId)
    {
        // Find the notification by ID and mark it as read
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }
}
