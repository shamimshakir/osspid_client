/**
 * OSSPID Client - Main Application Entry Point
 * Node.js/Express application for OAuth2/OpenID Connect authentication
 */

// Load environment variables from .env file
require('dotenv').config();

const express = require('express');
const session = require('express-session');
const config = require('./config');
const authRoutes = require('./src/routes/authRoutes');
const webRoutes = require('./src/routes/webRoutes');

// Create Express app
const app = express();

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Session middleware
app.use(session(config.session));

// Routes
app.use('/', webRoutes);
app.use('/', authRoutes);

// Error handling middleware
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(500).send(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>Error</title>
      <style>
        body { font-family: Arial, sans-serif; padding: 50px; background: #f5f5f5; }
        .error { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #d32f2f; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; }
      </style>
    </head>
    <body>
      <div class="error">
        <h1>Application Error</h1>
        <p>${err.message || 'An unexpected error occurred'}</p>
        ${process.env.NODE_ENV !== 'production' ? `<pre>${err.stack}</pre>` : ''}
        <p><a href="/">← Back to Home</a></p>
      </div>
    </body>
    </html>
  `);
});

// Start server
const { host, port } = config.server;
app.listen(port, host, () => {
  console.log(`🚀 OSSPID Client server running at http://${host}:${port}/`);
  console.log(`📝 Environment: ${process.env.NODE_ENV || 'development'}`);
  console.log(`📝 Available routes:`);
  console.log(`   - http://${host}:${port}/ (Home)`);
  console.log(`   - http://${host}:${port}/health (Health Check)`);
  console.log(`   - http://${host}:${port}/osspid-direct-login (Direct OSSPID)`);
  console.log(`   - http://${host}:${port}/oidc-login (OIDC login)`);
});

module.exports = app;
