<?php

namespace EZFW\Http;

use EZFW\App;

abstract class Plugin
{
    abstract public function boot(App $app, array $config);
}
