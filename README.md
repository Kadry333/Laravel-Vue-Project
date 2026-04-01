# Simple Hotel System

## Overview
Grand Luxury is a hotel management system built with Laravel, Vue.js, and Inertia.js. It is designed to manage hotels, users, rooms, floors, reservations, approvals, and role-based access in one application.

The project separates responsibilities between administrators, managers, receptionists, and clients. The backend handles authorization, approvals, reservations, notifications, scheduling, and API access, while the frontend provides the dashboard and booking flow through Vue and Inertia.

## How the System Works
- Admin has full access to the system and can manage all core modules and permissions.
- Manager works inside the operational dashboard and manages floors, rooms, receptionists, clients, and statistics according to the assigned permissions.
- Receptionist reviews pending client accounts, approves clients, and views approved clients and their reservations.
- Client can register an account, wait for approval, browse available rooms, create reservations, and complete payment using Stripe.
- When a client is approved, the system sends a notification so the client can continue using the platform.
- The application also sends scheduled reminder notifications to inactive approved clients.
- Some shared data, such as countries, is loaded through a package that supports caching.
- The application includes an API route protected with Sanctum for authenticated room access.

## Roles and Permissions

### Admin
- Has full access across the system.
- Manages managers.
- Manages receptionists.
- Manages floors and rooms.
- Manages clients and client approvals.
- Reviews reservations and statistics.
- Exports clients to Excel.

### Manager
- Manages floors assigned through the dashboard.
- Manages rooms and room-related operations.
- Manages receptionists.
- Manages clients and can approve them.
- Views approved clients and client reservations.
- Accesses statistics for operational reporting.

### Receptionist
- Reviews pending client accounts.
- Approves client registrations.
- Views approved clients.
- Views client reservations.

### Client
- Registers a new account.
- Waits for approval before using booking features.
- Views available rooms.
- Creates reservations.
- Pays for reservations using Stripe.
- Manages profile information and views personal reservation data.

## Main Modules

### Authentication
The system uses Laravel authentication for login, registration, email verification, password reset, and session-based access. Clients register through the public flow, while staff accounts are managed from the dashboard.

### Profile Management
All roles can update profile information such as personal details and account settings from the profile area.

### Managers Management
Admins can create, view, update, and delete manager accounts. This module is used to define who is responsible for hotel operations.

### Receptionists Management
Admins and managers can manage receptionist accounts. This module supports staff creation, updates, and access control for receptionist users.

### Clients Management
Admins, managers, and receptionists work with client records depending on role. The module covers client registration review, approval status, and client account administration.

### Floors Management
Admins and managers manage hotel floors. Floors are used as the structural level for organizing room records.

### Rooms Management
Admins and managers create and maintain room records, including capacity, floor assignment, and pricing.

### Available Rooms
Clients can browse rooms that are available for reservation. Room availability is checked against reservation dates before a booking is accepted.

### Reservations
Clients create reservations from the available rooms flow. Reservation records store booking details, payment session data, and status updates after Stripe checkout or webhook processing.

### Approved Clients
Receptionists can review approved clients separately from pending accounts and inspect their reservation information when needed.

### Statistics
Admins and managers can access operational statistics such as revenue, client activity, gender-based reservation data, and country-based summaries.

### Export Clients to Excel
Admins and managers can export client data to Excel files for reporting or review.

### Deployment
The project includes deployment responsibility as part of the team scope and is structured as a standard Laravel + Vite application for local and server environments.

## Tech Stack
- Laravel
- Vue.js
- Inertia.js
- Tailwind CSS
- shadcn-vue
- TanStack Table
- Chart.js
- MySQL

