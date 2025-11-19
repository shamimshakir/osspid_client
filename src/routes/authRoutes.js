/**
 * Authentication Routes
 * Handles OAuth2/OpenID Connect authentication with Keycloak and OSSPID
 */

const express = require('express');
const axios = require('axios');
const querystring = require('querystring');
const jwt = require('jsonwebtoken');
const crypto = require('crypto');
const config = require('../../config');

const router = express.Router();

/**
 * Build authorization URL for OAuth2 flow with Keycloak
 * @param {string|null} idpHint - Identity provider hint (optional)
 * @returns {string} Authorization URL
 */
function buildAuthUrl(idpHint = null) {
  const url = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/auth`;
  
  const credentials = {
    response_type: 'code',
    scope: 'openid email',
    client_id: config.keycloak.clientId,
    redirect_uri: config.keycloak.redirectUrl,
  };
  
  if (idpHint) {
    credentials.kc_idp_hint = idpHint;
  }
  
  return `${url}?${querystring.stringify(credentials)}`;
}

/**
 * Build authorization URL for UATID (separate Keycloak realm)
 * @returns {string} Authorization URL
 */
function buildUatidAuthUrl() {
  const url = `${config.keycloak.host}/realms/${config.uatid.realm}/protocol/openid-connect/auth`;
  
  const credentials = {
    response_type: 'code',
    scope: 'openid profile email',
    client_id: config.uatid.clientId,
    redirect_uri: config.uatid.redirectUrl,
  };
  
  if (config.uatid.idpHint) {
    credentials.kc_idp_hint = config.uatid.idpHint;
  }
  
  return `${url}?${querystring.stringify(credentials)}`;
}

/**
 * Build authorization URL for direct OSSPID OAuth2 flow
 * @param {object} session - Express session object
 * @returns {string} Authorization URL
 */
function buildDirectOsspidAuthUrl(session) {
  // Generate and store state parameter for CSRF protection
  const state = crypto.randomBytes(16).toString('hex');
  session.oauth2_state = state;
  
  const url = `${config.osspid.host}/osspid-client/openid/v2/authorize`;
  
  const credentials = {
    client_id: config.osspid.clientId,
    redirect_uri: config.osspid.redirectUrl,
    response_type: 'code',
    scope: 'openid profile email',
    state: state,
  };
  
  return `${url}?${querystring.stringify(credentials)}`;
}

/**
 * Decode JWT token without verification (for ID token payload extraction)
 * @param {string} token - JWT token
 * @returns {object|null} Decoded payload or null
 */
function decodeJWT(token) {
  try {
    const parts = token.split('.');
    if (parts.length !== 3) return null;
    
    const payload = Buffer.from(parts[1].replace(/-/g, '+').replace(/_/g, '/'), 'base64').toString();
    return JSON.parse(payload);
  } catch (err) {
    console.error('Error decoding JWT:', err);
    return null;
  }
}

/**
 * OSSPID Login Route
 * Redirects user to Keycloak authentication page with OSSPID as IDP
 */
router.get('/osspid-login', (req, res) => {
  const authorizeUrl = buildAuthUrl(config.keycloak.idp);
  res.redirect(authorizeUrl);
});

/**
 * BanglaBizz Login Route
 * Redirects user to Keycloak authentication page with BanglaBizz as IDP
 */
router.get('/banglabiz-login', (req, res) => {
  const authorizeUrl = buildAuthUrl('banglabizz');
  res.redirect(authorizeUrl);
});

/**
 * HelloApp Login Route
 * Redirects user to Keycloak authentication page with HelloApp as IDP
 */
router.get('/helloapp-login', (req, res) => {
  const authorizeUrl = buildAuthUrl('helloapp');
  res.redirect(authorizeUrl);
});

/**
 * UATID Login Route
 * Redirects user to UATID Keycloak realm (separate realm with its own client)
 */
router.get('/uatid-login', (req, res) => {
  const authorizeUrl = buildUatidAuthUrl();
  res.redirect(authorizeUrl);
});

/**
 * All Login Options Route
 * Redirects user to Keycloak authentication page showing all available IDPs
 */
router.get('/all-login', (req, res) => {
  const authorizeUrl = buildAuthUrl();
  res.redirect(authorizeUrl);
});

/**
 * Direct OSSPID Login Route
 * Redirects user directly to OSSPID authentication page (bypassing Keycloak)
 */
router.get('/osspid-direct-login', (req, res) => {
  const authorizeUrl = buildDirectOsspidAuthUrl(req.session);
  res.redirect(authorizeUrl);
});

/**
 * Logout Route
 * Destroys local session, logs out from Keycloak SSO or direct OSSPID, and redirects back to login page
 */
router.get('/logout', (req, res) => {
  const idToken = req.session.id_token;
  const loginType = req.session.login_type || 'keycloak';
  
  // Destroy session
  req.session.destroy((err) => {
    if (err) {
      console.error('Session destruction error:', err);
    }
  });
  
  // Build logout URL based on login type
  let logoutUrl;
  let params = {
    post_logout_redirect_uri: `http://${config.server.host}:${config.server.port}/`,
  };
  
  if (loginType === 'osspid_direct') {
    // Direct OSSPID logout
    logoutUrl = `${config.osspid.host}/osspid-client/openid/v2/logout`;
    params.client_id = config.osspid.clientId;
  } else if (loginType === 'uatid') {
    // UATID Keycloak realm logout
    logoutUrl = `${config.keycloak.host}/realms/${config.uatid.realm}/protocol/openid-connect/logout`;
    params.client_id = config.uatid.clientId;
  } else {
    // Default Keycloak logout
    logoutUrl = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/logout`;
    params.client_id = config.keycloak.clientId;
  }
  
  if (idToken) {
    params.id_token_hint = idToken;
  }
  
  res.redirect(`${logoutUrl}?${querystring.stringify(params)}`);
});

/**
 * OAuth2 Callback Route for Keycloak
 * Handles the callback from Keycloak after user authentication
 * Exchanges authorization code for access token and retrieves user information
 */
router.get('/keycloak/callback', async (req, res) => {
  const code = req.query.code;
  
  if (!code) {
    return res.status(400).send('Invalid callback: No authorization code received');
  }
  
  try {
    // Exchange authorization code for access token
    const tokenEndpoint = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/token`;
    
    const postData = {
      grant_type: 'authorization_code',
      code: code,
      client_id: config.keycloak.clientId,
      client_secret: config.keycloak.clientSecret,
      redirect_uri: config.keycloak.redirectUrl,
    };
    
    const tokenResponse = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    
    const { access_token, refresh_token, id_token } = tokenResponse.data;
    
    if (!access_token) {
      throw new Error('Access token not found in response');
    }
    
    // Store tokens in session
    req.session.login_type = 'keycloak';
    req.session.refresh_token = refresh_token;
    req.session.id_token = id_token;
    
    // Fetch user information
    const userinfoEndpoint = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/userinfo`;
    
    const userinfoResponse = await axios.get(userinfoEndpoint, {
      headers: {
        'Authorization': `Bearer ${access_token}`
      }
    });
    
    const userData = userinfoResponse.data;
    
    if (userData.preferred_username || userData.sub) {
      req.session.user = userData;
      res.redirect('/');
    } else {
      throw new Error('Failed to retrieve user information');
    }
    
  } catch (error) {
    console.error('OAuth Callback Error:', error.message);
    const errorMsg = error.response?.data?.error_description || error.message;
    res.status(500).send(`Authentication Error: ${errorMsg}`);
  }
});

/**
 * Direct OSSPID OAuth2 Callback Route
 * Handles the callback from direct OSSPID after user authentication
 * Exchanges authorization code for access token and retrieves user information
 */
router.get('/osspid-direct/callback', async (req, res) => {
  const { code, state } = req.query;
  
  // Verify state parameter for CSRF protection
  if (!req.session.oauth2_state || state !== req.session.oauth2_state) {
    delete req.session.oauth2_state;
    return res.status(400).send('Invalid state parameter. Possible CSRF attack.');
  }
  delete req.session.oauth2_state;
  
  if (!code) {
    return res.status(400).send('Invalid callback: No authorization code received');
  }
  
  try {
    // Exchange authorization code for access token
    const tokenEndpoint = `${config.osspid.host}/osspid-client/openid/v2/token`;
    
    const postData = {
      grant_type: 'authorization_code',
      code: code,
      client_id: config.osspid.clientId,
      client_secret: config.osspid.clientSecret,
      redirect_uri: config.osspid.redirectUrl,
    };
    
    const tokenResponse = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    
    const { access_token, refresh_token, id_token } = tokenResponse.data;
    
    if (!access_token) {
      const errorMsg = tokenResponse.data.error_description || tokenResponse.data.error || 'Access token not found in response';
      throw new Error(errorMsg);
    }
    
    // Store tokens in session
    req.session.login_type = 'osspid_direct';
    req.session.access_token = access_token;
    req.session.refresh_token = refresh_token;
    req.session.id_token = id_token;
    
    // Decode ID token to get user info
    if (id_token) {
      const userData = decodeJWT(id_token);
      if (userData) {
        req.session.user = userData;
        return res.redirect('/');
      }
    }
    
    // Fallback user data if ID token decoding fails
    req.session.user = {
      sub: 'osspid_user',
      authenticated: true,
      provider: 'osspid_direct'
    };
    
    res.redirect('/');
    
  } catch (error) {
    console.error('Direct OSSPID OAuth Callback Error:', error.message);
    const errorMsg = error.response?.data?.error_description || error.response?.data?.error || error.message;
    res.status(500).send(`Authentication Error: ${errorMsg}`);
  }
});

/**
 * UATID OAuth2 Callback Route
 * Handles the callback from UATID Keycloak realm after user authentication
 * Exchanges authorization code for access token and retrieves user information
 */
router.get('/uatid/callback', async (req, res) => {
  const code = req.query.code;
  
  if (!code) {
    return res.status(400).send('Invalid callback: No authorization code received');
  }
  
  try {
    // Exchange authorization code for access token using UATID realm
    const tokenEndpoint = `${config.keycloak.host}/realms/${config.uatid.realm}/protocol/openid-connect/token`;
    
    const postData = {
      grant_type: 'authorization_code',
      code: code,
      client_id: config.uatid.clientId,
      client_secret: config.uatid.clientSecret,
      redirect_uri: config.uatid.redirectUrl,
    };
    
    const tokenResponse = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    
    const { access_token, refresh_token, id_token } = tokenResponse.data;
    
    if (!access_token) {
      throw new Error('Access token not found in response');
    }
    
    // Store tokens in session
    req.session.login_type = 'uatid';
    req.session.refresh_token = refresh_token;
    req.session.id_token = id_token;
    
    // Fetch user information
    const userinfoEndpoint = `${config.keycloak.host}/realms/${config.uatid.realm}/protocol/openid-connect/userinfo`;
    
    const userinfoResponse = await axios.get(userinfoEndpoint, {
      headers: {
        'Authorization': `Bearer ${access_token}`
      }
    });
    
    const userData = userinfoResponse.data;
    
    if (userData.preferred_username || userData.sub) {
      req.session.user = userData;
      res.redirect('/');
    } else {
      throw new Error('Failed to retrieve user information');
    }
    
  } catch (error) {
    console.error('UATID OAuth Callback Error:', error.message);
    const errorMsg = error.response?.data?.error_description || error.message;
    res.status(500).send(`Authentication Error: ${errorMsg}`);
  }
});

module.exports = router;
