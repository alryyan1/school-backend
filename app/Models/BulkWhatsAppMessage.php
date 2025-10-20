<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkWhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'bulk_whatsapp_messages';

    protected $fillable = [
        'bulk_whatsapp_send_id',
        'recipient',
        'message',
        'sequence_order',
        'scheduled_at',
        'sent_at',
        'status',
        'error_message',
        'ultramsg_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function bulkSend(): BelongsTo
    {
        return $this->belongsTo(BulkWhatsAppSend::class, 'bulk_whatsapp_send_id', 'id');
    }
}
