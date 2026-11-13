<?php

// Run with: php tests/script-output.php
namespace Grav\Common {
    class Plugin
    {
        public $config;
        public $grav;
        public $admin = false;

        public function isAdmin()
        {
            return $this->admin;
        }
    }
}

namespace {
    require dirname(__DIR__) . '/plausible.php';

    class FakeConfig
    {
        private $values;

        public function __construct(array $values)
        {
            $this->values = $values;
        }

        public function get($key, $default = null)
        {
            $key = substr($key, strlen('plugins.plausible.'));
            return array_key_exists($key, $this->values) ? $this->values[$key] : $default;
        }
    }

    class FakeAssets
    {
        public $calls = [];

        public function addJs($source, array $options = [])
        {
            $this->calls[] = ['external', $source, $options];
        }

        public function addInlineJs($source, array $options = [])
        {
            $this->calls[] = ['inline', $source, $options];
        }
    }

    function collectScripts(array $config, $admin = false)
    {
        $assets = new FakeAssets();
        $plugin = new \Grav\Plugin\PlausiblePlugin();
        $plugin->config = new FakeConfig($config);
        $plugin->grav = ['assets' => $assets, 'config' => $plugin->config];
        $plugin->admin = $admin;
        $plugin->onAssetsInitialized();
        return $assets->calls;
    }

    function expectSame($expected, $actual, $message)
    {
        if ($expected !== $actual) {
            throw new \RuntimeException($message . '\nExpected: ' . var_export($expected, true)
                . '\nActual: ' . var_export($actual, true));
        }
    }

    function expectPattern($pattern, $actual, $message)
    {
        if (preg_match($pattern, $actual) !== 1) {
            throw new \RuntimeException($message);
        }
    }

    $tests = [];

    $tests['modern ignores every combination of legacy flags'] = function () {
        foreach ([false, true] as $outbound) {
            foreach ([false, true] as $selfHosted) {
                foreach ([false, true] as $customEvents) {
                    foreach (['', 'legacy.example'] as $domain) {
                        $calls = collectScripts([
                            'script_mode' => 'modern',
                            'script_url' => " \t https://plausible.io/js/pa-site-specific.js \n",
                            'data_domain' => $domain,
                            'outbound_link_tracking' => $outbound,
                            'self_hosting' => $selfHosted,
                            'custom_domain' => 'https://legacy.example',
                            'custom_event_goals' => $customEvents,
                        ]);
                        expectSame(2, count($calls), 'Modern mode must emit exactly two assets');
                        expectSame(['external', 'https://plausible.io/js/pa-site-specific.js', [
                            'loading' => 'async', 'pipeline' => false, 'position' => 'before',
                        ]], $calls[0], 'Modern external script must be trimmed, async and unpipelined');
                        expectSame('inline', $calls[1][0], 'Modern initialization must follow the external asset');
                        expectSame(['pipeline' => false, 'position' => 'before'], $calls[1][2],
                            'Modern initialization must run before pipelined scripts');
                        $js = $calls[1][1];
                        expectPattern('/window\.plausible\s*=\s*window\.plausible\s*\|\|\s*function\s*\(\s*\)\s*\{\s*\(window\.plausible\.q\s*=\s*window\.plausible\.q\s*\|\|\s*\[\]\)\.push\(arguments\)/',
                            $js, 'Modern script must preserve or create the Plausible queue shim');
                        expectPattern('/window\.plausible\.init\s*=\s*window\.plausible\.init\s*\|\|\s*function\s*\(options\)\s*\{\s*window\.plausible\.o\s*=\s*options\s*\|\|\s*\{\}/',
                            $js, 'Modern init fallback must preserve existing init and store options');
                        expectPattern('/window\.plausible\.init\(\s*\)\s*;/', $js,
                            'Modern script must invoke init');
                    }
                }
            }
        }
    };

