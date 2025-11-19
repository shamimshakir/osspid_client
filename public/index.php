<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Create Slim app
$app = AppFactory::create();

// Add error middleware (for debugging)
$app->addErrorMiddleware(true, true, true);

// Import routes
require __DIR__ . '/../src/Routes/WebRoutes.php';
require __DIR__ . '/../src/Routes/AuthRoutes.php';

$app->run();