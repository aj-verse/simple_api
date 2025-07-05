<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Laravel Simple API

A simple Laravel API project with user registration, authentication, and product management functionality.

## Features

- User registration and authentication with JWT tokens
- Product CRUD operations with automatic slug generation
- Input validation and error handling
- JSON API responses
- Comprehensive test coverage

## API Endpoints

### Authentication

#### User Registration

**POST** `/api/register`

Register a new user with the following fields:
- `name` (required): User's full name (string, max 255 characters)
- `email` (required): User's email address (must be unique, valid email format)
- `password` (required): User's password (minimum 8 characters)
- `password_confirmation` (required): Password confirmation (must match password)

#### User Login

**POST** `/api/login`

Login with the following fields:
- `email` (required): User's email address
- `password` (required): User's password

**Response includes:**
- User information
- Bearer token for authentication

#### User Logout

**POST** `/api/logout`

**Headers:** `Authorization: Bearer {token}`

Revokes the current user's authentication token.

### Products

#### Get All Products

**GET** `/api/products`

Returns a paginated list of all products.

**Query Parameters:**
- `page` (optional): Page number for pagination

#### Create Product

**POST** `/api/products`

Create a new product with the following fields:
- `name` (required): Product name (string, max 255 characters)
- `description` (optional): Product description (text)
- `price` (required): Product price (numeric, minimum 0)
- `slug` (optional): URL-friendly slug (string, must be unique)
- `quantity` (required): Available quantity (integer, minimum 0)

**Note:** If slug is not provided, it will be automatically generated from the name.

#### Get Single Product

**GET** `/api/products/{slug}`

Returns a specific product by its slug.

#### Update Product

**PUT** `/api/products/{id}`

Update an existing product by ID. All fields are optional:
- `name` (optional): Product name (string, max 255 characters)
- `description` (optional): Product description (text)
- `price` (optional): Product price (numeric, minimum 0)
- `slug` (optional): URL-friendly slug (string, must be unique)
- `quantity` (optional): Available quantity (integer, minimum 0)

#### Delete Product

**DELETE** `/api/products/{id}`

Deletes a specific product by its ID.

## Example Requests

### Create Product

```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "iPhone 15 Pro",
    "description": "Latest iPhone with advanced features",
    "price": 999.99,
    "quantity": 50
  }'
```

### Get All Products

```bash
curl -X GET http://localhost:8000/api/products
```

### Get Single Product

```bash
curl -X GET http://localhost:8000/api/products/iphone-15-pro
```

### Update Product

```bash
curl -X PUT http://localhost:8000/api/products/1 \
  -H "Content-Type: application/json" \
  -d '{
    "price": 899.99,
    "quantity": 45
  }'
```

### Delete Product

```bash
curl -X DELETE http://localhost:8000/api/products/1
```

## Response Format

### Success Response

```json
{
  "success": true,
  "message": "Product created successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "iPhone 15 Pro",
      "description": "Latest iPhone with advanced features",
      "price": "999.99",
      "slug": "iphone-15-pro",
      "quantity": 50,
      "created_at": "2024-01-01T00:00:00.000000Z",
      "updated_at": "2024-01-01T00:00:00.000000Z"
    }
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "name": ["The name field is required."],
    "price": ["The price must be at least 0."]
  }
}
```

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run migrations: `php artisan migrate`
5. Start the development server: `php artisan serve`

## Testing

Run the tests with:

```bash
php artisan test
```

The project includes comprehensive tests for:
- User registration and authentication
- Product CRUD operations
- Validation scenarios
- Error handling

## Database

### Users Table
- `id` (primary key)
- `name` (string)
- `email` (unique string)
- `password` (hashed string)
- `email_verified_at` (nullable timestamp)
- `remember_token` (nullable string)
- `created_at` and `updated_at` timestamps

### Products Table
- `id` (primary key)
- `name` (string)
- `description` (nullable text)
- `price` (decimal, 10 digits, 2 decimal places)
- `slug` (unique string)
- `quantity` (integer, default 0)
- `created_at` and `updated_at` timestamps

## Features

- **Automatic Slug Generation**: Product slugs are automatically generated from the name if not provided
- **Validation**: Comprehensive input validation for all endpoints
- **Error Handling**: Proper HTTP status codes and error messages
- **Pagination**: Product listing includes pagination
- **Token Authentication**: Secure API authentication using Laravel Sanctum
