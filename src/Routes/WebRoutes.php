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
    <title>ShakirAuth Hub - Unified Identity Portal</title>
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
        <h1>🔐 ShakirAuth Hub 🔐</h1>
        <h2 style="color: #667eea; font-size: 16px; font-weight: 400; margin-top: -10px; margin-bottom: 20px; text-align: center;">Your Gateway to Seamless Authentication</h2>';
    
        
    if (!isset($_SESSION['user'])) {
        $body .= '
        <p class="welcome-text">Choose your preferred login method to continue</p>
        <div class="login-options">
            <a href="/osspid-direct-login" class="btn btn-direct">🚀 Direct OSSPID Login</a>
            <div class="divider"></div>
            <a href="/osspid-login" class="btn btn-primary">Login with OSSPID (via Keycloak)</a>
            <a href="/banglabiz-login" class="btn btn-secondary">Login with BanglaBizz</a>
            <a href="/helloapp-login" class="btn btn-danger">Login with HelloApp</a>
            <a href="/uatid-login" class="btn btn-primary">Login with UATID</a>
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
 * UAT-ID Configuration Check Route
 * Fetches and displays UAT-ID's OpenID Connect configuration
 */
$app->get('/check-uatid-config', function (Request $request, Response $response) {
    // Try multiple possible well-known URLs
    $possibleUrls = [
        'https://uat-id.oss.net.bd/.well-known/openid-configuration',
        'https://uat-id.oss.net.bd/osspid-client/.well-known/openid-configuration',
        'https://uat-id.oss.net.bd/osspid-client/openid/.well-known/openid-configuration',
        'https://uat-id.oss.net.bd/osspid-client/openid/v2/.well-known/openid-configuration',
    ];
    
    $successConfig = null;
    $successUrl = null;
    $attempts = [];
    
    foreach ($possibleUrls as $wellKnownUrl) {
        $ch = curl_init($wellKnownUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        
        $curlResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        $attempts[] = [
            'url' => $wellKnownUrl,
            'status' => $httpCode,
            'error' => $error
        ];
        
        if ($httpCode == 200 && $curlResponse) {
            $config = json_decode($curlResponse, true);
            if ($config && isset($config['issuer'])) {
                $successConfig = $config;
                $successUrl = $wellKnownUrl;
                break;
            }
        }
    }
    
    $wellKnownUrl = $successUrl ?: $possibleUrls[0];
    $curlResponse = $successConfig ? json_encode($successConfig) : null;
    $httpCode = $successConfig ? 200 : $attempts[0]['status'];
    $error = $successConfig ? null : $attempts[0]['error'];
    
    $html = '<!DOCTYPE html>
<html>
<head>
    <title>UAT-ID Configuration Check</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .success { color: green; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { background: #fff3e0; padding: 10px; border-radius: 4px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="container">
        <h1>UAT-ID OpenID Connect Configuration</h1>';
    
    if ($successUrl) {
        $html .= '<p><strong>✓ Found configuration at:</strong> ' . htmlspecialchars($successUrl) . '</p>';
    } else {
        $html .= '<h3>Attempted URLs:</h3><table><tr><th>URL</th><th>Status</th></tr>';
        foreach ($attempts as $attempt) {
            $html .= '<tr><td>' . htmlspecialchars($attempt['url']) . '</td><td>' . $attempt['status'] . '</td></tr>';
        }
        $html .= '</table>';
    }
    
    if ($curlResponse && $httpCode == 200) {
        $config = json_decode($curlResponse, true);
        
        $html .= '<div class="success"><strong>✓ Successfully fetched configuration</strong></div>';
        
        if (isset($config['scopes_supported'])) {
            $html .= '<div class="info"><h3>Supported Scopes:</h3><ul>';
            foreach ($config['scopes_supported'] as $scope) {
                $html .= '<li>' . htmlspecialchars($scope) . '</li>';
            }
            $html .= '</ul><p><strong>Recommendation:</strong> Use these scopes in Keycloak: <code>' . 
                     htmlspecialchars(implode(' ', $config['scopes_supported'])) . '</code></p></div>';
        }
        
        $html .= '<h3>Full Configuration:</h3><pre>' . json_encode($config, JSON_PRETTY_PRINT) . '</pre>';
    } else {
        $html .= '<div class="error"><strong>✗ Could not find OpenID configuration</strong><br>';
        $html .= 'None of the standard paths returned a valid configuration.</div>';
        
        $html .= '<div class="warning"><h3>⚠️ Workaround Solution</h3>
        <p>Since UAT-ID doesn\'t have a discoverable well-known configuration, the scope issue is likely due to Keycloak\'s default settings.</p>
        <h4>Try this in Keycloak:</h4>
        <ol>
            <li>Go to your UAT-ID identity provider settings in Keycloak</li>
            <li>In the Keycloak admin, go to <strong>Configure → Client scopes</strong></li>
            <li>Find any client scopes that have <code>offline_access</code></li>
            <li>Or, try setting the UAT-ID provider to use <strong>prompt=none</strong> in advanced settings</li>
            <li>Most commonly: The default mapper configuration sends scopes that UAT-ID doesn\'t support</li>
        </ol>
        <h4>Quick Fix - Try these scopes in Keycloak:</h4>
        <ul>
            <li><code>openid</code> - Only the basic scope</li>
            <li><code>openid profile</code> - With profile information</li>
            <li><code>openid profile email</code> - Full standard scopes</li>
        </ul>
        <p><strong>Remove these if present:</strong> <code>offline_access</code>, <code>microprofile-jwt</code>, or any custom scopes</p>
        </div>';
        
        $html .= '<div class="info"><h3>Manual URLs to try:</h3><ul>';
        foreach ($possibleUrls as $url) {
            $html .= '<li><a href="' . htmlspecialchars($url) . '" target="_blank">' . htmlspecialchars($url) . '</a></li>';
        }
        $html .= '</ul></div>';
    }
    
    $html .= '<p><a href="/">← Back to Home</a></p>
    </div>
</body>
</html>';
    
    $response->getBody()->write($html);
    return $response;
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
