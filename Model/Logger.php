<?php

namespace Orangecat\Checkip\Model;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Orangecat\Checkip\Model\Service\LogService;

class Logger extends \Monolog\Logger
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('Checkip', $this->initHandlers());
    }

    /**
     * Init Handlers
     *
     * @return array
     */
    private function initHandlers(): array
    {
        return [
            (new StreamHandler(BP . '/var/ipblacklist/log/' . LogService::LOG_FILENAME . '.log'))
                ->setFormatter(new LineFormatter("%datetime%|%message%\n", "Y-m-d|H:i:s"))
        ];
    }
}
