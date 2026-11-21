# Skuul API

All endpoints are JSON. Except `POST /api/auth/login`, every endpoint requires a Sanctum token.

## Authentication

Send the token on every authenticated request:

```http
Authorization: Bearer <token>
```

### Login

`POST /api/auth/login`

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

```json
{
  "token": "1|...",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "Admin", "email": "admin@example.com", "roles": [{ "name": "admin" }] }
}
```

Invalid credentials return `401`.

| Method | Endpoint | Description |
| --- | --- | --- |
| POST | `/api/auth/login` | Authenticate and issue a token named `api-token` |
| GET | `/api/auth/me` | Authenticated user with roles |
| POST | `/api/auth/logout` | Delete the current access token |

## Dashboard

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/dashboard` | Role-aware counts and quick stats; returns `{ "role": "...", "data": {...} }` |

## Catalog

Reads are available to any authenticated user. `POST`, `PUT`, and `DELETE` require the `admin` role.

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/students` | Paginated students |
| POST | `/api/students` | Create student (admin) |
| GET | `/api/students/{id}` | Show student |
| PUT | `/api/students/{id}` | Update student (admin) |
| DELETE | `/api/students/{id}` | Delete student (admin) |
| GET | `/api/teachers` | Paginated teachers |
| POST | `/api/teachers` | Create teacher (admin) |
| GET | `/api/teachers/{id}` | Show teacher |
| PUT | `/api/teachers/{id}` | Update teacher (admin) |
| DELETE | `/api/teachers/{id}` | Delete teacher (admin) |
| GET | `/api/classrooms` | Paginated classrooms |
| POST | `/api/classrooms` | Create classroom (admin) |
| GET | `/api/classrooms/{id}` | Show classroom |
| PUT | `/api/classrooms/{id}` | Update classroom (admin) |
| DELETE | `/api/classrooms/{id}` | Delete classroom (admin) |
| GET | `/api/subjects` | Paginated subjects |
| POST | `/api/subjects` | Create subject (admin) |
| GET | `/api/subjects/{id}` | Show subject |
| PUT | `/api/subjects/{id}` | Update subject (admin) |
| DELETE | `/api/subjects/{id}` | Delete subject (admin) |
| GET | `/api/academic-years` | Paginated academic years |
| POST | `/api/academic-years` | Create academic year (admin) |
| GET | `/api/academic-years/{id}` | Show academic year |
| PUT | `/api/academic-years/{id}` | Update academic year (admin) |
| DELETE | `/api/academic-years/{id}` | Delete academic year (admin) |
| GET | `/api/announcements` | Paginated announcements |
| POST | `/api/announcements` | Create announcement (admin; `user_id` defaults to the caller) |
| GET | `/api/announcements/{id}` | Show announcement |
| PUT | `/api/announcements/{id}` | Update announcement (admin) |
| DELETE | `/api/announcements/{id}` | Delete announcement (admin) |

`per_page` (1–100, default 15) is supported on all paginated endpoints.

## Academic

All role-filtered relative to the authenticated user. Students and parents only see published exams; exam results are limited to the student or the parent's children.

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/timetables` | Admin all, teacher own, student/parent by classroom |
| GET | `/api/grades` | Admin all, teacher own subjects, student own, parent children |
| GET | `/api/attendance` | Admin all, teacher own, student own, parent children |
| GET | `/api/exams` | Admin all, teacher own subjects, student/parent published exams |

## Finance

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/fee-structures` | Admin all, others active only |
| GET | `/api/fee-records` | Admin all, parent children, student own |
| POST | `/api/fee-records` | Create fee record (admin) |
| PUT | `/api/fee-records/{feeRecord}` | Update fee record (admin) |

`POST /api/fee-records` accepts `student_id`, `fee_structure_id`, `amount`, `amount_paid`, `due_date`, `status`, `paid_at`. `PUT` accepts any subset of `amount`, `amount_paid`, `due_date`, `status`, `paid_at`.

## Library

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/books` | Paginated books; optional `search` |
| GET | `/api/book-issues` | Admin all, student/teacher own, parent children |
| POST | `/api/book-issues` | Issue a book (admin) |
| PUT | `/api/book-issues/{bookIssue}/return` | Return a book (admin) |

`POST /api/book-issues` accepts `book_id`, `borrower_type` (`student` or `teacher`), `borrower_id`, `issued_at`, `due_date`. Availability is guarded inside a database transaction; `409` is returned when no copies are available or the book was already returned.

## Transport

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/api/transport-routes` | Admin all, others active only |
| GET | `/api/vehicles` | Admin all, others active only |
| GET | `/api/transport-assignments` | Admin all, student/parent/teacher own |

## Error responses

| Status | Meaning |
| --- | --- |
| `401` | Missing/invalid token or invalid credentials |
| `403` | Authenticated but role is not allowed |
| `404` | Unknown resource or record |
| `409` | Library availability conflict |
| `422` | Validation failure |
