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
                ['onPluginsInitialized', 0],
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

    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            return;
        }

        $this->enable([
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onPageContentProcessed' => ['onPageContentProcessed', 10]
        ]);
    }

    public function onAssetsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        $plausibleDomain = $this->config->get('plugins.plausible.data_domain');
        if (!isset($plausibleDomain)) {
            return;
        }

        $externalLinksEnabled = $this->config->get('plugins.plausible.outbound_link_tracking');
        $customEventsEnabled = $this->config->get('plugins.plausible.custom_event_goals');

        $plausibleSrcUrl = 'https://plausible.io/js/plausible' . ($externalLinksEnabled ? '.outbound-links' : '') . '.js';
        $this->grav['assets']->addJs($plausibleSrcUrl, ['loading' => 'async defer', 'data-domain' => $plausibleDomain]);

        if ($customEventsEnabled) {
            // see https://docs.plausible.io/custom-event-goals
            $plausibleCustomEventJs = 'window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) }';
            $this->grav['assets']->addInlineJs($plausibleCustomEventJs);
        }
    }

    public function onPageContentProcessed()
    {

        $plausiblePublicEnabled = $this->grav['config']->get('plugins.plausible.public_dashboard_visible');
        if (!$plausiblePublicEnabled) {
            return;
        }

        $plausiblePublicUrl = $this->grav['config']->get('plugins.plausible.public_dashboard_url');
        if (isset($plausiblePublicUrl)) {
            $rawContent = $this->grav['page']->getRawContent();
            $editedContent = preg_replace('/^/', "<!-- Plausible Analytics public dashboard URL : $plausiblePublicUrl -->\n$1", $rawContent);
            $this->grav['page']->setRawContent($editedContent);
        }
    }
}
