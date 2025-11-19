<?php

/**
 * Web Routes
 * Handles public web pages and UI routes
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

/**
 * Home Page Route
 * Displays login options or user dashboard based on authentication status
 */
$app->get('/', function (Request $request, Response $response) {
    session_start();
    
    $body = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSSPID Client Portal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }
        
        h2 {
            color: #667eea;
            font-size: 22px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .welcome-text {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        
        .login-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn {
            display: block;
            text-decoration: none;
            padding: 14px 24px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.4);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 172, 254, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(250, 112, 154, 0.4);
        }
        
        .btn-direct {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            position: relative;
            overflow: hidden;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
        }
        
        .btn-direct::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-direct:hover::before {
            left: 100%;
        }
        
        .btn-direct:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(17, 153, 142, 0.5);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            margin: 20px 0;
        }
        
        .user-info h3 {
            color: #333;
            font-size: 18px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #667eea;
        }
        
        .info-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 80px;
            margin-right: 10px;
        }
        
        .info-value {
            color: #333;
            flex: 1;
        }
        
        .greeting {
            text-align: center;
            color: #667eea;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 OSSPID Client Portal 🔐</h1>';
    
        
    if (!isset($_SESSION['user'])) {
        $body .= '
        <p class="welcome-text">Choose your preferred login method to continue</p>
        <div class="login-options">
            <a href="/osspid-direct-login" class="btn btn-direct">🚀 Direct OSSPID Login</a>
            <div class="divider"></div>
            <a href="/osspid-login" class="btn btn-primary">Login with OSSPID (via Keycloak)</a>
            <a href="/banglabiz-login" class="btn btn-secondary">Login with BanglaBizz</a>
            <a href="/helloapp-login" class="btn btn-danger">Login with HelloApp</a>
            <a href="/all-login" class="btn btn-success">All Login Options</a>
        </div>';
    }
    
    if (isset($_SESSION['user'])) {
        $preferred_username = htmlspecialchars($_SESSION['user']['preferred_username'] ?? $_SESSION['user']['sub'] ?? 'User');
        $name = htmlspecialchars($_SESSION['user']['name'] ?? 'N/A');
        $email = htmlspecialchars($_SESSION['user']['email'] ?? 'N/A');
        $loginType = htmlspecialchars($_SESSION['login_type'] ?? 'Unknown');
        $idp = htmlspecialchars($_SESSION['user']['idp_denotation'] ?? ($loginType === 'osspid_direct' ? 'Direct OSSPID' : 'N/A'));
        
        $body .= "
        <div class='greeting'>Hello, $preferred_username! 👋</div>
        <div class='divider'></div>
        <div class='user-info'>
            <h3>Your Information</h3>
            <div class='info-item'>
                <span class='info-label'>Name:</span>
                <span class='info-value'>$name</span>
            </div>
            <div class='info-item'>
                <span class='info-label'>Email:</span>
                <span class='info-value'>$email</span>
            </div>
            <div class='info-item'>
                <span class='info-label'>IDP:</span>
                <span class='info-value'>$idp</span>
            </div>
            <div class='info-item'>
                <span class='info-label'>Login Via:</span>
                <span class='info-value'>$loginType</span>
            </div>
        </div>
        <div class='login-options'>
            <a href='/logout' class='btn btn-danger'>Logout</a>
        </div>";
    }
    
    $body .= '
    </div>
</body>
</html>';
    
    $response->getBody()->write($body);
    return $response;
});

/**
 * API Status Endpoint
 * Returns JSON response with API status
 */
$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/status', function (Request $request, Response $response) {
        $data = [
            'status' => 'OK',
            'timestamp' => date('c'),
            'version' => '1.0.0'
        ];
        
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    });
});

/**
 * Example POST Route
 * Demonstrates handling POST data
 * 
 * @deprecated This is a demo route and should be removed in production
 */
$app->post('/hola', function (Request $request, Response $response) {
    $requestBody = $request->getParsedBody();
    $name = $requestBody['name'] ?? 'Guest';
    $age = $requestBody['age'] ?? 'unknown';
    
    $message = "Hola, " . htmlspecialchars($name) . "! ";
    $message .= "You are " . htmlspecialchars($age) . " years old.";
    
    $response->getBody()->write($message);
    return $response;
});
