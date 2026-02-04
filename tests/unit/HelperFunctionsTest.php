<?php

/**
 * Example: Testing extracted helper functions
 *
 * BEST PRACTICE: Extract complex logic from .page files into separate
 * PHP files that can be unit tested more easily.
 *
 * For example, instead of having all logic inline in MyPlugin-Config.page,
 * you might create:
 *   - php/helpers.php - Utility functions
 *   - php/api.php - Backend API handlers
 *
 * This makes testing much easier because you can test individual functions
 * without needing to render full page output.
 *
 * @see https://github.com/mstrhakr/plugin-tests for full documentation
 */

declare(strict_types=1);

namespace PluginName\Tests;

use PluginTests\TestCase;

class HelperFunctionsTest extends TestCase
{
    /**
     * Example: Test a hypothetical sanitization function
     *
     * If you had a helpers.php file with utility functions,
     * you would test them here.
     */
    public function testExampleSanitizeFunction(): void
    {
        // Example of what a testable helper might look like
        $sanitize = function (string $input): string {
            return (string) preg_replace('/[^a-z0-9\-_]/i', '', $input);
        };

        $this->assertEquals('valid-name_123', $sanitize('valid-name_123'));
        $this->assertEquals('nospaces', $sanitize('no spaces'));
        $this->assertEquals('nospecialchars', $sanitize('no<special>chars!'));
    }

    /**
     * Example: Test version string parsing
     *
     * Demonstrates testing logic that parses Unraid version strings.
     */
    public function testVersionParsing(): void
    {
        // Simulate parsing /etc/unraid-version
        $versionFile = ['version' => '7.2.0'];

        $version = $versionFile['version'];

        $this->assertEquals('7.2.0', $version);

        // Parse major.minor.patch
        $parts = explode('.', $version);
        $this->assertEquals('7', $parts[0]);
        $this->assertEquals('2', $parts[1]);
        $this->assertEquals('0', $parts[2]);
    }

    /**
     * Example: Test path validation
     *
     * Plugin paths should be validated to prevent directory traversal.
     */
    public function testPathValidation(): void
    {
        // Valid paths
        $this->assertTrue($this->isValidPluginPath('/mnt/user/data/'));
        $this->assertTrue($this->isValidPluginPath('/mnt/cache/appdata/'));

        // Invalid paths (directory traversal attempts)
        $this->assertFalse($this->isValidPluginPath('/mnt/../etc/passwd'));
        $this->assertFalse($this->isValidPluginPath('/etc/passwd'));
    }

    /**
     * Helper method demonstrating path validation logic
     */
    private function isValidPluginPath(string $path): bool
    {
        // Must start with /mnt/
        if ( ! str_starts_with($path, '/mnt/')) {
            return false;
        }

        // No directory traversal
        if (str_contains($path, '..')) {
            return false;
        }

        return true;
    }
}
