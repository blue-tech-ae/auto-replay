<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GraphService
{
    protected string $base;

    public function __construct()
    {
        $this->base = 'https://graph.facebook.com/' . env('FB_GRAPH_VERSION');
    }

    public function privateReply(string $commentId, string $message, string $pageAccessToken): array
    {
        $resp = Http::asForm()->post("{$this->base}/{$commentId}/private_replies", [
            'message'      => $message,
            'access_token' => $pageAccessToken,
        ]);

        return $resp->json();
    }

    public function publicComment(string $commentId, string $message, string $pageAccessToken): array
    {
        $resp = Http::asForm()->post("{$this->base}/{$commentId}/comments", [
            'message'      => $message,
            'access_token' => $pageAccessToken,
        ]);

        return $resp->json();
    }
}
