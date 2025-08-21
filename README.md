# Laravel 12 + Redis + MySQL + Nginx Docker Template

A reusable boilerplate for Laravel 12 APIs, with Docker-Compose services for PHP-FPM, Nginx, MySQL, and Redis.

---

### 📂 Project Structure

```
project-root/
├── src/                     # Laravel application root
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── tests/
│   └── .env.example         # Example environment file
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf       # Global nginx settings (gzip, includes)
│   │   ├── default.conf     # Site server blocks (HTTP→HTTPS, PHP proxy)
│   │   └── ssl/             # Self-signed certificates for development
│   └── php/
│       ├── Dockerfile       # PHP-FPM image (extensions, appuser, Redis)
│       └── www.conf         # PHP-FPM pool config (pm=dynamic, user/appuser)
├── docker-compose.yml       # Defines app, nginx, mysql, redis services
└── README.md                # This file
```

---

### ⚙️ Prerequisites

- Docker & Docker-Compose installed
- Git
- (Optional) Composer CLI for local scaffolding

---

### 🚀 Setup & Quickstart

1. **Clone the template**

   ```bash
   git clone git@github.com:<username>/openai-no-rag
   cd my-project
   ```

2. **Copy & configure your environment file**

   ```bash
   cp src/.env.example src/.env
   ```

   Then open `src/.env` and update:

   ```dotenv
   APP_URL=https://localhost
   DB_CONNECTION=mysql
   DB_HOST=mysql
   DB_PORT=3306
   DB_DATABASE=openai
   DB_USERNAME=openai
   DB_PASSWORD=openai

   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379

   # add openai key and details
   OPENAI_API_KEY=
   OPENAI_MODEL=gpt-4o-mini
   OPENAI_TIMEOUT=20
   ```

3. **Build and start all services**

   ```bash
   docker-compose up -d --build
   ```

4. **Install Laravel dependencies & generate app key**

   ```bash
   docker-compose exec app bash
   composer install
   php artisan key:generate
   exit
   ```

5. **Run database migrations**

   ```bash
   docker-compose exec app php artisan migrate
   ```

6. **Verify**

   - Visit: `https://localhost` (accept the self-signed certificate warning)
   - API endpoints under: `https://localhost/api/...`

7. **Run Open AI command**

   - php artisan llm:test "Say hello to John politely. Keep it under 20 words."
   - make sure you have credits
   - change the Open ai request to anything you want Open AI to answer

---

### 📜 License

This project is open-source under the MIT License. See the `LICENSE` file for details.

