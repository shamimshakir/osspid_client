# 🐳 OSSPID Client - Docker Deployment Guide

## 📋 Prerequisites

Before you begin, make sure you have:

- ✅ **Docker Desktop** installed ([Download](https://www.docker.com/products/docker-desktop/))
- ✅ **Docker Compose** installed (included with Docker Desktop)
- ✅ **Git Bash** (for Windows) or Terminal (for Linux/Mac)

### Verify Installation:

```bash
# Check Docker version
docker --version
# Output: Docker version 24.x.x or higher

# Check Docker Compose version
docker-compose --version
# Output: Docker Compose version v2.x.x or higher
```

---

## 🚀 Step-by-Step Guide to Run the Application

### **Step 1: Clone or Navigate to the Project**

```bash
# If you haven't cloned the repository yet:
git clone https://github.com/shamimshakir/osspid_client.git
cd osspid_client

# If you already have the project:
cd /path/to/osspid_client
```

---

### **Step 2: Review the Configuration (Optional)**

The application is pre-configured with default values in `docker-compose.yml`. You can review or modify the environment variables:

```bash
# Open docker-compose.yml in any text editor
notepad docker-compose.yml    # Windows
nano docker-compose.yml       # Linux/Mac
```

**Default Configuration:**
- **Port:** 3000
- **Keycloak Host:** http://192.168.0.108:8880
- **OSSPID Host:** http://192.168.0.108:8050
- **UATID Host:** https://idpv2.oss.net.bd

---

### **Step 3: Build the Docker Image**

```bash
# Build the Docker image (this may take 1-2 minutes)
docker-compose build

# You should see output like:
# [+] Building 45.2s (12/12) FINISHED
# => => naming to docker.io/library/osspid_client-osspid-client:latest
```

**What this does:**
- Downloads Node.js 18 Alpine base image
- Installs application dependencies
- Copies your application code into the container
- Creates an optimized production-ready image

---

### **Step 4: Start the Application**

```bash
# Start the container in detached mode (background)
docker-compose up -d

# You should see output like:
# [+] Running 2/2
# ✔ Network osspid_client_osspid-network  Created
# ✔ Container osspid-client                Started
```

**What happens:**
- Docker creates a network for the container
- Starts the `osspid-client` container
- Application runs on port 3000
- Health checks begin automatically

---

### **Step 5: Verify the Application is Running**

#### **Method 1: Check Container Status**

```bash
# View running containers
docker ps

# You should see:
# CONTAINER ID   IMAGE                         STATUS        PORTS
# xxxxxxxxxxxx   osspid_client-osspid-client   Up 10 seconds 0.0.0.0:3000->3000/tcp
```

#### **Method 2: Check Health**

```bash
# Test the health endpoint
curl http://localhost:3000/health

# Expected output:
# {"status":"healthy","timestamp":"2025-11-20T09:00:00.000Z","uptime":10.5}
```

#### **Method 3: View Logs**

```bash
# View application logs
docker-compose logs -f

# You should see:
# 🚀 OSSPID Client server running at http://0.0.0.0:3000/
# 📝 Environment: production
# 📝 Available routes:
#    - http://0.0.0.0:3000/ (Home)
#    - http://0.0.0.0:3000/health (Health Check)
# ...

# Press Ctrl+C to exit log viewing
```

---

### **Step 6: Access the Application**

Open your web browser and navigate to:

🌐 **http://localhost:3000**

You should see the **ShakirAuth Hub** login page with these options:
- 🚀 Direct OSSPID Login
- Login with OSSPID (via Keycloak)
- Login with UATID
- Keycloak IDP Login

---

### **Step 7: Test the Application**

1. **Click on any login option** (e.g., "Direct OSSPID Login")
2. **You'll be redirected** to the authentication provider
3. **Enter credentials** and authenticate
4. **You'll be redirected back** to the application
5. **See your user information** displayed on the dashboard

---

## 🛠️ Common Commands

### **View Logs in Real-Time**

```bash
docker-compose logs -f
# Or using helper script:
./docker.sh logs        # Linux/Mac/Git Bash
docker.bat logs         # Windows CMD
```

### **Stop the Application**

```bash
docker-compose down
# Or using helper script:
./docker.sh down        # Linux/Mac/Git Bash
docker.bat down         # Windows CMD
```

### **Restart the Application**

```bash
docker-compose restart
# Or using helper script:
./docker.sh restart     # Linux/Mac/Git Bash
docker.bat restart      # Windows CMD
```

### **Check Container Status**

```bash
docker ps --filter "name=osspid-client"
# Or using helper script:
./docker.sh status      # Linux/Mac/Git Bash
docker.bat status       # Windows CMD
```

### **Rebuild After Code Changes**

```bash
# Stop, rebuild, and restart
docker-compose down
docker-compose up -d --build
```

### **Access Container Shell**

```bash
docker exec -it osspid-client sh
# Or using helper script:
./docker.sh shell       # Linux/Mac/Git Bash
docker.bat shell        # Windows CMD
```

---

## 🔧 Troubleshooting

### **Issue 1: Port 3000 is already in use**

**Error:**
```
Error starting userland proxy: listen tcp4 0.0.0.0:3000: bind: address already in use
```

**Solution:**
```bash
# Option A: Stop the process using port 3000
# Windows:
netstat -ano | findstr :3000
taskkill /PID <PID_NUMBER> /F

# Linux/Mac:
lsof -ti:3000 | xargs kill -9

# Option B: Change the port in docker-compose.yml
# Edit: ports: - "8080:3000"  # Use port 8080 on host instead
```

---

### **Issue 2: Container exits immediately**

**Check logs:**
```bash
docker-compose logs osspid-client
```

**Common causes:**
- Missing dependencies
- Syntax errors in code
- Environment variable issues

**Solution:**
```bash
# Rebuild the image
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

---

### **Issue 3: Health check failing**

**Check health status:**
```bash
docker inspect --format='{{.State.Health.Status}}' osspid-client
```

**Test health endpoint manually:**
```bash
curl http://localhost:3000/health
```

**Solution:**
- Wait 10-30 seconds after starting (health checks have a start period)
- Check logs for errors
- Verify application is listening on port 3000

---

### **Issue 4: Cannot access external services (Keycloak, OSSPID)**

**Solution:**
- Verify the external services are running
- Check network connectivity from container:
  ```bash
  docker exec -it osspid-client ping 192.168.0.108
  ```
- Update environment variables in `docker-compose.yml` if hosts changed

---

## 🎓 Understanding the Docker Setup

### **What Files Were Created:**

```
osspid_client/
├── Dockerfile              # Instructions to build the image
├── docker-compose.yml      # Orchestration configuration
├── .dockerignore          # Files to exclude from build
├── docker.sh              # Helper script (Linux/Mac)
└── docker.bat             # Helper script (Windows)
```

### **How Configuration Works:**

1. **`docker-compose.yml`** sets environment variables:
   ```yaml
   environment:
     - SERVER_PORT=3000
     - SERVER_HOST=0.0.0.0
   ```

2. **`config.js`** reads these variables:
   ```javascript
   port: parseInt(process.env.SERVER_PORT) || 8010
   ```

3. **Docker overrides defaults**, so:
   - Without Docker: Runs on port **8010**
   - With Docker: Runs on port **3000**

---

## ✅ What Was Done

1. **Created Docker Configuration Files:**
   - `Dockerfile` - Container image definition
   - `docker-compose.yml` - Multi-container orchestration
   - `.dockerignore` - Exclude unnecessary files from build
   - `.env.example` - Environment variable template

2. **Added Health Check Endpoint:**
   - Route: `/health`
   - Returns: `{"status":"healthy","timestamp":"...","uptime":...}`
   - Used by Docker for container health monitoring

3. **Updated Application:**
   - Added `dotenv` configuration loading
   - Updated `config.js` to use environment variables
   - Added npm scripts for Docker commands
   - Created helper scripts (`docker.bat`, `docker.sh`)

4. **Documentation:**
   - `DOCKER.md` - Complete Docker deployment guide
   - `README-DOCKER.md` - This step-by-step guide

## 📚 Additional Resources

### **Helper Scripts**

For easier Docker management, use the provided scripts:

**Windows:**
```cmd
docker.bat build     # Build image
docker.bat up        # Start containers
docker.bat down      # Stop containers
docker.bat logs      # View logs
docker.bat status    # Check status
docker.bat restart   # Restart containers
docker.bat shell     # Open container shell
docker.bat clean     # Remove everything
```

**Linux/Mac/Git Bash:**
```bash
./docker.sh build    # Build image
./docker.sh up       # Start containers
./docker.sh down     # Stop containers
./docker.sh logs     # View logs
./docker.sh status   # Check status
./docker.sh restart  # Restart containers
./docker.sh shell    # Open container shell
./docker.sh clean    # Remove everything
```

---

## 🌟 Next Steps

### **For Development Without Docker:**

```bash
# Install dependencies
npm install

# Run without Docker (uses port 8010)
npm start

# Run with auto-reload
npm run dev
```

### **For Production Deployment:**

1. **Security:**
   - Set strong `SESSION_SECRET` in `docker-compose.yml`
   - Enable HTTPS/SSL
   - Use `SESSION_SECURE=true` for HTTPS

2. **Scalability:**
   - Use external session store (Redis)
   - Set up load balancing
   - Use Docker Swarm or Kubernetes

3. **Monitoring:**
   - Set up log aggregation (ELK, Loki)
   - Configure application monitoring (Prometheus, Grafana)
   - Set up alerts for health check failures

4. **Backup:**
   - Regular container backups
   - Database backups if using one
   - Configuration file version control

---

## 🎓 Understanding Docker Concepts

### **Key Terms:**

- **Dockerfile** = Recipe for building an image (like a cooking recipe)
- **Image** = Blueprint for containers (like a template)
- **Container** = Running instance of an image (like a running program)
- **docker-compose** = Tool to manage multiple containers (like an orchestrator)

### **Your Application Flow:**

```
User Browser → Docker Container → Express Server → Routes → Services → External APIs
                                                            ↓
                                                        Response
```

### **Port Mapping Explained:**

```yaml
ports:
  - "3000:3000"
    ↑      ↑
    │      └── Container port (inside Docker)
    └────────── Host port (your computer)
```

This means:
- Access from **your computer**: http://localhost:3000
- Inside **container**: Application listens on 0.0.0.0:3000

---

## 📞 Support & Documentation

- **Docker Documentation:** https://docs.docker.com/
- **Node.js Documentation:** https://nodejs.org/docs/
- **Express.js Documentation:** https://expressjs.com/
- **Project Repository:** https://github.com/shamimshakir/osspid_client

---

## ✅ Quick Reference Card

```bash
# START APPLICATION
docker-compose up -d

# VIEW LOGS
docker-compose logs -f

# STOP APPLICATION
docker-compose down

# RESTART APPLICATION
docker-compose restart

# REBUILD (after code changes)
docker-compose down && docker-compose up -d --build

# CHECK STATUS
docker ps

# TEST HEALTH
curl http://localhost:3000/health

# ACCESS APPLICATION
http://localhost:3000
```

---

**🎉 Congratulations! Your OSSPID Client is now running with Docker! 🐳**

If you followed all steps, you should now have:
- ✅ Application running on http://localhost:3000
- ✅ Health checks passing
- ✅ All authentication routes working
- ✅ Production-ready Docker setup

**Happy coding! 🚀**
