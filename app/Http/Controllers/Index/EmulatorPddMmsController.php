<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/5/31
 * Time: 下午11:41
 */

namespace App\Http\Controllers\Index;


use App\Exceptions\JsonException;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Exception\RequestException;

class EmulatorPddMmsController extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
//        $jar = new FileCookieJar(storage_path('test'));
//        $this->client = new Client([
//            'cookies' => $jar,
//        ]);
    }

    public function login()
    {
        $url = 'https://mms.pinduoduo.com/Pdd.html';

        try {
            $this->client->get($url);
            return [];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }

    public function doLogin()
    {
        $url = 'https://mms.pinduoduo.com/auth';

        $username = request('username');
        $password = request('password');
        $authCode = request('authCode');
        $token = request('token');
        $verificationCode = request('verificationCode');
        $json = [
            'username' => $username,
            'password' => $password,
            'authCode' => $authCode,
            'token' => $token
        ];
        if ($verificationCode) {
            $json['verificationCode'] = $verificationCode;
        }

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

    public function captcha()
    {
        $url = 'https://mms.pinduoduo.com/earth/api/merchant/getCaptchaCode';

        try {
            $res = $this->client->get($url);
            $data = json_decode($res->getBody()->getContents(), true);
            return ['data' => $data];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }

    public function sms()
    {
        $url = 'https://mms.pinduoduo.com/earth/api/user/getLoginVerificationCode';

        $username = request('username');

        try {
            $res = $this->client->post($url, [
                'json' => [
                    'username' => $username
                ]
            ]);
            $data = json_decode($res->getBody()->getContents(), true);
            return ['data' => $data];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }

    protected function risk()
    {

    }
}