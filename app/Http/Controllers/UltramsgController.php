<?php

namespace App\Http\Controllers;

use App\Services\Ultramsg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWhatsAppText;

class UltramsgController extends Controller
{
    protected Ultramsg $ultramsg;

    public function __construct(Ultramsg $ultramsg)
    {
        $this->ultramsg = $ultramsg;
    }

    public function sendText(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'to' => 'required|string',
            'body' => 'required|string|max:4096',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $result = $this->ultramsg->sendText($data['to'], $data['body']);

        if ($result['sent']) {
            return response()->json([
                'sent' => true,
                'message' => 'ok',
                'id' => $result['id'],
            ]);
        }

        return response()->json([
            'sent' => false,
            'message' => $result['message'] ?? 'Failed to send',
            'response' => $result['response'] ?? null,
        ], 400);
    }

    public function sendDocument(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'to' => 'required|string',
            'filename' => 'required|string|max:255',
            'document' => 'required|string',
            'caption' => 'nullable|string|max:1024',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $result = $this->ultramsg->sendDocument(
            $data['to'],
            $data['filename'],
            $data['document'],
            $data['caption'] ?? null
        );

        if ($result['sent']) {
            return response()->json([
                'sent' => true,
                'message' => 'ok',
                'id' => $result['id'],
            ]);
        }

        return response()->json([
            'sent' => false,
            'message' => $result['message'] ?? 'Failed to send',
            'response' => $result['response'] ?? null,
        ], 400);
    }

    /**
     * Bulk send WhatsApp text to multiple recipients using background jobs.
     * Each message is delayed by 30 seconds from the previous one.
     */
    public function bulkSendText(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
            'body' => 'required|string|max:4096',
            'start_delay_seconds' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $recipients = $data['recipients'];
        $body = $data['body'];
        $startDelay = (int)($data['start_delay_seconds'] ?? 0);

        $delaySecondsBetween = 30; // fixed interval between messages

        // Create bulk send record
        $bulkSend = \App\Models\BulkWhatsAppSend::create([
            'message' => $body,
            'total_recipients' => count($recipients),
            'sent_count' => 0,
            'failed_count' => 0,
            'pending_count' => count($recipients),
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        // Create individual message records and dispatch jobs
        foreach (array_values($recipients) as $index => $to) {
            $delay = $startDelay + ($index * $delaySecondsBetween);
            $scheduledAt = now()->addSeconds($delay);
            
            $bulkMessage = \App\Models\BulkWhatsAppMessage::create([
                'bulk_whatsapp_send_id' => $bulkSend->id,
                'recipient' => $to,
                'message' => $body,
                'sequence_order' => $index + 1,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
            ]);

            SendWhatsAppText::dispatch($to, $body, $bulkMessage->id)->delay($scheduledAt);
        }

        return response()->json([
            'bulk_send_id' => $bulkSend->id,
            'queued' => count($recipients),
            'interval_seconds' => $delaySecondsBetween,
        ]);
    }

    /**
     * Get status of a bulk WhatsApp send operation.
     */
    public function getBulkSendStatus($id)
    {
        $bulkSend = \App\Models\BulkWhatsAppSend::with(['messages' => function($query) {
            $query->orderBy('sequence_order');
        }])->find($id);

        if (!$bulkSend) {
            return response()->json(['message' => 'Bulk send not found'], 404);
        }

        $nextMessage = $bulkSend->next_scheduled_message;
        $timeUntilNext = $bulkSend->time_until_next;

        return response()->json([
            'id' => $bulkSend->id,
            'message' => $bulkSend->message,
            'status' => $bulkSend->status,
            'progress' => [
                'total' => $bulkSend->total_recipients,
                'sent' => $bulkSend->sent_count,
                'failed' => $bulkSend->failed_count,
                'pending' => $bulkSend->pending_count,
                'percentage' => round($bulkSend->progress_percentage, 2),
            ],
            'timing' => [
                'started_at' => $bulkSend->started_at,
                'completed_at' => $bulkSend->completed_at,
                'next_scheduled_at' => $nextMessage?->scheduled_at,
                'time_until_next_seconds' => $timeUntilNext,
            ],
            'messages' => $bulkSend->messages->map(function($msg) {
                return [
                    'id' => $msg->id,
                    'recipient' => $msg->recipient,
                    'sequence_order' => $msg->sequence_order,
                    'status' => $msg->status,
                    'scheduled_at' => $msg->scheduled_at,
                    'sent_at' => $msg->sent_at,
                    'error_message' => $msg->error_message,
                    'ultramsg_id' => $msg->ultramsg_id,
                ];
            }),
        ]);
    }
}


