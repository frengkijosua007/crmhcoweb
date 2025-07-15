<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewClientAssigned;
use Illuminate\Support\Facades\Notification;


class ClientController extends Controller
{
    // Remove __construct() method - middleware will be defined in routes
    
    public function index(Request $request)
    {
        $query = Client::with(['pic', 'projects']);
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by PIC (for marketing role)
        if (Auth::user() && Auth::user()->hasRole('marketing')) {
            $query->where('pic_id', Auth::id());
        }
        
        $clients = $query->latest()->paginate(10);
        
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $pics = User::role(['marketing', 'admin'])->get();
        return view('clients.create', compact('pics'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'required|string',
            'pic_id' => 'required|exists:users,id',
            'source' => 'required|in:referral,website,walk-in,social-media,other',
            'source_detail' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        // If marketing, force PIC to themselves
        if (Auth::user()->hasRole('marketing')) {
            $validated['pic_id'] = Auth::id();
        }
        
        $client = Client::create($validated);
        
        // Send notification to admin and managers
        $adminAndManagers = User::role(['admin', 'manager'])->get();
        
        if ($adminAndManagers->isNotEmpty()) {
            Notification::send($adminAndManagers, new NewClientAssigned($client, Auth::user()));
        }
        
        return redirect()->route('clients.show', $client)
            ->with('success', 'Client berhasil ditambahkan!');
    }

    public function show(Client $client)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $client->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $client->load(['projects', 'pic']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $client->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $pics = User::role(['marketing', 'admin'])->get();
        return view('clients.edit', compact('client', 'pics'));
    }

    public function update(Request $request, Client $client)
    {
        // Check authorization
        if (Auth::user()->hasRole('marketing') && $client->pic_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'address' => 'required|string',
            'status' => 'required|in:prospek,aktif,selesai',
            'pic_id' => 'required|exists:users,id',
            'source' => 'required|in:referral,website,walk-in,social-media,other',
            'source_detail' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);
        
        // If marketing, force PIC to themselves
        if (Auth::user()->hasRole('marketing') && !Auth::user()->hasRole('admin')) {
            $validated['pic_id'] = Auth::id();
        }
        
        $client->update($validated);
        
        return redirect()->route('clients.show', $client)
            ->with('success', 'Data client berhasil diupdate!');
    }

    public function destroy(Client $client)
    {
        // Only admin can delete
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized');
        }
        
        // Check if client has projects
        if ($client->projects()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus client yang memiliki project!');
        }
        
        $client->delete();
        
        return redirect()->route('clients.index')
            ->with('success', 'Client berhasil dihapus!');
    }
}