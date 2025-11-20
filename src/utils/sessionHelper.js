/**
 * Session Helper Functions
 */

/**
 * Store authentication data in session
 * @param {object} session - Express session
 * @param {string} loginType - Type of login
 * @param {object} tokens - Token object {access_token, refresh_token, id_token}
 * @param {object} userData - User data object
 */
function storeAuthData(session, loginType, tokens, userData) {
  session.login_type = loginType;
  session.refresh_token = tokens.refresh_token;
  session.id_token = tokens.id_token;
  
  if (tokens.access_token && loginType === 'osspid_direct') {
    session.access_token = tokens.access_token;
  }
  
  session.user = userData;
}

/**
 * Clear authentication data from session
 * @param {object} session - Express session
 * @returns {Promise<void>}
 */
function clearAuthData(session) {
  return new Promise((resolve, reject) => {
    session.destroy((err) => {
      if (err) {
        reject(err);
      } else {
        resolve();
      }
    });
  });
}

/**
 * Verify OAuth2 state parameter for CSRF protection
 * @param {object} session - Express session
 * @param {string} state - State parameter from callback
 * @returns {boolean} True if valid, false otherwise
 */
function verifyState(session, state) {
  const isValid = session.oauth2_state && state === session.oauth2_state;
  delete session.oauth2_state;
  return isValid;
}

module.exports = {
  storeAuthData,
  clearAuthData,
  verifyState
};
