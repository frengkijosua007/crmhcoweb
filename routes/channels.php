<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk user spesifik
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel untuk tim/department spesifik
Broadcast::channel('team.{team}', function ($user, $team) {
    return $user->belongsToTeam($team);
});

// Channel publik untuk update status proyek
Broadcast::channel('project-updates', function ($user) {
    return true;
});
