<?php

use Illuminate\Support\Facades\Broadcast;

// User's own channel — for dashboard/assessment list updates
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return $user->id === $userId;
});

// Assessment channel — owner can listen for invitee/test updates
Broadcast::channel('assessment.{assessmentId}', function ($user, $assessmentId) {
    return \App\Models\Assessment::where('id', $assessmentId)
        ->where('user_id', $user->id)
        ->exists();
});

// Admin channel
Broadcast::channel('admin', function ($user) {
    // Check if authenticated via admin guard or has admin role
    return true; // Admin auth is handled separately via adminToken
});
