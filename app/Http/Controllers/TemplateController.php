<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\PostTemplate;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Templates")
 */
class TemplateController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/pages/{page_id}/posts/{post_id}/template",
     *  tags={"Templates"}, summary="Get template for a post",
     *  security={{"bearerAuth":{}}},
     *  @OA\Parameter(name="page_id", in="path", required=true, @OA\Schema(type="string")),
     *  @OA\Parameter(name="post_id", in="path", required=true, @OA\Schema(type="string")),
     *  @OA\Response(response=200, description="OK")
     * )
     */
    public function show($page_id, $post_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $post = Post::where('page_id', $page->id)->where('post_id', $post_id)->firstOrFail();
        $tpl  = PostTemplate::where('page_id', $page->id)->where('post_id', $post->post_id)->first();
        return response()->json($tpl);
    }

    /**
     * @OA\Post(
     *  path="/api/pages/{page_id}/posts/{post_id}/template",
     *  tags={"Templates"}, summary="Create template",
     *  security={{"bearerAuth":{}}},
     *  @OA\RequestBody(required=true, @OA\JsonContent(
     *    @OA\Property(property="private_reply_template", type="string"),
     *    @OA\Property(property="public_reply_template", type="string"),
     *    @OA\Property(property="is_active", type="boolean")
     *  )),
     *  @OA\Response(response=200, description="OK")
     * )
     */
    public function store(Request $r, $page_id, $post_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $post = Post::where('page_id', $page->id)->where('post_id', $post_id)->firstOrFail();

        $tpl = PostTemplate::updateOrCreate(
            ['page_id' => $page->id, 'post_id' => $post->post_id],
            [
                'private_reply_template' => $r->string('private_reply_template'),
                'public_reply_template'  => $r->input('public_reply_template'),
                'is_active'              => $r->boolean('is_active', true),
            ]
        );
        return response()->json($tpl);
    }

    /**
     * @OA\Put(
     *  path="/api/pages/{page_id}/posts/{post_id}/template",
     *  tags={"Templates"}, summary="Update template",
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(response=200, description="OK")
     * )
     */
    public function update(Request $r, $page_id, $post_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $tpl = PostTemplate::where('page_id', $page->id)->where('post_id', $post_id)->firstOrFail();

        $tpl->private_reply_template = $r->input('private_reply_template', $tpl->private_reply_template);
        $tpl->public_reply_template  = $r->input('public_reply_template',  $tpl->public_reply_template);
        $tpl->is_active              = $r->has('is_active') ? $r->boolean('is_active') : $tpl->is_active;
        $tpl->save();

        return response()->json($tpl);
    }

    /**
     * @OA\Delete(
     *  path="/api/pages/{page_id}/posts/{post_id}/template",
     *  tags={"Templates"}, summary="Delete template",
     *  security={{"bearerAuth":{}}},
     *  @OA\Response(response=200, description="OK")
     * )
     */
    public function destroy($page_id, $post_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        PostTemplate::where('page_id', $page->id)->where('post_id', $post_id)->delete();
        return response()->json(['deleted' => true]);
    }
}
