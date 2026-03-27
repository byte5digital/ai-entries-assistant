<?php

namespace Byte5\AiEntriesAssistant\Tests;

use Byte5\AiEntriesAssistant\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}