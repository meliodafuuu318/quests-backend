<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepository;
use App\Models\Notification;
use Carbon\Carbon;

class MarkNotificationReadRepository extends BaseRepository
{
    public function execute($request)
    {
        $userId = auth()->id();

        if ($request->boolean('all')) {
            // Mark all unread notifications as read
            Notification::where('user_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => Carbon::now()]);

            return $this->success('All notifications marked as read', [], 200);
        }

        $request->validate(['notificationId' => 'required|exists:notifications,id']);

        $notification = Notification::where('id', $request->notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return $this->error('Notification not found', 404);
        }

        $notification->update(['read_at' => Carbon::now()]);

        return $this->success('Notification marked as read', [], 200);
    }
}