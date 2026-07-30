<?php

namespace App\Http\Controllers;

use App\Models\Product;
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

        $systemPrompt = [
            'role' => 'user',
            'parts' => [['text' => "You are a product management assistant. The Product model has: name (string), description (text), price (decimal), stock (integer).

For actions, respond ONLY with JSON (no other text):
- List all: {\"action\":\"list\"}
- Find by name: {\"action\":\"search\",\"name\":\"product name\"}
- View by ID: {\"action\":\"show\",\"id\": NUMBER}
- Add: {\"action\":\"create\",\"product\":{\"name\":\"...\",\"description\":\"...\",\"price\": NUMBER,\"stock\": NUMBER}}
- Update: {\"action\":\"update\",\"id\": NUMBER,\"product\":{\"name\":\"...\",\"price\": NUMBER}}
- Update by name: {\"action\":\"update\",\"name\":\"product name\",\"product\":{\"price\": NUMBER}}
- Delete: {\"action\":\"delete\",\"id\": NUMBER}
- Delete by name: {\"action\":\"delete\",\"name\":\"product name\"}

Search is preferred for looking up products by name. Use the exact name the user provided.

For greetings/questions, respond naturally in English."]]
        ];

        $contents = array_merge([$systemPrompt], $history);

        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=' . config('app.gemini_api_key'), [
            'contents' => $contents,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to contact AI service'], 500);
        }

        $text = $response->json('candidates.0.content.parts.0.text');
        $history[] = ['role' => 'model', 'parts' => [['text' => $text]]];

        $actionResult = $this->handleAction($text);

        session()->put('chat_history', $history);

        return response()->json([
            'reply' => $actionResult['message'],
            'action' => $actionResult['action'],
        ]);
    }

    private function handleAction(string $text): array
    {
        $json = json_decode($text, true);

        if (! $json || ! isset($json['action'])) {
            return ['message' => $text, 'action' => null];
        }

        return match ($json['action']) {
            'list' => $this->listProducts(),
            'search' => $this->searchProduct($json['name'] ?? ''),
            'show' => $this->showProduct($json['id'] ?? null),
            'create' => $this->createProduct($json['product'] ?? []),
            'update' => $this->updateProduct($json['id'] ?? null, $json['product'] ?? [], $json['name'] ?? null),
            'delete' => $this->deleteProduct($json['id'] ?? null, $json['name'] ?? null),
            default => ['message' => "Unknown action: {$json['action']}", 'action' => null],
        };
    }

    private function listProducts(): array
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            return ['message' => 'No products found.', 'action' => 'list'];
        }
        $list = $products->map(fn($p) => "- {$p->name} (\${$p->price}, stock: {$p->stock})")->implode("\n");
        return ['message' => "**Products:**\n$list", 'action' => 'list'];
    }

    private function showProduct($id): array
    {
        $product = Product::find($id);
        if (! $product) {
            return ['message' => "Product #{$id} not found.", 'action' => null];
        }
        return ['message' => "**{$product->name}**\n- Price: \${$product->price}\n- Stock: {$product->stock}\n- Description: {$product->description}", 'action' => 'show'];
    }

    private function createProduct(array $data): array
    {
        $name = $data['name'] ?? 'Unnamed Product';
        $existing = Product::where('name', $name)->first();
        if ($existing) {
            return ['message' => " Product **{$name}** already exists (ID: {$existing->id}). Use update to modify it.", 'action' => null];
        }
        $product = Product::create([
            'name' => $name,
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
            'stock' => $data['stock'] ?? 0,
        ]);
        return ['message' => " Product **{$product->name}** created successfully! (ID: {$product->id})", 'action' => 'create'];
    }

    private function searchProduct(string $name): array
    {
        $product = Product::where('name', 'like', "%{$name}%")->first();
        if (! $product) {
            return ['message' => "No product found matching \"{$name}\".", 'action' => null];
        }
        return ['message' => "**{$product->name}** (ID: {$product->id})\n- Price: \${$product->price}\n- Stock: {$product->stock}\n- Description: {$product->description}", 'action' => 'show'];
    }

    private function updateProduct($id, array $data, $name = null): array
    {
        $product = $id ? Product::find($id) : ($name ? Product::where('name', 'like', "%{$name}%")->first() : null);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }
        $product->update(array_filter($data));
        return ['message' => " Product **{$product->name}** updated successfully!", 'action' => 'update'];
    }

    private function deleteProduct($id, $name = null): array
    {
        $product = $id ? Product::find($id) : ($name ? Product::where('name', 'like', "%{$name}%")->first() : null);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }
        $product->delete();
        return ['message' => " Product deleted successfully!", 'action' => 'delete'];
    }

    public function clear()
    {
        session()->forget('chat_history');
        return response()->json(['status' => 'ok']);
    }
}
