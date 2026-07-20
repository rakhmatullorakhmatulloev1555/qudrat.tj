<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = AuditLog::with('user')
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q) => $q
                    ->where('user_email', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%")
                    ->orWhere('model_label', 'like', "%{$s}%")
                )
            )
            ->when($request->module, fn($q, $m) => $q->where('module', $m))
            ->when($request->action, fn($q, $a) => $q->where('action', $a))
            ->when($request->date_from, fn($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to,   fn($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString()
            ->through(fn($l) => [
                'id'          => $l->id,
                'user_name'   => $l->user_name ?? 'System',
                'user_email'  => $l->user_email,
                'action'      => $l->action,
                'module'      => $l->module,
                'description' => $l->description,
                'model_label' => $l->model_label,
                'ip_address'  => $l->ip_address,
                'old_values'  => $l->old_values,
                'new_values'  => $l->new_values,
                'created_at'  => $l->created_at->format('d.m.Y H:i:s'),
            ]);

        $modules = AuditLog::distinct()->orderBy('module')->pluck('module')->filter();
        $actions = AuditLog::distinct()->orderBy('action')->pluck('action')->filter();

        return Inertia::render('Admin/Security/AuditLogs', [
            'logs'    => $logs,
            'modules' => $modules,
            'actions' => $actions,
            'filters' => $request->only(['search','module','action','date_from','date_to']),
        ]);
    }

    public function loginHistory(Request $request): Response
    {
        $history = LoginHistory::with('user')
            ->when($request->search, fn($q, $s) =>
                $q->where('email', 'like', "%{$s}%")
                  ->orWhere('ip_address', 'like', "%{$s}%")
            )
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString()
            ->through(fn($h) => [
                'id'         => $h->id,
                'email'      => $h->email,
                'ip_address' => $h->ip_address,
                'device'     => $h->device,
                'browser'    => $h->browser,
                'status'     => $h->status,
                'two_fa'     => $h->two_fa_used,
                'created_at' => $h->created_at->format('d.m.Y H:i:s'),
            ]);

        return Inertia::render('Admin/Security/LoginHistory', [
            'history' => $history,
            'filters' => $request->only(['search','status']),
        ]);
    }
}
