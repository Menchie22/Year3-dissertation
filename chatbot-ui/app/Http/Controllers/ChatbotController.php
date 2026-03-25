<?php

namespace App\Http\Controllers;

use App\Services\FastApiService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('chatbot');
    }

    public function chat(Request $request, FastApiService $fastApi)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $result = $fastApi->analyze(
            $validated['message'],
            'transformer',
            2
        );

        $analysis = $result['analysis'] ?? [];
        $recommendation = $result['recommendation'] ?? [];
        $recommendations = $recommendation['recommendations'] ?? [];

        $reply = "I think you're feeling " . ($analysis['emotion'] ?? 'unknown') . ".";

        if (!empty($recommendation['mood'])) {
            $reply .= " Your current mood seems to be " . $recommendation['mood'] . ".";
        }

        if (!empty($recommendations)) {
            $reply .= " Here are some recommendations for you.";
        }

        return response()->json([
            'reply' => $reply,
            'emotion' => $analysis['emotion'] ?? null,
            'confidence' => $analysis['confidence'] ?? null,
            'method' => $analysis['method'] ?? null,
            'recommendations' => $recommendations,
        ]);
    }
}