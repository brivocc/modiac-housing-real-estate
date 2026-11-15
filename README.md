# Modiac Housing – Real Estate Website (PHP & MySQL)

A property listing website where buyers browse and buy property, agents list and manage properties, and an admin manages the whole site.

> **Credits.** This project is based on the open-source **Real Estate PHP** project by **Noor Muhammed** (see `DATABASE FILE/01 LOGIN DETAILS & PROJECT INFO.txt`).
> It was customised as **Modiac Housing**; the site footer credits **Ian Waweru**. This repository is published by **Bevon Manyara** ([@brivocc](https://github.com/brivocc)).

## Features in this version

- **Property listings:** grid and detail pages, search by state and city, featured properties
- **Kenyan property data:** Kenyan locations and listings (`kenyan_property_update.sql`, `get_cities.php`)
- **Purchase system:** buyers purchase property; agents see their sales (`purchase.php`, `agent_purchases.php`, `DATABASE FILE/updates_for_purchase_system.sql`)
- **Dashboards:** separate dashboards for users and agents (`user_dashboard.php`, `agent_dashboard.php`)
- **Chat box:** messaging between buyers and agents (`chatbox.php`, `chatbox.sql`)
- **Admin panel:** manage properties, agents, users, cities, about page, contacts and feedback, and generate invoices (`admin/`)
- **Property submission:** users and agents add, edit and delete their own listings
- Mortgage/loan calculator (`calc.php`)

## Tech

PHP · MySQL (mysqli) · Bootstrap · jQuery · HTML · CSS

## Run it locally (XAMPP)

1. Copy this folder into `C:\xampp\htdocs\real-estate`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open **http://localhost/phpmyadmin**, create a database named **`realestatephp`**, and import:
   - `DATABASE FILE/realestatephp.sql`
   - `DATABASE FILE/updates_for_purchase_system.sql`
   - `kenyan_property_update.sql`
   - `chatbox.sql`
4. Visit **http://localhost/real-estate**.
5. Admin panel: **http://localhost/real-estate/admin**. The demo admin login is in `DATABASE FILE/01 LOGIN DETAILS & PROJECT INFO.txt`.

Database settings are in `config.php` and `admin/config.php` (default XAMPP: user `root`, no password).

> ⚠️ **Before putting this online:** change the admin password, use a MySQL user with a strong password, and review the code for security (use prepared statements for all queries).

Recommended PHP version: 7.4.
