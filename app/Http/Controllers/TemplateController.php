<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PostTemplate;

class TemplateController extends Controller
{
    public function save(Request $request)
    {
        $page = Page::where('page_id', $request->page_id)->firstOrFail();

        return PostTemplate::updateOrCreate(
            ['page_id' => $page->id, 'post_id' => $request->post_id],
            [
                'private_reply_template' => $request->private_reply_template,
                'public_reply_template'  => $request->public_reply_template,
                'is_active'              => $request->boolean('is_active', true),
            ]
        );
    }
}
