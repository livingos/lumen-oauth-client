<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ErudusAPIController extends Controller
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

        $http = new Client;
        $response = $http->post('http://erudus-one.app/api/access_token', [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => '1248c32d0ecb4d0fb3a3d77ffd6e96f9',
                'client_secret' => '960e03ec9ae34f2887e94c3cfd57d376',
                'scope'          => 'PUBLIC'
            ],
        ]);

        $tokens = json_decode((string)$response->getBody(), true);

        //put in cache for now for trying out api routes below
        Cache::put('erudus_token', $tokens['access_token'], 60);


        return $tokens;

    }


    public function product($id)
    {

        // just for now we are saving the token in cache
        $token = Cache::get('erudus_token');
        $http = new Client;
        $response = $http->get('http://erudus-one.app/api/public/v1/products/'.$id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'       => 'application/json',
            ]
        ]);


        return $response->getBody();
    }


    public function pdf($id)
    {

        // just for now we are saving the token in cache
        $token = Cache::get('erudus_token');
        $http = new Client;
        $response = $http->get('http://erudus-one.app/api/public/v1/products/'.$id.'/pdf', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'       => 'application/pdf',
            ],
            'sink' => storage_path().'/app/'.$id.'.pdf'
        ]);

        dd($response);

        $content = Storage::disk('local')->get($id.'.pdf' );

        return response($content, 200)
        ->header('Content-Type', 'application/pdf');

    }
}
