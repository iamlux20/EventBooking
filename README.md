# Event Booking API

A Laravel-based REST API for event booking system with role-based access control.

## Setup

1. **Clone and install dependencies**

```bash
git clone <repository-url>
cd EventBooking
composer install
```

2. **Environment setup**

```bash
cp .env.example .env
php artisan key:generate
```

3. **Database setup**

```bash
php artisan migrate
php artisan db:seed
```

4. **Start server**

```bash
php artisan serve
```

## API Endpoints

### Authentication

-   `POST /api/register` - Register user
-   `POST /api/login` - Login user
-   `POST /api/logout` - Logout user (auth required)
-   `GET /api/me` - Get current user (auth required)

### Events (Public)

-   `GET /api/events` - List all events
-   `GET /api/events/{id}` - Get single event

### Events (Organizer only)

-   `POST /api/events` - Create event
-   `PUT /api/events/{id}` - Update event
-   `DELETE /api/events/{id}` - Delete event

### Tickets (Organizer only)

-   `POST /api/events/{event_id}/tickets` - Create ticket
-   `PUT /api/tickets/{id}` - Update ticket
-   `DELETE /api/tickets/{id}` - Delete ticket

### Bookings (Customer only)

-   `POST /api/tickets/{id}/bookings` - Create booking
-   `GET /api/bookings` - List user bookings
-   `PUT /api/bookings/{id}/cancel` - Cancel booking

### Payments

-   `POST /api/bookings/{id}/payment` - Make payment (customer only)
-   `GET /api/payments/{id}` - View payment (auth required)

## Usage

### 1. Register as Customer

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "phone_number": "1234567890",
    "role": "customer"
  }'
```

### 2. Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password"
  }'
```

### 3. View Events

```bash
curl -X GET http://localhost:8000/api/events
```

### 4. Book Ticket (Customer)

```bash
curl -X POST http://localhost:8000/api/tickets/1/bookings \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 2}'
```

### 5. Make Payment

```bash
curl -X POST http://localhost:8000/api/bookings/1/payment \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"amount": 100.00}'
```

## User Roles

-   **Customer**: Can view events, book tickets, make payments
-   **Organizer**: Can create/manage events and tickets

## Testing

```bash
php artisan test
```
