# TravelEase - Travel Booking Website

A comprehensive travel booking platform built with PHP, MySQL, JavaScript, HTML, and CSS.

## Features

- **Authentication System**
  - JWT-based authentication
  - Admin and Client roles
  - Secure login and registration

- **Booking System**
  - Flight reservations
  - Hotel bookings
  - Cruise bookings
  - Real-time availability

- **Admin Dashboard**
  - Manage bookings
  - Inventory management
  - User management
  - Analytics

- **Client Dashboard**
  - View/manage bookings
  - Profile management
  - Booking history

## Tech Stack

- **Backend:** PHP (MVC architecture)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Authentication:** JWT tokens
- **Security:** 
  - Password hashing (bcrypt)
  - SQL injection prevention
  - XSS protection
  - CSRF protection
  - Input validation

## Installation

1. Clone the repository
2. Create a MySQL database named `travel_booking`
3. Import the database schema from `database/setup.sql`
4. Configure database connection in `config/config.php`
5. Set up a virtual host or use PHP's built-in server:

```bash
php -S localhost:8000
```

## Default Users

- **Admin:**
  - Email: admin@travelease.com
  - Password: admin123

- **Client:**
  - Email: client@example.com
  - Password: password123

## Project Structure

```
/
├── config/             # Configuration files
├── controllers/        # MVC controllers
│   ├── api/            # API controllers
├── helpers/            # Helper functions
├── models/             # Database models
├── public/             # Publicly accessible files
│   ├── assets/         # CSS, JS, images
├── views/              # View templates
│   ├── auth/           # Authentication views
│   ├── layout/         # Layout templates
│   ├── admin/          # Admin dashboard views
├── database/           # Database schema and migrations
├── index.php           # Application entry point
└── README.md
```

## Security Considerations

- All user inputs are sanitized and validated
- Passwords are hashed using bcrypt
- CSRF tokens for form submissions
- Prepared statements for all database queries
- JWT tokens for API authentication
- HTTP-only cookies for session management
- XSS protection through output escaping

## Responsive Design

The website is fully responsive with breakpoints at:
- 320px (mobile)
- 768px (tablet)
- 1024px (desktop)
- 1280px (large desktop)

## License

This project is licensed under the MIT License - see the LICENSE file for details.