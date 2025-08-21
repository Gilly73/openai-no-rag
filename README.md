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
   git clone git@github.com:<username>/laravel-12-redis-mysql-nginx-docker-template.git my-project
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
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password

   CACHE_DRIVER=redis
   QUEUE_CONNECTION=redis
   REDIS_HOST=redis
   REDIS_PASSWORD=null
   REDIS_PORT=6379
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

---

### 🛠️ Common Docker Commands

| Command                                       | Description                                         |
| --------------------------------------------- | --------------------------------------------------- |
| `docker-compose build`                        | Build all images (or only changed Dockerfiles)      |
| `docker-compose up -d`                        | Start all services in detached mode                 |
| `docker-compose up -d --build`                | Rebuild then start                                  |
| `docker-compose ps`                           | List running services & ports                       |
| `docker-compose ps -a`                        | List all containers (running & exited)              |
| `docker-compose logs -f <service>`            | Stream logs for `app`, `nginx`, `mysql`, or `redis` |
| `docker-compose exec app bash`                | Open a shell in the PHP-FPM container               |
| `docker-compose exec nginx nginx -t`          | Test nginx configuration inside the container       |
| `docker-compose exec app php artisan migrate` | Run Laravel database migrations                     |
| `docker-compose exec app php artisan test`    | Execute Laravel’s test suite                        |

---

### 📝 Usage

1. **Define API endpoints**

   - Add routes in `src/routes/api.php`.
   - Create controllers under `src/app/Http/Controllers/`.

2. **TDD workflow**

   ```bash
   docker-compose exec app php artisan test
   ```

3. **API verification**

   - Import your Postman collection and point to `https://localhost/api/...`


---

### 📜 License

This project is open-source under the MIT License. See the `LICENSE` file for details.

