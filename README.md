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

3. Configure environment variables:
```bash
# Copy the example environment file
cp .env.example .env

# Edit .env file with your configuration
nano .env  # or use your preferred editor
```

4. Update the `.env` file with your actual configuration values:
   - Server host and port
   - Keycloak credentials and endpoints
   - Direct OSSPID credentials
   - UATID credentials
   - Session secret (important for production!)

## Running the Application

Start the application in production mode:
```bash
npm start
```

Or in development mode with auto-reload:
```bash
npm run dev
```

The application will be available at: `http://[SERVER_HOST]:[SERVER_PORT]/`

## Configuration

The application uses environment variables for configuration. Copy `.env.example` to `.env` and update the values:

### Environment Variables

#### Server Configuration
- `SERVER_HOST` - Server hostname (e.g., `192.168.0.108`)
- `SERVER_PORT` - Server port (e.g., `8010`)
- `APP_URL` - Full application URL (e.g., `http://192.168.0.108:8010`)

#### Keycloak Configuration
- `KEYCLOAK_HOST` - Keycloak server URL
- `KEYCLOAK_REALM` - Keycloak realm name
- `KEYCLOAK_CLIENT_ID` - Client ID
- `KEYCLOAK_CLIENT_SECRET` - Client secret
- `KEYCLOAK_IDP` - Default identity provider

#### Direct OSSPID Configuration
- `OSSPID_HOST` - OSSPID server URL
- `OSSPID_CLIENT_ID` - OSSPID client ID
- `OSSPID_CLIENT_SECRET` - OSSPID client secret

#### UATID Configuration
- `UATID_HOST` - UATID Keycloak host (e.g., `https://idpv2.oss.net.bd`)
- `UATID_REALM` - UATID realm name
- `UATID_CLIENT_ID` - UATID client ID
- `UATID_CLIENT_SECRET` - UATID client secret
- `UATID_IDP_HINT` - Identity provider hint

#### Session Configuration
- `SESSION_SECRET` - Secret key for session encryption (change in production!)
- `SESSION_SECURE` - Set to `true` for HTTPS (production)

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
├── config.js               # Configuration loader (reads from .env)
├── .env                    # Environment variables (not in git)
├── .env.example            # Example environment variables
├── src/
│   └── routes/
│       ├── authRoutes.js   # Authentication routes
│       └── webRoutes.js    # Web/UI routes
├── package.json            # Dependencies and scripts
└── README.md              # This file
```

## Security Notes

- Never commit `.env` file to version control
- Always change `SESSION_SECRET` in production
- Use HTTPS in production and set `SESSION_SECURE=true`
- Keep client secrets confidential

## License

MIT License

## Author

Shamim Shakir
