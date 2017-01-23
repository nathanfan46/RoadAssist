<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TireposController extends Controller
{
	protected $base_uri = 'http://ec2-52-40-100-7.us-west-2.compute.amazonaws.com/tirepos/index.php/';
    //
    public function proxy(Request $request)
    {
        $client = new Client();
        $method = $request->method();
        // $auth = $request->header('AUTH');
        // $apikey = $request->header('APIKEY');
        // $contenttype = $request->header('Content-Type');

        $auth = 'f4a281c647c03cc11381d22a4d2a14ef';
        $apikey = 'testkey';
        $contenttype = 'application/json';

        $url = $request->header('X-Proxy-Url');
        $input = $request->all();

        if(!$url) {
        	return array('Status' => false, 'Error' => 'proxy url is missing');
        }

        $args = array();

        $args['headers'] = array(
        	'Content-Type' => $contenttype,
        	'AUTH' => $auth,
        	'APIKEY' => $apikey
        );

        if($request->isMethod('post')) {
        	// echo json_encode($input);
        	$args['body'] = json_encode($input);
        }

        try {
		    $response = $client->request($method, $this->base_uri.$url, $args);
		    return json_decode($response->getBody(), TRUE);
		} catch (RequestException $exception) {
		    $responseBody = $exception->getResponse()->getBody(true);
		    return json_decode($responseBody, TRUE);
		} catch (\Exception $exception) {
		    // $responseBody = $exception->getResponse()->getBody(true);
		    // return json_decode($responseBody, TRUE);
		    return 'unknown exception';
		}
        
        
        // return $res;

        // $res = $client->request('POST', $url, [
        //     'form_params' => [
        //         'client_id' => 'test_id',
        //         'secret' => 'test_secret',
        //     ]
        // ]);
        // echo $res->getStatusCode();
        // // "200"
        // echo $res->getHeader('content-type');
        // // 'application/json; charset=utf8'
        // echo $res->getBody();
        // {"type":"User"...'
    }
}
