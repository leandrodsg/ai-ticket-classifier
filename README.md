# Smart Support Classifier

An AI-powered ticket classification system built with Laravel 12 and DeepSeek AI.

## Features

- Automatic ticket classification using AI
- Laravel 12 framework
- Docker containerization with Laravel Sail
- MySQL database
- Redis caching
- Mailpit for email testing

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

## Development

Start the development server:
```bash
./vendor/bin/sail up
```

## Testing

Run the test suite:
```bash
./vendor/bin/sail test
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
