<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\MessageCounter;
use App\Models\Page;
use App\Models\PageSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(SubscriptionRequest $r, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        PageSubscription::where('page_id', $page->id)->whereNull('ends_at')->update(['ends_at' => now()->toDateString()]);
        $sub = PageSubscription::create([
            'page_id' => $page->id,
            'plan_id' => $r->integer('plan_id'),
            'starts_at' => $r->date('starts_at'),
            'ends_at' => $r->input('ends_at'),
        ]);
        return $sub->load('plan');
    }

    public function unsubscribe(Request $r, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $sub = PageSubscription::where('page_id', $page->id)->whereNull('ends_at')->first();
        if (!$sub) {
            return response()->json(['message' => 'No active subscription'], 404);
        }
        $sub->update(['ends_at' => now()->toDateString()]);
        return response()->json(['unsubscribed' => true]);
    }

    public function current(Request $r, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $sub = PageSubscription::where('page_id', $page->id)->whereNull('ends_at')->with('plan')->first();
        return $sub ?: response()->json(['message' => 'No active subscription'], 404);
    }

    public function quota(Request $r, $page_id)
    {
        $page = Page::where('page_id', $page_id)->firstOrFail();
        $sub = PageSubscription::where('page_id', $page->id)->whereNull('ends_at')->with('plan')->first();
        if (!$sub) {
            return response()->json(['message' => 'No active subscription'], 404);
        }

        $today = now()->toDateString();
        $month = now()->format('Y-m');
        $ctr = MessageCounter::firstOrCreate([
            'page_id' => $page->id, 'day' => $today, 'month' => $month,
        ]);

        return [
            'plan' => $sub->plan->only(['name', 'daily_private_replies', 'monthly_private_replies', 'max_active_posts']),
            'usage' => [
                'today' => $ctr->count_day,
                'month' => $ctr->count_month,
            ],
            'limits' => [
                'today' => $sub->plan->daily_private_replies,
                'month' => $sub->plan->monthly_private_replies,
            ],
        ];
    }
}

