# INTRANET VEHICLES SELLERS

## Commercial System for Vehicle Sellers

This system offers a comprehensive commercial solution for vehicle concessionaires, managing offers, vehicles, and sales processes.

### 🚀 Getting Started

This software is the result of accumulated experience in the vehicle sales sector, covering Commercial, Administration, and Production departments. It addresses the need for generating precise offers with detailed information on accessories, components, and work required, along with discount application and sales control.

By formalizing all information into a single document, it allows for the generation of precise repair orders, minimizing wasted time and improving company performance.

### 🛠️ Technology Stack

The application is built using **PHP 7.4** following the **MVC** pattern and is designed to run as an intranet on a local server.

**Key Technologies:**
*   **Core:** PHP 7.4, OOP
*   **Routing:** Aura Router
*   **Templating:** Twig
*   **Database ORM:** Eloquent (Laravel)
*   **Dependency Injection:** PHP-DI
*   **Validation:** Respect/Validation
*   **Logging:** Monolog
*   **Migrations:** Phinx
*   **Excel/PDF:** PhpSpreadsheet, FPDF
*   **Frontend:** CKEditor, DataTables (via Bower)

### 📋 Requirements

*   **PHP 7.4**
*   **Composer** (https://getcomposer.org/)
*   **MySQL** / MariaDB
*   **Bower** (for frontend dependencies)

### ⚙️ Installation

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    cd Intranet
    ```

2.  **Install Backend Dependencies**
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**
    ```bash
    bower install
    ```

4.  **Database Configuration**
    *   Create a MySQL database.
    *   Configure your database connection settings in `phinx.yml` or your environment configuration file.

5.  **Run Migrations**
    Initialize the database schema using Phinx:
    ```bash
    php vendor/bin/phinx migrate
    ```

### 🧪 Testing

The project uses **Codeception** for testing.

**Running Acceptance Tests:**

1.  Start Chromedriver (required for acceptance tests):
    ```bash
    # From your webdriver bin directory (e.g., C:/Webdriver/bin)
    chromedriver --url-base=/wd/hub --port=4123
    ```

2.  Run tests:
    ```bash
    vendor/bin/codecept run
    ```

### 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### 👥 Authors

*   **TpGimeno** - *Initial work* - [tonyllomouse@gmail.com](mailto:tonyllomouse@gmail.com)
