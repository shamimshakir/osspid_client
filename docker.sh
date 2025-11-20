#!/bin/bash
# Docker Helper Script for Windows Git Bash

case "$1" in
  build)
    echo "🐳 Building Docker image..."
    docker build -t osspid-client:latest .
    echo "✅ Build complete!"
    ;;
  up)
    echo "🚀 Starting containers..."
    docker-compose up -d
    echo "✅ Containers started!"
    echo "🌐 Application running at: http://localhost:3000"
    ;;
  down)
    echo "🛑 Stopping containers..."
    docker-compose down
    echo "✅ Containers stopped!"
    ;;
  logs)
    echo "📋 Viewing logs (Ctrl+C to exit)..."
    docker-compose logs -f
    ;;
  status)
    echo "📊 Container Status:"
    docker ps --filter "name=osspid-client"
    echo ""
    echo "🏥 Health Check:"
    curl -s http://localhost:3000/health | json_pp 2>/dev/null || curl -s http://localhost:3000/health
    ;;
  restart)
    echo "🔄 Restarting containers..."
    docker-compose restart
    echo "✅ Containers restarted!"
    ;;
  clean)
    echo "🧹 Cleaning up..."
    docker-compose down
    docker rmi osspid-client:latest 2>/dev/null
    echo "✅ Cleanup complete!"
    ;;
  shell)
    echo "🐚 Opening shell in container..."
    docker exec -it osspid-client sh
    ;;
  *)
    echo "🐳 Docker Helper Script"
    echo ""
    echo "Usage: ./docker.sh [command]"
    echo ""
    echo "Commands:"
    echo "  build   - Build Docker image"
    echo "  up      - Start containers"
    echo "  down    - Stop containers"
    echo "  logs    - View logs"
    echo "  status  - Check status"
    echo "  restart - Restart containers"
    echo "  clean   - Stop and remove everything"
    echo "  shell   - Open shell in container"
    echo ""
    ;;
esac
