<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PageSubscription;
use App\Models\MessageCounter;

class EnforceQuotas
{
    public static function canSendPrivateReply(int $pageDbId): bool
    {
        $sub = PageSubscription::where('page_id', $pageDbId)->whereNull('ends_at')->orderByDesc('starts_at')->first();
        if (!$sub) {
            return false;
        }

        $plan = $sub->plan;
        $today = now()->toDateString();
        $month = now()->format('Y-m');

        $counter = MessageCounter::firstOrCreate([
            'page_id' => $pageDbId,
            'day'     => $today,
            'month'   => $month,
        ]);

        if ($counter->count_day >= $plan->daily_private_replies) {
            return false;
        }
        if ($counter->count_month >= $plan->monthly_private_replies) {
            return false;
        }

        return true;
    }

    public static function incrementCounters(int $pageDbId): void
    {
        $today = now()->toDateString();
        $month = now()->format('Y-m');
        $counter = MessageCounter::firstOrCreate([
            'page_id' => $pageDbId,
            'day'     => $today,
            'month'   => $month,
        ]);
        $counter->increment('count_day');
        $counter->increment('count_month');
    }

    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
