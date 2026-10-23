<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EpaperWorkflowLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->query('action');
        $search = $request->query('search');

        $query = EpaperWorkflowLog::with(['edition.region', 'user'])
            ->when($action, fn ($q) => $q->where('action', $action))
            ->when($search, function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            })
            ->latest();

        $logs = $query->paginate(20)->withQueryString();

        $actions = EpaperWorkflowLog::select('action')->distinct()->pluck('action');

        return view('admin.audit_logs.index', compact('logs', 'actions', 'action', 'search'));
    }
}
