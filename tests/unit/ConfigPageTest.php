<?php

/**
 * Tests for the plugin's configuration page (MyPlugin-Config.page)
 *
 * This demonstrates testing a settings page that:
 *   - Loads configuration with parse_plugin_cfg()
 *   - Uses autov() for cache-busted asset URLs
 *   - Renders form inputs based on config values
 *
 * KEY CONCEPTS:
 *
 *   1. PluginBootstrap::setPluginConfig() - Mock config values for tests
 *   2. parse_plugin_cfg() - Works just like on Unraid, but reads mocked data
 *   3. autov() - Returns versioned URLs for cache busting
 *   4. _() - Translation function, passes through in test mode
 *
 * @see https://github.com/mstrhakr/plugin-tests for full documentation
 */

declare(strict_types=1);

namespace PluginName\Tests;

use PluginTests\TestCase;

class ConfigPageTest extends TestCase
{
    /**
     * Test that parse_plugin_cfg() returns expected config structure
     *
     * The mock parse_plugin_cfg() reads from your local default.cfg file
     * (if it exists) and merges with any test-specific overrides.
     */
    public function testParsePluginCfgReturnsConfig(): void
    {
        // Set up mock config values for this test
        // This simulates what would be in /boot/config/plugins/my-plugin/my-plugin.cfg
        // The mockPluginConfig() method is inherited from PluginTests\TestCase
        $this->mockPluginConfig('my-plugin', [
            'TEXT_VALUE'       => 'Hello World',
            'SELECTABLE_VALUE' => 'option2',
            'FILE_PICKER'      => '/mnt/user/data/',
        ]);

        // Call the mocked function - works exactly like on Unraid
        $cfg = parse_plugin_cfg('my-plugin');

        // Assert the config was loaded correctly
        $this->assertIsArray($cfg);
        $this->assertEquals('Hello World', $cfg['TEXT_VALUE']);
        $this->assertEquals('option2', $cfg['SELECTABLE_VALUE']);
        $this->assertEquals('/mnt/user/data/', $cfg['FILE_PICKER']);
    }

    /**
     * Test that autov() returns properly formatted asset URLs
     *
     * The autov() function on Unraid adds version query strings for cache busting.
     * Our mock returns the path with a predictable ?v=test suffix.
     */
    public function testAutovReturnsVersionedUrl(): void
    {
        $result = autov('/webGui/javascript/jquery.filetree.js');

        // The mock returns the path with a version suffix
        $this->assertStringContainsString('/webGui/javascript/jquery.filetree.js', $result);
        $this->assertStringContainsString('?v=', $result);
    }

    /**
     * Test that translation function _() returns the input string
     *
     * The translation function wraps strings for localization.
     * Our mock passes through the string unchanged (like English locale).
     */
    public function testTranslationFunctionPassthrough(): void
    {
        // The _() function should return the translated string
        // In test mode (and English locale), it returns the input
        $result = _('Text Input');

        $this->assertEquals('Text Input', $result);
    }

    /**
     * Test configuration with empty/default values
     *
     * Demonstrates testing edge cases where config values may be missing.
     */
    public function testConfigWithMissingValues(): void
    {
        // Set up partial config - some values missing
        $this->mockPluginConfig('my-plugin', [
            'TEXT_VALUE' => '',
            // SELECTABLE_VALUE intentionally omitted
        ]);

        $cfg = parse_plugin_cfg('my-plugin');

        // Empty string is still a valid value
        $this->assertEquals('', $cfg['TEXT_VALUE']);

        // Missing keys should not cause errors when accessed with ?? operator
        $this->assertNull($cfg['SELECTABLE_VALUE'] ?? null);
    }

    /**
     * Test htmlspecialchars escaping for XSS prevention
     *
     * The config page uses htmlspecialchars() to escape user input.
     * This test verifies that pattern works correctly.
     */
    public function testHtmlSpecialCharsEscaping(): void
    {
        // Simulate malicious input
        $maliciousInput = '<script>alert("xss")</script>';

        $this->mockPluginConfig('my-plugin', [
            'TEXT_VALUE' => $maliciousInput,
        ]);

        $cfg     = parse_plugin_cfg('my-plugin');
        $escaped = htmlspecialchars((string) $cfg['TEXT_VALUE']);

        // The script tags should be escaped
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }
}
