# SHE SHOP - Women's E-Commerce (Simple PHP, HTML, CSS & MySQL)

A clean, standalone, lightweight B2C women's fashion e-commerce web application created using **Pure/Core PHP, HTML5, modern CSS3, and MySQL**.

> **No Composer, No Node.js, No npm, and No complex framework setup needed!**

---

##  How to Run

### Option 1: Run with XAMPP Apache (Recommended)
Because the project is located in `C:\xampp\htdocs\women-ecommerce`:
1. Open **XAMPP Control Panel**.
2. Ensure **Apache** and **MySQL** are running (started).
3. Open your browser and navigate directly to:
   - **Customer Storefront:** [http://localhost/women-ecommerce/](http://localhost/women-ecommerce/)
   - **Admin Control Panel:** [http://localhost/women-ecommerce/admin/](http://localhost/women-ecommerce/admin/)

### Option 2: Run with PHP Built-in Server
Open your terminal in this directory and run:
```bash
php -S localhost:8000
```
Then visit [http://localhost:8000](http://localhost:8000).

### Option 3: Run with Docker & Docker Compose
To run in an isolated Docker container with automated MySQL setup:
```bash
docker compose up -d --build
```
- **Customer Storefront:** [http://localhost:8080/](http://localhost:8080/)
- **Admin Control Panel:** [http://localhost:8080/admin/](http://localhost:8080/admin/)
- Database is automatically imported from `database.sql` into the containerized MySQL on first run.
- To stop the containers:
```bash
docker compose down
```

---

##  Login Credentials

### 1. Administrator
* **Email:** `admin@gmail.com`
* **Password:** `admin`
* **Direct Admin URL:** `http://localhost/women-ecommerce/admin/login.php`

### 2. Customer
* **Email:** `customer@example.com`
* **Password:** `password`
* **Customer Login URL:** `http://localhost/women-ecommerce/login.php`

---

##  Database Setup (MySQL / phpMyAdmin)
The database is configured in `config/db.php`:
* **Host:** `127.0.0.1` (Port: `3306`)
* **Database Name:** `women_ecommerce`
* **User:** `root`
* **Password:** *(empty by default in XAMPP)*

If you ever need to re-import the database into phpMyAdmin:
1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create or select database `women_ecommerce`
3. Click **Import** and upload `database.sql`

---

##  Project Structure

```
women-ecommerce/
├── config/
│   └── db.php                  # PDO MySQL connection & session helpers
├── assets/
│   └── css/
│       └── style.css           # Clean, responsive CSS stylesheet
├── includes/
│   ├── header.php              # Global navigation header, cart badge, flash alerts
│   └── footer.php              # 4-column footer
├── index.php                   # Home page with hero, categories & trending products
├── shop.php                    # Full catalog with search, category & price filters
├── product.php                 # Single product details & add to bag
├── cart.php                    # Shopping cart with quantity updates & delete
├── checkout.php                # Shipping address & order placement
├── order_success.php           # Order confirmation
├── wishlist.php                # Customer wishlist manager
├── feedback.php                # Customer feedback form
├── login.php                   # Customer & Admin login
├── register.php                # New user registration
├── logout.php                  # Session logout
├── dashboard.php               # Customer account profile & order history
├── database.sql                # Complete SQL schema & seed data
└── admin/
    ├── includes/
    │   ├── admin_header.php    # Admin sidebar & topbar
    │   └── admin_footer.php    # Admin footer
    ├── login.php               # Dedicated admin login
    ├── index.php               # Overview dashboard with revenue & order stats
    ├── categories.php          # Category CRUD
    ├── products.php            # Products list with stock badges
    ├── product_form.php        # Add / Edit product
    ├── orders.php              # Order status & payment management
    ├── customers.php           # Customer accounts list
    ├── feedback.php            # Customer inquiries & feedback inbox
    └── analytics.php           # Visual sales charts (Chart.js)
```


---

##  CI/CD Pipeline (GitHub Actions)

This repository includes an automated CI/CD pipeline configured at `.github/workflows/ci-cd.yml`:

1. **Continuous Integration (CI):**
   - Automatically triggered on every `git push` or `pull request` to `main` or `master`.
   - Runs syntax linting (`php -l`) across all PHP files to catch errors early.
   - Builds the Docker image and tests container startup via Docker Compose.

2. **Continuous Deployment (CD):**
   - On pushes to `main`, optionally pushes the tested Docker image to Docker Hub (if repository secrets `DOCKER_USERNAME` and `DOCKER_PASSWORD` are configured in GitHub Settings -> Secrets).
