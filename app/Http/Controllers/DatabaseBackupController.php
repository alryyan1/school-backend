<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestination;

class DatabaseBackupController extends Controller
{
    /**
     * Create a database backup
     * POST /api/database-backup/create
     */
    public function create(Request $request)
    {
        try {
            // Create backup using Spatie Backup package
            Artisan::call('backup:run', ['--only-db' => true]);
            
            // Get the latest backup
            $backupDestination = BackupDestination::create(config('backup.backup.destination.disks')[0], config('backup.backup.name'));
            $backups = $backupDestination->backups();
            
            if ($backups->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء النسخة الاحتياطية'
                ], 500);
            }
            
            $latestBackup = $backups->first();
            $disk = config('backup.backup.destination.disks')[0];
            $backupPath = $latestBackup->path();
            $backupSize = Storage::disk($disk)->size($backupPath);
            
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء النسخة الاحتياطية بنجاح',
                'backup' => [
                    'path' => $backupPath,
                    'size' => $backupSize,
                    'date' => $latestBackup->date()->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List all backups
     * GET /api/database-backup/list
     */
    public function list()
    {
        try {
            $disk = config('backup.backup.destination.disks')[0];
            $backupDestination = BackupDestination::create($disk, config('backup.backup.name'));
            $backups = $backupDestination->backups();
            
            $backupList = $backups->map(function ($backup) use ($disk) {
                $backupPath = $backup->path();
                $backupSize = Storage::disk($disk)->size($backupPath);
                
                return [
                    'path' => $backupPath,
                    'size' => $backupSize,
                    'size_human' => $this->formatBytes($backupSize),
                    'date' => $backup->date()->format('Y-m-d H:i:s'),
                    'age' => $backup->date()->diffForHumans(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'backups' => $backupList->values()->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب قائمة النسخ الاحتياطية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a backup file
     * GET /api/database-backup/download
     */
    public function download(Request $request)
    {
        try {
            $path = $request->input('path');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'مسار الملف مطلوب'
                ], 400);
            }
            
            // Get the disk from config
            $disk = config('backup.backup.destination.disks')[0];
            
            if (!Storage::disk($disk)->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'الملف غير موجود'
                ], 404);
            }
            
            return Storage::disk($disk)->download($path);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحميل الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup file
     * DELETE /api/database-backup/delete
     */
    public function delete(Request $request)
    {
        try {
            $path = $request->input('path');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'مسار الملف مطلوب'
                ], 400);
            }
            
            $disk = config('backup.backup.destination.disks')[0];
            
            if (!Storage::disk($disk)->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'الملف غير موجود'
                ], 404);
            }
            
            Storage::disk($disk)->delete($path);
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف النسخة الاحتياطية بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الملف: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

