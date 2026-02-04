<?php

/**
 * PHPUnit Bootstrap for Unraid Plugin Testing
 *
 * This file initializes the testing environment for Unraid plugins.
 * It uses the plugin-tests framework (https://github.com/mstrhakr/plugin-tests)
 * to provide:
 *
 *   - Stream wrapper interception: Redirects Unraid paths like
 *     /usr/local/emhttp/plugins/... to your local source files
 *
 *   - Mocked Unraid globals: Provides $var, $disks, $shares arrays
 *     that tests can configure
 *
 *   - Mocked Unraid functions: autov(), parse_plugin_cfg(), _(),
 *     notify(), etc. work without a real Unraid server
 *
 * USAGE:
 *   1. Add plugin-tests as a git submodule:
 *      git submodule add https://github.com/mstrhakr/plugin-tests.git tests/framework
 *      cd tests/framework && git checkout v0.2
 *
 *   2. Configure phpunit.xml to use this bootstrap
 *
 *   3. Run: php vendor/bin/phpunit
 *
 * @see https://github.com/mstrhakr/plugin-tests for full documentation
 */

declare(strict_types=1);

// Load the testing framework's bootstrap
require_once __DIR__ . '/framework/src/php/bootstrap.php';

// Import the PluginBootstrap class for easy configuration
use PluginTests\PluginBootstrap;

/**
 * Initialize the testing environment for this plugin.
 *
 * PluginBootstrap::init() sets up:
 *   - Path mappings: Local source files are accessible via Unraid paths
 *   - Global variables: $var, $disks, $shares, etc. are initialized
 *   - Function mocks: Unraid functions like autov() and parse_plugin_cfg() work
 *
 * Parameters:
 *   - Plugin name: Used to construct paths like /usr/local/emhttp/plugins/{name}/
 *   - Source root: Where your plugin's PHP files live locally
 *   - Options: Configure subpaths, additional mappings, etc.
 */
PluginBootstrap::init(
    // The plugin name - this should match your plugin's directory name in /usr/local/emhttp/plugins/
    // For templates, replace 'plugin-name' with your actual plugin name
    pluginName: 'plugin-name',

    // Path to your plugin's source files relative to this bootstrap file
    // This maps to /usr/local/emhttp/plugins/{pluginName}/ on a real Unraid server
    sourceRoot: __DIR__ . '/../src/usr/local/emhttp/plugins/plugin-name',

    // Optional: Configure where your PHP files live within the plugin
    // Common patterns:
    //   - 'php' (default): Files in a /php/ subdirectory
    //   - '' (empty): Files directly in the plugin root (like this template)
    //   - ['php', 'include']: Multiple subdirectories
    options: [
        'subPath' => '',  // This template has .page files directly in the plugin root
    ]
);

/**
 * CUSTOMIZATION TIPS:
 *
 * 1. If your plugin uses a default.cfg file, create it at:
 *    src/usr/local/emhttp/plugins/{name}/default.cfg
 *    The parse_plugin_cfg() mock will use it automatically.
 *
 * 2. If you need to mock a specific $var state, do it in your test:
 *    PluginBootstrap::setVar(['mdState' => 'STARTED']);
 *
 * 3. If you need custom path mappings (e.g., shared include files):
 *    PluginBootstrap::addMapping('/some/unraid/path', '/local/path');
 *
 * 4. See tests/framework/docs/MOCKING-GUIDE.md for full documentation
 */
