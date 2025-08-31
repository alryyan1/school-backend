<?php // app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use App\Models\FeeInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Use Laravel's HTTP Client
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // For authorization
use Illuminate\Support\Str; // For string manipulation

class NotificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Sends a WhatsApp reminder for a specific fee installment.
     * POST /api/notify/whatsapp/installment/{feeInstallment}
     */
    public function sendInstallmentReminder(Request $request, FeeInstallment $feeInstallment)
    {
        // Authorization (Example: Allow admin)
        // $this->authorize('sendNotifications', $feeInstallment); // Define policy if needed

        // Load necessary relationships
        $feeInstallment->load([
            'student:id,student_name,father_phone,mother_phone'
        ]);

        $student = $feeInstallment->student;

        if (!$student) {
            return response()->json(['message' => 'بيانات الطالب غير متوفرة.'], 404);
        }

        // --- Determine Recipient Phone Number ---
        // Priority: Father, then Mother (adjust as needed)
        $recipientPhoneRaw = $student->father_phone ?? $student->mother_phone;

        if (!$recipientPhoneRaw) {
            return response()->json(['message' => 'رقم هاتف ولي الأمر غير متوفر لهذا الطالب.'], 400);
        }
        // Format to number@c.us (common WhatsApp ID format)
        $defaultCountryCode = config('services.waapi.default_country_code', '249');
        $recipientPhoneDigits = preg_replace('/[^0-9]/', '', $recipientPhoneRaw);

        if (Str::startsWith($recipientPhoneDigits, '0')) {
            $recipientPhoneDigits = $defaultCountryCode . substr($recipientPhoneDigits, 1);
        } elseif (!Str::startsWith($recipientPhoneDigits, $defaultCountryCode)) {
            $recipientPhoneDigits = $defaultCountryCode . $recipientPhoneDigits;
        }
        // Final WAAPI format
        $chatId = $recipientPhoneDigits . '@c.us';

        // Example Result: 9639...

        // --- Construct Message ---
        // WARNING: Many WAAPI services require pre-approved templates for business-initiated messages.
        // Adjust this message or use template parameters based on your provider.
        $amountDue = number_format((float)$feeInstallment->amount_due, 2);
        $dueDate = $feeInstallment->due_date->format('Y/m/d');
        $message = "تذكير بقسط دراسي للطالبـ/ـة: {$student->student_name}\n\n";
        $message .= "القسط: {$feeInstallment->title}\n";
        $message .= "المبلغ المستحق: {$amountDue} \n";
        $message .= "تاريخ الاستحقاق: {$dueDate}\n\n";
        $message .= "يرجى المبادرة بالسداد. شكراً لتعاونكم.";

        // --- Call WAAPI Service ---
        $baseUrl = config('services.waapi.url');
        $instanceId = config('services.waapi.instance_id');
        $token = config('services.waapi.token');

        if (!$baseUrl || !$instanceId || !$token) {
            Log::error('WAAPI Service URL, Instance ID, or Token not configured.');
            return response()->json(['message' => 'خدمة إرسال WhatsApp غير مهيأة بشكل صحيح.'], 500);
        }

        // Construct the specific endpoint URL
        $endpoint = rtrim($baseUrl, '/') . '/instances/' . $instanceId . '/client/action/send-message';

        try {
            // Use Laravel HTTP Client, matching Guzzle example structure
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                // Assuming token is sent via Authorization header (adjust if different)
                'Authorization' => 'Bearer ' . $token
            ])
                ->timeout(20) // Increase timeout slightly maybe
                ->post($endpoint, [
                    'chatId' => $chatId,
                    'message' => $message,
                    // Add other WAAPI specific parameters if needed here
                ]);

            // Check WAAPI response status
            if ($response->successful()) {
                // WAAPI might have its own internal status in the JSON body
                $responseData = $response->json();
                // Example check (ADAPT BASED ON ACTUAL WAAPI RESPONSE):
                // if (isset($responseData['status']) && $responseData['status'] === 'success') {
                if (true) { // Assume success if HTTP status is 2xx for now
                    Log::info("WAAPI message request sent successfully to {$chatId} for installment ID {$feeInstallment->id}. WAAPI Response: " . json_encode($responseData));
                    return response()->json(['message' => 'تم إرسال طلب التذكير عبر WhatsApp بنجاح.']);
                }
                // } else {
                //     Log::warning("WAAPI reported failure for installment ID {$feeInstallment->id}: " . $response->body());
                //     return response()->json([
                //         'message' => 'فشل إرسال الرسالة (تقرير من خدمة WhatsApp).',
                //         'waapi_response' => $responseData
                //     ], 400); // Or appropriate status based on WAAPI error
                // }
            } else {
                // Handle HTTP errors (4xx, 5xx) from WAAPI server
                Log::error("WAAPI HTTP Error for installment ID {$feeInstallment->id}: Status " . $response->status() . " Body: " . $response->body());
                return response()->json([
                    'message' => 'فشل إرسال الرسالة عبر خدمة WhatsApp.',
                    'waapi_status' => $response->status(),
                    'waapi_error' => $response->json() ?? $response->body()
                ], 502); // 502 Bad Gateway typical for API errors
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("WAAPI Connection Error for installment ID {$feeInstallment->id}: " . $e->getMessage());
            return response()->json(['message' => 'لا يمكن الاتصال بخدمة WhatsApp حالياً.'], 504);
        } catch (\Exception $e) {
            Log::error("WAAPI General Error for installment ID {$feeInstallment->id}: " . $e->getMessage());
            report($e);
            return response()->json(['message' => 'حدث خطأ غير متوقع أثناء إرسال الرسالة.'], 500);
        }
    }
}