    $tests['modern missing or blank URL emits nothing'] = function () {
        expectSame([], collectScripts([
            'script_mode' => 'modern', 'data_domain' => 'legacy.example',
            'custom_event_goals' => true,
        ]), 'Modern missing URL must emit nothing');
        foreach ([null, '', " \t\n"] as $url) {
            expectSame([], collectScripts([
                'script_mode' => 'modern', 'script_url' => $url,
                'data_domain' => 'legacy.example', 'custom_event_goals' => true,
            ]), 'Modern blank URL must not fall back to legacy or emit inline initialization');
        }
    };

    $tests['legacy defer and data-domain remain unchanged'] = function () {
        expectSame([['external', 'https://plausible.io/js/plausible.js', [
            'loading' => 'defer', 'data-domain' => 'one.example,two.example',
        ]]], collectScripts([
            'script_mode' => 'legacy', 'data_domain' => '  one.example,two.example  ',
            'script_url' => 'https://ignored.example/script.js',
        ]), 'Legacy script must retain defer and trimmed data-domain without modern options');
    };

    $tests['legacy outbound and self-hosted URLs'] = function () {
        foreach ([false, true] as $outbound) {
            foreach ([false, true] as $selfHosted) {
                foreach (['', '  https://analytics.example  '] as $customDomain) {
                    $base = $selfHosted && trim($customDomain) !== ''
                        ? 'https://analytics.example' : 'https://plausible.io';
                    expectSame([['external', $base . '/js/plausible'
                        . ($outbound ? '.outbound-links' : '') . '.js', [
                            'loading' => 'defer', 'data-domain' => 'example.com',
                        ]]], collectScripts([
                            'script_mode' => 'legacy', 'data_domain' => 'example.com',
                            'outbound_link_tracking' => $outbound,
                            'self_hosting' => $selfHosted, 'custom_domain' => $customDomain,
                        ]), 'Legacy outbound/self-hosting URL selection changed');
                }
            }
        }
    };

    $tests['legacy custom events use only the optional old shim'] = function () {
        foreach ([false, true] as $enabled) {
            $calls = collectScripts([
                'script_mode' => 'legacy', 'data_domain' => 'example.com',
                'custom_event_goals' => $enabled,
            ]);
            expectSame($enabled ? 2 : 1, count($calls), 'Legacy custom-event shim must be optional');
            if ($enabled) {
                expectSame(['inline', 'window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) }', []],
                    $calls[1], 'Legacy must retain only its old queue shim, with no init');
            }
        }
    };

    $tests['missing mode defaults to legacy'] = function () {
        expectSame([['external', 'https://plausible.io/js/plausible.js', [
            'loading' => 'defer', 'data-domain' => 'example.com',
        ]]], collectScripts([
            'data_domain' => 'example.com', 'script_url' => 'https://ignored.example/script.js',
        ]), 'Saved configurations without script_mode must remain legacy');
    };

    $tests['legacy blank domain emits nothing'] = function () {
        expectSame([], collectScripts([
            'script_mode' => 'legacy', 'data_domain' => " \t\n", 'custom_event_goals' => true,
        ]), 'Legacy blank domain must suppress all scripts');
    };

    $tests['admin emits nothing in either mode'] = function () {
        foreach (['modern', 'legacy'] as $mode) {
            expectSame([], collectScripts([
                'script_mode' => $mode, 'script_url' => 'https://example.com/script.js',
                'data_domain' => 'example.com', 'custom_event_goals' => true,
            ], true), 'Admin requests must not emit analytics scripts');
        }
    };

    $failed = 0;
    foreach ($tests as $name => $test) {
        try {
            $test();
            echo "PASS: $name\n";
        } catch (\Throwable $error) {
            ++$failed;
            fwrite(STDERR, "FAIL: $name\n" . $error->getMessage() . "\n");
        }
    }
    echo sprintf("\n%d tests, %d passed, %d failed\n", count($tests), count($tests) - $failed, $failed);
    exit($failed === 0 ? 0 : 1);
}
