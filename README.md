# Smart Accident Alert System

A web-based road-accident alert system. A smartphone browser reads GPS and motion sensors, detects a probable accident using a weighted risk score, gives the driver a 15-second chance to cancel, and then e-mails emergency contacts and nearby hospitals with the live location.

B.Sc. Computer Science (Sem V), University of Mumbai, S.R.M. College, Kudal — Project 2026-27.
Live site: https://accidentalert2026.infinityfreeapp.com

## Features
- Signup / login with hashed passwords and prepared SQL statements
- Live GPS location, speed and risk level on the dashboard
- Accident detection: sudden speed drop (up to 55) + impact above 2.8 g (up to 30) + post-impact stillness (up to 15) = risk score 0-100
- 15-second cancellable countdown to reduce false alarms
- Manual SOS button
- Emergency contact management, alert history, driving-behaviour chart
- E-mail alerts through the Brevo API; nearby hospitals from OpenStreetMap Overpass

## Tech stack
HTML, CSS, JavaScript (Geolocation + DeviceMotion APIs) · PHP 7.4+ · MySQL/MariaDB · Brevo e-mail API · OpenStreetMap Overpass API

## Project structure
```
.
├── login.php / signup.php / logout.php
├── dashboard.php        live monitoring page
├── contacts.php         emergency contacts
├── history.php          alert history
├── sos_alert.php        creates alert, finds hospitals, sends e-mails
├── cancel_alert.php     marks an alert as Cancelled
├── log_driving.php      stores periodic speed samples
├── schema.sql           database tables
├── includes/            config.sample.php, functions.php, navbar.php
└── assets/js/detection.js   detection engine
```

## Setup (local, XAMPP/WAMP)
1. Copy the folder to `htdocs` and start Apache and MySQL.
2. Create a database in phpMyAdmin and import `schema.sql`.
3. Copy `includes/config.sample.php` to `includes/config.php` and fill in `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.
4. For real e-mails: create a free Brevo account, generate an API key, verify a sender address, then set `BREVO_API_KEY` and `EMAIL_FROM_ADDRESS` in `config.php`.
5. Open `http://localhost/<folder>/signup.php`. Sensors need HTTPS on a phone (or localhost), so use the hosted site for real device testing.

`includes/config.php` is in `.gitignore` — never commit real keys or passwords.

## Database tables
`users`, `emergency_contacts`, `alerts`, `driving_logs` (see `schema.sql`).

## Testing
18 manual test cases (signup, login, contacts, monitoring, SOS, countdown cancel, history) — all passed. See Chapter III, section 11 of the project report.

## Author
Balkrishna Zilu Keluskar — Guides: Asst. Prof. Santosh Chougule, Asst. Prof. Prakash Talankar
