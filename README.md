# 🧵 Amigurumi Pattern Designer

A web-based tool for designing and managing **Amigurumi crochet patterns** with an intuitive interface. Built with **Laravel** and **Vue.js**, it allows creators to organize patterns into sections and rows, edit them easily, and export finished patterns — with plans for multilingual and PDF support.

---

## ✨ Features

- 🧩 Create and edit Amigurumi patterns with:
  - Sections and rows
  - Stitch count, instructions, comments
  - Drag & drop ordering
- 📌 Add optional comments per row (with toggle)
- 🔁 Duplicate and reorder rows and sections
- 📋 Future: Copy & paste support for elements
- 🌍 Future: Multilingual support (with Vue i18n)
- 🖼️ Future: Image upload for each pattern
- 📁 Future: Folder-based organization (e.g., Animals, Flowers)
- 📄 Planned: PDF export with:
  - Background and cover image
  - Social media links and author details
  - Custom colors and fonts
  - “Tips and Tricks” section

---

## 🛠 Tech Stack

- **Frontend**: Vue 3 + Bootstrap 5
- **Backend**: Laravel 10
- **PDF Export (planned)**: Laravel DomPDF
- **Database**: MySQL or SQLite (configurable)

---

## 📦 Installation

### Prerequisites

- PHP 8.1+
- Composer
- Node.js + npm
- MySQL / SQLite

### Setup Instructions

```bash
# 1. Clone the repository
git clone https://github.com/your-username/amigurumi-pattern-designer.git

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# 5. Configure your database in `.env`

# 6. Run migrations
php artisan migrate

# 7. Start the development server
php artisan serve

# 8. Run Vite for frontend development
npm run dev
🧪 Development Notes
Vue components are in resources/js/components/

Blade views use Bootstrap 5 classes

API endpoints follow RESTful conventions

Editing uses drag-and-drop via vuedraggable

Pattern data is updated via AJAX PUT requests

✅ Planned Improvements
See the TODO list for upcoming features such as:

Auto-generated rows based on inc / dec logic

Validation for stitch number divisibility

PDF export styling and structure

Image handling and pattern previews