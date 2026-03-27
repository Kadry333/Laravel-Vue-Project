<?php

namespace App\Http\Controllers\AdminsDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\HttpCache\Store;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth()->user();
        dd($user);

        $query = User::role('client')->with('approvedBy:id,name');

        if ($user->hasRole('receptionist')) {
            $query->where('is_approved', false);
        }

        $clients = $query->latest()->paginate(10);

        return Inertia::render('Admin/Clients/Index', [
            'clients' => $clients,
        ]);

        return Inertia::render('AdminDashboard/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }

        return Inertia::render('AdminDashboard/Clients/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }
        $data = $request->validated();
        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')
                ->store('avatars', 'public');
        }
        $data['password'] = Hash::make($data['password']);
        $data['is_approved'] = true;
        $data['approved_by'] = auth()->user()->id;
        $data['created_by'] = auth()->user()->id;
        $client = User::create($data);
        $client->assignRole('client');
        return redirect()->route('admins.clients.index')
            ->with('success', 'Client created successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $client)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }
        return Inertia::render('AdminDashboard/Clients/Edit', [
            'client' => $client,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, User $client)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }
        $data = $request->validated();
        if ($request->hasFile('avatar_image')) {
            $data['avatar_image'] = $request->file('avatar_image')
                ->store('avatars', 'public');
        } else {
            unset($data['avatar_image']); // keep whatever is already in the database
        }
        $client->update($data);
        return redirect()->route('admins.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $client)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }

        if ($client->reservations()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete a client with reservations.']);
        }

        $client->delete();

        return redirect()->route('admins.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function approve(User $client)
    {
        if ($client->is_approved) {
            return back()->withErrors(['error' => 'Client is already approved.']);
        }
        $client->update([
            'is_approved' => true,
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Client approved successfully, an email will be sent to the client');
    }

    public function approvedClients()
    {
        $clients = User::role('client')
            ->where('is_approved', true)
            ->where('approved_by', auth()->id()) // MY approved clients regardless of role
            ->latest()
            ->paginate(10);

        return Inertia::render('AdminDashboard/Clients/Approved', [
            'clients' => $clients,
        ]);
    }
}
