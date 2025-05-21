# Symfony News Application

A feature-rich news portal built with **Symfony**, **PostgreSQL**, and **Docker**.

## 🧰 Technology Stack

- **Backend Framework:** Symfony 7.2
- **Database:** PostgreSQL 15 with Doctrine ORM 3.3
- **Web Server:** Nginx
- **PHP Version:** 8.3 (requires >= 8.2)
- **Image Handling:** LiipImagineBundle
- **Containerization:** Docker

## 🚀 Section 1: Getting Started (with Docker - Recommended)

### 1. Clone the repository
```bash
git clone https://github.com/gbandurdev/news_app.git
cd news-app

docker compose up -d

# The Docker setup will:
# - Install PHP dependencies
# - Set up the PostgreSQL database
# - Run database migrations
# - Load development fixtures
```


### 2. Access the application
   Open your browser at: http://localhost:8080

### 3. Run Tests
```bash
docker compose exec app php bin/phpunit
```


## 📄 Section 2: Manual Installation

### 1. Clone the repository
```bash
git clone https://github.com/yourusername/news-app.git
cd news-app
composer install
```
### 2. Configure Database
DATABASE_URL="postgresql://username:password@127.0.0.1:5432/news_app?serverVersion=15&charset=utf8"

### 3. Run Database Migrations And Load Fixtures (If needed)
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 4. Serve the app
```bash
symfony serve
```

## Section 3: Testing & Future Plans
To send statistics (Assuming using Mailtrap or something else)
```bash
 docker compose exec app php bin/console  app:send-top-news-statistis
 docker compose exec app php bin/console messenger:consume async
```
At this point I made some basic tests, I would add more to raise coverage.

For Statistics report I would extend this implementation by adding support for multiple recipient groups
with customizable statistics preferences. I'd implement an analytics dashboard to visualize trending content patterns over time.
The caching strategy could benefit from a distributed approach using Redis to improve scalability across multiple instances.

I could add an abstraction layer for controllers and repositories, but decided to focus on other improvements to save time.
