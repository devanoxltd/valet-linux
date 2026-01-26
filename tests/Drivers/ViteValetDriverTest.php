<?php

use Valet\Drivers\Specific\ViteValetDriver;

class ViteValetDriverTest extends BaseDriverTestCase
{
    public function test_it_serves_vite_projects()
    {
        $driver = new ViteValetDriver;

        $this->assertTrue($driver->serves($this->projectDir('vite'), 'my-site', '/'));
    }

    public function test_it_doesnt_serve_non_vite_projects()
    {
        $driver = new ViteValetDriver;

        $this->assertFalse($driver->serves($this->projectDir('public-with-index-non-laravel'), 'my-site', '/'));
    }

    public function test_it_gets_static_files_from_dist_directory()
    {
        $driver = new ViteValetDriver;

        $this->assertEquals(
            $this->projectDir('vite').'/dist/assets/app.css',
            $driver->isStaticFile($this->projectDir('vite'), 'my-site', '/assets/app.css')
        );
    }

    public function test_it_returns_false_for_missing_static_files()
    {
        $driver = new ViteValetDriver;

        $this->assertFalse($driver->isStaticFile($this->projectDir('vite'), 'my-site', '/missing.css'));
    }

    public function test_it_serves_index_html_as_front_controller()
    {
        $driver = new ViteValetDriver;

        $this->assertEquals(
            $this->projectDir('vite').'/dist/index.html',
            $driver->frontControllerPath($this->projectDir('vite'), 'my-site', '/')
        );
    }

    public function test_it_falls_back_to_index_html_for_spa_routing()
    {
        $driver = new ViteValetDriver;

        $this->assertEquals(
            $this->projectDir('vite').'/dist/index.html',
            $driver->frontControllerPath($this->projectDir('vite'), 'my-site', '/about')
        );
    }
}
