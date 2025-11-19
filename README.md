# OSSPID Client

A PHP-based OAuth2/OpenID Connect client application that demonstrates authentication integration with both Keycloak and direct OSSPID server.

## Features

- 🔐 **Multiple Authentication Methods**:
  - Direct OSSPID login (bypassing Keycloak)
  - OSSPID login via Keycloak
  - BanglaBizz login via Keycloak
  - HelloApp login via Keycloak
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

- **Framework**: [Slim Framework 4](https://www.slimframework.com/)
- **OAuth2**: OAuth2/OpenID Connect
- **PHP Version**: 7.4+

## Installation

1. Clone the repository:
```bash
git clone https://github.com/shamimshakir/osspid_client.git
cd osspid_client
```

2. Install dependencies using Composer:
```bash
composer install
```

3. Configure your settings in `src/Routes/AuthRoutes.php`:
   - Update Keycloak configuration (KHOST, KRealms, etc.)
   - Update Direct OSSPID configuration (OSSPID_HOST, OSSPID_CLIENT_ID, etc.)

## Running the Application

Start the built-in PHP server:
```bash
composer serve
```

Or manually:
```bash
php -S 192.168.30.56:8010 -t public/
```

The application will be available at: `http://192.168.30.56:8010/`

## Configuration

### Keycloak Configuration
- **Host**: `http://192.168.30.56:8880`
- **Realm**: `myrealm`
- **Callback URL**: `http://192.168.30.56:8010/keycloak/callback`

### Direct OSSPID Configuration
- **Host**: `http://192.168.30.56:8050`
- **Client Name**: `osspid-client-direct`
- **Callback URL**: `http://192.168.30.56:8010/osspid-direct/callback`
- **Scopes**: `openid profile email`

## Routes

### Authentication Routes
- `/osspid-direct-login` - Direct OSSPID authentication
- `/osspid-login` - OSSPID via Keycloak
- `/banglabiz-login` - BanglaBizz via Keycloak
- `/helloapp-login` - HelloApp via Keycloak
- `/all-login` - View all available providers
- `/logout` - Logout from current session

### Callback Routes
- `/osspid-direct/callback` - Direct OSSPID callback handler
- `/keycloak/callback` - Keycloak callback handler

## Project Structure

```
osspid_client/
├── public/
│   └── index.php           # Application entry point
├── src/
│   └── Routes/
│       ├── AuthRoutes.php  # Authentication routes
│       └── WebRoutes.php   # Web/UI routes
├── composer.json           # Dependencies
└── README.md              # This file
```

## License

MIT License

## Author

Shamim Shakir
