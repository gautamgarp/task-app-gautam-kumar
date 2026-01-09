#!/bin/bash

# Docker Setup Helper Script for Task Management Application

set -e

echo "🐳 Task Management App - Docker Setup"
echo "======================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo -e "${YELLOW}❌ Docker is not installed. Please install Docker first.${NC}"
    exit 1
fi

# Docker Compose v2 is included with Docker Desktop, no separate check needed
# If using older docker-compose command, update it manually

# Step 1: Copy .env if it doesn't exist
if [ ! -f .env ]; then
    echo -e "${BLUE}📝 Creating .env file from .env.example...${NC}"
    cp .env.example .env
    echo -e "${GREEN}✓ .env file created${NC}"
else
    echo -e "${GREEN}✓ .env file already exists${NC}"
fi

# Step 2: Build and start containers
echo ""
echo -e "${BLUE}🏗️  Building Docker images (this may take a few minutes)...${NC}"
docker compose up --build -d

echo -e "${GREEN}✓ Containers built and started${NC}"

# Wait for MySQL to be healthy
echo ""
echo -e "${BLUE}⏳ Waiting for MySQL to be ready...${NC}"
for i in {1..30}; do
    if docker compose exec -T mysql mysqladmin ping -h localhost &> /dev/null; then
        echo -e "${GREEN}✓ MySQL is ready${NC}"
        break
    fi
    echo "  Attempt $i/30... waiting for MySQL"
    sleep 2
done

# Step 3: Run migrations
echo ""
echo -e "${BLUE}📦 Running Laravel migrations...${NC}"
docker compose exec -T backend php artisan migrate --force

echo -e "${GREEN}✓ Migrations completed${NC}"

# Step 4: Run seeders
echo ""
echo -e "${BLUE}🌱 Seeding database...${NC}"
docker compose exec -T backend php artisan db:seed --force

echo -e "${GREEN}✓ Database seeded${NC}"

# Step 5: Generate application key
echo ""
echo -e "${BLUE}🔑 Generating application key...${NC}"
docker compose exec -T backend php artisan key:generate

echo -e "${GREEN}✓ Application key generated${NC}"

# Step 6: Verify services
echo ""
echo -e "${BLUE}✅ Verifying services...${NC}"
docker compose ps

# Display access information
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}✨ Setup Complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}Access your application at:${NC}"
echo -e "  ${GREEN}Frontend:${NC} http://localhost:8080"
echo -e "  ${GREEN}API:${NC} http://localhost:8080/api/tasks"
echo ""
echo -e "${BLUE}Useful commands:${NC}"
echo "  docker compose logs -f backend    # View Laravel logs"
echo "  docker compose logs -f web        # View Nginx logs"
echo "  docker compose exec backend php artisan tinker  # Laravel REPL"
echo "  docker compose exec backend php artisan test    # Run tests"
echo "  docker compose down               # Stop all containers"
echo ""
