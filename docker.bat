@echo off
REM Docker Helper Script for Windows

if "%1"=="build" (
    echo 🐳 Building Docker image...
    docker build -t osspid-client:latest .
    echo ✅ Build complete!
    goto :eof
)

if "%1"=="up" (
    echo 🚀 Starting containers...
    docker-compose up -d
    echo ✅ Containers started!
    echo 🌐 Application running at: http://localhost:3000
    goto :eof
)

if "%1"=="down" (
    echo 🛑 Stopping containers...
    docker-compose down
    echo ✅ Containers stopped!
    goto :eof
)

if "%1"=="logs" (
    echo 📋 Viewing logs (Ctrl+C to exit)...
    docker-compose logs -f
    goto :eof
)

if "%1"=="status" (
    echo 📊 Container Status:
    docker ps --filter "name=osspid-client"
    echo.
    echo 🏥 Health Check:
    curl -s http://localhost:3000/health
    goto :eof
)

if "%1"=="restart" (
    echo 🔄 Restarting containers...
    docker-compose restart
    echo ✅ Containers restarted!
    goto :eof
)

if "%1"=="clean" (
    echo 🧹 Cleaning up...
    docker-compose down
    docker rmi osspid-client:latest 2>nul
    echo ✅ Cleanup complete!
    goto :eof
)

if "%1"=="shell" (
    echo 🐚 Opening shell in container...
    docker exec -it osspid-client sh
    goto :eof
)

echo 🐳 Docker Helper Script
echo.
echo Usage: docker.bat [command]
echo.
echo Commands:
echo   build   - Build Docker image
echo   up      - Start containers
echo   down    - Stop containers
echo   logs    - View logs
echo   status  - Check status
echo   restart - Restart containers
echo   clean   - Stop and remove everything
echo   shell   - Open shell in container
echo.
