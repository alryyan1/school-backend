<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkWhatsAppSend extends Model
{
    use HasFactory;

    protected $table = 'bulk_whatsapp_sends';

    protected $fillable = [
        'message',
        'total_recipients',
        'sent_count',
        'failed_count',
        'pending_count',
        'started_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(BulkWhatsAppMessage::class, 'bulk_whatsapp_send_id', 'id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_recipients === 0) return 0;
        return ($this->sent_count + $this->failed_count) / $this->total_recipients * 100;
    }

    public function getNextScheduledMessageAttribute(): ?BulkWhatsAppMessage
    {
        return $this->messages()
            ->where('status', 'pending')
            ->orderBy('scheduled_at')
            ->first();
    }

    public function getTimeUntilNextAttribute(): ?int
    {
        $next = $this->next_scheduled_message;
        if (!$next) return null;
        
        return max(0, now()->diffInSeconds($next->scheduled_at, false));
    }
}
