# Smart Support Classifier

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php)
![Tests](https://img.shields.io/badge/Tests-90%20passing-success)
![Coverage](https://img.shields.io/badge/Coverage-268%20assertions-blue)
![License](https://img.shields.io/badge/License-MIT-green)

An AI-powered ticket classification system built with Laravel 12 and OpenRouter AI. Automatically categorizes support tickets, analyzes sentiment, and calculates ITIL-based priorities with SLA management.

## Features

### Core Functionality
- **AI-Powered Classification**: Automatic ticket categorization using OpenRouter API with multiple fallback models
- **Sentiment Analysis**: Real-time analysis of customer sentiment (Positive, Negative, Neutral)
- **Multi-Category Support**: Classification into Technical, Commercial, Billing, General, and Support categories
- **Interactive Dashboard**: Real-time statistics and analytics with caching for performance

### ITIL Priority Management
- **ITIL v4 Compliance**: Complete priority matrix implementation (Impact × Urgency)
- **Automatic SLA Calculation**: Response times based on priority levels (1 hour to 48 hours)
- **Priority Distribution Analytics**: Visual charts and alerts for priority management
- **SLA Breach Monitoring**: Real-time tracking of compliance metrics

### Technical Features
- **Comprehensive Test Suite**: 90+ tests covering all functionality (268 assertions)
- **Docker Containerization**: Laravel Sail for consistent development environment
- **Database Caching**: Redis-backed caching for improved performance
- **Rate Limiting**: API protection with configurable limits
- **Responsive Design**: Mobile-friendly web interface with Tailwind CSS

## Requirements

- Docker & Docker Compose
- PHP 8.4
- Composer
- Node.js & npm

## Installation

1. Clone the repository:
```bash
git clone https://github.com/leandrodsg/ai-ticket-classifier.git
cd ai-ticket-classifier
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file and configure:
```bash
cp .env.example .env
# Edit .env with your configuration
```

4. Start the Docker containers:
```bash
./vendor/bin/sail up -d
```

5. Generate application key:
```bash
./vendor/bin/sail artisan key:generate
```

6. Run database migrations:
```bash
./vendor/bin/sail artisan migrate
```

7. Install Node dependencies and build assets:
```bash
npm install
npm run build
```

## Usage

Access the application at `http://localhost`

## How It Works

### Ticket Classification Process

1. **Ticket Creation**: User submits a ticket with title and description
2. **AI Classification**: System automatically classifies using OpenRouter API
3. **Category Assignment**: Ticket categorized as Technical, Commercial, Billing, General, or Support
4. **Sentiment Analysis**: Sentiment classified as Positive, Negative, or Neutral
5. **Confidence Scoring**: AI provides confidence level (0.0-1.0)
6. **Logging**: All classifications logged for audit and debugging

### AI Integration

The system uses OpenRouter as the primary AI provider with multiple model fallback:

- **Primary**: Meta Llama 3.3 70B Instruct
- **Fallback 1**: Meta Llama 3.2 3B Instruct
- **Fallback 2**: OpenAI GPT-OSS 20B
- **Fallback 3**: Google Gemma 3N E2B IT
- **Fallback 4**: Mistral 7B Instruct

### Mock Mode vs Real AI

#### Real AI Mode (Production)
- Uses actual OpenRouter API calls
- Provides accurate classifications
- Subject to API rate limits and costs
- Requires valid API key

#### Mock Mode (Development/Testing)
- Uses rule-based classification
- No API calls or costs
- Instant response times
- Perfect for testing and development

**Environment Variable**: `AI_ALWAYS_USE_MOCK=true/false`

### ITIL Priority System

The system implements a complete **ITIL v4-based priority management** with automatic SLA calculation:

#### Priority Matrix (Impact × Urgency)

```
Impact \ Urgency     | High Urgency | Medium Urgency | Low Urgency
---------------------|--------------|----------------|-------------
Critical Impact      | Critical     | Critical       | High
High Impact          | Critical     | High           | Medium
Medium Impact        | High         | Medium         | Low
Low Impact           | Medium       | Low            | Low
```

#### Category to Impact Mapping
- **Technical** → Critical Impact (system down, critical functionality)
- **Billing** → High Impact (financial impact, payment issues)
- **Commercial** → Medium Impact (business operations affected)
- **General/Support** → Low Impact (general inquiries, minor issues)

#### Sentiment to Urgency Mapping
- **Negative** → High Urgency (angry customers, urgent issues)
- **Neutral** → Medium Urgency (standard requests)
- **Positive** → Low Urgency (positive feedback, non-urgent)

#### SLA Definitions
- **Critical Priority**: 1 hour response time
- **High Priority**: 4 hours response time
- **Medium Priority**: 24 hours response time
- **Low Priority**: 48 hours response time

#### Automatic Priority Calculation
1. **Ticket Creation**: AI classifies category and sentiment
2. **Priority Calculation**: System applies ITIL matrix automatically
3. **SLA Assignment**: Due date calculated based on priority
4. **Re-calculation**: Priority updates automatically when ticket description changes

#### Dashboard Analytics
- Priority distribution charts
- SLA compliance metrics (percentage on time)
- Critical ticket alerts
- SLA breach notifications
- Real-time priority statistics

## Development

Start the development server:
```bash
./vendor/bin/sail up
```

## Testing

Run the comprehensive test suite:
```bash
./vendor/bin/sail test
```

The test suite includes:
- **Validation Tests**: Form validation and data constraints
- **CRUD Tests**: Complete ticket management operations
- **AI Tests**: Classification service functionality
- **Dashboard Tests**: Performance and caching verification

## Artisan Commands

The application includes several useful Artisan commands for development and maintenance:

### Database Management
```bash
# Run migrations
./vendor/bin/sail artisan migrate

# Rollback migrations
./vendor/bin/sail artisan migrate:rollback

# Create new migration
./vendor/bin/sail artisan make:migration create_example_table

# Run seeders
./vendor/bin/sail artisan db:seed
```

### AI Classification
```bash
# Test AI classification manually
./vendor/bin/sail artisan test:ai-classification

# Recalculate priorities for existing tickets
./vendor/bin/sail artisan tickets:recalculate-priorities

# Debug dashboard data
./vendor/bin/sail artisan debug:dashboard
```

### Development Tools
```bash
# Generate application key
./vendor/bin/sail artisan key:generate

# Clear various caches
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

# Run Tinker (interactive shell)
./vendor/bin/sail artisan tinker
```

### Testing
```bash
# Run all tests
./vendor/bin/sail test

# Run specific test file
./vendor/bin/sail test tests/Feature/DashboardTest.php

# Run tests with coverage
./vendor/bin/sail test --coverage
```

## Database Seeding

The application includes comprehensive seeders to populate the database with realistic sample data:

### Basic Seeding
```bash
# Run all seeders
./vendor/bin/sail artisan db:seed

# Run specific seeder
./vendor/bin/sail artisan db:seed --class=TicketSeeder
```

### Sample Data Created
The `TicketSeeder` creates 30+ sample tickets with:
- **Categories**: Technical, Commercial, Billing, General, Support
- **Sentiments**: Positive, Negative, Neutral (distributed realistically)
- **Statuses**: Open, Closed, In Progress
- **Priorities**: Critical, High, Medium, Low (calculated via ITIL matrix)
- **SLA Due Dates**: Automatically calculated based on priority
- **AI Logs**: Simulated classification logs for each ticket

### Refresh Database
To completely reset and reseed the database:
```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

## API Documentation

### AI Classification
- **Provider**: OpenRouter (multiple models with fallback)
- **Categories**: technical, commercial, billing, general, support
- **Sentiment**: positive, negative, neutral
- **Rate Limit**: 10 requests/minute

### Dashboard Statistics
Real-time metrics including:
- Total tickets by category
- Sentiment distribution
- Status breakdown
- Performance monitoring

## Architecture

- **Backend**: Laravel 12 with service-oriented architecture
- **AI Integration**: OpenRouter API with intelligent fallback
- **Database**: MySQL with Redis caching
- **Frontend**: Blade templates with Tailwind CSS
- **Testing**: PHPUnit with comprehensive coverage
- **Containerization**: Docker with Laravel Sail

## Security Features

### API Security
- **Rate Limiting**: Configurable limits on AI API calls (10 requests/minute for tickets, 30 requests/minute for dashboard)
- **API Key Protection**: OpenRouter API keys stored securely in environment variables
- **Request Throttling**: Laravel's built-in throttling middleware prevents abuse

### Data Protection
- **Input Validation**: Comprehensive validation rules for all form inputs using Laravel's validation system
- **Data Sanitization**: Automatic sanitization of user inputs to prevent XSS attacks
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **CSRF Protection**: Cross-Site Request Forgery tokens on all forms

### Authentication & Authorization
- **Secure Sessions**: Laravel's secure session management with encryption
- **Password Security**: Secure password hashing with bcrypt
- **Session Management**: Automatic session expiration and secure cookie handling

### Logging & Monitoring
- **Audit Logging**: All AI classifications logged for debugging and compliance
- **Error Handling**: Secure error logging without exposing sensitive information
- **Performance Monitoring**: Response time tracking and performance metrics

### Infrastructure Security
- **Container Security**: Docker containers with minimal attack surface
- **Environment Isolation**: Sensitive configuration separated from codebase
- **Database Security**: MySQL with proper user permissions and prepared statements

## License

This project is licensed under the MIT License - see the LICENSE file for details.
