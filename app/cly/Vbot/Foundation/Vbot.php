<?php
/**
 * Created by PhpStorm.
 * User: Cly
 * Date: 2016/12/9
 * Time: 21:22.
 */

namespace Cly\Vbot\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;

/**
 * Class Vbot.ShareFactory.
 *
 * @property \Cly\Vbot\Core\Server $server
 * @property \Cly\Vbot\Core\Swoole $swoole
 * @property \Cly\Vbot\Core\MessageHandler $messageHandler
 * @property \Cly\Vbot\Core\MessageFactory $messageFactory
 * @property \Cly\Vbot\Core\ShareFactory $shareFactory
 * @property \Cly\Vbot\Extension\MessageExtension $messageExtension
 * @property \Cly\Vbot\Message\Text $text
 * @property \Cly\Vbot\Core\Sync $sync
 * @property \Cly\Vbot\Core\ContactFactory $contactFactory
 * @property \Cly\Vbot\Foundation\ExceptionHandler $exception
 * @property \Cly\Vbot\Support\Log $log
 * @property \Cly\Vbot\Support\Log $messageLog
 * @property \Cly\Vbot\Support\Http $http
 * @property \Cly\Vbot\Api\ApiHandler $api
 * @property \Cly\Vbot\Console\QrCode $qrCode
 * @property \Cly\Vbot\Console\Console $console
 * @property \Cly\Vbot\Observers\Observer $observer
 * @property \Cly\Vbot\Observers\QrCodeObserver $qrCodeObserver
 * @property \Cly\Vbot\Observers\NeedActivateObserver $needActivateObserver
 * @property \Cly\Vbot\Observers\LoginSuccessObserver $loginSuccessObserver
 * @property \Cly\Vbot\Observers\ReLoginSuccessObserver $reLoginSuccessObserver
 * @property \Cly\Vbot\Observers\ExitObserver $exitObserver
 * @property \Cly\Vbot\Observers\FetchContactObserver $fetchContactObserver
 * @property \Cly\Vbot\Observers\BeforeMessageObserver $beforeMessageObserver
 * @property \Illuminate\Config\Repository $config
 * @property \Illuminate\Cache\Repository $cache
 * @property \Cly\Vbot\Contact\Myself $myself
 * @property \Cly\Vbot\Contact\Friends $friends
 * @property \Cly\Vbot\Contact\Contacts $contacts
 * @property \Cly\Vbot\Contact\Groups $groups
 * @property \Cly\Vbot\Contact\Members $members
 * @property \Cly\Vbot\Contact\Officials $officials
 * @property \Cly\Vbot\Contact\Specials $specials
 */
class Vbot extends Container
{
    protected static $instance;
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\LogServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\ExceptionServiceProvider::class,
        ServiceProviders\CacheServiceProvider::class,
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\ObserverServiceProvider::class,
        ServiceProviders\ConsoleServiceProvider::class,
        ServiceProviders\MessageServiceProvider::class,
        ServiceProviders\ContactServiceProvider::class,
        ServiceProviders\ApiServiceProvider::class,
        ServiceProviders\ExtensionServiceProvider::class,
    ];

    public function __construct(array $config)
    {
        $this->initializeConfig($config);

        (new Kernel($this))->bootstrap();

        static::$instance = $this;

        require_once __DIR__ . '/../Support/helpers.php';
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    private function initializeConfig(array $config)
    {
        $this->config = new Repository($config);
    }

    /**
     * Register providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    private function register(ServiceProviderInterface $instance)
    {
        $instance->register($this);
    }
}
