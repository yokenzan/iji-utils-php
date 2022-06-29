<?php

declare(strict_types=1);

use DI\Container;
use IjiUtils\App\Cli\Commands\CalculatePatientBurdenCommand;
use IjiUtils\App\Cli\Commands\InsuranceNumberCompleteDigitCommand;
use IjiUtils\App\Http\Presenter\Output as PresenterOutput;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\Console\Input\StringInput;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(LoggerInterface::class, new Logger('std', []));
AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/insurance-number/complete', function (Request $request, Response $response) use ($app) {
    /** @var InsuranceNumberCompleteDigitCommand $command */
    $command     = $app->getContainer()->get(InsuranceNumberCompleteDigitCommand::class);
    $queryParams = $request->getQueryParams();

    try {
        $command->run(
            new StringInput(key_exists('number', $queryParams) ? $queryParams['number'] : null),
            new PresenterOutput($response)
        );
    } catch (Exception $e) {
        $response->getBody()->write($e->getMessage());
    }

    return $response;
});

$app->get('/calculate/burden', function (Request $request, Response $response) use ($app) {
    /** @var CalculatePatientBurdenCommand $command */
    $command     = $app->getContainer()->get(CalculatePatientBurdenCommand::class);
    $queryParams = $request->getQueryParams();

    try {
        $parameters = [
            '--comma-separated-amount',
            key_exists('point', $queryParams) ? $queryParams['point'] : '',
        ];

        foreach ($queryParams as $key => $value) {
            if ($key === 'point') {
                continue;
            }
            $isLongOption   = strlen($key) === 1;
            $isBooleanValue = $value       === 1;
            $parameters[]   = sprintf(
                '%s%s%s%s',
                $isLongOption ? '-' : '--',
                $key,
                $isBooleanValue ? '' : ($isLongOption ? '' : '='),
                $isBooleanValue ? '' : $value
            );
        }

        $command->run(
            new StringInput(implode(' ', $parameters)),
            new PresenterOutput($response)
        );
    } catch (Exception $e) {
        $response->getBody()->write($e->getMessage());
    }

    return $response;
});

$app->run();
