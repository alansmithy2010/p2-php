<?php

namespace ImageCache2\Console;

use Symfony\Component\Console\Application as sfConsoleApplication;
use ImageCache2\Console\Command;

// TODO: make autoloadable
require_once __DIR__ . '/Command/Setup.php';

class Application extends sfConsoleApplication
{
    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Application::__construct()
     */
    public function __construct()
    {
        parent::__construct('ImageCache2 console script', '1.0');
        $this->addCommands(array(
            new Command\Setup(),
        ));
    }
}
