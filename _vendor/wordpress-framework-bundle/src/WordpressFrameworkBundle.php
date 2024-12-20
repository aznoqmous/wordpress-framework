<?php

namespace Addictic\WordpressFrameworkBundle;

use Addictic\WordpressFrameworkBundle\Attributes\PostTypeManager;
use Env\Env;
use Addictic\WordpressFrameworkBundle\Helpers\PathHelper;
use ReflectionClass;
use Roots\WPConfig\Config;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddAnnotationsCachedReaderPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddDebugLogProcessorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AddExpressionLanguageProvidersPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\AssetsContextPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ContainerBuilderDebugDumpPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\DataCollectorTranslatorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ErrorLoggerCompilerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\LoggingTranslatorPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\ProfilerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\RemoveUnusedSessionMarshallingHandlerPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\SessionPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerRealRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\TestServiceContainerWeakRefPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\UnusedTagsPass;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\Compiler\WorkflowGuardListenerPass;
use Symfony\Component\Cache\DependencyInjection\CacheCollectorPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolClearerPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPass;
use Symfony\Component\Cache\DependencyInjection\CachePoolPrunerPass;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\RegisterReverseContainerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\HttpClient\DependencyInjection\HttpClientPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\FragmentRendererPass;
use Symfony\Component\HttpKernel\DependencyInjection\LoggerPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\ResettableServicePass;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\DependencyInjection\RoutingResolverPass;
use function Env\env;

class WordpressFrameworkBundle extends Bundle
{
    private $root_dir;
    private $public_dir;
    private $log_dir;
    private $router;

    private static $booted = false;

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function boot()
    {
        if (self::$booted) return;
        self::$booted = true;

        Env::$options = Env::USE_ENV_ARRAY;

        $kernel = $this->container->get('kernel');

        $this->log_dir = $kernel->getLogDir();
        $this->root_dir = $kernel->getProjectDir();
        $this->router = $this->container->get('router');

        $this->public_dir = $this->root_dir . (is_dir($this->root_dir . '/public') ? '/public' : '/web');

        $this->resolveServer();

        $this->load();

        if (isset($GLOBALS['WPFB_ADMIN'])) $this->loadWordpressAdmin();
        else $this->loadWordpress();
    }

    private function resolveServer()
    {

        $context = $this->router->getContext();

        if (!isset($_SERVER['HTTP_HOST'])) {

            $multisite = env('WP_MULTISITE');

            if ($multisite && php_sapi_name() == 'cli') {

                $url = parse_url($multisite);

                $_SERVER['SERVER_PORT'] = $url['port'] ?? 80;
                $_SERVER['REQUEST_SCHEME'] = $url['scheme'] ?? 'https';

                if ($_SERVER['REQUEST_SCHEME'] == 'https')
                    $_SERVER['HTTP_HOST'] = $url['host'] ?? '127.0.0.1' . ($_SERVER['SERVER_PORT'] != 443 ? ':' . $_SERVER['SERVER_PORT'] : '');
                else
                    $_SERVER['HTTP_HOST'] = $url['host'] ?? '127.0.0.1' . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
            } else {

                $_SERVER['SERVER_PORT'] = $context->isSecure() ? $context->getHttpsPort() : $context->getHttpPort();
                $_SERVER['REQUEST_SCHEME'] = $context->isSecure() ? 'https' : 'http';

                if ($context->isSecure())
                    $_SERVER['HTTP_HOST'] = $context->getHost() . ($context->getHttpsPort() != 443 ? ':' . $context->getHttpsPort() : '');
                else
                    $_SERVER['HTTP_HOST'] = $context->getHost() . ($context->getHttpPort() != 80 ? ':' . $context->getHttpPort() : '');
            }

            $_SERVER['HTTPS'] = $_SERVER['REQUEST_SCHEME'] == 'https' ? 'on' : 'off';
        }

        if (!isset($_SERVER['REMOTE_ADDR']) && php_sapi_name() == "cli")
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    /**
     * @see wp-includes/class-wp.php, main function
     */
    private function loadWordpress()
    {

        if (!file_exists($this->public_dir . '/wp-config.php'))
            return;

        if (!defined('WP_DEBUG_LOG')) Config::define('WP_DEBUG_LOG', realpath($this->log_dir . '/wp-errors.log'));

        $wp_path = PathHelper::getWordpressRoot($this->root_dir);

        $wp_load_script = $this->root_dir . '/' . $wp_path . 'wp-load.php';

        if (!file_exists($wp_load_script))
            return;

        include $wp_load_script;
        remove_action('template_redirect', 'redirect_canonical');

    }

    private function loadWordpressAdmin()
    {
        global $menu;
        $menu = [];
        include $this->public_dir . '/wp/wp-admin/index.php';
        exit;
    }

    private function load()
    {
        PostTypeManager::getInstance()
            ->add("$this->root_dir/src")
            ->register()
        ;
    }
}