<?php

namespace App\Policies;

use App\Models\Page;
use App\Models\User;

class PagePolicy
{
    public function view(User $u, Page $p): bool
    {
        return $p->shop && ($p->shop->owner_id === $u->id || $p->shop->users()->where('user_id', $u->id)->exists());
    }

    public function manage(User $u, Page $p): bool
    {
        return $p->shop && $p->shop->owner_id === $u->id;
    }
}

