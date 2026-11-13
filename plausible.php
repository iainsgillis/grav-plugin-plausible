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
     * @return array<string, array<int, array{0: string, 1: int}>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAssetsInitialized' => [
                ['onAssetsInitialized', 0],
            ],
            'onPageContentProcessed' => [
                ['onPageContentProcessed', 10],
            ],
        ];
    }

    /**
     * Composer autoload.
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    public function onAssetsInitialized(): void
    {
        if ($this->isAdmin()) {
            return;
        }

        // Configurations saved before script_mode was introduced are legacy.
        $scriptMode = (string) $this->config->get('plugins.plausible.script_mode', 'legacy');

        if ($scriptMode === 'modern') {
            $plausibleSrcUrl = trim((string) $this->config->get('plugins.plausible.script_url'));
            if ($plausibleSrcUrl === '') {
                return;
            }

            $this->grav['assets']->addJs($plausibleSrcUrl, [
                'loading' => 'async',
                'pipeline' => false,
                'position' => 'before',
            ]);

            $plausibleInitJs = <<<'JS'
                window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments); };
                window.plausible.init = window.plausible.init || function(options) { window.plausible.o = options || {}; };
                window.plausible.init();
                JS;

            // Keep initialization synchronous even when Grav's JS pipeline is enabled.
            $this->grav['assets']->addInlineJs($plausibleInitJs, [
                'pipeline' => false,
                'position' => 'before',
            ]);

            return;
        }

        $dataDomain = trim((string) $this->config->get('plugins.plausible.data_domain'));
        if ($dataDomain === '') {
            return;
        }

        $externalLinksEnabled = $this->config->get('plugins.plausible.outbound_link_tracking');
        $selfHostingEnabled = $this->config->get('plugins.plausible.self_hosting');
        $selfHostingUrl = trim((string) $this->config->get('plugins.plausible.custom_domain'));
        $plausibleBaseUrl = $selfHostingEnabled && $selfHostingUrl !== '' ? $selfHostingUrl : 'https://plausible.io';
        $plausibleSrcUrl = $plausibleBaseUrl . '/js/plausible' . ($externalLinksEnabled ? '.outbound-links' : '') . '.js';

        $this->grav['assets']->addJs($plausibleSrcUrl, [
            'loading' => 'defer',
            'data-domain' => $dataDomain,
        ]);

        if ($this->config->get('plugins.plausible.custom_event_goals')) {
            // see https://plausible.io/docs/custom-event-goals
            $plausibleCustomEventJs = 'window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) }';
            $this->grav['assets']->addInlineJs($plausibleCustomEventJs);
        }
    }

    public function onPageContentProcessed(): void
    {
        if ($this->isAdmin()) {
            return;
        }

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
