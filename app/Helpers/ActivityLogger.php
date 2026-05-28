<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an administrative action.
     */
    public static function log($action, $model = null, $description = null, $metadata = null)
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? class_basename($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}
