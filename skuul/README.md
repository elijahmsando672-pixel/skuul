# SKUUL Management System

A comprehensive school management system built with Laravel 11, PHP 8.5, and Docker.

## Features

### Authentication & RBAC
- User registration and login
- Role-based access control (Admin, Teacher, Student, Parent)
- Spatie Laravel Permission integration
- Laravel Sanctum API authentication

### Admin Module
- User management (CRUD with role assignment)
- Student management (enrollment, profiles)
- Teacher management (profiles, departments)
- Classroom management (sections, capacity, teacher assignment)
- Subject management (codes, credits, classroom/teacher assignment)
- Academic year management
- Announcement management (targeted by audience and priority)

### Teacher Module
- Dashboard with overview
- Attendance tracking (mark present/absent/late/excused)
- Grade entry (quiz, assignment, midterm, final, project)

### Student Module
- Dashboard with personal info
- Attendance history
- Grade/view report cards

### Parent Module
- Dashboard showing all linked children
- View children's attendance
- View children's grades

## Technology Stack

- **Backend**: Laravel 11, PHP 8.5
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Database**: MySQL 8.4 (via Docker)
- **Cache/Queue**: Redis 7
- **Authentication**: Laravel Sanctum
- **Authorization**: Spatie Laravel Permission
- **Container**: Docker & Docker Compose

## Project Structure

```
skuul/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── Admin/
│   │   │   ├── Teacher/
│   │   │   ├── Student/
│   │   │   └── Parent/
│   │   ├── Kernel.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Teacher.php
│   │   ├── ParentModel.php
│   │   ├── Classroom.php
│   │   ├── Subject.php
│   │   ├── Enrollment.php
│   │   ├── Attendance.php
│   │   ├── Grade.php
│   │   ├── Announcement.php
│   │   ├── AcademicYear.php
│   │   └── Term.php
│   ├── Providers/
│   └── Exceptions/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── components/
│   │   ├── auth/
│   │   ├── admin/
│   │   ├── teacher/
│   │   ├── student/
│   │   ├── parent/
│   │   ├── attendance/
│   │   └── grades/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── docker/
│   ├── Dockerfile
│   ├── supervisord.conf
│   ├── php.ini
│   └── start-container
├── docker-compose.yml
├── composer.json
├── artisan
└── README.md
```

## Installation

### Using Docker (Recommended)

1. **Start the containers:**
   ```bash
   docker-compose up -d --build
   ```

2. **Install PHP dependencies:**
   ```bash
   docker-compose exec skuul-app composer install
   ```

3. **Generate application key:**
   ```bash
   docker-compose exec skuul-app php artisan key:generate
   ```

4. **Run migrations and seeders:**
   ```bash
   docker-compose exec skuul-app php artisan migrate --seed
   ```

5. **Install frontend dependencies:**
   ```bash
   docker-compose exec skuul-app npm install
   docker-compose exec skuul-app npm run build
   ```

6. **Access the application:**
   - URL: http://localhost
   - MailHog: http://localhost:8025

### Default Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@skuul.local | password |
| Teacher | teacher@skuul.local | password |
| Student | student@skuul.local | password |
| Parent | parent@skuul.local | password |

## Database Schema

### Core Tables
- **users** - System users with role_type (admin, teacher, student, parent)
- **students** - Student profiles linked to users
- **teachers** - Teacher profiles linked to users
- **parents** - Parent profiles linked to users
- **academic_years** - School years (e.g., 2024-2025)
- **terms** - Terms/semesters within academic years
- **classrooms** - Class sections (Grade 10-A, etc.)
- **subjects** - Academic subjects
- **enrollments** - Student-classroom assignments
- **attendance** - Daily attendance records
- **grades** - Student grades/scores
- **announcements** - School-wide announcements

### Spatie Permission Tables
- **roles** - admin, teacher, student, parent
- **permissions** - Granular permissions
- **model_has_roles** - Role assignments
- **model_has_permissions** - Direct permission assignments
- **role_has_permissions** - Role-permission mappings

## API Endpoints

### Authentication
- `POST /api/login` - Authenticate and receive token
- `POST /api/logout` - Revoke token

## Development

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Run tests
php artisan test

# Clear cache
php artisan optimize:clear

# Route list
php artisan route:list
```

## License

MIT