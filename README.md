# 📁 Gestione Dipendenti API (Laravel RESTful)

Sistema completo per la **gestione di dipendenti**, documenti, gruppi e scadenze, progettato con **Laravel 12** e pronto per integrazione con frontend React/Angular.

---

## 🌟 Funzionalità

| Modulo | Cosa fa? |
|--------|----------|
| **Autenticazione** | Login locale o via Active Directory (LDAP) con token Bearer (Sanctum) |
| **Dipendenti** | CRUD, gruppi di appartenenza, documenti obbligatori, scadenze |
| **Documenti** | CRUD, upload immagini/allegati, export PDF / Excel |
| **Gruppi** | CRUD, mappano set di documenti da richiedere al dipendente |
| **Presenze / Assenze** | Ferie, malattia, ulteriori tipi personalizzabili |
| **Ruoli** | `admin` (accesso globale) · `operator` (vincolato ai gruppi LDAP) |
| **Log & Audit** | channel `auth`, `audit`, `error` (+ storage su Docker volume) |
| **Docker** | nginx + PHP‑FPM + MySQL + scheduler in background |

---

## ⚙️ Requisiti

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Laravel 12
- Docker

---

## 🚀 Setup locale

```bash
git clone https://github.com/<tuo-user>/GestioneDipendentiAPI.git
cd GestioneDipendentiAPI
cp .env.example .env           # configura DB + LDAP
docker compose up -d           # nginx 8080, mysql 3307, php‑fpm
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

---

## 🔐 Autenticazione

- API protette da **Laravel Sanctum**
- Login via token Bearer
- Utenti con ruoli: `admin` o `operator`

---

## 🔗 API Endpoint principali

## 🔐 Autenticazione & Token

| Metodo | Endpoint | Descrizione | Esempio payload |
|--------|----------|-------------|-----------------|
| `POST` | `/api/login` | Login locale (username/email + password) | `{ "username": "admin", "password": "secret" }` |
| `POST` | `/api/ldap-login` | Login via Active Directory | `{ "username": "mario.rossi", "password": "Passw0rd!" }` |
| `POST` | `/api/logout` | Invalida token corrente | *Header* `Authorization: Bearer <token>` |
| `POST` | `/api/register` | **Solo admin** – crea utente locale | `{ "name": "Utente Demo", "username":"demo", "email":"demo@site.com", "password":"secret", "password_confirmation":"secret" }` |

---

## 👤 Dipendenti

| Metodo | Endpoint | Descrizione | Payload / Query string |
|--------|----------|-------------|------------------------|
| `GET`  | `/api/employees` | Lista completa (groups + documents) | — |
| `POST` | `/api/employees` | Crea dipendente | `{ "name":"Mario", "surname":"Rossi", "email":"m.rossi@azienda.it", "phone":"+390123456" }` |
| `GET`  | `/api/employees/{id}` | Dettaglio + relazioni | — |
| `PUT`/`PATCH` | `/api/employees/{id}` | Aggiorna dati | stesso payload `POST` |
| `DELETE` | `/api/employees/{id}` | Elimina record | — |
| `GET` | `/api/employees/{id}/documents` | Documenti assegnati con scadenze | — |
| `GET` | `/api/employees/{id}/missing-documents` | Documenti richiesti ma non assegnati | — |
| `POST` | `/api/employees/{id}/assign-group` | Assegna **un** gruppo | `{ "group_id": 3 }` |
| `POST` | `/api/employees/{id}/assign-groups` | Assegna **più** gruppi | `{ "group_ids": [2,3,7] }` |
| `POST` | `/api/employees/{id}/detach-group` | Rimuove un gruppo | `{ "group_id": 2 }` |
| `POST` | `/api/employees/{id}/assign-document` | Assegna documento puntuale | `{ "document_id": 10, "expiration_date":"2025-12-31" }` |
| `PATCH` | `/api/employees/{id}/documents/{docId}` | Aggiorna scadenza | `{ "expiration_date":"2026-01-31" }` |

### 🔍 Filtri scadenze

| Metodo | Endpoint | Significato |
|--------|----------|-------------|
| `GET` | `/api/employees/documents/expired` | Tutti i documenti già scaduti |
| `GET` | `/api/employees/documents/expiring?days=30` | Scadranno entro 30 giorni (default 30) |

### 💾 Export

| Endpoint | Tipo file | Note |
|----------|-----------|------|
| `/api/employees/{id}/documents/export` | Excel | |
| `/api/employees/{id}/documents/export/pdf` | PDF | |
| `/api/documents/export/all` | Excel globale | |

---

## 📂 Gruppi

| Metodo | Endpoint | Descrizione |
|--------|----------|-------------|
| `GET`  | `/api/groups` | Lista gruppi |
| `POST` | `/api/groups` | Crea gruppo `{ "name":"Officina" }` |
| `PUT`/`PATCH` | `/api/groups/{id}` | Rinomina gruppo |
| `DELETE` | `/api/groups/{id}` | Elimina gruppo |
| `POST` | `/api/groups/{id}/attach-documents` | Collega documenti `{ "document_ids":[4,5] }` |
| `POST` | `/api/groups/{id}/detach-documents` | Scollega documenti |

---

## 📄 Documenti

| Metodo | Endpoint | Descrizione |
|--------|----------|-------------|
| `GET`  | `/api/documents` | Elenco documenti |
| `POST` | `/api/documents` | Nuovo documento `{ "name":"Patente" }` |
| `PUT`/`PATCH` | `/api/documents/{id}` | Rinomina documento |
| `DELETE` | `/api/documents/{id}` | Cancella documento |

---

## 🖼️ Immagini / Allegati

| Metodo | Endpoint | Payload |
|--------|----------|---------|
| `POST` | `/api/employee-documents/{pivotId}/images` | `multipart/form-data` files[] |
| `POST` | `/api/employee-documents/{pivotId}/attachments` | `multipart/form-data` files[] (PDF/DOC) |
| `GET`  | `/api/employee-documents/{pivotId}/images` | Elenco immagini |
| `DELETE` | `/api/employee-documents/images/{imgId}` | — |

---

## 📆 Presenze & Assenze

| Metodo | Endpoint | Descrizione | Payload esempio |
|--------|----------|-------------|-----------------|
| `GET` | `/api/presences` | Elenco assenze/presenze | — |
| `POST` | `/api/presences` | Nuova voce | `{ "employee_id":1, "type":"ferie", "start_date":"2025-08-01", "end_date":"2025-08-15", "note":"Ferie estive" }` |
| `GET` | `/api/presences/{id}` | Dettaglio | — |
| `PATCH` | `/api/presences/{id}` | Aggiorna | `{ "note":"Rientro anticipato" }` |
| `DELETE` | `/api/presences/{id}` | Elimina | — |

*(Analoghi endpoint per `/api/attendances` se usi timbrature giornaliere.)*

---

## 🔑 Ruoli & LDAP

* `admin`: accesso a **tutte** le rotte (middleware `isAdmin`).
* `operator`: autorizzato **solo** se appartiene al gruppo LDAP richiesto (middleware `admin.or.ldap:<CN>`).

Esempio route protetta:

```php
Route::middleware('admin.or.ldap:GESTIONALE-Dipendenti')
      ->apiResource('employees', EmployeeController::class);
