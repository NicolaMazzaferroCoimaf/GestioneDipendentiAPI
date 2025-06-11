# 📁 Gestione Dipendenti API (Laravel RESTful)

Sistema completo per la **gestione di dipendenti**, documenti, gruppi e scadenze, progettato con **Laravel 12** e pronto per integrazione con frontend React/Angular.

---

## 📌 Funzionalità principali

- 👤 Gestione dipendenti con dati anagrafici
- 📂 Assegnazione di gruppi (es. operaio, canalista)
- 📄 Documenti associati ai gruppi, con scadenze per dipendente
- 🖼️ Upload immagini e allegati per ogni documento
- 🔐 Autenticazione tramite Laravel Sanctum
- 🛡️ Ruoli admin/operator con autorizzazioni
- ⏰ Filtri scadenze (scaduti, prossimi)
- 📊 Export PDF ed Excel (per singolo dipendente e globale)
- 📱 Pronto per frontend (React o Angular)

---

## ⚙️ Requisiti

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL
- Laravel 12

---

## 🚀 Setup locale

```bash
git clone https://github.com/tuo-utente/gestione-dipendenti-api.git
cd gestione-dipendenti-api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

> ☝️ Aggiungi anche: `php artisan storage:link` per gestire le immagini dei documenti.

---

## 🔐 Autenticazione

- API protette da **Laravel Sanctum**
- Login via token Bearer
- Utenti con ruoli: `admin` o `operator`

---

## 🔗 API Endpoint principali

### 👥 Autenticazione

| Metodo | Endpoint         | Descrizione           |
|--------|------------------|------------------------|
| POST   | /api/register     | Registra utente        |
| POST   | /api/login        | Login con token        |
| POST   | /api/logout       | Logout autenticato     |

### 👤 Dipendenti

| Metodo | Endpoint                          | Descrizione                    |
|--------|-----------------------------------|--------------------------------|
| GET    | /api/employees                    | Lista dipendenti               |
| POST   | /api/employees                    | Crea dipendente                |
| GET    | /api/employees/{id}/documents     | Documenti con scadenze         |
| GET    | /api/employees/{id}/missing-documents | Documenti mancanti         |
| GET    | /api/employees/{id}/documents/export | Export Excel                  |
| GET    | /api/employees/{id}/documents/export/pdf | Export PDF               |

### 📂 Gruppi

| Metodo | Endpoint                              | Descrizione                         |
|--------|---------------------------------------|-------------------------------------|
| GET    | /api/groups                           | Lista gruppi                        |
| POST   | /api/employees/{id}/assign-group      | Assegna gruppo a dipendente         |
| POST   | /api/employees/{id}/detach-group      | Rimuovi gruppo da dipendente        |
| POST   | /api/groups/{id}/assign-documents     | Assegna documenti a gruppo          |
| POST   | /api/groups/{id}/detach-documents     | Rimuovi documenti da gruppo         |

### 📄 Documenti

| Metodo | Endpoint                                             | Descrizione                          |
|--------|------------------------------------------------------|--------------------------------------|
| POST   | /api/documents                                       | Crea nuovo documento                 |
| PATCH  | /api/employees/{employee}/documents/{document}       | Modifica scadenza per un documento  |
| GET    | /api/documents/export/all                            | Export Excel di tutti i documenti    |

### 🖼️ Immagini e allegati

| Metodo | Endpoint                                    | Descrizione                           |
|--------|---------------------------------------------|---------------------------------------|
| POST   | /api/employee-documents/{id}/images         | Upload immagini                       |
| GET    | /api/employee-documents/{id}/images         | Elenco immagini associate             |
| DELETE | /api/employee-documents/images/{id}         | Cancella immagine                     |

### ⏰ Scadenze

| Metodo | Endpoint                          | Descrizione                          |
|--------|-----------------------------------|--------------------------------------|
| GET    | /api/employees/documents/expired | Tutti i documenti scaduti            |
| GET    | /api/employees/documents/expiring?days=30 | In scadenza entro 30 giorni     |

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
