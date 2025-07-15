<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\SurveyAssigned;
use App\Notifications\FollowUpReminder;
use App\Notifications\ProjectStarting;
use App\Notifications\SurveyDeadline;
use App\Notifications\QuotationExpired;
use App\Models\Notification;


class NotificationController extends Controller
{
    // Get unread notifications with limited results and notification details
    public function getUnread()
    {
        $notifications = Auth::user()->unreadNotifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => class_basename($notification->type),
                    'data' => $notification->data,
                    'time' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type)
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ]);
    }

    // Display paginated list of all notifications
    public function index()
    {
        // Paginate notifications (e.g., 10 per page)
        $notifications = Auth::user()->notifications()->latest()->paginate(10);

        // Kelompokkan notifikasi berdasarkan tanggal
        $groupedNotifications = $notifications->groupBy(function($notification) {
            return $notification->created_at->toDateString();
        });

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('groupedNotifications', 'unreadCount', 'notifications'));
    }


    // Mark a specific notification as read
    public function markAllAsRead()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Mark all unread notifications as read for the authenticated user
        $user->unreadNotifications->markAsRead();

        // Redirect back or send a response
        return redirect()->route('notifications.index')->with('success', 'All notifications marked as read.');
    }

    public function clearAll()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Delete all notifications for the user
        $user->notifications()->delete();

        // Redirect back or send a response
        return redirect()->route('notifications.index')->with('success', 'All notifications cleared.');
    }

    public function show($id)
    {
        // Retrieve the notification by ID
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark the notification as read (optional)
        $notification->markAsRead();

        return view('notifications.show', compact('notification'));
    }

    // Get the appropriate icon for the notification type
    private function getNotificationIcon($type)
    {
        $icons = [
            SurveyAssigned::class => 'bi-clipboard-check',
            FollowUpReminder::class => 'bi-calendar-check',
            ProjectStarting::class => 'bi-gear',
            SurveyDeadline::class => 'bi-calendar-x',
            QuotationExpired::class => 'bi-file-earmark-excel',
            'NewClientAssigned' => 'bi-person-plus'
        ];
        
        return $icons[$type] ?? 'bi-bell';
    }

    // Get the appropriate color for the notification type
    private function getNotificationColor($type)
    {
        $colors = [
            SurveyAssigned::class => 'success',
            FollowUpReminder::class => 'warning',
            ProjectStarting::class => 'info',
            SurveyDeadline::class => 'danger',
            QuotationExpired::class => 'secondary',
            'NewClientAssigned' => 'primary'
        ];
        
        return $colors[$type] ?? 'secondary';
    }

    // Optional: Redirect to the related resource after marking as read
    private function redirectToRelatedResource($notification)
    {
        $data = $notification->data;

        switch ($notification->type) {
            case SurveyAssigned::class:
                return redirect()->route('surveys.show', $data['survey_id']);
            case FollowUpReminder::class:
                return redirect()->route('projects.show', $data['project_id']);
            case ProjectStarting::class:
                return redirect()->route('projects.show', $data['project_id']);
            case SurveyDeadline::class:
                return redirect()->route('surveys.show', $data['survey_id']);
            case QuotationExpired::class:
                return redirect()->route('documents.show', $data['document_id']);
            default:
                return redirect()->route('notifications.index');
        }
    }
}
