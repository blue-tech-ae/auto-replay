<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkTemplateRequest;
use App\Http\Requests\BulkToggleRequest;
use App\Models\CommentLog;
use App\Models\Page;
use App\Models\PageSubscription;
use App\Models\Post;
use App\Models\PostTemplate;
use App\Services\GraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageManagementController extends Controller
{
    /**
     * @OA\Delete(
     *   path="/api/pages/{page_id}",
     *   tags={"Pages"},
     *   summary="Delete a page locally (not on Facebook)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Deleted"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function destroy($page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $this->authorize('manage', $page);

        DB::transaction(function () use ($page) {
            PostTemplate::whereIn('post_id', Post::where('page_id', $page->id)->pluck('post_id'))->delete();
            CommentLog::where('page_id', $page->id)->delete();
            Post::where('page_id', $page->id)->delete();
            $page->delete();
        });

        return response()->json(['deleted' => true]);
    }

    /**
     * @OA\Post(
     *   path="/api/pages/{page_id}/resubscribe",
     *   tags={"Pages"},
     *   summary="Re-subscribe page webhooks (feed,messages)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Resubscribed"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function resubscribe($page_id, GraphService $graph)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $this->authorize('manage', $page);

        $graph->subscribePageWebhooks($page->page_id, $page->access_token);
        return response()->json(['resubscribed' => true]);
    }

    /**
     * @OA\Patch(
     *   path="/api/pages/{page_id}/posts/bulk-toggle",
     *   tags={"Posts"},
     *   summary="Bulk enable/disable posts for a page",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="post_ids", type="array", @OA\Items(type="string")),
     *       @OA\Property(property="enable", type="boolean")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=422, description="Plan limit reached/validation"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function bulkToggle(BulkToggleRequest $request, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $this->authorize('manage', $page);

        $ids    = $request->validated()['post_ids'];
        $enable = $request->boolean('enable');

        if ($enable) {
            $sub = PageSubscription::where('page_id', $page->id)->whereNull('ends_at')->with('plan')->first();
            if ($sub) {
                $activeCount = Post::where('page_id', $page->id)->where('is_active', true)->count();
                $remaining   = max(0, $sub->plan->max_active_posts - $activeCount);
                if ($remaining <= 0) {
                    return response()->json(['error' => 'Active posts limit reached'], 422);
                }
                $ids = array_slice($ids, 0, $remaining);
            }
        }

        Post::where('page_id', $page->id)->whereIn('post_id', $ids)->update(['is_active' => $enable]);
        return response()->json(['updated' => count($ids), 'enable' => $enable]);
    }

    /**
     * @OA\Post(
     *   path="/api/pages/{page_id}/posts/bulk-template",
     *   tags={"Templates"},
     *   summary="Bulk apply templates to many posts",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="post_ids", type="array", @OA\Items(type="string")),
     *       @OA\Property(property="private_reply_template", type="string"),
     *       @OA\Property(property="public_reply_template", type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Updated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function bulkTemplate(BulkTemplateRequest $request, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $this->authorize('manage', $page);

        $data  = $request->validated();
        $count = 0;

        foreach ($data['post_ids'] as $pid) {
            PostTemplate::updateOrCreate(
                ['page_id' => $page->id, 'post_id' => $pid],
                [
                    'private_reply_template' => $data['private_reply_template'] ?? '',
                    'public_reply_template'  => $data['public_reply_template'] ?? null,
                    'is_active'              => true,
                ]
            );
            $count++;
        }

        return response()->json(['updated' => $count]);
    }

    /**
     * @OA\Get(
     *   path="/api/pages/{page_id}/logs",
     *   tags={"Logs"},
     *   summary="List comment delivery logs with filters",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"sent","failed","skipped"})),
     *   @OA\Parameter(name="q", in="query", @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function logs(Request $request, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $this->authorize('view', $page);

        $q = CommentLog::where('page_id', $page->id);

        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        if ($term = $request->get('q')) {
            $q->where(function ($w) use ($term) {
                $w->where('comment_id', 'like', "%{$term}%")
                    ->orWhere('post_id', 'like', "%{$term}%")
                    ->orWhere('error_message', 'like', "%{$term}%");
            });
        }

        return response()->json($q->orderByDesc('id')->paginate(30));
    }
}

