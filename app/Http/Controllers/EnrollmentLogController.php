<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentLog;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentLogController extends Controller
{
    /**
     * Display a listing of enrollment logs with filtering.
     */
    public function index(Request $request)
    {
        $query = EnrollmentLog::with([
            'enrollment.student',
            'enrollment.gradeLevel',
            'enrollment.school',
            'user:id,name'
        ]);

        // Filter by student name
        if ($request->filled('student_name')) {
            $query->whereHas('enrollment.student', function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->student_name . '%');
            });
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('changed_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('changed_at', '<=', $request->date_to);
        }

        // Filter by school
        if ($request->filled('school_id')) {
            $query->whereHas('enrollment', function ($q) use ($request) {
                $q->where('school_id', $request->school_id);
            });
        }

        // Filter by user who made the change
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Sort by changed_at desc by default
        $query->orderBy('changed_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 15);
        $logs = $query->paginate($perPage);

        // Add action type labels to the items
        $items = $logs->items();
        foreach ($items as $log) {
            $log->action_type_label = $log->action_type_label;
        }

        return response()->json([
            'data' => $items,
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
            'filters' => [
                'action_types' => [
                    'grade_level_change' => 'تغيير المرحلة الدراسية',
                    'status_change' => 'تغيير حالة التسجيل',
                    'classroom_change' => 'تغيير الفصل الدراسي',
                    'fees_change' => 'تغيير الرسوم',
                    'discount_change' => 'تغيير الخصم',
                    'academic_year_change' => 'تغيير العام الدراسي',
                ]
            ]
        ]);
    }

    /**
     * Get statistics for enrollment logs.
     */
    public function statistics(Request $request)
    {
        // Helper function to create base query with filters
        $createBaseQuery = function () use ($request) {
            $query = EnrollmentLog::query();
            
            if ($request->filled('date_from')) {
                $query->whereDate('changed_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('changed_at', '<=', $request->date_to);
            }

            if ($request->filled('school_id')) {
                $query->whereHas('enrollment', function ($q) use ($request) {
                    $q->where('school_id', $request->school_id);
                });
            }
            
            return $query;
        };

        // Get action type counts
        $actionTypeStats = $createBaseQuery()
            ->select('action_type', DB::raw('count(*) as count'))
            ->groupBy('action_type')
            ->pluck('count', 'action_type')
            ->toArray();

        // Get daily activity for the last 30 days
        $dailyActivity = $createBaseQuery()
            ->where('changed_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(changed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('DATE(changed_at)'))
            ->orderBy(DB::raw('DATE(changed_at)'), 'desc')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Get top users by activity
        $topUsers = $createBaseQuery()
            ->with('user:id,name')
            ->select('user_id', DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name ?? 'غير محدد',
                    'count' => $item->count
                ];
            });

        // Get total logs count
        $totalLogs = $createBaseQuery()->count();

        return response()->json([
            'action_type_stats' => $actionTypeStats,
            'daily_activity' => $dailyActivity,
            'top_users' => $topUsers,
            'total_logs' => $totalLogs,
        ]);
    }

    /**
     * Show a specific enrollment log.
     */
    public function show(EnrollmentLog $enrollmentLog)
    {
        $enrollmentLog->load([
            'enrollment.student',
            'enrollment.gradeLevel',
            'enrollment.school',
            'enrollment.classroom',
            'user:id,name'
        ]);

        return response()->json([
            'data' => $enrollmentLog
        ]);
    }
}
