# 🔐 OSSPID Client

A Node.js/Express-based OAuth2/OpenID Connect client application that demonstrates authentication integration with OSSPID (direct and via Keycloak). **Runs with Docker for convenient deployment.**

## ✨ Features

- 🔐 **Multiple Authentication Methods**:
- 🔐 **Multiple Authentication Methods**:
  - Direct OSSPID login (bypassing an external broker)
  - Keycloak login (OSSPID via Keycloak)

- 🐳 **Docker-Ready**:
  - Production-optimized Docker setup
  - Health checks built-in
  - Easy deployment and scaling
  - Consistent environment across all platforms

- ✨ **Security Features**:
  - OAuth2 Authorization Code Flow
  - CSRF protection with state parameter
  - Secure session management
  - Proper logout handling for both Keycloak and OSSPID

- 🎨 **Modern UI**:
  - Responsive design
  - Beautiful gradient buttons with hover effects
  - Clean user dashboard

## 🛠️ Technology Stack

- **Runtime**: Node.js 18
- **Framework**: Express.js
- **OAuth2**: OAuth2/OpenID Connect
- **HTTP Client**: Axios
- **Session Management**: express-session
- **Containerization**: Docker & Docker Compose

## 🚀 Quick Start (Docker)

### Prerequisites

- Docker Desktop installed ([Download](https://www.docker.com/products/docker-desktop/))
- Docker Compose (included with Docker Desktop)

### Installation & Run

```bash
# 1. Clone the repository
git clone https://github.com/shamimshakir/osspid_client.git
cd osspid_client

# 2. Start the application with Docker
npm start
# or
docker-compose up -d

# 3. View logs
npm run logs
# or
docker-compose logs -f

# 4. Access the application
# Open browser: http://localhost:3000
```

That's it! The application is now running on **http://localhost:3000** 🎉

### Quick Commands

```bash
npm start          # Start application
npm stop           # Stop application
npm run logs       # View logs
npm run restart    # Restart application
npm run rebuild    # Rebuild and restart
npm run status     # Check container status
npm run shell      # Open container shell
```

## ⚙️ Configuration

Configuration is managed through environment variables in `docker-compose.yml`. The application comes pre-configured with default values.

### Default Configuration

- **Port**: 3000
- **Host**: 0.0.0.0 (accessible from all interfaces)
- **App URL**: http://localhost:3000

### Customizing Configuration

Edit `docker-compose.yml` to modify environment variables:

```yaml
environment:
  # Server Configuration
  - SERVER_PORT=3000
  - APP_URL=http://localhost:3000
  
  # Keycloak Configuration
  (Keycloak configuration present — this project uses Keycloak and direct OSSPID flows.)
  
  # OSSPID Configuration
  - OSSPID_HOST=http://192.168.0.108:8050
  - OSSPID_CLIENT_ID=your-client-id
  - OSSPID_CLIENT_SECRET=your-secret
  
  (UATID configuration removed)
  
  # Session Configuration
  - SESSION_SECRET=change-this-in-production
```

After editing, restart the application:
```bash
npm run rebuild
```

### Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| `SERVER_PORT` | Application port | `3000` |
| `SERVER_HOST` | Bind address | `0.0.0.0` |
| `APP_URL` | External URL | `http://localhost:3000` |
<!-- Keycloak variables removed -->
| `OSSPID_HOST` | OSSPID server URL | `http://192.168.0.108:8050` |
| `OSSPID_CLIENT_ID` | OSSPID client ID | - |
| `OSSPID_CLIENT_SECRET` | OSSPID client secret | - |
<!-- UATID variables removed -->
| `SESSION_SECRET` | Session encryption key | ⚠️ Change in production! |

## 🌐 Available Routes

### Home & Health
- **`/`** - Home page with login options
- **`/health`** - Health check endpoint (for Docker)
- **`/api/status`** - API status endpoint

### Authentication Routes
- **`/osspid-direct-login`** - Direct OSSPID authentication
- **`/keycloak-login`** - Keycloak login
- **`/osspid-direct-login`** - Direct OSSPID authentication
- **`/logout`** - Logout from current session

### Callback Routes
- **`/osspid-direct/callback`** - Direct OSSPID callback handler
- **`/keycloak/callback`** - Keycloak callback handler

## 📁 Project Structure

```
osspid_client/
├── index.js                      # Application entry point
├── config.js                     # Configuration (environment variables)
├── Dockerfile                    # Docker image definition
├── docker-compose.yml            # Docker orchestration
├── .dockerignore                 # Docker build exclusions
├── package.json                  # Dependencies and npm scripts
├── src/
│   ├── routes/
│   │   ├── authRoutes.js        # Authentication routes
│   │   └── webRoutes.js         # Web/UI routes
│   ├── services/
│   │   ├── authUrlBuilder.js    # OAuth URL construction
│   │   └── tokenService.js      # Token exchange & user info
│   ├── middleware/
│   │   └── errorHandler.js      # Error handling
│   └── utils/
│       ├── jwtUtils.js          # JWT decoding
│       └── sessionHelper.js     # Session management
├── README.md                     # This file
├── README-DOCKER.md              # Docker step-by-step guide
└── DOCKER.md                     # Complete Docker documentation
```

## 📚 Documentation

- **[README-DOCKER.md](README-DOCKER.md)** - Step-by-step Docker deployment guide
- **[DOCKER.md](DOCKER.md)** - Complete Docker reference and troubleshooting

## 🔒 Security Notes

- ⚠️ **Always change `SESSION_SECRET`** in production
- 🔐 Use HTTPS in production and set `SESSION_SECURE=true`
- 🔑 Keep client secrets confidential
- 🚫 Never expose sensitive environment variables
- 🔄 Rotate secrets regularly

## 🐛 Troubleshooting

### Container won't start
```bash
npm run logs
```

### Port 3000 already in use
```bash
# Stop existing containers
docker ps
docker stop <container-id>

# Or change port in docker-compose.yml
ports:
  - "8080:3000"  # Use 8080 on host
```

### Rebuild after code changes
```bash
npm run rebuild
```

### Access container shell
```bash
npm run shell
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

MIT License

## 👤 Author

**Shamim Shakir**
- GitHub: [@shamimshakir](https://github.com/shamimshakir)
- Repository: [osspid_client](https://github.com/shamimshakir/osspid_client)

---

**Made with ❤️ using Node.js, Express, and Docker**
