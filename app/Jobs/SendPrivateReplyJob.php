<?php

namespace App\Jobs;

use App\Models\CommentLog;
use App\Models\Page;
use App\Services\GraphService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPrivateReplyJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $pageId,
        public string $pageAccessToken,
        public string $commentId,
        public string $postId,
        public string $template
    ) {}

    public function handle(GraphService $graph): void
    {
        $postUrl = "https://facebook.com/{$this->postId}";
        $msg = strtr($this->template, [
            '{post_url}' => $postUrl,
        ]);

        $resp = $graph->privateReply($this->commentId, $msg, $this->pageAccessToken);

        CommentLog::updateOrCreate(
            ['comment_id' => $this->commentId],
            [
                'page_id'       => Page::where('page_id', $this->pageId)->value('id'),
                'post_id'       => $this->postId,
                'status'        => isset($resp['error']) ? 'failed' : 'sent',
                'error_code'    => $resp['error']['code'] ?? null,
                'error_message' => $resp['error']['message'] ?? null,
                'sent_at'       => now(),
            ]
        );
    }
}
