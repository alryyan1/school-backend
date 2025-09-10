<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Ultramsg
{
    protected string $baseUrl;
    protected ?string $instanceId;
    protected ?string $token;

    public function __construct()
    {
        $config = config('services.ultramsg');
        $this->baseUrl = rtrim((string)($config['base_url'] ?? 'https://api.ultramsg.com'), '/');
        $this->instanceId = $config['instance_id'] ?? null;
        $this->token = $config['token'] ?? null;
    }

    /**
     * Send a text message via UltraMsg WhatsApp API
     *
     * @param string $to E.164 phone number or chatID (e.g. +14155552671)
     * @param string $body Message text (UTF-8/UTF-16)
     * @return array{sent:bool,message:string,id:int|null,response:mixed}
     */
    public function sendText(string $to, string $body): array
    {
        if (!$this->instanceId || !$this->token) {
            return [
                'sent' => false,
                'message' => 'Ultramsg not configured',
                'id' => null,
                'response' => null,
            ];
        }

        $url = $this->baseUrl . '/' . $this->instanceId . '/messages/chat';

        try {
            $response = Http::asForm()
                ->withOptions([
                    'verify' => false, // mirror provided cURL sample; consider enabling in production
                    'timeout' => 30,
                ])
                ->post($url, [
                    'token' => $this->token,
                    'to' => $to,
                    'body' => $body,
                ]);

            $json = $response->json();
            $sent = false;
            $id = null;
            $message = '';

            if (is_array($json)) {
                // API returns {"sent":"true","message":"ok","id":3964}
                $sent = ($json['sent'] ?? 'false') === 'true' || ($json['sent'] ?? false) === true;
                $message = (string)($json['message'] ?? '');
                $id = isset($json['id']) ? (int) $json['id'] : null;
            } else {
                $message = 'Invalid response';
            }

            if (!$response->successful() && !$sent) {
                Log::warning('Ultramsg sendText non-2xx', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return [
                'sent' => $sent,
                'message' => $message,
                'id' => $id,
                'response' => $json,
            ];
        } catch (\Throwable $e) {
            Log::error('Ultramsg sendText error', ['error' => $e->getMessage()]);
            return [
                'sent' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'id' => null,
                'response' => null,
            ];
        }
    }

    /**
     * Send a document to a phone number or group via UltraMsg
     * Docs: https://docs.ultramsg.com/api/post/messages/document
     *
     * @param string $to E.164 number or chatID
     * @param string $filename Example: Hello.pdf
     * @param string $documentUrlOrBase64 HTTP link or base64 string
     * @param string|null $caption Optional caption (max 1024)
     * @return array{sent:bool,message:string,id:int|null,response:mixed}
     */
    public function sendDocument(string $to, string $filename, string $documentUrlOrBase64, ?string $caption = null): array
    {
        if (!$this->instanceId || !$this->token) {
            return [
                'sent' => false,
                'message' => 'Ultramsg not configured',
                'id' => null,
                'response' => null,
            ];
        }

        $url = $this->baseUrl . '/' . $this->instanceId . '/messages/document';

        try {
            $payload = [
                'token' => $this->token,
                'to' => $to,
                'filename' => $filename,
                'document' => $documentUrlOrBase64,
            ];
            if ($caption !== null && $caption !== '') {
                $payload['caption'] = $caption;
            }

            $response = Http::asForm()
                ->withOptions([
                    'verify' => false,
                    'timeout' => 60,
                ])
                ->post($url, $payload);

            $json = $response->json();
            $sent = false;
            $id = null;
            $message = '';

            if (is_array($json)) {
                $sent = ($json['sent'] ?? 'false') === 'true' || ($json['sent'] ?? false) === true;
                $message = (string)($json['message'] ?? '');
                $id = isset($json['id']) ? (int) $json['id'] : null;
            } else {
                $message = 'Invalid response';
            }

            if (!$response->successful() && !$sent) {
                Log::warning('Ultramsg sendDocument non-2xx', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return [
                'sent' => $sent,
                'message' => $message,
                'id' => $id,
                'response' => $json,
            ];
        } catch (\Throwable $e) {
            Log::error('Ultramsg sendDocument error', ['error' => $e->getMessage()]);
            return [
                'sent' => false,
                'message' => 'Exception: ' . $e->getMessage(),
                'id' => null,
                'response' => null,
            ];
        }
    }
}


