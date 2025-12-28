/**
 * Authentication URL Builder Service
 * Handles construction of OAuth2/OIDC authorization URLs
 */

const querystring = require('querystring');
const crypto = require('crypto');
const config = require('../../config');

class AuthUrlBuilder {
  // Supported flows: Keycloak and direct OSSPID
  static buildKeycloakAuthUrl(idpHint = null) {
    const url = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/auth`;
    
    const params = {
      response_type: 'code',
      scope: 'openid email',
      client_id: config.keycloak.clientId,
      redirect_uri: config.keycloak.redirectUrl,
    };
    
    if (idpHint) {
      params.kc_idp_hint = idpHint;
    }
    
    return `${url}?${querystring.stringify(params)}`;
  }

  // UATID flow removed: application supports Keycloak and direct OSSPID only.

  /**
   * Build direct OSSPID authorization URL
   * @param {object} session - Express session object
   * @returns {string} Authorization URL
   */
  static buildDirectOsspidAuthUrl(session) {
    const state = crypto.randomBytes(16).toString('hex');
    session.oauth2_state = state;
    
    const url = `${config.osspid.host}/osspid-client/openid/v2/authorize`;
    
    const params = {
      client_id: config.osspid.clientId,
      redirect_uri: config.osspid.redirectUrl,
      response_type: 'code',
      scope: 'openid',
      state: state,
    };
    
    return `${url}?${querystring.stringify(params)}`;
  }

  /**
   * Build logout URL
  * @param {string} loginType - Type of login (keycloak, osspid_direct)
   * @param {string|null} idToken - ID token for logout hint
   * @param {string} redirectUri - Post-logout redirect URI
   * @returns {string} Logout URL
   */
  static buildLogoutUrl(loginType, idToken = null, redirectUri) {
    let logoutUrl;
    let params = {
      post_logout_redirect_uri: redirectUri,
    };
    
    switch (loginType) {
      case 'keycloak':
        logoutUrl = `${config.keycloak.host}/realms/${config.keycloak.realm}/protocol/openid-connect/logout`;
        params.client_id = config.keycloak.clientId;
        break;
      case 'osspid_direct':
        logoutUrl = `${config.osspid.host}/osspid-client/openid/v2/logout`;
        params.client_id = config.osspid.clientId;
        break;
      default:
        // Default to OSSPID direct logout when no specific provider is set
        logoutUrl = `${config.osspid.host}/osspid-client/openid/v2/logout`;
        params.client_id = config.osspid.clientId;
    }
    
    if (idToken) {
      params.id_token_hint = idToken;
    }
    
    return `${logoutUrl}?${querystring.stringify(params)}`;
  }

  /**
   * Build IDP logout URL
   * Note: brokered IDP logout flow removed; default to OSSPID logout redirect
   * @param {string} redirectUri - Final redirect URI after IDP logout
   * @returns {string} IDP logout URL
   */
  static buildIdpLogoutUrl(redirectUri) {
    return `${config.osspid.host}/osspid-client/openid/v2/logout?${querystring.stringify({ redirect_uri: redirectUri })}`;
  }
}

module.exports = AuthUrlBuilder;
