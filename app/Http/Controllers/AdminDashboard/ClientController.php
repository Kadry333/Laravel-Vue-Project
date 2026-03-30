<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\Admin\ManageClientRequests\UpdateClientRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ClientController extends Controller
{
    private function getCountries(): array
    {
        return DB::table('lc_countries')
            ->where('is_visible', true)
            ->orderBy('official_name')
            ->select('id', 'official_name')
            ->get()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->official_name])
            ->toArray();
    }

    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = User::role('client')->with('approvedBy:id,name', 'country:id,official_name');

        if ($user->hasRole('receptionist')) {
            $query->where('is_approved', false);
        } else {
            if ($request->filled('status')) {
                $query->where('is_approved', $request->status === 'approved');
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

           $clients   = $query->latest()->paginate(10)->withQueryString();
        $countries = $this->getCountries();

        return Inertia::render('AdminDashboard/Clients/Index', [
            'clients'   => $clients,
            'countries' => $countries,
            'filters'   => $request->only(['search', 'country_id', 'gender', 'status']),
            'canCreate' => $user->hasAnyRole(['admin', 'manager']),
            'canDelete' => $user->hasRole('admin'),
            'role'      => $user->getRoleNames()->first(),
        ]);
    }

    public function create()
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }

        return Inertia::render('AdminDashboard/Clients/Create', [
            'countries' => $this->getCountries(),
        ]);
    }

    public function store(StoreClientRequest $request)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }
        

        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('avatar_image')) {
            $file = $request->file('avatar_image');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('avatars', $filename, 'public');

            $data['avatar_image'] = $filename; 
        }

        $data['password']    = Hash::make($data['password']);
        $data['is_approved'] = true;
        $data['approved_by'] = $user->id;
        $data['created_by']  = $user->id;

        $client = User::create($data);
        $client->assignRole('client');

        return redirect()->route('admins.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function edit(User $client)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }

        return Inertia::render('AdminDashboard/Clients/Edit', [
            'client'    => $client,
            'countries' => $this->getCountries(),
        ]);
    }

    public function update(UpdateClientRequest $request, User $client)
    {
        if (auth()->user()->hasRole('receptionist')) {
            abort(403);
        }

        $data = $request->validated();

         if ($request->hasFile('avatar_image')) {
            $file = $request->file('avatar_image');

            $filename = time() . '_' . $file->getClientOriginalName();

            $file->storeAs('avatars', $filename, 'public');

            $data['avatar_image'] = $filename; 
        } else {
            unset($data['avatar_image']);
        }

        $client->update($data);

        return redirect()->route('admins.clients.index')
            ->with('success', 'Client updated successfully.');
    }

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
        $client->notify(new \App\Notifications\ClientApprovedNotification());

        return back()->with('success', 'Client approved successfully, Email has been sent to client.');
    }

    public function approvedClients(Request $request)
    {
        $user  = auth()->user();
        $query = User::role('client')
            ->with('country:id,official_name')
            ->where('is_approved', true)
            ->where('approved_by', $user->id);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

                $clients   = $query->latest()->paginate(10)->withQueryString();

        $countries = $this->getCountries();

        return Inertia::render('AdminDashboard/Clients/Index', [
            'clients'   => $clients,
            'countries' => $countries,
            'filters'   => $request->only(['search', 'country_id', 'gender']),
            'canCreate' => false,
            'canDelete' => false,
            'role'      => $user->getRoleNames()->first(),
            'pageTitle' => 'My Approved Clients',
        ]);
    }
}
