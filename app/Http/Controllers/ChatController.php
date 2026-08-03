<?php

namespace App\Http\Controllers;

use App\Ai\ActionRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat.index');
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $history = session()->get('chat_history', []);
        $history[] = ['role' => 'user', 'parts' => [['text' => $request->message]]];

        $contents = array_merge([['role' => 'user', 'parts' => [['text' => $this->systemPrompt()]]]], $history);

        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=' . config('app.gemini_api_key'), [
            'contents' => $contents,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to contact AI service'], 500);
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        $history[] = ['role' => 'model', 'parts' => [['text' => $text]]];

        $action = json_decode($text, true) ?? [];
        $result = app(ActionRegistry::class)->handle(auth()->user(), $action);

        session()->put('chat_history', $history);

        return response()->json([
            'reply' => $result['message'],
            'action' => $result['action'],
        ]);
    }

    public function clear()
    {
        session()->forget('chat_history');

        return response()->json(['status' => 'ok']);
    }

    private function systemPrompt(): string
    {
        return "You are an inventory management assistant. Respond to greetings/questions naturally in English.

For actions, respond ONLY with a single JSON object (no other text, no markdown fences). Available actions:

Products:
- List all: {\"action\":\"product.list\"}
- Search by name: {\"action\":\"product.search\",\"name\":\"...\"}
- Show by id or name: {\"action\":\"product.show\",\"id\":N} or {\"action\":\"product.show\",\"name\":\"...\"}
- Create: {\"action\":\"product.create\",\"product\":{\"name\":\"...\",\"sku\":\"...\",\"description\":\"...\",\"price\":N,\"stock\":N,\"reorder_level\":N}}
- Update by id or name: {\"action\":\"product.update\",\"id\":N,\"product\":{\"price\":N}}
- Delete (admin only): {\"action\":\"product.delete\",\"id\":N}

Categories (view allowed for all, create/update/delete admin only):
- {\"action\":\"category.list\"}
- {\"action\":\"category.create\",\"name\":\"...\",\"description\":\"...\"}
- {\"action\":\"category.update\",\"id\":N,\"name\":\"...\"}
- {\"action\":\"category.delete\",\"id\":N}

Suppliers (view allowed for all, create/update/delete admin only):
- {\"action\":\"supplier.list\"}
- {\"action\":\"supplier.create\",\"name\":\"...\",\"email\":\"...\",\"phone\":\"...\"}
- {\"action\":\"supplier.update\",\"id\":N,\"name\":\"...\"}
- {\"action\":\"supplier.delete\",\"id\":N}

Stock movements (product by id or name):
- Stock in: {\"action\":\"stock.in\",\"id\":N,\"quantity\":N,\"reason\":\"...\"}
- Stock out: {\"action\":\"stock.out\",\"name\":\"...\",\"quantity\":N}
- Adjust: {\"action\":\"stock.adjust\",\"id\":N,\"quantity\":N} (quantity is the new absolute stock value)
- History: {\"action\":\"stock.history\",\"id\":N}

Reports:
- {\"action\":\"low_stock\"}
- {\"action\":\"report_valuation\"}
- {\"action\":\"report_summary\"}

Prefer searching by name when the user names a product. Use the exact name the user provided.";
    }
}
