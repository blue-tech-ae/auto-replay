<?php

namespace App\Jobs;

use App\Services\GraphService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPublicReplyJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public string $pageAccessToken,
        public string $commentId,
        public string $template
    ) {}

    public function handle(GraphService $graph): void
    {
        $graph->publicComment($this->commentId, $this->template, $this->pageAccessToken);
    }
}
