<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    /**
     * Handle the AI assistant request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleRequest(Request $request)
    {
        // Validez la requête de l'utilisateur
        $validated = $request->validate([
            'prompt' => 'required|string',
        ]);

        // Remplacez 'VOTRE_CLE_API' par votre clé API Google Gemini
        $apiKey = env('GEMINI_API_KEY');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $validated['prompt']],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $generatedText = $response->json()['candidates'][0]['content']['parts'][0]['text'];
                return response()->json(['success' => true, 'response' => $generatedText]);
            } else {
                return response()->json(['success' => false, 'message' => 'API request failed.'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display AI assistant usage statistics.
     *
     * @return \Illuminate\View\View
     */
    public function aiStats()
    {
        // Logique pour récupérer les statistiques d'utilisation de l'IA
        // Par exemple, depuis une base de données ou un fichier log
        $stats = [
            'total_requests' => 125,
            'requests_today' => 15,
            'last_request' => '2024-09-06 10:30:00',
        ];

        return view('admin.ai-stats', compact('stats'));
    }
}
