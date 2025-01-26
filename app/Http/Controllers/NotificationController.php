<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead()
    {
        // Mark all unread notifications as read for the authenticated user
        Auth::user()->unreadNotifications->markAsRead();

        // Redirect back to the dashboard or any other page
        return redirect()->route('dashboard');
    }
}
