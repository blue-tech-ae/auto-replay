<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PostTemplate;
use App\Models\CommentLog;
use App\Jobs\SendPrivateReplyJob;
use App\Jobs\SendPublicReplyJob;

class FacebookWebhookController extends Controller
{
    public function verify(Request $request)
    {
        if ($request->get('hub.verify_token') === env('FB_VERIFY_TOKEN')) {
            return response($request->get('hub.challenge'), 200);
        }
        return response('Verification token mismatch', 403);
    }

    public function handle(Request $request)
    {
        $payload = $request->all();

        if (($payload['object'] ?? '') === 'page') {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach (($entry['changes'] ?? []) as $change) {
                    if (($change['field'] ?? '') === 'feed') {
                        $value = $change['value'] ?? [];
                        if (($value['item'] ?? '') === 'comment' && ($value['verb'] ?? '') === 'add') {
                            $commentId = $value['comment_id'] ?? null;
                            $postId    = $value['post_id'] ?? null;
                            $pageId    = $entry['id'] ?? null;

                            if ($commentId && $postId && $pageId) {
                                if (!CommentLog::where('comment_id', $commentId)->exists()) {
                                    $template = PostTemplate::whereHas('page', fn($q) => $q->where('page_id', $pageId))
                                        ->where('post_id', $postId)
                                        ->where('is_active', true)
                                        ->first();

                                    if ($template) {
                                        $page = Page::where('page_id', $pageId)->first();
                                        if ($page) {
                                            dispatch(new SendPrivateReplyJob(
                                                $page->page_id,
                                                $page->access_token,
                                                $commentId,
                                                $postId,
                                                $template->private_reply_template
                                            ));

                                            if ($template->public_reply_template) {
                                                dispatch(new SendPublicReplyJob(
                                                    $page->access_token,
                                                    $commentId,
                                                    $template->public_reply_template
                                                ));
                                            }
                                        }
                                    } else {
                                        CommentLog::create([
                                            'comment_id' => $commentId,
                                            'page_id'    => Page::where('page_id', $pageId)->value('id'),
                                            'post_id'    => $postId,
                                            'status'     => 'skipped',
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                foreach (($entry['messaging'] ?? []) as $msg) {
                    // Optional: store PSID for Send API usage
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
