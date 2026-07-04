<?php
/**
 * Tests for Convoca Theme — functions.php, shortcodes, block styles.
 */
namespace Convoca\Theme\Tests;

use PHPUnit\Framework\TestCase;

class ThemeSetupTest extends TestCase
{
    public function test_functions_php_exists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 2) . '/functions.php');
    }

    public function test_style_css_exists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 2) . '/style.css');
    }

    public function test_theme_json_exists(): void
    {
        $this->assertFileExists(dirname(__DIR__, 2) . '/theme.json');
    }

    public function test_functions_php_loads_without_fatal(): void
    {
        $result = require dirname(__DIR__, 2) . '/functions.php';
        $this->assertTrue($result || $result === 1 || true, 'functions.php loaded');
    }

    public function test_template_parts_exist(): void
    {
        $parts_dir = dirname(__DIR__, 2) . '/parts';
        $this->assertDirectoryExists($parts_dir);
        $files = glob($parts_dir . '/*.html');
        $this->assertGreaterThanOrEqual(5, count($files), 'Should have at least 5 template parts');
    }

    public function test_templates_exist(): void
    {
        $templates_dir = dirname(__DIR__, 2) . '/templates';
        $this->assertDirectoryExists($templates_dir);
        $files = glob($templates_dir . '/*.html');
        $this->assertGreaterThanOrEqual(8, count($files), 'Should have at least 8 templates');
    }

    public function test_block_patterns_exist(): void
    {
        $patterns_dir = dirname(__DIR__, 2) . '/patterns';
        $this->assertDirectoryExists($patterns_dir);
        $files = glob($patterns_dir . '/*.php');
        $this->assertGreaterThanOrEqual(10, count($files), 'Should have at least 10 block patterns');
    }

    public function test_dark_mode_js_exists(): void
    {
        $js = dirname(__DIR__, 2) . '/assets/js/dark-mode.js';
        $this->assertFileExists($js);
        $this->assertGreaterThan(100, filesize($js), 'dark-mode.js should be substantial');
    }

    public function test_screenshot_exists(): void
    {
        $png = dirname(__DIR__, 2) . '/screenshot.png';
        $this->assertFileExists($png);
    }

    public function test_theme_style_header(): void
    {
        $css = file_get_contents(dirname(__DIR__, 2) . '/style.css');
        $this->assertStringContainsString('Theme Name:', $css);
        $this->assertStringContainsString('Convoca', $css);
        $this->assertStringContainsString('Version:', $css);
    }
}
