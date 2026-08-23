<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MemberNotification;
use App\Models\MemberNotificationRead;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $memberId = auth()->guard('member')->id();

        $notifications = MemberNotification::latest('id')->paginate(15);

        $readIds = MemberNotificationRead::where('member_id', $memberId)
            ->whereIn('member_notification_id', $notifications->pluck('id'))
            ->pluck('member_notification_id')
            ->flip();

        $notifications->getCollection()->transform(function ($notification) use ($readIds) {
            $notification->is_read = $readIds->has($notification->id);
            return $notification;
        });

        return inertia('User/Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark one notification read, then send the member on to whatever it
     * links to (the event/post/merchan/announcement page).
     */
    public function read($id)
    {
        $notification = MemberNotification::findOrFail($id);
        $memberId = auth()->guard('member')->id();

        MemberNotificationRead::firstOrCreate(
            ['member_notification_id' => $notification->id, 'member_id' => $memberId],
            ['read_at' => now()]
        );

        return redirect($notification->link);
    }

    public function readAll(Request $request)
    {
        $memberId = auth()->guard('member')->id();

        $alreadyRead = MemberNotificationRead::where('member_id', $memberId)->pluck('member_notification_id');

        $unreadIds = MemberNotification::whereNotIn('id', $alreadyRead)->pluck('id');

        $rows = $unreadIds->map(fn ($id) => [
            'member_notification_id' => $id,
            'member_id' => $memberId,
            'read_at' => now(),
        ])->all();

        if (!empty($rows)) {
            MemberNotificationRead::insert($rows);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
