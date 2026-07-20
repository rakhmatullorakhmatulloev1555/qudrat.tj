<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $q = Document::query();
        if ($request->search) $q->where('title','like',"%{$request->search}%");
        if ($request->type) $q->where('type', $request->type);
        if ($request->status) $q->where('status', $request->status);
        if ($request->related_type) $q->where('related_type', $request->related_type);
        $documents = $q->orderBy('created_at','desc')->paginate(25)->withQueryString();
        $stats = [
            'total'    => Document::count(),
            'active'   => Document::where('status','active')->orWhere('status','signed')->count(),
            'expiring' => Document::whereNotNull('expires_at')->where('expires_at','<=',now()->addDays(30))->where('expires_at','>=',now())->count(),
            'draft'    => Document::where('status','draft')->count(),
        ];
        return Inertia::render('Admin/Documents/Index', [
            'documents' => $documents,
            'stats'     => $stats,
            'filters'   => $request->only('search','type','status','related_type'),
        ]);
    }
    public function store(Request $request)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'=>'required|string|max:255',
            'type'=>'required|in:contract,invoice,certificate,permit,act,report,power_of_attorney,other',
            'related_type'=>'nullable|string|max:50',
            'related_id'=>'nullable|integer',
            'related_name'=>'nullable|string|max:255',
            'status'=>'required|in:draft,review,signed,active,expired,archived',
            'issued_at'=>'nullable|date',
            'expires_at'=>'nullable|date',
            'signed_by'=>'nullable|string|max:255',
            'notes'=>'nullable|string',
        ]);
        Document::create($data);
        return back()->with('success', 'Документ добавлен');
    }
    public function update(Request $request, Document $document)
    {
        Gate::authorize('manage');

        $data = $request->validate([
            'title'=>'required|string|max:255',
            'type'=>'required|in:contract,invoice,certificate,permit,act,report,power_of_attorney,other',
            'related_type'=>'nullable|string|max:50',
            'related_id'=>'nullable|integer',
            'related_name'=>'nullable|string|max:255',
            'status'=>'required|in:draft,review,signed,active,expired,archived',
            'issued_at'=>'nullable|date',
            'expires_at'=>'nullable|date',
            'signed_by'=>'nullable|string|max:255',
            'notes'=>'nullable|string',
        ]);
        $document->update($data);
        return back()->with('success', 'Документ обновлён');
    }
    public function destroy(Document $document)
    {
        Gate::authorize('delete-record');

        $document->delete();
        return back()->with('success', 'Документ удалён');
    }
}
