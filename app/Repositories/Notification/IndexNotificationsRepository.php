<?php

namespace App\Repositories\Notification;

use App\Repositories\BaseRepository;
use App\Models\Notification;
use Carbon\Carbon;

class IndexNotificationsRepository extends BaseRepository
{
    public function execute($request)
    {
        $userId = auth()->id();

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $transformed = $notifications->getCollection()->map(function ($n) {
            return [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'post_id'    => $n->post_id,
                'read'       => $n->read_at !== null,
                'created_at' => $n->created_at->format('Y-m-d h:i'),
            ];
        });

        $notifications->setCollection($transformed);

        $unread = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return $this->success('Notifications fetched', [
            'notifications' => $notifications,
            'unread_count'  => $unread,
        ], 200);
    }
}