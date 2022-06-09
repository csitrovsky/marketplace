<?php

namespace app;

class System extends Autoload
{

    public function __construct()
    {
    }

    public function register($prepend = false): void
    {
        spl_autoload_register(function ($namespace)
        {
            $this->include_system_file($namespace);
        }, true, $prepend);
    }

}