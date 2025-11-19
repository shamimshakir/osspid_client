<?php

/**
 * Authentication Routes
 * Handles OAuth2/OpenID Connect authentication with Keycloak
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Keycloak Configuration
define('KHOST', 'http://192.168.30.56:8880');
define('KRealms', 'myrealm');
define('KClientId', 'ss-client');
define('KClientSecret', '2QvtLNIDHLlvAMHsAMqwoba0AfF091Cu');
define('RedirectURL', 'http://192.168.30.56:8010/keycloak/callback');
define('IDP', 'osspid');

// Direct OSSPID Configuration
define('OSSPID_HOST', 'http://192.168.30.56:8050');
define('OSSPID_CLIENT_ID', '2520c78a5763a4ca5154224a38da6faf2cbfd4ec');
define('OSSPID_CLIENT_SECRET', '3e5ea9864ed71e05727a08611a1b1357b01f2f70');
define('OSSPID_REDIRECT_URL', 'http://192.168.30.56:8010/osspid-direct/callback');

/**
 * Debug helper function
 * 
 * @param mixed $data Data to dump
 * @return void
 */
function dd($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    die();
}

/**
 * Build authorization URL for OAuth2 flow
 * 
 * @param string|null $idpHint Identity provider hint (optional)
 * @return string Authorization URL
 */
function buildAuthUrl(?string $idpHint = null): string
{
    $url = KHOST . '/realms/' . KRealms . '/protocol/openid-connect/auth';
    
    $credentials = [
        'client_id' => KClientId,
        'redirect_uri' => RedirectURL,
        'response_type' => 'code',
        'scope' => 'openid',
    ];
    
    if ($idpHint !== null) {
        $credentials['kc_idp_hint'] = $idpHint;
    }
    
    return $url . '?' . http_build_query($credentials);
}

/**
 * Build authorization URL for direct OSSPID OAuth2 flow
 * 
 * @return string Authorization URL
 */
function buildDirectOsspidAuthUrl(): string
{
    // Generate and store state parameter for CSRF protection
    session_start();
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth2_state'] = $state;
    
    $url = OSSPID_HOST . '/osspid-client/openid/v2/authorize';
    
    $credentials = [
        'client_id' => OSSPID_CLIENT_ID,
        'redirect_uri' => OSSPID_REDIRECT_URL,
        'response_type' => 'code',
        'scope' => 'openid profile email',
        'state' => $state,
    ];
    
    return $url . '?' . http_build_query($credentials);
}

/**
 * OSSPID Login Route
 * Redirects user to Keycloak authentication page with OSSPID as IDP
 */
$app->get('/osspid-login', function (Request $request, Response $response) {
    $authorizeUrl = buildAuthUrl(IDP);
    return $response->withHeader('Location', $authorizeUrl)->withStatus(302);
});

/**
 * BanglaBizz Login Route
 * Redirects user to Keycloak authentication page with BanglaBizz as IDP
 */
$app->get('/banglabiz-login', function (Request $request, Response $response) {
    $authorizeUrl = buildAuthUrl('banglabizz');
    return $response->withHeader('Location', $authorizeUrl)->withStatus(302);
});

/**
 * HelloApp Login Route
 * Redirects user to Keycloak authentication page with HelloApp as IDP
 */
$app->get('/helloapp-login', function (Request $request, Response $response) {
    $authorizeUrl = buildAuthUrl('helloapp');
    return $response->withHeader('Location', $authorizeUrl)->withStatus(302);
});

/**
 * All Login Options Route
 * Redirects user to Keycloak authentication page showing all available IDPs
 */
$app->get('/all-login', function (Request $request, Response $response) {
    $authorizeUrl = buildAuthUrl();
    return $response->withHeader('Location', $authorizeUrl)->withStatus(302);
});

/**
 * Direct OSSPID Login Route
 * Redirects user directly to OSSPID authentication page (bypassing Keycloak)
 */
$app->get('/osspid-direct-login', function (Request $request, Response $response) {
    $authorizeUrl = buildDirectOsspidAuthUrl();
    return $response->withHeader('Location', $authorizeUrl)->withStatus(302);
});

/**
 * Logout Route
 * Destroys local session, logs out from Keycloak SSO or direct OSSPID, and redirects back to login page
 */
$app->get('/logout', function (Request $request, Response $response) {
    session_start();
    
    // Get ID token and login type for proper OIDC logout
    $idToken = $_SESSION['id_token'] ?? null;
    $loginType = $_SESSION['login_type'] ?? 'keycloak'; // Default to keycloak for backward compatibility
    
    // Destroy session
    session_destroy();
    
    // Build logout URL based on login type
    if ($loginType === 'osspid_direct') {
        // Direct OSSPID logout
        $logoutUrl = OSSPID_HOST . '/osspid-client/openid/v2/logout';
        
        $params = [
            'post_logout_redirect_uri' => 'http://192.168.30.56:8010/',
            'client_id' => OSSPID_CLIENT_ID,
        ];
        
        if ($idToken) {
            $params['id_token_hint'] = $idToken;
        }
    } else {
        // Keycloak logout
        $logoutUrl = KHOST . '/realms/' . KRealms . '/protocol/openid-connect/logout';
        
        $params = [
            'post_logout_redirect_uri' => 'http://192.168.30.56:8010/',
            'client_id' => KClientId,
        ];
        
        if ($idToken) {
            $params['id_token_hint'] = $idToken;
        }
    }
    
    $logoutUrl .= '?' . http_build_query($params);
    
    return $response->withHeader('Location', $logoutUrl)->withStatus(302);
});

/**
 * OAuth2 Callback Route
 * Handles the callback from Keycloak after user authentication
 * Exchanges authorization code for access token and retrieves user information
 */
