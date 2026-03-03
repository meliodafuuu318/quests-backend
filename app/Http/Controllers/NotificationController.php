<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Notification\{
    IndexNotificationsRepository,
    MarkNotificationReadRepository,
};

class NotificationController extends Controller
{
    protected $index, $markRead;

    public function __construct(
        IndexNotificationsRepository  $index,
        MarkNotificationReadRepository $markRead
    ) {
        $this->index    = $index;
        $this->markRead = $markRead;
    }

    public function index(Request $request)
    {
        return $this->index->execute($request);
    }

    public function markRead(Request $request)
    {
        return $this->markRead->execute($request);
    }
}