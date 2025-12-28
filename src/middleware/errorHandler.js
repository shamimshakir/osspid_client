/**
 * Error Handler Middleware
 */

/**
 * OAuth error handler
 * @param {Error} error - Error object
 * @param {object} res - Express response object
 * @param {string} context - Error context (e.g., 'Keycloak')
 */
function handleOAuthError(error, res, context = 'Authentication') {
  console.error(`${context} Error:`, error.message);
  
  const errorMsg = error.response?.data?.error_description 
    || error.response?.data?.error 
    || error.message;
  
  res.status(500).send(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Authentication Error</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
        .error { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #d32f2f; }
        .error-msg { background: #ffebee; padding: 15px; border-radius: 4px; border-left: 4px solid #d32f2f; margin: 20px 0; }
        a { color: #1976d2; text-decoration: none; }
        a:hover { text-decoration: underline; }
      </style>
    </head>
    <body>
      <div class="error">
        <h1>${context} Error</h1>
        <div class="error-msg">${errorMsg}</div>
        <p><a href="/">← Back to Home</a></p>
      </div>
    </body>
    </html>
  `);
}

/**
 * Validate callback code parameter
 * @param {string|undefined} code - Authorization code
 * @param {object} res - Express response object
 * @returns {boolean} True if valid, false otherwise
 */
function validateCallbackCode(code, res) {
  if (!code) {
    res.status(400).send(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Invalid Callback</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
          .error { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
          h1 { color: #d32f2f; }
          a { color: #1976d2; text-decoration: none; }
          a:hover { text-decoration: underline; }
        </style>
      </head>
      <body>
        <div class="error">
          <h1>Invalid Callback</h1>
          <p>No authorization code received. Please try logging in again.</p>
          <p><a href="/">← Back to Home</a></p>
        </div>
      </body>
      </html>
    `);
    return false;
  }
  return true;
}

module.exports = {
  handleOAuthError,
  validateCallbackCode
};
