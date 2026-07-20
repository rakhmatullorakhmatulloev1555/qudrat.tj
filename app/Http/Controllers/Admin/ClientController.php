<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with('assignee')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        return Inertia::render('Admin/Clients/Index', [
            'clients'  => $query->paginate(20)->withQueryString(),
            'managers' => User::whereIn('role', ['admin', 'manager'])->select('id', 'name')->get(),
            'filters'  => $request->only(['status', 'search']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:30',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
            'birth_date'  => 'nullable|date',
            'passport'    => 'nullable|string|max:50',
            'type'        => 'required|in:individual,company',
            'status'      => 'required|in:lead,active,vip,inactive',
            'source'      => 'required|in:website,phone,referral,social,other',
            'assigned_to' => 'nullable|exists:users,id',
            'notes'       => 'nullable|string',
        ]);

        Client::create($data);
        return back()->with('success', 'Клиент добавлен.');
    }

    public function update(Request $request, Client $client)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:30',
            'email'       => 'nullable|email',
            'address'     => 'nullable|string',
            'birth_date'  => 'nullable|date',
            'passport'    => 'nullable|string|max:50',
            'type'        => 'required|in:individual,company',
            'status'      => 'required|in:lead,active,vip,inactive',
            'source'      => 'required|in:website,phone,referral,social,other',
            'assigned_to' => 'nullable|exists:users,id',
            'notes'       => 'nullable|string',
        ]);

        $client->update($data);
        return back()->with('success', 'Клиент обновлён.');
    }

    public function destroy(Client $client)
    {
        Gate::authorize('delete-record');

        $client->delete();
        return back()->with('success', 'Клиент удалён.');
    }
}
