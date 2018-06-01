<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/31
 * Time: 上午11:07
 */

namespace App\Http\Controllers\Index;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;

class EmulatorPddController extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $jar = new FileCookieJar(storage_path('test'));
        $this->client = new Client([
            'cookies' => $jar,
        ]);
    }

    public function login()
    {
        $url = 'http://mobile.yangkeduo.com/login.html';

        try {
            $this->client->get($url);
            return [];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }

    public function sms()
    {
        $url = 'http://apiv3.yangkeduo.com/mobile/code/request?pdduid=0';

        $mobile = request('mobile');
        $str = '{"platform":"4","fingerprint":"{\"innerHeight\":904,\"innerWidth\":1050,\"devicePixelRatio\":1,\"availHeight\":1001,\"availWidth\":1920,\"height\":1080,\"width\":1920,\"colorDepth\":24,\"locationHerf\":\"http://mobile.yangkeduo.com/login.html\",\"timezoneOffset\":-480,\"navigator\":{\"appCodeName\":\"Mozilla\",\"appName\":\"Netscape\",\"hardwareConcurrency\":4,\"language\":\"zh-CN\",\"cookieEnabled\":true,\"platform\":\"MacIntel\",\"doNotTrack\":null,\"ua\":\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36\",\"vendor\":\"Google Inc.\",\"product\":\"Gecko\",\"productSub\":\"20030107\"}}","touchevent":"{\"mobileInputEditStartTime\":1527766086618,\"mobileInputKeyboardEvent\":\"0|0|0|796-950-1152-1376-1528-1791-1960-2145-2521-2697-2865\",\"mobileInputEditFinishTime\":1527766090584,\"sendSmsButtonTouchPoint\":\"755,161\",\"sendSmsButtonClickTime\":1527766090698,\"smsInputEditStartTime\":1527764918419,\"smsInputKeyboardEvent\":\"0|0|0|\",\"smsInputEditFinishTime\":1527764918426}","mobile":"18677303808","screen_token":"0aaeJxd0LtOAkEUBuB/ZlAMGCstTUhMCPECu8OOu9qZTXwCC01MyN4EFFijS4yN0cbWzkYbY2urhYWd76EVeEFNLGg9gMVCtXO+/+xcDgPAbTwwJ908DA5K9dCt1gKmJ7Dx3ooT1y2yj7O4Cd1aJvxcj2OC0CTtZOM61tM+P8V5vM9F8q/nuCcHrlHwHcaDif+g98tPFoK5wDnzBPgUxx0E0m+0FhzXtM4dCbBtjttUtbHfjEpe6AcCiwcC4pKSRyRgz4xTf4fjKt1LS24zisIGbf46S57j2Bx6Gzbaa+S/HDcj/d17cOYjYsHQYQvdWJnA5FZ7qDaKGKrt6ZORfV8uRi7Qeh492cY828lWomh/tVAY9OWPnUZ5L/CbYd4L64VaWK428pWoXgONrAykWIWGQKNKnmKOVTOuq3mGMlwl6aObnhFYvqZblu8UDccPisiw3VlLasow1YqUypRKLulKmuaypqTUlfEH1Pmo7g=="}';
        $json = [];//json_decode($str, true);
        $json['mobile'] = $mobile;
        try {
            $res = $this->client->post($url, [
                'json' => $json
            ]);
            $data = json_decode($res->getBody()->getContents(), true);
            return ['data' => $data];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }

    public function doLogin()
    {
        $url = 'http://apiv3.yangkeduo.com/login?pdduid=0';

        $mobile = request('mobile');
        $code = request('code');

        try {
            $res = $this->client->post($url, [
                'json' => [
                    'mobile' => $mobile,
                    'code' => $code,
                ]
            ]);
            $data = json_decode($res->getBody()->getContents(), true);
            return ['data' => $data];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }
}