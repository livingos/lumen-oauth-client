<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function oauth(){

      $query = http_build_query([
         'client_id'     => 6,
         'redirect_uri'  => 'http://lumen.app/callback',
         'response_type' => 'code',
         'scope'         => '',
       ]);

      return redirect('http://laravel53.app/oauth/authorize?' . $query);

    }

    public function callback(Request $request){

        $http = new Client;
        $response = $http->post('http://laravel53.app/oauth/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => '6',
                'client_secret' => '6HZTAgyG0BrzPHCZF8Ib27hKpfjCdq0feN3NZmgq',
                'redirect_uri'  => 'http://lumen.app/callback',
                'code'          => $request->code
            ],
        ]);

        $tokens = json_decode((string)$response->getBody(), true);

        //put in cache for now for trying out api routes below
        Cache::put('access_token', $tokens['access_token'], 60);
        Cache::put('refresh_token', $tokens['refresh_token'], 60);

        return $tokens;

    }

    public function user()
    {

        // just for now we are saving the token in cache
        $token = Cache::get('access_token');
        $http = new Client;
        $response = $http->get('http://laravel53.app/api/user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accepts'       => 'application/json',
            ]
        ]);


        return $response->getBody();
    }

    public function refresh()
    {

        $http = new Client;

        $response = $http->post('http://laravel53.app/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => Cache::get('refresh_token'),
                'client_id' => '6',
                'client_secret' => '6HZTAgyG0BrzPHCZF8Ib27hKpfjCdq0feN3NZmgq',
                'scope' => '',
            ],
        ]);

        $tokens = json_decode((string)$response->getBody(), true);

        //put in cache for now for trying out api routes below
        Cache::put('access_token', $tokens['access_token'], 60);
        Cache::put('refresh_token', $tokens['refresh_token'], 60);

        return $tokens;
    }
}
