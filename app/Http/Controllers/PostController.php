<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\PostTemplate;
use App\Models\PageSubscription;
use App\Services\GraphService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Posts")
 */
class PostController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/pages/{page_id}/posts/import",
     *   tags={"Posts"},
     *   summary="Import latest page posts from Facebook",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Imported")
     * )
     */
    public function import($page_id, GraphService $graph)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $data = $graph->getPagePosts($page->page_id, $page->access_token, 50);

        foreach ($data as $row) {
            $postId = $row['id'];
            Post::updateOrCreate(
                ['post_id' => $postId],
                [
                    'page_id'       => $page->id,
                    'permalink_url' => $row['permalink_url'] ?? null,
                    'title'         => mb_substr($row['message'] ?? '', 0, 120),
                    'is_active'     => true,
                ]
            );
        }
        return response()->json(['imported' => count($data)]);
    }

    /**
     * @OA\Get(
     *   path="/api/pages/{page_id}/posts",
     *   tags={"Posts"},
     *   summary="List posts",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function index($page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $posts = Post::with('template')->where('page_id', $page->id)->orderByDesc('id')->paginate(20);
        return response()->json($posts);
    }

    /**
     * @OA\Patch(
     *   path="/api/pages/{page_id}/posts/{post_id}/toggle",
     *   tags={"Posts"},
     *   summary="Toggle post active state",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Parameter(name="post_id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK")
     * )
     */
    public function toggle($page_id, $post_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $post = Post::where('page_id', $page->id)->where('post_id', $post_id)->firstOrFail();

        $post->is_active = !$post->is_active;

        if ($post->is_active) {
            $sub = PageSubscription::where('page_id', $page->id)
                ->whereNull('ends_at')
                ->orderByDesc('starts_at')->first();
            if ($sub) {
                $activeCount = Post::where('page_id', $page->id)->where('is_active', true)->count();
                if ($activeCount >= $sub->plan->max_active_posts) {
                    return response()->json(['error' => 'Active posts limit reached for current plan'], 422);
                }
            }
        }

        $post->save();
        return response()->json(['is_active' => $post->is_active]);
    }
}
