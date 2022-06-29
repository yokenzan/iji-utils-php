<?php

declare(strict_types=1);

namespace IjiUtils\App\Http\Presenter;

use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Console\Output\Output as ConsoleOutput;

class Output extends ConsoleOutput
{
    private Response $response;

    public function __construct(
        Response $response,
        int      $verbosity = self::VERBOSITY_NORMAL,
    ) {
        $this->response = $response;
        parent::__construct($verbosity);
    }

    /**
     * {@inheritdoc}
     */
    public function doWrite(string $message, bool $_newline): void
    {
        $this->response->getBody()->write($message);
    }
}
