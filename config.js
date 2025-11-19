/**
 * Application Configuration
 * Contains all OAuth2/OpenID Connect and application settings
 */

module.exports = {
  // Server Configuration
  server: {
    host: '192.168.0.108',
    port: 8010
  },

  // Keycloak Configuration
  keycloak: {
    host: 'http://192.168.0.108:8880',
    realm: 'myrealm',
    clientId: 'ss-client',
    clientSecret: '2QvtLNIDHLlvAMHsAMqwoba0AfF091Cu',
    redirectUrl: 'http://192.168.0.108:8010/keycloak/callback',
    idp: 'osspid'
  },

  // Direct OSSPID Configuration
  osspid: {
    host: 'http://192.168.0.108:8050',
    clientId: '2520c78a5763a4ca5154224a38da6faf2cbfd4ec',
    clientSecret: '3e5ea9864ed71e05727a08611a1b1357b01f2f70',
    redirectUrl: 'http://192.168.0.108:8010/osspid-direct/callback'
  },

  // UATID Configuration (via Keycloak - separate realm and client)
  uatid: {
    realm: 'osspid',
    clientId: 'id-client-local',
    clientSecret: 'cxYiKNaFMNS7gIjTiRYUipjYeE1fD5oJ',
    redirectUrl: 'http://192.168.0.108:8010/uatid/callback',
    idpHint: 'uatid'
  },

  // Session Configuration
  session: {
    secret: 'your-secret-key-change-this-in-production',
    resave: false,
    saveUninitialized: false,
    cookie: {
      secure: false, // Set to true in production with HTTPS
      httpOnly: true,
      maxAge: 24 * 60 * 60 * 1000 // 24 hours
    }
  }
};