$app->get('/keycloak/callback', function (Request $request, Response $response) {
    $code = $request->getQueryParams()['code'] ?? null;
    
    if (empty($code)) {
        $response->getBody()->write('Invalid callback: No authorization code received');
        return $response->withStatus(400);
    }
    
    try {
        // Exchange authorization code for access token
        $tokenEndpoint = KHOST . '/realms/' . KRealms . '/protocol/openid-connect/token';
        
        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => KClientId,
            'client_secret' => KClientSecret,
            'redirect_uri' => RedirectURL,
        ];
        
        $ch = curl_init($tokenEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        
        $curlResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($curlResponse === false) {
            throw new Exception('Failed to connect to token endpoint.');
        }
        
        $responseData = json_decode($curlResponse, true);
        
        if (!isset($responseData['access_token'])) {
            $errorMsg = $responseData['error_description'] ?? 'Access token not found in response.';
            throw new Exception($errorMsg);
        }
        
        $accessToken = $responseData['access_token'];
        
        // Store tokens in session for logout and token refresh capability
        session_start();
        $_SESSION['login_type'] = 'keycloak'; // Mark this as Keycloak login
        if (isset($responseData['refresh_token'])) {
            $_SESSION['refresh_token'] = $responseData['refresh_token'];
        }
        if (isset($responseData['id_token'])) {
            $_SESSION['id_token'] = $responseData['id_token'];
        }
        
        // Fetch user information using access token
        $userinfoEndpoint = KHOST . '/realms/' . KRealms . '/protocol/openid-connect/userinfo';
        
        $ch = curl_init($userinfoEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $userinfoResponse = curl_exec($ch);
        curl_close($ch);
        
        $userData = json_decode($userinfoResponse, true);
        
        if (isset($userData['preferred_username'])) {
            session_start();
            $_SESSION['user'] = $userData;
            
            return $response->withHeader('Location', '/')->withStatus(302);
        } else {
            throw new Exception('Failed to retrieve user information.');
        }
        
    } catch (Exception $e) {
        // Log error for debugging (in production, use proper logging)
        error_log('OAuth Callback Error: ' . $e->getMessage());
        
        $response->getBody()->write('Authentication Error: ' . htmlspecialchars($e->getMessage()));
        return $response->withStatus(500);
    }
});

/**
 * Direct OSSPID OAuth2 Callback Route
 * Handles the callback from direct OSSPID after user authentication
 * Exchanges authorization code for access token and retrieves user information
 */
$app->get('/osspid-direct/callback', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $code = $params['code'] ?? null;
    $state = $params['state'] ?? null;
    
    // Verify state parameter for CSRF protection
    session_start();
    if (!isset($_SESSION['oauth2_state']) || $state !== $_SESSION['oauth2_state']) {
        unset($_SESSION['oauth2_state']);
        $response->getBody()->write('Invalid state parameter. Possible CSRF attack.');
        return $response->withStatus(400);
    }
    unset($_SESSION['oauth2_state']); // Clean up state after verification
    
    if (empty($code)) {
        $response->getBody()->write('Invalid callback: No authorization code received');
        return $response->withStatus(400);
    }
    
    try {
        // Exchange authorization code for access token
        $tokenEndpoint = OSSPID_HOST . '/osspid-client/openid/v2/token';
        
        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => OSSPID_CLIENT_ID,
            'client_secret' => OSSPID_CLIENT_SECRET,
            'redirect_uri' => OSSPID_REDIRECT_URL,
        ];
        
        $ch = curl_init($tokenEndpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        
        $curlResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception('cURL Error: ' . $error);
        }
        
        curl_close($ch);
        
        if ($curlResponse === false) {
            throw new Exception('Failed to connect to token endpoint.');
        }
        
        $responseData = json_decode($curlResponse, true);
        
        if (!isset($responseData['access_token'])) {
            $errorMsg = $responseData['error_description'] ?? $responseData['error'] ?? 'Access token not found in response.';
            throw new Exception($errorMsg . ' (HTTP ' . $httpCode . ')');
        }
        
        $accessToken = $responseData['access_token'];
        
        // Store tokens in session for logout and token refresh capability
        session_start();
        $_SESSION['login_type'] = 'osspid_direct'; // Mark this as direct OSSPID login
        $_SESSION['access_token'] = $accessToken;
        
        if (isset($responseData['refresh_token'])) {
            $_SESSION['refresh_token'] = $responseData['refresh_token'];
        }
        if (isset($responseData['id_token'])) {
            $_SESSION['id_token'] = $responseData['id_token'];
        }
        
        // Fetch user information using access token
        // Note: OSSPID might provide user info in the token response or via a userinfo endpoint
        // If userinfo is embedded in the ID token, decode it
        if (isset($responseData['id_token'])) {
            // Decode JWT ID token to get user info
            $tokenParts = explode('.', $responseData['id_token']);
            if (count($tokenParts) === 3) {
                $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
                $userData = json_decode($payload, true);
                
                if ($userData) {
                    $_SESSION['user'] = $userData;
                    return $response->withHeader('Location', '/')->withStatus(302);
                }
            }
        }
        
        // If no user data in ID token, try userinfo endpoint if available
        // OSSPID may or may not have a separate userinfo endpoint
        // For now, redirect with access token stored
        $_SESSION['user'] = [
            'sub' => 'osspid_user',
            'authenticated' => true,
            'provider' => 'osspid_direct'
        ];
        
        return $response->withHeader('Location', '/')->withStatus(302);
        
    } catch (Exception $e) {
        // Log error for debugging (in production, use proper logging)
        error_log('Direct OSSPID OAuth Callback Error: ' . $e->getMessage());
        
        $response->getBody()->write('Authentication Error: ' . htmlspecialchars($e->getMessage()));
        return $response->withStatus(500);
    }
});
