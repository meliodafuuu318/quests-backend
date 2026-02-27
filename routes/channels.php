<?php

use Illuminate\Support\Facades\Broadcast;

// Default Laravel model channel
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private per-user notification channel
// Flutter SDK will request auth for "private-user.{userId}"
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});