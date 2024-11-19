# Laravel Project

## Project Overview

This is a Laravel-based application built using Laravel 11, designed as a RESTful API service with integrated Swagger documentation. The application leverages Docker for a consistent development environment and includes tools for debugging, testing, and code quality checks.

### Features
- **RESTful API**: Endpoints for CRUD operations.
- **Swagger Documentation**: Available at `/api/documentation`.
- **Dockerized Environment**: Simplified setup and deployment.
- **Development Tools**: Includes PHPUnit for testing and Laravel Sail for local development.

---

## Installation Guide

Follow these steps to set up the project locally:

### Prerequisites
Ensure you have the following installed:
- **Docker & Docker Compose**: [Install Docker](https://docs.docker.com/get-docker/)
- **Composer**: [Install Composer](https://getcomposer.org/download/)
- **Node.js & npm**: [Install Node.js](https://nodejs.org/)

### Steps

1. **Clone the Repository**
   ```bash
   git clone git@github.com:LikeAshraful/news-aggregator.git
   cd news-aggregator
   ```

2. **Install Dependencies** Run the following commands to install backend and frontend dependencies:
    ```bash
    composer install
    npm install
    ```

3. **Environment Configuration** Copy the example .env file and configure as needed:
    ```bash
    cp .env.example .env
    ```
    Update the environment variables in .env to match your local setup. Ensure database credentials match those in the docker-compose.yml file.

4. **Start Docker Services** Start the application using Laravel Sail:

    ```bash
    ./vendor/bin/sail up -d
    ```

5. **Run Migrations** Execute the database migrations:

    ```bash
    ./vendor/bin/sail artisan migrate
    ```

6. **Generate Application Key** Generate the application key if not already set:

    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

7. **Compile Assets** Build the frontend assets:

    ```bash
    npm run dev
    ```

8. **Access the Application**

    The API server will be accessible at http://localhost:8000.
    API documentation is available at http://localhost:8000/api/documentation.



## API Documentation
Access the Swagger API documentation at:
http://localhost:8000/api/documentation

Swagger integration uses the darkaonline/l5-swagger package.