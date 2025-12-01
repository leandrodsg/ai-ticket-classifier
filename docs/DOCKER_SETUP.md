# Docker Setup Guide

This guide provides detailed instructions for setting up the Docker environment for the Smart Support Classifier project.

## Prerequisites

- Docker Desktop installed and running
- Docker Compose V2 (included with Docker Desktop)
- At least 4GB of available RAM for containers

## Environment Configuration

Before starting the containers, ensure your `.env` file is properly configured:

```bash
# Copy the example file
cp .env.example .env

# Edit .env with your specific configuration
# Key settings for Docker:
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

## Starting the Containers

1. **Build and start all services:**
```bash
docker compose up -d
```

This will start the following services:
- **Laravel App** (Port 80)
- **MySQL** (Port 3306)
- **Redis** (Port 6379)
- **Mailpit** (Ports 1025/8025)

2. **Wait for containers to be healthy:**
```bash
docker compose ps
```

Look for "healthy" status for mysql and redis services.

## Testing the Setup

### 1. Application Access
- Open `http://localhost` in your browser
- You should see the Laravel welcome page

### 2. Database Connection
```bash
# Connect to MySQL container
docker compose exec mysql mysql -u sail -p laravel

# Test Redis connection
docker compose exec redis redis-cli ping
```

Expected responses:
- MySQL: `Welcome to the MySQL monitor...`
- Redis: `PONG`

### 3. Mail Testing
- Access Mailpit web interface at `http://localhost:8025`
- Send a test email from your application
- Check if it appears in Mailpit

## Useful Commands

### Container Management
```bash
# View running containers
docker compose ps

# View logs
docker compose logs

# View specific service logs
docker compose logs mysql
docker compose logs redis
docker compose logs laravel.test

# Stop containers
docker compose down

# Stop and remove volumes
docker compose down -v
```

### Laravel Artisan Commands
```bash
# Run artisan commands inside the container
docker compose exec laravel.test php artisan --version

# Generate application key
docker compose exec laravel.test php artisan key:generate

# Run migrations
docker compose exec laravel.test php artisan migrate

# Run tests
docker compose exec laravel.test php artisan test
```

### Database Operations
```bash
# Access MySQL shell
docker compose exec mysql mysql -u sail -p laravel

# Create a backup
docker compose exec mysql mysqldump -u sail -p laravel laravel > backup.sql

# Restore from backup
docker compose exec -T mysql mysql -u sail -p laravel laravel < backup.sql
```

## Troubleshooting

### Common Issues

1. **Port conflicts:**
   - Ensure ports 80, 3306, 6379, 1025, 8025 are not in use
   - Check with: `netstat -an | findstr :80`

2. **Permission issues on Windows:**
   - Ensure Docker Desktop is running as administrator
   - Check file sharing settings in Docker Desktop

3. **MySQL connection fails:**
   - Wait for MySQL to be fully healthy
   - Check logs: `docker compose logs mysql`

4. **Redis connection fails:**
   - Ensure Redis service is running
   - Check logs: `docker compose logs redis`

### Performance Tips

- Allocate at least 4GB RAM to Docker Desktop
- Use Docker Desktop's advanced settings for better performance
- Keep containers updated regularly

## Development Workflow

1. Make code changes
2. Test locally with containers
3. Run tests: `docker compose exec laravel.test php artisan test`
4. Commit changes
5. Push to repository

## Production Deployment

For production deployment, consider:
- Using environment-specific docker-compose files
- Setting up proper SSL certificates
- Configuring backup strategies
- Setting up monitoring and logging
