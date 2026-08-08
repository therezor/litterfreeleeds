<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Lazily, so the tests that never touch the database still pay nothing for
     * a migration run.
     */
    use LazilyRefreshDatabase;
}
