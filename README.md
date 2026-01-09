📌 Task Management Application

Laravel 10+ (PHP 8.1+) · Vue.js 3

📖 Overview

This project is a Task Management Application built as part of a technical assessment for a Senior Laravel Developer role.

The application provides a RESTful API built with Laravel and a simple Vue.js frontend focused on functionality rather than UI styling.

Key Highlights

RESTful API using Laravel 10+

Frontend built with Vue.js 3 (Composition API)

MySQL / PostgreSQL support

Feature tests, factories, and seeders

Clean architecture following PSR-12 and SOLID principles

Optional Docker setup (bonus)

🛠️ Technology Stack
Layer	Technology
Backend	Laravel 10+, PHP 8.1+
Frontend	Vue.js 3 (Composition API), Vite
Database	MySQL / PostgreSQL
Testing	Pest (PHPUnit under the hood)
Containerization	Docker & Docker Compose (Bonus)
✅ Functional Requirements Coverage
Backend (REST API)

✔ Create Task

✔ List Tasks

✔ Get Single Task

✔ Update Task

✔ Delete Task

Task Fields

title (required, string, max 255)

description (optional, text)

status (pending, in_progress, completed)

due_date (optional, date)

🎨 Frontend Functional Coverage (Vue.js 3)

The Vue.js frontend implements all required features:

✔ Display list of tasks

✔ Create a new task

✔ Update task status (dropdown)

✔ Delete a task

UI styling is intentionally minimal, focusing on functionality as required.

🌐 API Endpoints
Method	Endpoint	Description
GET	/api/tasks	List all tasks (pagination + optional status filter)
POST	/api/tasks	Create new task
GET	/api/tasks/{id}	Get single task
PUT	/api/tasks/{id}	Update task
DELETE	/api/tasks/{id}	Delete task (soft delete)
Example: List Tasks
curl -X GET http://127.0.0.1:8000/api/tasks?status=pending

⚙️ Bonus Features Implemented

✔ Task filtering by status
GET /api/tasks?status=pending

✔ Pagination (10 items per page)

✔ Soft Deletes (Laravel)

✔ Docker setup (optional / bonus)

🧱 Code Quality & Architecture

✔ PSR-12 coding standards

✔ SOLID principles

✔ Form Request validation

✔ API Resource response formatting

✔ Proper HTTP status codes

✔ Clean and modular folder structure

🧪 Testing (MANDATORY – COMPLETED)

5 Feature Tests Implemented

#	Test Case	Status
1	Create task (success)	✅
2	Create task (validation failure)	✅
3	List tasks	✅
4	Update task	✅
5	Delete task	✅
Run Tests
php artisan test

🗄️ Database Setup
Migrations
php artisan migrate
php artisan migrate:fresh
php artisan migrate:status

Seeders
php artisan migrate --seed
php artisan db:seed --class=TaskSeeder
php artisan migrate:fresh --seed


Verify seeded data:

php artisan tinker
App\Models\Task::count();


Expected: 5–10

🚀 Running the Application (Local Setup)
1️⃣ Clone Repository
git clone https://github.com/your-username/task-app-yourname.git
cd task-app-yourname

2️⃣ Install Backend Dependencies
composer install

3️⃣ Environment Setup
cp .env.example .env
php artisan key:generate


Update .env:

DB_DATABASE=task_app
DB_USERNAME=root
DB_PASSWORD=

4️⃣ Run Migrations & Seeders
php artisan migrate --seed

5️⃣ Start Laravel Server
php artisan serve


Backend available at:

http://127.0.0.1:8000

🎨 Running the Frontend (Vue.js 3)
cd frontend
npm install
npm run dev


Frontend available at:

http://localhost:5173

API Connection

frontend/src/services/api.js

import axios from 'axios';

export default axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
});

🐳 Docker Setup (Bonus)

Docker is included as a bonus feature and is not required to run the project.

Quick Start
docker compose up --build -d
docker compose exec backend php artisan migrate --seed


Access:

Backend API: http://localhost/api/tasks

Frontend: http://localhost

🧪 Useful Commands
php artisan serve
php artisan migrate --seed
php artisan migrate:fresh --seed
php artisan test

📌 Submission Checklist

✔ Public GitHub repository

✔ Repository name: task-app-yourname

✔ Meaningful commit history (no squashing)

✔ Backend API working

✔ Vue frontend working

✔ Tests passing

✔ README included

👤 Author

Name: Gautam Kumar

Time Taken: approx 40-50 minutes 

Repository: task-app-gautam-kumar

✅ Final Confirmation

All mandatory requirements from the task document have been fully implemented and verified.