<?php

namespace App\Jobs;

use App\Models\BulkWhatsAppMessage;
use App\Models\BulkWhatsAppSend;
use App\Services\Ultramsg;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendWhatsAppText implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The recipient phone number (E.164).
     */
    protected string $to;

    /**
     * The message body.
     */
    protected string $body;

    /**
     * The bulk message record ID for tracking.
     */
    protected ?int $bulkMessageId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $to, string $body, ?int $bulkMessageId = null)
    {
        $this->to = $to;
        $this->body = $body;
        $this->bulkMessageId = $bulkMessageId;
    }

    /**
     * Execute the job.
     */
    public function handle(Ultramsg $ultramsg): void
    {
        Log::info('SendWhatsAppText executing', [
            'to' => $this->to,
            'at' => now()->toDateTimeString(),
            'bulk_message_id' => $this->bulkMessageId,
        ]);

        $result = $ultramsg->sendText($this->to, $this->body);
        
        // Update tracking record if we have one
        if ($this->bulkMessageId) {
            $this->updateBulkMessageStatus($result);
        }

        if (!($result['sent'] ?? false)) {
            Log::warning('SendWhatsAppText failed', [
                'to' => $this->to,
                'message' => $result['message'] ?? 'unknown',
                'response' => $result['response'] ?? null,
            ]);
        }
    }

    /**
     * Update the bulk message status and parent bulk send counters.
     */
    protected function updateBulkMessageStatus(array $result): void
    {
        if (!$this->bulkMessageId) return;

        DB::transaction(function () use ($result) {
            $bulkMessage = BulkWhatsAppMessage::find($this->bulkMessageId);
            if (!$bulkMessage) {
                Log::warning('BulkWhatsAppMessage not found', ['id' => $this->bulkMessageId]);
                return;
            }

            Log::info('Found bulk message', [
                'message_id' => $bulkMessage->id,
                'bulk_send_id' => $bulkMessage->bulk_whatsapp_send_id,
                'recipient' => $bulkMessage->recipient
            ]);

            $isSuccess = $result['sent'] ?? false;
            
            // Update the individual message
            $bulkMessage->update([
                'status' => $isSuccess ? 'sent' : 'failed',
                'sent_at' => $isSuccess ? now() : null,
                'error_message' => $isSuccess ? null : ($result['message'] ?? 'Unknown error'),
                'ultramsg_id' => $result['id'] ?? null,
            ]);

            // Update parent bulk send counters
            $bulkSend = $bulkMessage->bulkSend;
            if (!$bulkSend) {
                Log::warning('BulkWhatsAppSend not found for message', [
                    'message_id' => $this->bulkMessageId,
                    'bulk_send_id' => $bulkMessage->bulk_whatsapp_send_id
                ]);
                
                // Try to find the bulk send directly
                $bulkSend = BulkWhatsAppSend::find($bulkMessage->bulk_whatsapp_send_id);
                if (!$bulkSend) {
                    Log::error('BulkWhatsAppSend record does not exist', [
                        'bulk_send_id' => $bulkMessage->bulk_whatsapp_send_id
                    ]);
                    return;
                }
                Log::info('Found bulk send directly', ['bulk_send_id' => $bulkSend->id]);
            }

            if ($isSuccess) {
                $bulkSend->increment('sent_count');
            } else {
                $bulkSend->increment('failed_count');
            }
            $bulkSend->decrement('pending_count');

            // Check if bulk send is complete
            if ($bulkSend->pending_count <= 0) {
                $bulkSend->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
                $bulkSend->update(['status' => 'in_progress']);
            }
        });
    }
}


