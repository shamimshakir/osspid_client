# 🐳 Docker Deployment Guide

## ✅ Quick Start (3 Methods)

### Method 1: Using Helper Scripts (Easiest)

**Windows:**
```cmd
docker.bat up          # Start application
docker.bat logs        # View logs
docker.bat status      # Check status
docker.bat down        # Stop application
```

**Linux/Mac/Git Bash:**
```bash
./docker.sh up         # Start application
./docker.sh logs       # View logs
./docker.sh status     # Check status
./docker.sh down       # Stop application
```

### Method 2: Using docker-compose

```bash
# Start the application
docker-compose up -d

# View logs
docker-compose logs -f

# Check status
docker ps

# Stop the application
docker-compose down
```

### Method 3: Using npm Scripts

```bash
# Build Docker image
npm run docker:build

# Run container
npm run docker:run

# View logs
npm run docker:logs

# Stop container
npm run docker:stop

# Using docker-compose
npm run docker:compose:up
npm run docker:compose:logs
npm run docker:compose:down
```

## 📋 Current Status

Your application is **running** in Docker! ✅

- **Container Name:** `osspid-client`
- **Port:** 3000
- **URL:** http://localhost:3000
- **Health Check:** http://localhost:3000/health

## 🔧 Available Commands

| Command | Description |
|---------|-------------|
| `docker.bat up` / `./docker.sh up` | Start containers |
| `docker.bat down` / `./docker.sh down` | Stop containers |
| `docker.bat logs` / `./docker.sh logs` | View logs |
| `docker.bat status` / `./docker.sh status` | Check status & health |
| `docker.bat restart` / `./docker.sh restart` | Restart containers |
| `docker.bat shell` / `./docker.sh shell` | Open shell in container |
| `docker.bat clean` / `./docker.sh clean` | Remove everything |

## 🌐 Accessing the Application

Once running, access:
- **Home Page:** http://localhost:3000/
- **Health Check:** http://localhost:3000/health
- **OSSPID Login:** http://localhost:3000/osspid-login
- **Direct OSSPID:** http://localhost:3000/osspid-direct-login
- **UATID Login:** http://localhost:3000/uatid-login

## ⚙️ Configuration

Environment variables are configured in `docker-compose.yml`. Key settings:

```yaml
SERVER_HOST=0.0.0.0
SERVER_PORT=3000
APP_URL=http://localhost:3000
NODE_ENV=production

KEYCLOAK_HOST=http://192.168.0.108:8880
KEYCLOAK_REALM=myrealm
KEYCLOAK_CLIENT_ID=ss-client

OSSPID_HOST=http://192.168.0.108:8050
UATID_HOST=https://idpv2.oss.net.bd
```

To modify, edit `docker-compose.yml` and restart:
```bash
docker-compose down
docker-compose up -d
```

## 🐛 Troubleshooting

### Container won't start
```bash
docker-compose logs osspid-client
```

### Check container health
```bash
docker inspect --format='{{.State.Health.Status}}' osspid-client
```

### Access container shell
```bash
docker exec -it osspid-client sh
```

### Rebuild after code changes
```bash
docker-compose down
docker-compose up -d --build
```

## 📊 Monitoring

### View real-time logs
```bash
docker-compose logs -f
```

### Check resource usage
```bash
docker stats osspid-client
```

### Inspect container
```bash
docker inspect osspid-client
```

## 🚀 Production Deployment

For production, consider:

1. **Use a reverse proxy** (Nginx/Traefik) for SSL/TLS
2. **Set strong `SESSION_SECRET`** in environment variables
3. **Enable `SESSION_SECURE=true`** when using HTTPS
4. **Use external session store** (Redis) instead of MemoryStore
5. **Set up log aggregation** (ELK, Loki)
6. **Configure monitoring** and alerts
7. **Use Docker Swarm or Kubernetes** for orchestration

## 📝 Notes

- The container uses Node.js 18 Alpine (lightweight)
- Health checks run every 30 seconds
- Logs are persisted in `./logs` directory
- Container automatically restarts on failure
