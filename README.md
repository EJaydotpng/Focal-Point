# IT Support Ticketing System (Laravel)

A lightweight, self-hosted Jira-style ticketing board for internal IT support.

## Features
- **Kanban board** with drag-and-drop tickets between columns (built with SortableJS, no page reload)
- **Custom progress indicators (columns)** — create/rename/recolor/delete your own stages (To Do, In Progress, Checking, Done, or anything else)
- **Custom categories** — tag tickets (Hardware, Software, Network, etc.) and filter the board by category
- **Ticket details**: title, description, priority, reporter, assignee, due date
- **Image & video attachments per ticket** — upload photos/screen recordings of the problem, and later use the same area to upload your "means of verification" (MOV) once resolved
- Built with plain Blade + Tailwind (CDN) + Alpine.js + SortableJS — **no npm build step required**

## ⚠️ Important — how to install this

This folder contains only the **application code** (models, controllers, migrations, routes, views) — not a full Laravel installation (the `vendor/` folder and Laravel framework itself, which is ~50MB of files pulled from Packagist). You need to generate a fresh Laravel skeleton yourself and then copy these files on top of it. This only takes a couple of minutes.

### Step 1 — Create a fresh Laravel project
On a machine with internet + Composer + PHP installed:

```bash
composer create-project laravel/laravel it-ticketing-system
cd it-ticketing-system
```

### Step 2 — Copy these files into it
Copy the contents of this package into the new project, overwriting/adding:

```bash
# From inside this "ticketing-system" folder:
cp -r app/Models/*           <path-to-project>/app/Models/
cp -r app/Http/Controllers/* <path-to-project>/app/Http/Controllers/
cp -r database/migrations/*  <path-to-project>/database/migrations/
cp -r database/seeders/*     <path-to-project>/database/seeders/
cp -r resources/views/*      <path-to-project>/resources/views/
cp routes/web.php            <path-to-project>/routes/web.php
```

(On Windows, just drag-and-drop/merge the folders in File Explorer or your IDE.)

### Step 3 — Configure the database
Open `.env` in the new project. The simplest option is SQLite (already the Laravel 11/12 default):

```
DB_CONNECTION=sqlite
```
```bash
touch database/database.sqlite
```

Or use MySQL — set `DB_CONNECTION=mysql` and fill in `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, etc.

### Step 4 — Link storage (so uploaded images/videos are viewable)
```bash
php artisan storage:link
```

### Step 5 — Migrate and seed default columns/categories
```bash
php artisan migrate --seed
```
This creates the default columns **To Do, In Progress, Checking, Done** and a few starter categories (Hardware, Software, Network, Account Access). You can rename/delete/add to these anytime from the "Columns" and "Categories" pages in the app.

### Step 6 — Allow bigger uploads (for video files)
By default PHP limits uploads to 2MB. Edit your `php.ini` (or set in `.htaccess` / server config):
```
upload_max_filesize = 100M
post_max_size = 100M
```
Restart PHP/your webserver after changing this.

### Step 7 — Run it
```bash
php artisan serve
```
Visit **http://localhost:8000** — you'll land on the ticket board.

## How to use it
1. Go to **Columns** to set up your workflow stages (defaults already there: To Do, In Progress, Checking, Done — add more like "Waiting for Parts" if you want).
2. Go to **Categories** to define ticket tags (Hardware, Software, Network, etc.).
3. Click **+ New Ticket**, fill in the details, optionally attach photos/screen recordings of the problem right away.
4. On the board, **drag a ticket card** between columns as it progresses — the change saves automatically.
5. Open any ticket to **upload more images/videos** (e.g. proof/MOV once it's fixed), change its status, edit details, or delete it.
6. Use the **category filter** dropdown on the board to narrow it down.

## Notes / things you may want to add later
- **Authentication** — this version has no login system (assignee/reporter are free-text fields), since it wasn't requested. If you want per-user logins, install Laravel Breeze (`composer require laravel/breeze --dev && php artisan breeze:install`) and add `auth` middleware to the routes in `routes/web.php`.
- **Comments/activity log per ticket** — not included; can be added as a simple `comments` table + relation if you want a running history on each ticket.
- **Email notifications** on ticket assignment/status change — can be added with Laravel's Mail/Notifications once you configure SMTP.
- Accepted upload types: images (jpg, jpeg, png, gif, webp) and videos (mp4, mov, avi, webm, mkv), 100MB max per file — adjust in `app/Http/Controllers/TicketController.php` (`storeAttachments`/validation rules) if needed.

## File map
```
app/Models/           Status, Category, Ticket, Attachment
app/Http/Controllers/ StatusController, CategoryController, TicketController, AttachmentController
database/migrations/  statuses, categories, tickets, category_ticket (pivot), attachments
database/seeders/      DefaultDataSeeder (seeds default columns + categories)
resources/views/       layouts/app, tickets/*, statuses/index, categories/index
routes/web.php          all routes
```
