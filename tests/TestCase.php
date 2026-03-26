<?php

namespace Byte5\AiEntriesChatbot\Tests;

use Byte5\AiEntriesChatbot\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}