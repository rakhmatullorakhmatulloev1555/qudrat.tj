<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\AuditLog;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    /**
     * List activities for a related model (lead or deal).
     * GET /admin/activities?related_type=lead&related_id=5
     */
    public function index(Request $request)
    {
        $request->validate([
            'related_type' => 'required|in:lead,deal',
            'related_id'   => 'required|integer',
        ]);

        $modelClass = match($request->related_type) {
            'deal' => Deal::class,
            default => Lead::class,
        };
        $model = $modelClass::findOrFail($request->related_id);

        $activities = $model->activities()
            ->with('user:id,name')
            ->paginate(30);

        return response()->json($activities);
    }

    /**
     * Store a new activity.
     * POST /admin/activities
     */
    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'related_type'    => 'required|in:lead,deal',
            'related_id'      => 'required|integer',
            'type'            => 'required|in:call,email,note,task,meeting,whatsapp,telegram',
            'subject'         => 'nullable|string|max:255',
            'body'            => 'nullable|string',
            'outcome'         => 'nullable|string|max:60',
            'direction'       => 'nullable|in:in,out,internal',
            'scheduled_at'    => 'nullable|date',
            'completed_at'    => 'nullable|date',
            'duration_seconds'=> 'nullable|integer|min:0',
            'is_done'         => 'nullable|boolean',
            'meta'            => 'nullable|array',
        ]);

        $modelClass = match($data['related_type']) {
            'deal' => Deal::class,
            default => Lead::class,
        };
        $model = $modelClass::findOrFail($data['related_id']);

        $activity = $model->activities()->create([
            'type'             => $data['type'],
            'subject'          => $data['subject'] ?? null,
            'body'             => $data['body'] ?? null,
            'outcome'          => $data['outcome'] ?? null,
            'direction'        => $data['direction'] ?? 'out',
            'scheduled_at'     => $data['scheduled_at'] ?? null,
            'completed_at'     => $data['is_done'] ? ($data['completed_at'] ?? now()) : ($data['completed_at'] ?? null),
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'is_done'          => $data['is_done'] ?? false,
            'meta'             => $data['meta'] ?? null,
            'user_id'          => auth()->id(),
        ]);

        // Update last_activity_at on lead
        if ($model instanceof Lead) {
            $model->update(['last_activity_at' => now()]);
        }

        AuditLog::record('created', 'Activities', "Активность «{$data['type']}» добавлена");

        return response()->json([
            'ok'       => true,
            'activity' => $activity->load('user:id,name'),
        ]);
    }

    /**
     * Update an activity (e.g., mark done).
     */
    public function update(Request $request, Activity $activity)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'subject'          => 'nullable|string|max:255',
            'body'             => 'nullable|string',
            'outcome'          => 'nullable|string|max:60',
            'is_done'          => 'nullable|boolean',
            'completed_at'     => 'nullable|date',
            'duration_seconds' => 'nullable|integer|min:0',
            'meta'             => 'nullable|array',
        ]);

        if (isset($data['is_done']) && $data['is_done'] && !$activity->completed_at) {
            $data['completed_at'] = now();
        }

        $activity->update($data);

        return response()->json(['ok' => true, 'activity' => $activity->fresh()->load('user:id,name')]);
    }

    /**
     * Delete an activity.
     */
    public function destroy(Activity $activity)
    {
        Gate::authorize('delete-record');

        $activity->delete();
        return response()->json(['ok' => true]);
    }
}
