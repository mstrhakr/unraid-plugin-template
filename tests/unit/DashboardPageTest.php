<?php

/**
 * Tests for the plugin's dashboard card (MyPlugin-Dashboard.page)
 *
 * Dashboard cards are more complex because they:
 *   - Check Unraid version for compatibility
 *   - Generate HTML that integrates with the dashboard
 *   - May include JavaScript for interactivity
 *
 * KEY CONCEPTS:
 *
 *   1. version_compare() - Standard PHP function for comparing version strings
 *   2. Testing HTML output - Verify expected elements are present
 *   3. Testing conditional rendering - Different output for different Unraid versions
 *
 * @see https://github.com/mstrhakr/plugin-tests for full documentation
 */

declare(strict_types=1);

namespace PluginName\Tests;

use PluginTests\TestCase;

class DashboardPageTest extends TestCase
{
    /**
     * Test version comparison for responsive webgui detection
     *
     * The dashboard page checks the Unraid version to determine
     * if it should render responsive (7.2+) or legacy markup.
     */
    public function testVersionComparisonForResponsiveWebgui(): void
    {
        // Test responsive webgui (7.2+)
        $this->assertTrue(
            version_compare('7.2.0', '7.2', '>='),
            'Version 7.2.0 should be considered responsive'
        );

        // Test legacy webgui (pre-7.2)
        $this->assertFalse(
            version_compare('7.1.0', '7.2', '>='),
            'Version 7.1.0 should not be considered responsive'
        );

        // Test edge case
        $this->assertTrue(
            version_compare('7.2', '7.2', '>='),
            'Version 7.2 exactly should be considered responsive'
        );
    }

    /**
     * Test dashboard card name sanitization
     *
     * The card name is used in HTML IDs and must be valid.
     */
    public function testCardNameIsValidHtmlId(): void
    {
        $cardName = 'my-plugin';

        // HTML IDs should start with a letter and contain only valid characters
        $this->assertMatchesRegularExpression(
            '/^[a-z][a-z0-9\-]*$/i',
            $cardName,
            'Card name should be a valid HTML ID'
        );
    }

    /**
     * Test that dashboard generates expected HTML structure
     *
     * This test demonstrates verifying HTML output contains expected elements.
     * Note: For complex pages, you might extract the PHP logic into
     * separate functions that are easier to unit test.
     */
    public function testDashboardHtmlStructure(): void
    {
        $cardName = 'my-plugin';

        // Build expected parts of the dashboard HTML
        $expectedElements = [
            "id='{$cardName}-dashboard-card'",
            "id='{$cardName}-dashboard-title'",
            "id=\"{$cardName}-settings-button\"",
            'href="/Settings/MyPlugin-Config"',
            'fa-cog',  // Settings icon
        ];

        // Simulate the mytiles array that the dashboard populates
        $html = <<<EOT
                <tbody title="My New Plugin">
                  <tr>
                    <td>
                      <div class='tile-header' id='{$cardName}-dashboard-card'>
                        <div class='tile-header-left'>
                          <i class='icon-performance f32'></i>
                          <div class='section'>
                            <h3 class='tile-header-main' id='{$cardName}-dashboard-title'>
                              My New Plugin
                            </h3>
                            <span>Subtitle</span>
                          </div>
                        </div>
                        <div class='tile-header-right'>
                          <div class='tile-ctrl'>
                          </div>
                          <div class='tile-header-right-controls'>
                            <a id="{$cardName}-settings-button" href="/Settings/MyPlugin-Config" title="_(Settings)_"><i class="fa fa-fw fa-cog control"></i></a>
                          </div>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
            EOT;

        // Verify expected elements are present
        foreach ($expectedElements as $element) {
            $this->assertStringContainsString(
                $element,
                $html,
                "Dashboard HTML should contain: {$element}"
            );
        }
    }
}
