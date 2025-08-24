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
        $pageDbId = Page::where('page_id', $this->pageId)->value('id');

        if (!\App\Http\Middleware\EnforceQuotas::canSendPrivateReply($pageDbId)) {
            CommentLog::updateOrCreate(
                ['comment_id' => $this->commentId],
                [
                    'page_id' => $pageDbId,
                    'post_id' => $this->postId,
                    'status'  => 'failed',
                    'error_code' => 429,
                    'error_message' => 'Quota exceeded',
                    'sent_at' => now(),
                ]
            );
            return;
        }

        $postUrl = "https://facebook.com/{$this->postId}";
        $msg = strtr($this->template, ['{post_url}' => $postUrl]);

        $resp = $graph->privateReply($this->commentId, $msg, $this->pageAccessToken);

        $statusFail = isset($resp['error']);
        CommentLog::updateOrCreate(
            ['comment_id' => $this->commentId],
            [
                'page_id'       => $pageDbId,
                'post_id'       => $this->postId,
                'status'        => $statusFail ? 'failed' : 'sent',
                'error_code'    => $resp['error']['code'] ?? null,
                'error_message' => $resp['error']['message'] ?? null,
                'sent_at'       => now(),
            ]
        );

        if (!$statusFail) {
            \App\Http\Middleware\EnforceQuotas::incrementCounters($pageDbId);
        }
    }
}
