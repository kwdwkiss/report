<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/7/9
 * Time: 下午5:22
 */

namespace Cly\Emulator;


use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class Pdd
{
    protected $client;

    public function __construct($cookie = '')
    {
        $cookie = $cookie ?: storage_path('test');
        $this->client = new Client([
            'cookies' => new FileCookieJar($cookie),
        ]);
    }


    public function personal()
    {
        $url = 'http://mobile.yangkeduo.com/personal.html';

        $response = $this->client->get($url);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    public function isLogin()
    {
        $html = $this->personal();

        if (!preg_match('/window\.rawData=\s+(.*);/', $html, $matches)) {
            throw new \Exception('text match error');
        }

        if ($matches[1] == 'null') {
            return false;
        }

        return true;
    }

    public function login()
    {
        $url = 'http://mobile.yangkeduo.com/login.html';

        $response = $this->client->get($url);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }
    }

    public function sms($mobile)
    {
        $url = 'http://apiv3.yangkeduo.com/mobile/code/request?pdduid=0';

        $str = '{"platform":"4","fingerprint":"{\"innerHeight\":902,\"innerWidth\":1000,\"devicePixelRatio\":1,\"availHeight\":999,\"availWidth\":1920,\"height\":1080,\"width\":1920,\"colorDepth\":24,\"locationHerf\":\"http://mobile.yangkeduo.com/login.html?from=http%3A%2F%2Fmobile.yangkeduo.com%2F&refer_page_name=index&refer_page_id=index_1530431990341_vtDADhtLnl&refer_page_sn=10002\",\"referer\":\"index\",\"timezoneOffset\":-480,\"navigator\":{\"appCodeName\":\"Mozilla\",\"appName\":\"Netscape\",\"hardwareConcurrency\":4,\"language\":\"zh-CN\",\"cookieEnabled\":true,\"platform\":\"MacIntel\",\"doNotTrack\":null,\"ua\":\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36\",\"vendor\":\"Google Inc.\",\"product\":\"Gecko\",\"productSub\":\"20030107\"}}","touchevent":"{\"mobileInputEditStartTime\":1530432377594,\"mobileInputKeyboardEvent\":\"0|0|0|861-1030-1260-1483-1652-1972-2107-2299-2643-2788-2940\",\"mobileInputEditFinishTime\":1530432381266,\"sendSmsButtonTouchPoint\":\"674,136\",\"sendSmsButtonClickTime\":1530432381411}","mobile":"18677303808","screen_token":"0aaeJzV0c9KG0EcB/DfzMREzblHpRLSm8nMzm6yWwglGDyUvsOyf2aza7O7EjetvXn0VHyGHlpIKagHwULaKB4EH8QX8AH8rfEwDX0BYRiYz/f3+83AEABgdbggXn1yoMZumvvJSBFRATZr6ESFXdqdbkzYHcQ/ZzpWELuof090XSm15Lmtc/WJJfrVoe61hXMMrkEPVp+DsuV6Doz4ACEJGNA1CqdQAfrqMwMWU5jhgU0VJg8UvteDPFSuPymKPEM//4q+QWHnn5cD+/0e/RuF46X6219ASQgWUcuDqksTLm+WWzfgJ4l+xEWx/7bdXtS1vnjZ8KMKJ3kryNP2KB8mWSsu0tG7aJynvbK2KftNYxfX/zqQ34xVhPfue0PlZl6qekkWqkNdk3BhrrAkN6VwHC5N4X4qBv1BXHzIRnrxQdYTnHMDYEqGL+ax6yTGz75nUDuCBkleWya3rcCLvED6vunY3OwIs2sg8qjj2wFskb1Nw+GWKQ3ewdjAbXtxpSGklJbzCHVf7YQ="}';
        $json = json_decode($str, true);
        $json['mobile'] = $mobile;

        $response = $this->client->post($url, [
            'json' => $json
        ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }

    public function doLogin($mobile, $code)
    {
        $url = 'http://apiv3.yangkeduo.com/login?pdduid=0';

        $response = $this->client->post($url, [
            'json' => [
                'mobile' => $mobile,
                'code' => $code,
            ]
        ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }
}