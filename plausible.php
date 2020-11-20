<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Plugin;

/**
 * Class PlausiblePlugin
 * @package Grav\Plugin
 */
class PlausiblePlugin extends Plugin
{
    /**
     * @return array
     * The getSubscribedEvents() gives the core a list of events
     *
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => [
                ['autoload', 100000], // TODO: Remove when plugin requires Grav >=1.7
                ['onAssetsInitialized', 0],
            ]
        ];
    }

    /**
     * Composer autoload.
     *is
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    public function onAssetsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }
        $plausibleDomain = $this->config->get('plugins.plausible.data_domain', 'example.com');
        $plausibleSrcUrl = "https://plausible.io/js/plausible.js";
        $this->grav['assets']->addJs($plausibleSrcUrl, ['loading' => 'async defer', 'data-domain' => $plausibleDomain]);
    }
}
