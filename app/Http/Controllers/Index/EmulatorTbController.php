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
use Illuminate\Http\Response;

class EmulatorTbController extends Controller
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

    public function index()
    {
        return view('emulator_tb/index');
    }

    public function member_request_nick_check()
    {
        $url = 'https://login.taobao.com/member/request_nick_check.do?_input_charset=utf-8';

        $params = request()->all();

        $response = $this->client->post($url, ['form_params' => $params]);

        return new Response($response->getBody()->getContents());
    }

    public function member_login()
    {
        $url = 'https://login.taobao.com/member/login.jhtml?redirectURL=https%3A%2F%2Fwww.taobao.com%2F';

        $params = request()->all();

        $response = $this->client->post($url, ['form_params' => $params]);

        return new Response($response->getBody()->getContents());
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
        $str = '{"platform":"4","fingerprint":"{\"innerHeight\":902,\"innerWidth\":1000,\"devicePixelRatio\":1,\"availHeight\":999,\"availWidth\":1920,\"height\":1080,\"width\":1920,\"colorDepth\":24,\"locationHerf\":\"http://mobile.yangkeduo.com/login.html?from=http%3A%2F%2Fmobile.yangkeduo.com%2F&refer_page_name=index&refer_page_id=index_1530431990341_vtDADhtLnl&refer_page_sn=10002\",\"referer\":\"index\",\"timezoneOffset\":-480,\"navigator\":{\"appCodeName\":\"Mozilla\",\"appName\":\"Netscape\",\"hardwareConcurrency\":4,\"language\":\"zh-CN\",\"cookieEnabled\":true,\"platform\":\"MacIntel\",\"doNotTrack\":null,\"ua\":\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36\",\"vendor\":\"Google Inc.\",\"product\":\"Gecko\",\"productSub\":\"20030107\"}}","touchevent":"{\"mobileInputEditStartTime\":1530432377594,\"mobileInputKeyboardEvent\":\"0|0|0|861-1030-1260-1483-1652-1972-2107-2299-2643-2788-2940\",\"mobileInputEditFinishTime\":1530432381266,\"sendSmsButtonTouchPoint\":\"674,136\",\"sendSmsButtonClickTime\":1530432381411}","mobile":"18677303808","screen_token":"0aaeJzV0c9KG0EcB/DfzMREzblHpRLSm8nMzm6yWwglGDyUvsOyf2aza7O7EjetvXn0VHyGHlpIKagHwULaKB4EH8QX8AH8rfEwDX0BYRiYz/f3+83AEABgdbggXn1yoMZumvvJSBFRATZr6ESFXdqdbkzYHcQ/ZzpWELuof090XSm15Lmtc/WJJfrVoe61hXMMrkEPVp+DsuV6Doz4ACEJGNA1CqdQAfrqMwMWU5jhgU0VJg8UvteDPFSuPymKPEM//4q+QWHnn5cD+/0e/RuF46X6219ASQgWUcuDqksTLm+WWzfgJ4l+xEWx/7bdXtS1vnjZ8KMKJ3kryNP2KB8mWSsu0tG7aJynvbK2KftNYxfX/zqQ34xVhPfue0PlZl6qekkWqkNdk3BhrrAkN6VwHC5N4X4qBv1BXHzIRnrxQdYTnHMDYEqGL+ax6yTGz75nUDuCBkleWya3rcCLvED6vunY3OwIs2sg8qjj2wFskb1Nw+GWKQ3ewdjAbXtxpSGklJbzCHVf7YQ="}';
        $json = json_decode($str, true);
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

    public function afterSales()
    {
        $url = 'http://apiv3.yangkeduo.com/after_sales/list?range=all&offset=0&size=10&pdduid=3498107458';

        $mobile = request('mobile');
        $code = request('code');

        try {
            $res = $this->client->get($url);
            $data = json_decode($res->getBody()->getContents(), true);
            return ['data' => $data];

        } catch (RequestException $e) {
            throw new JsonException($e->getMessage());
        }
    }
}