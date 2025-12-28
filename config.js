/**
 * Application Configuration
 * Contains all OAuth2/OpenID Connect and application settings
 */

require('dotenv').config();

module.exports = {
  // Server Configuration
  server: {
    host: process.env.SERVER_HOST || '0.0.0.0',
    port: parseInt(process.env.SERVER_PORT) || 8000,
    appUrl: process.env.APP_URL || 'http://localhost:8000'
  },

  // Keycloak Configuration (used for OSSPID via Keycloak)
  keycloak: {
    host: process.env.KEYCLOAK_HOST || 'http://192.168.0.108:8880',
    realm: process.env.KEYCLOAK_REALM || 'myrealm',
    clientId: process.env.KEYCLOAK_CLIENT_ID || 'ss-client',
    clientSecret: process.env.KEYCLOAK_CLIENT_SECRET || '2QvtLNIDHLlvAMHsAMqwoba0AfF091Cu',
    redirectUrl: process.env.APP_URL ? `${process.env.APP_URL}/keycloak/callback` : 'http://localhost:3000/keycloak/callback',
    idp: process.env.KEYCLOAK_IDP || 'osspid'
  },

  // Direct OSSPID Configuration
  osspid: {
    host: process.env.OSSPID_HOST || 'http://192.168.0.108:8050',
    clientId: process.env.OSSPID_CLIENT_ID || '2520c78a5763a4ca5154224a38da6faf2cbfd4ec',
    clientSecret: process.env.OSSPID_CLIENT_SECRET || '3e5ea9864ed71e05727a08611a1b1357b01f2f70',
    redirectUrl: process.env.APP_URL ? `${process.env.APP_URL}/osspid-direct/callback` : 'http://localhost:3000/osspid-direct/callback'
  },


  // Session Configuration
  session: {
    secret: process.env.SESSION_SECRET || 'your-secret-key-change-this-in-production',
    resave: false,
    saveUninitialized: false,
    cookie: {
      secure: process.env.SESSION_SECURE === 'true', // Set to true in production with HTTPS
      httpOnly: true,
      maxAge: 24 * 60 * 60 * 1000 // 24 hours
    }
  }
};
