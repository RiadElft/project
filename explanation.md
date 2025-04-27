# TravelEase - Travel Booking System

## Project Overview

TravelEase is a comprehensive travel booking platform that allows users to search and book hotels, flights, and cruises. The system includes both customer-facing interfaces and administrator panels for managing all aspects of the travel offerings.

## Technologies Used

- **Backend**: PHP (Traditional MVC pattern without framework)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Security**: JWT for authentication
- **Data Format**: JSON for API responses

## Project Structure

### Folders Organization

- **`controllers/`**: Contains controller classes that handle user requests
  - **`api/`**: API controllers returning JSON responses
  - **`admin/`**: Controllers specifically for admin functionality
- **`models/`**: Data models that interact with the database
- **`views/`**: Template files for rendering HTML
  - **`layout/`**: Contains header, footer, and other shared UI components
  - **`hotels/`**, **`flights/`**, **`cruises/`**: Specific views for each booking type
  - **`admin/`**: Admin panel interface
- **`public/`**: Publicly accessible files
  - **`images/`**: Stores images for hotels, flights, cruises, etc.
  - **`css/`**: Stylesheet files
  - **`js/`**: JavaScript files
- **`config/`**: Configuration files including database settings
- **`helpers/`**: Utility functions (currency formatting, auth helpers, etc.)

### MVC Pattern Implementation

The application follows the Model-View-Controller (MVC) pattern:
- **Models**: Handle data logic and database interactions
- **Views**: Present data to users with HTML templates
- **Controllers**: Process user input and coordinate between models and views

## Database Structure

### Main Tables

#### `users`
- `id`: Primary key
- `name`: User's full name
- `email`: User's email address (unique)
- `password`: Hashed password
- `role`: User role (customer, admin)
- `created_at`: Account creation timestamp

#### `hotels`
- `id`: Primary key
- `name`: Hotel name
- `city`: City location
- `country`: Country location
- `description`: Hotel description
- `price_per_night`: Base price
- `rating`: Star rating (1-5)
- `available_rooms`: Number of available rooms
- `image`: Image filename
- `status`: Active or inactive
- `amenities`: Comma-separated list of amenities
- `room_types`: Available room types

#### `flights`
- `id`: Primary key
- `airline_id`: Foreign key to airlines table
- `flight_number`: Unique flight number
- `departure_city`: Origin city
- `arrival_city`: Destination city
- `departure_time`: Departure timestamp
- `arrival_time`: Arrival timestamp
- `price`: Ticket price
- `stops`: Number of stops
- `available_seats`: Available seats
- `created_at`: Creation timestamp

#### `cruises`
- `id`: Primary key
- `name`: Cruise name
- `cruise_line`: Cruise company
- `departure_port`: Port of departure
- `destination_ports`: Comma-separated list of destinations
- `departure_date`: Departure date
- `return_date`: Return date
- `duration_days`: Length of cruise in days
- `price`: Base price per person
- `available_cabins`: Number of available cabins
- `image`: Image filename
- `created_at`: Creation timestamp

#### `bookings`
- `id`: Primary key
- `user_id`: Foreign key to users
- `booking_type`: Type (hotel, flight, cruise)
- `reference_id`: ID of the booked item
- `status`: Booking status
- `total_price`: Total price paid
- `created_at`: Booking timestamp

#### `cruise_destinations`
- `id`: Primary key
- `cruise_id`: Foreign key to cruises
- `port_name`: Destination port name
- `port_order`: Order of visit

#### `airlines`
- `id`: Primary key
- `name`: Airline name
- `logo_image`: Airline logo filename

#### `hotel_bookings`
- `id`: Primary key
- `booking_id`: Foreign key to bookings
- `hotel_id`: Foreign key to hotels
- `check_in`: Check-in date
- `check_out`: Check-out date
- `guests`: Number of guests

## Key Features

1. **User Authentication**: Secure login/registration with JWT tokens
2. **Search & Filtering**: Advanced search for all travel options
3. **Booking Management**: Create, view, and manage bookings
4. **Administration**: Admin interface for managing inventory
5. **Responsive Design**: Mobile-friendly interface

## Data Flow

1. User searches for travel options using filters
2. Controller processes the request and queries the model
3. Model retrieves data from database using SQL queries with filters
4. Controller passes data to the view
5. View renders the results to the user
6. User selects an option and completes booking
7. System updates inventory and creates booking record

## API Endpoints

The system includes a REST API for integration with other services:

- **GET /api/hotels**: List available hotels with optional filters
- **GET /api/flights**: List available flights with optional filters
- **GET /api/cruises**: List available cruises with optional filters
- **GET /api/bookings**: List user bookings
- **POST /api/bookings**: Create a new booking

## Security Measures

- Password hashing
- JWT token authentication
- Input validation and sanitization
- CSRF protection
- XSS prevention headers 