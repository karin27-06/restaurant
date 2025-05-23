# Sistema de Restaurante – Laravel 12 + Vue.js + PrimeVue + PostgreSQL

**Sistema de Restaurante** es una aplicación full-stack desarrollada por  
[karin27-06](https://github.com/karin27-06), [PabloLupuX](https://github.com/PabloLupuX) y [Jefferson0k](https://github.com/Jefferson0k),  
utilizando **Laravel 12**, **Vue.js** y la librería de componentes **PrimeVue** para la interfaz de usuario, con base de datos **PostgreSQL**.

Repositorio: [https://github.com/Jefferson0k/restaurant](https://github.com/Jefferson0k/restaurant.git)

---

## 🧰 Prerequisites

- PHP >= 8.1  
- Composer  
- Node.js and npm  
- PostgreSQL  

---

## 🚀 Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/Jefferson0k/restaurant.git
    cd restaurant
    ```

2. Copy the environment configuration:

    ```bash
    cp .env.example .env
    ```

3. Install PHP dependencies:

    ```bash
    composer install
    ```

4. Install frontend dependencies:

    ```bash
    npm install
    ```

5. Generate the application key:

    ```bash
    php artisan key:generate
    ```

6. Configure the database settings in `.env`:

    ```
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
    ```

7. Run migrations and seeders (if applicable):

    ```bash
    php artisan migrate --seed
    ```

8. Compile frontend assets:

    ```bash
    npm run dev
    ```

9. (Optional) Start the local development server:

    ```bash
    php artisan serve
    ```

---

## 🎨 UI Framework

The user interface is built with **Vue.js** and leverages **PrimeVue** for modern, responsive components.

---

## 👨‍💻 Developers

- [karin27-06](https://github.com/karin27-06)  
- [PabloLupuX](https://github.com/PabloLupuX)  
- [Jefferson0k](https://github.com/Jefferson0k)

---

## 📄 License

This project is protected under the  
**Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)** license.

You are welcome to study, use, and adapt this code strictly for **non-commercial purposes**.  
Any commercial use, distribution, or reproduction without explicit, prior written consent from the authors is strictly prohibited.

Please review the full terms and conditions in the [LICENSE](./LICENSE) file to ensure compliance.
