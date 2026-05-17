# Quiz System — DBX Tech Task

A Geography & Current Affairs quiz application built with **Laravel**, **MySQL**, and **AJAX**. Users enter their name, answer 5 randomly ordered questions, and receive a detailed result breakdown at the end.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Requirements Compliance](#requirements-compliance)
- [Installation](#installation)
- [Running the Application](#running-the-application)
- [Seeder](#seeder)
- [How It Works](#how-it-works)
- [AJAX Endpoints](#ajax-endpoints)
- [Browser Resume Feature](#browser-resume-feature)

---

## Features

- Name entry screen to start the quiz
- 5 geography / current affairs questions served in **random order** every session
- Each question has 4 answer options (radio buttons)
- **Skip** button to skip a question (recorded as skipped)
- **Next** button to submit the selected answer and move on
- No page refreshes — all question transitions are handled via **AJAX**
- Result page showing correct, wrong, and skipped counts with a per-question breakdown
- **Browser resume** — if a user closes the browser mid-quiz, they can re-enter their name and continue from where they left off
- Fully responsive UI using Bootstrap 5

---

## Tech Stack

| Layer      | Technology                  |
|------------|-----------------------------|
| Framework  | Laravel 11 (PHP 8.3)        |
| Database   | MySQL                       |
| Frontend   | Bootstrap 5, Vanilla JS     |
| AJAX       | Fetch API (JSON)            |
| Session    | Laravel file-based sessions |

---

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── UserController.php      # Name entry, session start, resume check
│       ├── QuizController.php      # Quiz page + next-question AJAX
│       ├── AnswerController.php    # Record answer/skip via AJAX
│       └── ResultController.php   # Final result page with aggregate query
└── Models/
    ├── User.php
    ├── Question.php
    ├── Answer.php
    └── Result.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_questions_table.php
│   ├── 2024_01_01_000003_create_answers_table.php
│   └── 2024_01_01_000004_create_results_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── QuizSeeder.php              # 5 questions × 4 answers

resources/views/
├── layouts/
│   └── app.blade.php               # Master layout (navbar, Bootstrap, styles)
├── welcome.blade.php               # Name entry page
├── quiz.blade.php                  # Quiz page (initial load + AJAX-driven)
└── result.blade.php                # Result summary page

routes/
└── web.php                         # All application routes
```

---

## Database Schema

### `users`
| Column       | Type         | Notes                  |
|--------------|--------------|------------------------|
| id           | BIGINT PK AI |                        |
| name         | VARCHAR(100) |                        |
| created_at   | TIMESTAMP    |                        |
| updated_at   | TIMESTAMP    |                        |

### `questions`
| Column        | Type         | Notes                  |
|---------------|--------------|------------------------|
| id            | BIGINT PK AI |                        |
| question_text | VARCHAR(500) |                        |
| created_at    | TIMESTAMP    |                        |
| updated_at    | TIMESTAMP    |                        |

### `answers`
| Column      | Type         | Notes                              |
|-------------|--------------|------------------------------------|
| id          | BIGINT PK AI |                                    |
| question_id | BIGINT FK    | References `questions.id` CASCADE  |
| answer_text | VARCHAR(300) |                                    |
| is_correct  | TINYINT(1)   | 1 = correct, 0 = wrong             |
| created_at  | TIMESTAMP    |                                    |
| updated_at  | TIMESTAMP    |                                    |

### `results`
| Column      | Type                          | Notes                                      |
|-------------|-------------------------------|--------------------------------------------|
| id          | BIGINT PK AI                  |                                            |
| user_id     | BIGINT FK                     | References `users.id` CASCADE              |
| question_id | BIGINT FK                     | References `questions.id` CASCADE          |
| answer_id   | BIGINT FK NULLABLE            | References `answers.id` SET NULL (skipped) |
| status      | ENUM('correct','wrong','skipped') |                                        |
| created_at  | TIMESTAMP                     |                                            |
| updated_at  | TIMESTAMP                     |                                            |

> Unique constraint on `(user_id, question_id)` prevents duplicate entries.

The schema is in **3NF** (Third Normal Form):
- No repeating groups
- All non-key attributes depend on the full primary key
- No transitive dependencies

---

## Requirements Compliance

| # | Requirement | Implementation |
|---|-------------|----------------|
| 1 | Session stores only user name/id | `session(['user_id' => $user->id])` — only the ID is stored |
| 2 | MVC pattern | Controllers in `app/Http/Controllers`, Models in `app/Models`, Views in `resources/views` |
| 3 | SQL Aggregate functions | `ResultController` uses `COUNT(*)`, `SUM(CASE WHEN ...)` for result summary |
| 4 | All server communication via AJAX with JSON | `/quiz/next`, `/answer`, `/check-resume` all use `Content-Type: application/json` |
| 5 | Normalized database | Schema is in 3NF with proper foreign keys |
| 6 | Elegant and readable code | PSR-12 style, single-responsibility controllers, descriptive names |
| 7 | PHP version min 7.3 | Built on PHP 8.3; uses typed return hints and named arguments |
| 8 | No unwanted variables/functions | Lean controllers, no dead code |
| 9 | SQL functions used where faster | `RAND()` via `inRandomOrder()`, `COUNT`, `SUM` used in DB layer instead of PHP |

**Optional features implemented:**
- ✅ Browser resume — user can close and reopen the browser and continue from where they left off
- ✅ Client-side validation — toast notification if no answer selected before clicking Next

---

## Installation

### Prerequisites

- PHP >= 7.3 (tested on 8.3)
- Composer
- MySQL
- A web server (Apache/Nginx or `php artisan serve`)

### Steps

**1. Clone the repository**

```bash
git clone https://github.com/akrazalive/ques_answers_system.git
cd ques_answers_system
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Copy environment file**

```bash
cp .env.example .env
```

**4. Configure `.env`**

Open `.env` and set your database credentials:

```env
APP_URL=http://localhost/dbx_tech_task/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbx_tech_task
DB_USERNAME=root
DB_PASSWORD=your_password
```

**5. Generate application key**

```bash
php artisan key:generate
```

**6. Create the database**

In MySQL:

```sql
CREATE DATABASE dbx_tech_task CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**7. Run migrations**

```bash
php artisan migrate
```

**8. Seed the database**

```bash
php artisan db:seed
```

---

## Running the Application

**Using WAMP / XAMPP:**

Place the project in your `www` or `htdocs` folder and access:

```
http://localhost/dbx_tech_task/public
```

**Using Laravel's built-in server:**

```bash
php artisan serve
```

Then open `http://127.0.0.1:8000`

---

## Seeder

The `QuizSeeder` inserts **5 geography / current affairs questions**, each with **4 answer options** (1 correct, 3 wrong):

| # | Question                                      | Correct Answer |
|---|-----------------------------------------------|----------------|
| 1 | What is the currency of Brazil?               | Real           |
| 2 | What is the capital city of Qatar?            | Doha           |
| 3 | Which country hosted the FIFA World Cup 2022? | Qatar          |
| 4 | What is the largest continent by area?        | Asia           |
| 5 | Which river is the longest in the world?      | Nile           |

To re-seed (after wiping data):

```bash
php artisan migrate:fresh --seed
```

---

## How It Works

### Flow

```
[Welcome Page]
    ↓  POST /start (form submit)
[Quiz Page]  ← initial question loaded server-side (non-AJAX, as per exemption)
    ↓  User selects answer → clicks Next
    POST /answer  (AJAX, JSON)  → records result in DB
    POST /quiz/next (AJAX, JSON) → returns next random unanswered question
    ↓  ... repeat until all questions answered ...
[Result Page]  ← redirect (GET /result)
```

### Random Question Order

- On every request to `/quiz/next`, the server queries questions **excluding already-answered IDs** and applies `ORDER BY RAND()` (MySQL) via Laravel's `inRandomOrder()`.
- This means the order is different every session and every "next" call is independently random.

### Result Calculation

The result page uses a single SQL query with conditional aggregation:

```sql
SELECT
    COUNT(*) AS total,
    SUM(CASE WHEN status = 'correct' THEN 1 ELSE 0 END) AS correct,
    SUM(CASE WHEN status = 'wrong'   THEN 1 ELSE 0 END) AS wrong,
    SUM(CASE WHEN status = 'skipped' THEN 1 ELSE 0 END) AS skipped
FROM results
WHERE user_id = ?
```

---

## AJAX Endpoints

All endpoints below require `Content-Type: application/json` and return JSON.

### `POST /quiz/next`

Returns the next random unanswered question for the current session user.

**Response (question available):**
```json
{
  "finished": false,
  "question": { "id": 3, "text": "Which river is the longest in the world?" },
  "answers":  [
    { "id": 9,  "answer_text": "Amazon" },
    { "id": 10, "answer_text": "Nile"   },
    { "id": 11, "answer_text": "Yangtze"},
    { "id": 12, "answer_text": "Congo"  }
  ],
  "remaining": 2
}
```

**Response (all done):**
```json
{ "finished": true }
```

---

### `POST /answer`

Records the user's answer or skip.

**Request body:**
```json
{ "question_id": 3, "answer_id": 10 }
```

Pass `"answer_id": null` to record a skip.

**Response:**
```json
{ "message": "Recorded." }
```

---

### `POST /check-resume`

Checks if a name has an in-progress quiz that can be resumed.

**Request body:**
```json
{ "name": "John" }
```

**Response (resumable):**
```json
{ "resumable": true, "user_id": 5, "answered": 3, "remaining": 2 }
```

**Response (not resumable):**
```json
{ "resumable": false }
```

---

## Browser Resume Feature

If a user closes the browser mid-quiz:

1. They return to the welcome page and type their name.
2. The app sends a `POST /check-resume` AJAX call (debounced, 500ms after typing stops).
3. If an incomplete session is found, a banner appears offering **Resume Quiz** or **Start Fresh**.
4. Choosing **Resume** restores the session and takes them back to the quiz with remaining questions.
5. Choosing **Start Fresh** creates a new user record and starts over.

---

## License

MIT
