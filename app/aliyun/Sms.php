<?php
/**
 * Created by PhpStorm.
 * User: kwdwkiss
 * Date: 2018/3/21
 * Time: 下午3:28
 */

namespace Aliyun;

require_once __DIR__ . '/api_sdk/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class Sms
{
    protected $accessKeyId;

    protected $accessKeySecret;

    protected $signName;

    protected $templateCode;

    protected $acsClient;

    public function __construct($accessKeyId, $accessKeySecret, $signName, $templateCode)
    {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $this->accessKeyId = $accessKeyId; // AccessKeyId

        $this->accessKeySecret = $accessKeySecret; // AccessKeySecret

        $this->signName = $signName;

        $this->templateCode = $templateCode;

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        //初始化acsClient,暂不支持region化
        $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }

    public function send($mobile, $code, $outId = null, $extendCode = null)
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($this->signName);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($this->templateCode);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam(json_encode(array(  // 短信模板中字段的值
            "code" => $code,
            "product" => "dsd"
        ), JSON_UNESCAPED_UNICODE));

        // 可选，设置流水号
        if ($outId) {
            $request->setOutId($outId);
        }

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        if ($extendCode) {
            $request->setSmsUpExtendCode($extendCode);
        }

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);

        $biz_id = '';
        $success = false;
        if ($acsResponse->Code == 'OK') {
            $biz_id = $acsResponse->BizId;
            $success = true;
        }

        return [
            'success' => $success,
            'message' => $acsResponse->Message,
            'code' => $acsResponse->Code,
            'request_id' => $acsResponse->RequestId,
            'biz_id' => $biz_id,
        ];
    }

    public function sendBatch($mobiles = [], $signNames = [], $params = [])
    {
        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendBatchSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填:待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $request->setPhoneNumberJson(json_encode($mobiles, JSON_UNESCAPED_UNICODE));

        // 必填:短信签名-支持不同的号码发送不同的短信签名
        $request->setSignNameJson(json_encode($signNames, JSON_UNESCAPED_UNICODE));

        // 必填:短信模板-可在短信控制台中找到
        $request->setTemplateCode($this->templateCode);

        // 必填:模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $request->setTemplateParamJson(json_encode($params, JSON_UNESCAPED_UNICODE));

        // 可选-上行短信扩展码(扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段)
        // $request->setSmsUpExtendCodeJson("[\"90997\",\"90998\"]");

        // 发起访问请求
        $acsResponse = $this->acsClient->getAcsResponse($request);

        return $acsResponse;
    }

    public function query($mobile, $date, $currentPage = 1, $pageSize = 10)
    {
        // 初始化QuerySendDetailsRequest实例用于设置短信查询的参数
        $request = new QuerySendDetailsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，短信接收号码
        $request->setPhoneNumber($mobile);

        // 必填，短信发送日期，格式Ymd，支持近30天记录查询
        $request->setSendDate($date);

        // 必填，分页大小
        $request->setPageSize($pageSize);

        // 必填，当前页码
        $request->setCurrentPage($currentPage);

        // 选填，短信发送流水号
        //$request->setBizId("yourBizId");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }
}