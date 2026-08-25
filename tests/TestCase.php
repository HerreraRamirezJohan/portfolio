<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The suite asserts on rendered markup, not on the asset pipeline --
        // don't make it depend on `npm run build` having been run.
        $this->withoutVite();
    }
}
