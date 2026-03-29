<?php

namespace App\Http\Controllers\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('client')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('national_id', 'like', '%' . $request->search . '%');
            });
        }

        $clients = $query->paginate(15);

        return Inertia::render('AdminDashboard/Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only(['search']),
        ]);
    }

    public function toggleApproval(User $client)
    {
        $client->is_approved = !$client->is_approved;
        $client->save();

        $status = $client->is_approved ? 'Approved' : 'Unapproved';
        return back()->with('success', "Client account has been {$status}.");
    }
}