```

---

## 📝 Esempio autenticazione con Postman

1. **POST /ldap-login**  
   Body JSON → `{ "username":"mario.rossi", "password":"Passw0rd!" }`  
   ➜ copia `access_token`.

2. In tutte le richieste successive:  
   Header → `Authorization: Bearer <access_token>`.

---

## ⌚ Scheduler & Notifiche

| Comando | Quando | Cosa fa |
|---------|--------|---------|
| `notify:expiring-documents` | daily | Invia mail o log su documenti in scadenza |

---

## 🗄️ Log Channels

* **auth.log** – login / logout / LDAP
* **audit.log** – azioni CRUD significative (assegnazioni, upload, export…)
* **error.log** – eccezioni non gestite

---

## 🗂️ Struttura cartelle (parziale)

```
app/
 ├── Http/
 │    ├── Controllers/
 │    │    ├── Api/
 │    │    │    ├── EmployeeController.php
 │    │    │    ├── GroupController.php
 │    │    │    ├── DocumentController.php
 │    │    │    └── AssignmentController.php
 ├── Models/
 │    ├── Employee.php
 │    ├── Group.php
 │    ├── Document.php
 │    ├── EmployeeDocument.php
 └── Exports/
      ├── EmployeeDocumentsExport.php
      └── AllEmployeeDocumentsExport.php
```

---

## 📦 Pacchetti utilizzati

- Laravel Sanctum
- Laravel Excel (Maatwebsite)
- barryvdh/laravel-dompdf

---

## 👨‍💻 Autore

**Nicola Mazzaferro**  
🛠️ Full-stack Developer

---
