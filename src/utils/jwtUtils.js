/**
 * JWT Utility Functions
 */

/**
 * Decode JWT token without verification
 * @param {string} token - JWT token
 * @returns {object|null} Decoded payload or null
 */
function decodeJWT(token) {
  try {
    const parts = token.split('.');
    if (parts.length !== 3) {
      return null;
    }
    
    const payload = Buffer.from(
      parts[1].replace(/-/g, '+').replace(/_/g, '/'),
      'base64'
    ).toString();
    
    return JSON.parse(payload);
  } catch (err) {
    console.error('Error decoding JWT:', err);
    return null;
  }
}

module.exports = {
  decodeJWT
};
