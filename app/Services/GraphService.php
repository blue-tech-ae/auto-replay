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

    public function getPagePosts(string $pageId, string $pageAccessToken, int $limit = 50): array
    {
        $resp = \Http::get("{$this->base}/{$pageId}/posts", [
            'fields'       => 'id,permalink_url,message',
            'limit'        => $limit,
            'access_token' => $pageAccessToken,
        ])->throw()->json();

        return $resp['data'] ?? [];
    }

    public function subscribePageWebhooks(string $pageId, string $pageAccessToken): array
    {
        $resp = Http::asForm()->post("{$this->base}/{$pageId}/subscribed_apps", [
            'subscribed_fields' => 'feed,messages,message_echoes',
            'access_token'      => $pageAccessToken,
        ]);

        return $resp->json();
    }
}
