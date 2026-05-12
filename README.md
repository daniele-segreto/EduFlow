# EduFlow — Dashboard insegnante / formatore

Applicazione web **PHP + MySQL + Bootstrap 5 + jQuery (AJAX)** per organizzare materie, lezioni, calendario, eventi/impegni e task quotidiani. Interfaccia pensata come prodotto **SaaS** minimale (bianco e celeste).

## Requisiti

- WAMP / XAMPP / stack LAMP con **PHP 8+** ed estensione **PDO MySQL**
- **MySQL 5.7+** / MariaDB 10.3+

## Installazione

1. Copia il progetto sotto la root del server (es. `c:\wamp64\www\cursor-dashboard`).
2. In **phpMyAdmin** (o CLI MySQL) importa il file:

   `database/schema.sql`

   Verrà creato il database `teacher_dashboard` (se non esiste) con tabelle, vincoli e dati demo.

3. Configura le credenziali MySQL in `config/database.php` (di default: utente `root`, password vuota, host `127.0.0.1`, database `teacher_dashboard`).

4. Se l’URL del progetto **non** è `http://localhost/cursor-dashboard/`, imposta la costante **`APP_URL_BASE`** in `config/app.php` (percorso senza slash finale, es. `/mio-progetto`, oppure stringa vuota se il sito è in root del virtual host).

5. Apri nel browser:

   `http://localhost/cursor-dashboard/login.php`

### Utente demo

| Campo    | Valore               |
| -------- | -------------------- |
| Email    | `demo@eduflow.local` |
| Password | `demo123`            |

## Struttura cartelle

- `assets/css`, `assets/js` — fogli di stile e script front-end
- `components/` — layout riutilizzabile (`header`, `navbar`, `sidebar`, `footer`, `modals`, `shell_start`)
- `pages/` — pagine applicative protette da sessione
- `ajax/` — endpoint JSON (CRUD, calendario, login, ecc.)
- `config/` — app e database
- `includes/` — funzioni comuni e autenticazione
- `database/schema.sql` — schema e seed

## Sicurezza e scalabilità

- Query tramite **PDO** con **prepared statements**
- Validazione e sanitizzazione lato server (`includes/functions.php`)
- Sessione con cookie **HttpOnly** e **SameSite=Lax** (`includes/auth.php`)
- Password demo memorizzata con **password_hash** (BCRYPT)

Per la produzione: disattivare `display_errors`, servire HTTPS, aggiungere CSRF sui form POST, hardening sessioni e policy password.

## Note tecniche

- Il calendario carica intervalli date via `ajax/load_calendar.php` e unisce **eventi** e **lezioni** (lezioni con colore della materia).
- I modali in `components/modals.php` sono condivisi; il salvataggio è centralizzato in `assets/js/app.js` con eventi custom `app:*-changed` per aggiornare le viste.

## Licenza

Uso interno / didattico; adatta licenza e marchio alle tue esigenze.
