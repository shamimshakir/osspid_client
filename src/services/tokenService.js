/**
 * Token Service
 * Handles OAuth2 token exchange and user info retrieval
 */

const axios = require('axios');
const querystring = require('querystring');
const config = require('../../config');
const { decodeJWT } = require('../utils/jwtUtils');

class TokenService {
  /**
   * Exchange authorization code for tokens (Keycloak)
   * @param {string} code - Authorization code
   * @returns {Promise<object>} Token response
   */
  static async exchangeKeycloakCode(code) {
    const tokenEndpoint = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/token`;
    
    const postData = {
      grant_type: 'authorization_code',
      code: code,
      client_id: config.keycloak.clientId,
      client_secret: config.keycloak.clientSecret,
      redirect_uri: config.keycloak.redirectUrl,
    };
    
    const response = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    
    return response.data;
  }
  // Keycloak exchange is available via `exchangeKeycloakCode`; UATID flow removed.

  // UATID exchange removed

  /**
   * Exchange authorization code for tokens (Direct OSSPID)
   * @param {string} code - Authorization code
   * @returns {Promise<object>} Token response
   */
  static async exchangeOsspidCode(code) {
    const tokenEndpoint = `${config.osspid.host}/osspid-client/openid/v2/token`;
    
    const postData = {
      grant_type: 'authorization_code',
      code: code,
      client_id: config.osspid.clientId,
      client_secret: config.osspid.clientSecret,
      redirect_uri: config.osspid.redirectUrl,
    };
    
    const response = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });
    
    return response.data;
  }

  // Keycloak userinfo available via `fetchKeycloakUserInfo`.

  /**
   * Fetch user information from Keycloak
   * @param {string} accessToken - Access token
   * @returns {Promise<object>} User data
   */
  static async fetchKeycloakUserInfo(accessToken) {
    const userinfoEndpoint = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/userinfo`;
    
    const response = await axios.get(userinfoEndpoint, {
      headers: {
        'Authorization': `Bearer ${accessToken}`
      }
    });
    
    return response.data;
  }

  /**
   * Extract user data from ID token
   * @param {string} idToken - ID token (JWT)
   * @returns {object|null} User data or null
   */
  static getUserDataFromIdToken(idToken) {
    return decodeJWT(idToken);
  }

  /**
   * Refresh access token using refresh token
   * @param {string} refreshToken - Refresh token
   * @param {string} loginType - Type of login ('oidc', 'osspid_direct')
   * @returns {Promise<object>} Token response
   */
  static async refreshAccessToken(refreshToken, loginType = 'osspid_direct') {
    let tokenEndpoint, clientId, clientSecret;

    if (loginType === 'oidc') {
      tokenEndpoint = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/token`;
      clientId = config.keycloak.clientId;
      clientSecret = config.keycloak.clientSecret;
    } else {
      // default to OSSPID direct token endpoint
      tokenEndpoint = `${config.osspid.host}/osspid-client/openid/v2/token`;
      clientId = config.osspid.clientId;
      clientSecret = config.osspid.clientSecret;
    }

    const postData = {
      grant_type: 'refresh_token',
      refresh_token: refreshToken,
      client_id: clientId,
      client_secret: clientSecret,
    };

    const response = await axios.post(tokenEndpoint, querystring.stringify(postData), {
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });

    return response.data;
  }
}

module.exports = TokenService;
