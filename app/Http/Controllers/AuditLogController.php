<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'user.role'])
            ->orderBy('created_at', 'desc');

        // Filtros opcionales
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->paginate(25);

        // Obtener estadísticas para el dashboard
        $stats = [
            'total_logs' => AuditLog::count(),
            'today_logs' => AuditLog::whereDate('created_at', today())->count(),
            'login_attempts' => AuditLog::where('action', 'login_google')->count(),
            'failed_logins' => AuditLog::where('action', 'login_google_denied')->count(),
            'user_changes' => AuditLog::whereIn('action', ['user_created', 'user_updated', 'user_deleted'])->count(),
        ];

        return view('audit-logs.index', compact('auditLogs', 'stats'));
    }

    /**
     * Display the specified audit log.
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['user', 'user.role']);
        return view('audit-logs.show', compact('auditLog'));
    }
}
