# Smart Support Classifier

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

An AI-powered ticket classification system built with Laravel 12 and OpenRouter AI.

## Features

- 🤖 Automatic ticket classification using AI (OpenRouter)
- 📊 Interactive dashboard with real-time statistics
- 🎯 Multi-category classification (Technical, Commercial, Billing, General, Support)
- 😊 Sentiment analysis (Positive, Negative, Neutral)
- 🔒 Rate limiting and security measures
- 🧪 Comprehensive test suite (25+ tests)
- 🐳 Docker containerization with Laravel Sail
- 🗄️ MySQL database with Redis caching
- 📧 Mailpit for email testing
- 📱 Responsive web interface

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

### Seeding Demo Data

Populate the database with realistic sample data:
```bash
./vendor/bin/sail artisan db:seed
```

This creates 30+ sample tickets across all categories with varied sentiments and statuses.

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

- Rate limiting on AI API calls
- Input validation and sanitization
- Secure logging (no sensitive data exposure)
- CSRF protection
- SQL injection prevention

## License

This project is licensed under the MIT License - see the LICENSE file for details.
