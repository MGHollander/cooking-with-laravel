<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class FrontendBuildTest extends TestCase
{
    /**
     * Test that the frontend assets can be built without errors.
     * This catches syntax errors in Vue/JS components.
     */
    public function test_frontend_assets_build_successfully(): void
    {
        $result = Process::run('npm run build');

        $this->assertTrue($result->successful(), "Frontend build failed! JS/Vue errors detected:\n".$result->errorOutput());
    }
}