## Packages Used
- `spatie/laravel-permission`: handles roles and permissions for admin, manager, receptionist, and client access.
- `cybercog/laravel-ban`: supports banning restricted users from protected areas of the application.
- `laravel/sanctum`: protects authenticated API access.
- `lwwcas/laravel-countries`: provides country data used in client-related forms and statistics.
- `maatwebsite/excel`: exports client data to Excel sheets.
- `stripe/stripe-php`: handles Stripe checkout sessions, payment callbacks, and reservation payment flow.
- Laravel Notifications: sends approval and reminder notifications.
- Laravel Scheduler / Queues: runs scheduled reminders and processes queued notifications.

## Team Responsibilities
- **Amr Shokry**
  - Responsible for Managers for Admin users
  - Responsible for Floors for Admin and Manager users
  - Responsible for Statistics for Admin and Manager users

- **Ramadan Mohamed**
  - Responsible for Rooms for Admin and Manager users
  - Responsible for Available Rooms for Client users
  - Responsible for Deployment

- **Kareem Kadry**
  - Responsible for Clients for Admin, Manager, and Receptionist users
  - Responsible for Approved Clients for Receptionist users
  - Responsible for Export Clients as Excel Sheets for Admin and Manager users

- **Ahmed Ibrahim Elemam**
  - Responsible for Profile for Client, Admin, Manager, and Receptionist users
  - Responsible for Manage Receptionists for Admin and Manager users

- **Mawadah Hassan**
  - Responsible for the Landing Page for all users
  - Responsible for Auth and Laravel Cache for all users
  - Responsible for Reservations for all users

## Installation
Clone the repository and move into the project directory:

```bash
git clone git@github.com:ramadan123462y/Laravel-Vue-Project.git
cd Laravel-Vue-Project
```

Install the backend and frontend dependencies:

```bash
composer install
npm install
```

Create the environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`, then run the database migrations:

```bash
php artisan migrate
```

Seed the countries package tables:

```bash

php artisan w-countries:install

You will be prompted with some questions:

Would you like to run the migrations now?
→ Type: yes
Select languages to install
→ Type: 0 (to install default English only)
(or choose any languages you prefer)
Do you want to choose again?
→ Type: no
```

Run the project seeders:

```bash
php artisan db:seed
```

Create the storage symlink:

```bash
php artisan storage:link
```

Start the local development servers:

```bash
php artisan serve
npm run dev
```

If you want queued notifications to run locally, start a queue worker in a separate terminal:

```bash
php artisan queue:work
```

## Environment Notes
Make sure the local environment is configured for:
- Database credentials
- Mail settings
- Queue connection
- Stripe keys
- Cache settings if needed

The countries package data should be seeded before using registration and client forms, because the application reads country values from the `lc_countries` tables.

## Default Accounts with password `123456` for all accounts
- Admin:
- Email: `admin@admin.com`

- Managers:
- Email: `ramadan@hotel.com`
- Email: `amr@hotel.com`
- Email: `ahmed.manager@hotel.com`

- Receptionists:
- Email: `omar@hotel.com`
- Email: `hana@hotel.com`

- Clients:
- Email: `ahmed@client.com`
- Email: `mona@client.com`

## Yoc can also create other admins through console command.
## For Example:
```bash
## php artisan create:admin --email=admin2@admin.com --password=1234566




## Important Technical Notes
- Prices are stored in cents in the database and displayed in dollars in the application.
- Tables use TanStack Table with server-side pagination.
- Charts use Chart.js and data is loaded dynamically from the backend.
- Countries come from the Laravel Countries package.
- Delete actions should be confirmed and handled with asynchronous requests through Inertia.
- Roles and permissions are handled using Spatie Permission.
- API authentication uses Sanctum.
- Forgot password should work with the default Laravel authentication flow.
- Notifications for approval must be queued.
- Daily scheduled reminders are sent to inactive clients.

## Running the Project
Run the backend and frontend development servers:

```bash
php artisan serve
npm run dev
```

Run the queue worker in another terminal if you want queued notifications to be processed locally:

```bash
php artisan queue:work
```

Then open the local application in the browser and either:
- Log in with the seeded accounts
- Register a client account and continue through the approval flow
