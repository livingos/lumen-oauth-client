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
                'client_id'     => 'ff85c26f0d9242148aefb3402bd1be6a',
                'client_secret' => '2e15dcba19c643aea2db510fb2a51e2d',
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


    public function pdf(Request $request, $id)
    {

        // just for now we are saving the token in cache
        $token = Cache::get('erudus_token');
        $http = new Client;
        $response = $http->get('http://erudus-one.app/api/public/v1/products/'.$id.'/pdf?type='.$request->get('type','full'), [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'       => 'application/pdf',
            ],
            'sink' => storage_path().'/app/'.$request->get('type','full').'-'.$id.'.pdf'
        ]);

        $content = Storage::disk('local')->get($request->get('type','full').'-'.$id.'.pdf' );

        return response($content, 200)
        ->header('Content-Type', 'application/pdf');

    }
}
