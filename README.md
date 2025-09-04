# 🚀 Docker Deployment Guide for GRTech App

This guide will help you deploy the GRTech Application either manually or using Docker & Docker Compose.
The application uses Laravel, and a database (PostgreSQL or MySQL), all running in separate containers.

## 🛠 Tech Stack

- **Backend:** [Laravel 12+](https://laravel.com/) (PHP 8.2+)
- **Frontend:** [Vite](https://vitejs.dev/) + [Vue.js 3](https://vuejs.org/)
- **Database:** PostgreSQL / MySQL
- **Cache & Queue:** Database
- **Package Manager:** Composer & npm
- **Containerization:** Docker & Docker Compose
- **Web Server (Docker):** Nginx
- **Spatie/Permission**
- **Mail:** Laravel Mail (SMTP, Mailgun, etc.)

## 📋 Prerequisites

- Docker installed
- Docker Compose installed
- Git (optional, for cloning the repository)
- PHP 8.2+, Composer, Node.js 22+ (for manual installation)

## ⚡ Manual Installation (without Docker)

1. **Clone the project**

   ```bash
   git clone https://github.com/habibiazmi123/test-case-grtech
   cd test-case-grtech
   ```

2. **Install dependencies**

   ```bash
   composer install
   npm install
   ```

3. **Set up environment file**

   ```bash
   cp .env.example .env

   php artisan key:generate
   php artisan storage:link
   ```

4. **Set up Database**

   ```bash
   DB_CONNECTION=mysql   # or pgsql
   DB_HOST=127.0.0.1
   DB_PORT=3306          # or 5432 for Postgres
   DB_DATABASE=grtech
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```
4. **Set up SMTP(Mail)**
   
   ```bash
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your-username
   MAIL_PASSWORD=your-password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@yourapp.com
   MAIL_FROM_NAME="GRTech App"
   ```

5. **Run migrations & seed database**

   ```bash
   php artisan migrate --seed
   ```

   After seeding, you can log in with these default accounts:

    - **Admin**
      - Email: `admin@grtech.com`
      - Password: `password`
    
    - **User**
      - Email: `user@grtech.com`
      - Password: `password`

6. **Run the development server**

   ```bash
   php artisan serve
   npm run dev

   or

   composer run dev
   ```
   
   The app will be available at:
   ```bash
   http://127.0.0.1:8000 
   ```

## Accessing the Application

Once all containers are up and running, you can access the application at:

```
http://localhost:8000
```

# 🐳 Installation with Docker (Recommended)

1. **Clone the project**

   ```bash
   git clone https://github.com/habibiazmi123/test-case-grtech
   cd test-case-grtech
   ```

2. **Set up environment file**

   ```bash
   cp .env.example .env
   ```
   
3. **Build and start containers**

   ```bash
   make up
   ```
   
4. **Run migrations & seed database**

   ```bash
   make artisan migrate
   make artisan db:seed
   ```
   
4. **Generate application key & Storage link**

   ```bash
   make artisan key:generate
   make artisan storage:link
   ```

6. **Build frontend assets**

   ```bash
   make npm install
   make run build
   ```
   
   The app will be available at:
   ```bash
   http://localhost
   ```

   The mailpit will be available at:
   
   ```bash
   http://localhost:8025
   ```
