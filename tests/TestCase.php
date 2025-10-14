<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    //delete all data before running inut test
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("delete from users");
    }
}
