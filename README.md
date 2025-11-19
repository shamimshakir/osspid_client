# OSSPID Client

A Node.js/Express-based OAuth2/OpenID Connect client application that demonstrates authentication integration with both Keycloak and direct OSSPID server.

## Features

- 🔐 **Multiple Authentication Methods**:
  - Direct OSSPID login (bypassing Keycloak)
  - OSSPID login via Keycloak
  - BanglaBizz login via Keycloak
  - HelloApp login via Keycloak
  - UATID login via Keycloak
  - All providers view via Keycloak

- ✨ **Security Features**:
  - OAuth2 Authorization Code Flow
  - CSRF protection with state parameter
  - Secure session management
  - Proper logout handling for both Keycloak and OSSPID

- 🎨 **Modern UI**:
  - Responsive design
  - Beautiful gradient buttons with hover effects
  - Clean user dashboard

## Technology Stack

- **Runtime**: Node.js
- **Framework**: [Express.js](https://expressjs.com/)
- **OAuth2**: OAuth2/OpenID Connect
- **HTTP Client**: Axios
- **Session Management**: express-session
- **Node Version**: 14.x or higher

## Installation

1. Clone the repository:
```bash
git clone https://github.com/shamimshakir/osspid_client.git
cd osspid_client
```

2. Install dependencies using npm:
```bash
npm install
```

3. Configure your settings in `config.js`:
   - Update Keycloak configuration (host, realm, clientId, clientSecret)
   - Update Direct OSSPID configuration (host, clientId, clientSecret)
   - Update UATID configuration
   - Update session secret

## Running the Application

Start the application in production mode:
```bash
npm start
```

Or in development mode with auto-reload:
```bash
npm run dev
```

The application will be available at: `http://192.168.30.56:8010/`

## Configuration

Edit `config.js` to configure the application:

### Keycloak Configuration
- **Host**: `http://192.168.30.56:8880`
- **Realm**: `myrealm`
- **Client ID**: `ss-client`
- **Callback URL**: `http://192.168.30.56:8010/keycloak/callback`

### Direct OSSPID Configuration
- **Host**: `http://192.168.30.56:8050`
- **Client ID**: `2520c78a5763a4ca5154224a38da6faf2cbfd4ec`
- **Callback URL**: `http://192.168.30.56:8010/osspid-direct/callback`
- **Scopes**: `openid profile email`

### UATID Configuration
- **Realm**: `uat-id-realm`
- **Client ID**: `uat-id-client`
- **Callback URL**: `http://192.168.30.56:8010/uatid/callback`

## Routes

### Authentication Routes
- `/osspid-direct-login` - Direct OSSPID authentication
- `/osspid-login` - OSSPID via Keycloak
- `/banglabiz-login` - BanglaBizz via Keycloak
- `/helloapp-login` - HelloApp via Keycloak
- `/uatid-login` - UATID via Keycloak
- `/all-login` - View all available providers
- `/logout` - Logout from current session

### Callback Routes
- `/osspid-direct/callback` - Direct OSSPID callback handler
- `/keycloak/callback` - Keycloak callback handler
- `/uatid/callback` - UATID callback handler

### API Routes
- `/api/status` - API status endpoint
- `/check-uatid-config` - Check UATID OpenID configuration

## Project Structure

```
osspid_client/
├── index.js                # Application entry point
├── config.js               # Configuration settings
├── src/
│   └── routes/
│       ├── authRoutes.js   # Authentication routes
│       └── webRoutes.js    # Web/UI routes
├── package.json            # Dependencies and scripts
└── README.md              # This file
```

## License

MIT License

## Author

Shamim Shakir
