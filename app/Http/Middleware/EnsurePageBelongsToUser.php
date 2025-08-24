<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePageBelongsToUser
{
    public function handle(Request $request, Closure $next)
    {
        $pageId = $request->route('page_id');
        $page = \App\Models\Page::where('page_id', $pageId)->first();
        abort_if(!$page, 404);

        $request->user()->can('view', $page) ?: abort(403);

        $request->attributes->set('pageDbId', $page->id);

        return $next($request);
    }
}

