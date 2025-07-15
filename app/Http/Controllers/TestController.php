<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewClientAssigned;

class TestController extends Controller
{
    public function testNotification()
    {
        $client = Client::first(); // Or create a dummy client if none exists
        Auth::user()->notify(new NewClientAssigned($client, Auth::user()));

        return response()->json(['message' => 'Test notification sent']);
    }
}
