<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('thang-chua-co-ny-create', function ($post) {
    return true;
});
Broadcast::channel('thang-chua-co-ny-delete', function ($post) {
    return true;
});
