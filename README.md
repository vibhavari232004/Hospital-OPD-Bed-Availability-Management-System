# Mint OPD (XAMPP Setup)

## ✅ What’s included
- `database.sql` — single SQL file you can import into phpMyAdmin
- `config.php` + `db.php` — centralized DB connection for all panels
- `index.php`, `admin.php`, `doctor.php`, `patient.php`, `receptionist.php` — all panels now include DB connectivity

> The UI still uses localStorage for data (for quick demo), but the PHP connection is now working and can be used for real queries.

---

## 🚀 Run on XAMPP (Windows)
1. **Copy project to XAMPP `htdocs`**
   - Copy the entire `Hopit` folder into `C:\xampp\htdocs\Hopit`

2. **Start Apache + MySQL**
   - Open the XAMPP Control Panel
   - Start **Apache** and **MySQL**

3. **Import `database.sql` into MySQL**
   - Open http://localhost/phpmyadmin
   - Click **Import** → choose `Hopit/database.sql` → click **Go**
   - If you see errors about `DELIMITER`, use the **Import** tab (not the SQL query box).

4. **Open the app in your browser**
   - Visit: http://localhost/Hopit/index.php

5. **If DB connection fails**
   - Edit `Hopit/config.php` and update `user`, `pass`, or `dbname` to match your MySQL credentials.
   - Common defaults for XAMPP:
     - `user = 'root'`
     - `pass = ''` (empty)
     - `host = '127.0.0.1'`

---

## 🛠️ What you can do next
- Replace the localStorage data logic with real queries using `$pdo` from `db.php`.
- Add API endpoints (e.g., `api/add_city.php`) that accept form submissions and insert rows into your MySQL tables.
- Use prepared statements (`$pdo->prepare(...)`) to keep your app secure.
