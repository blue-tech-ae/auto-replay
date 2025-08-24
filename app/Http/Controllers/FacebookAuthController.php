<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Models\Page;

class FacebookAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'pages_manage_metadata',
                'pages_read_engagement',
                'pages_manage_engagement',
                'pages_messaging',
                'pages_show_list',
            ])->redirect();
    }

    public function callback()
    {
        $fbUser = Socialite::driver('facebook')->user();
        $userAccessToken = $fbUser->token;

        $resp = Http::get('https://graph.facebook.com/' . env('FB_GRAPH_VERSION') . '/me/accounts', [
            'access_token' => $userAccessToken,
        ])->throw()->json();

        foreach (($resp['data'] ?? []) as $p) {
            $page = Page::updateOrCreate(
                ['page_id' => $p['id']],
                ['name' => $p['name'] ?? null, 'access_token' => $p['access_token']]
            );

            Http::asForm()->post('https://graph.facebook.com/' . env('FB_GRAPH_VERSION') . "/{$page->page_id}/subscribed_apps", [
                'subscribed_fields' => 'feed,messages,message_echoes',
                'access_token'      => $page->access_token,
            ])->throw();
        }

        return redirect('/')->with('ok', 'Facebook connected and webhooks subscribed.');
    }
}
