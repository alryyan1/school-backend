<?php

namespace App\Http\Controllers;

use App\Services\Ultramsg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}


