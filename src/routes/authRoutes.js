/**
 * Authentication Routes
 * Handles OAuth2/OpenID Connect authentication with Keycloak and OSSPID
 */

const express = require('express');
const config = require('../../config');
const AuthUrlBuilder = require('../services/authUrlBuilder');
const TokenService = require('../services/tokenService');
const { storeAuthData, clearAuthData, verifyState } = require('../utils/sessionHelper');
const { handleOAuthError, validateCallbackCode } = require('../middleware/errorHandler');

const router = express.Router();

// ==================== LOGIN ROUTES ====================

/**
 * OSSPID Login Route
 * Redirects user to Keycloak authentication page with OSSPID as IDP
 */
router.get('/osspid-login', (req, res) => {
  const authorizeUrl = AuthUrlBuilder.buildKeycloakAuthUrl(config.keycloak.idp);
  res.redirect(authorizeUrl);
});

/**
 * UATID Login Route
 * Redirects user to UATID Keycloak realm
 */
router.get('/uatid-login', (req, res) => {
  const authorizeUrl = AuthUrlBuilder.buildUatidAuthUrl();
  res.redirect(authorizeUrl);
});

/**
 * All Login Options Route
 * Redirects user to Keycloak showing all available IDPs
 */
router.get('/all-login', (req, res) => {
  const authorizeUrl = AuthUrlBuilder.buildKeycloakAuthUrl();
  res.redirect(authorizeUrl);
});

/**
 * Direct OSSPID Login Route
 * Redirects user directly to OSSPID (bypassing Keycloak)
 */
router.get('/osspid-direct-login', (req, res) => {
  const authorizeUrl = AuthUrlBuilder.buildDirectOsspidAuthUrl(req.session);
  res.redirect(authorizeUrl);
});

// ==================== LOGOUT ROUTES ====================

/**
 * Logout Route
 * Destroys local session and initiates SSO logout
 */
router.get('/logout', async (req, res) => {
  const idToken = req.session.id_token;
  const loginType = req.session.login_type || 'keycloak';
  
  // Special handling for UATID two-step logout
  if (loginType === 'uatid') {
    try {
      await clearAuthData(req.session);
    } catch (err) {
      console.error('Session destruction error:', err);
    }
    
    const idpLogoutCallback = `${config.server.appUrl}/logout-idp`;
    const logoutUrl = AuthUrlBuilder.buildLogoutUrl('uatid', idToken, idpLogoutCallback);
    
    return res.redirect(logoutUrl);
  }
  
  // Standard logout for other login types
  try {
    await clearAuthData(req.session);
  } catch (err) {
    console.error('Session destruction error:', err);
  }
  
  const redirectUri = `${config.server.appUrl}/`;
  const logoutUrl = AuthUrlBuilder.buildLogoutUrl(loginType, idToken, redirectUri);
  
  res.redirect(logoutUrl);
});

/**
 * IDP Logout Callback Route
 * Handles the second step of UATID logout
 */
router.get('/logout-idp', (req, res) => {
  const finalRedirectUri = `${config.server.appUrl}/`;
  const idpLogoutUrl = AuthUrlBuilder.buildIdpLogoutUrl(finalRedirectUri);
  
  res.redirect(idpLogoutUrl);
});

// ==================== CALLBACK ROUTES ====================

/**
 * Keycloak OAuth2 Callback Route
 */
router.get('/keycloak/callback', async (req, res) => {
  const { code } = req.query;
  
  if (!validateCallbackCode(code, res)) return;
  
  try {
    // Exchange code for tokens
    const tokens = await TokenService.exchangeKeycloakCode(code);
    
    if (!tokens.access_token) {
      throw new Error('Access token not found in response');
    }
    
    // Fetch user information
    const userData = await TokenService.fetchKeycloakUserInfo(tokens.access_token);
    
    if (!userData.preferred_username && !userData.sub) {
      throw new Error('Failed to retrieve user information');
    }
    
    // Store authentication data in session
    storeAuthData(req.session, 'keycloak', tokens, userData);
    
    res.redirect('/');
    
  } catch (error) {
    handleOAuthError(error, res, 'Keycloak OAuth');
  }
});

/**
 * Direct OSSPID OAuth2 Callback Route
 */
router.get('/osspid-direct/callback', async (req, res) => {
  const { code, state } = req.query;
  
  // Verify state for CSRF protection
  if (!verifyState(req.session, state)) {
    return res.status(400).send(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Security Error</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
          .error { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
          h1 { color: #d32f2f; }
        </style>
      </head>
      <body>
        <div class="error">
          <h1>Security Error</h1>
          <p>Invalid state parameter. Possible CSRF attack detected.</p>
          <p><a href="/">← Back to Home</a></p>
        </div>
      </body>
      </html>
    `);
  }
  
  if (!validateCallbackCode(code, res)) return;
  
  try {
    // Exchange code for tokens
    const tokens = await TokenService.exchangeOsspidCode(code);
    
    if (!tokens.access_token) {
      const errorMsg = tokens.error_description || tokens.error || 'Access token not found in response';
      throw new Error(errorMsg);
    }
    
    // Extract user data from ID token
    let userData = null;
    if (tokens.id_token) {
      userData = TokenService.getUserDataFromIdToken(tokens.id_token);
    }
    
    // Fallback user data if ID token decoding fails
    if (!userData) {
      userData = {
        sub: 'osspid_user',
        authenticated: true,
        provider: 'osspid_direct'
      };
    }
    
    // Store authentication data in session
    storeAuthData(req.session, 'osspid_direct', tokens, userData);
    
    res.redirect('/');
    
  } catch (error) {
    handleOAuthError(error, res, 'Direct OSSPID OAuth');
  }
});

/**
 * UATID OAuth2 Callback Route
 */
router.get('/uatid/callback', async (req, res) => {
  const { code } = req.query;
  
  if (!validateCallbackCode(code, res)) return;
  
  try {
    // Exchange code for tokens
    const tokens = await TokenService.exchangeUatidCode(code);
    
    if (!tokens.access_token) {
      throw new Error('Access token not found in response');
    }
    
    // Fetch user information
    const userData = await TokenService.fetchUatidUserInfo(tokens.access_token);
    
    if (!userData.preferred_username && !userData.sub) {
      throw new Error('Failed to retrieve user information');
    }
    
    // Store authentication data in session
    storeAuthData(req.session, 'uatid', tokens, userData);
    
    res.redirect('/');
    
  } catch (error) {
    handleOAuthError(error, res, 'UATID OAuth');
  }
});

module.exports = router;
