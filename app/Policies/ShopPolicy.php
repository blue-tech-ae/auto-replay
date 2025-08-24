<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function view(User $u, Shop $s): bool
    {
        return $s->owner_id === $u->id || $s->users()->where('user_id', $u->id)->exists();
    }

    public function manage(User $u, Shop $s): bool
    {
        return $s->owner_id === $u->id;
    }
}

