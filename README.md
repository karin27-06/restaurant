# Sistema de Restaurante – Laravel 12 + Vue.js + PrimeVue + PostgreSQL

**Sistema de Restaurante** es una aplicación full-stack desarrollada por  
[karin27-06](https://github.com/karin27-06), [PabloLupuX](https://github.com/PabloLupuX) y [Jefferson0k](https://github.com/Jefferson0k),  
utilizando **Laravel 12**, **Vue.js** y la librería de componentes **PrimeVue** para la interfaz de usuario, con base de datos **PostgreSQL**.

Repositorio: [https://github.com/Jefferson0k/restaurant](https://github.com/Jefferson0k/restaurant.git)

---

## 🧰 Prerrequisitos

- PHP >= 8.1  
- Composer  
- Node.js y npm  
- PostgreSQL  

---

## 🚀 Instalación

1. Clonar el repositorio:

    ```bash
    git clone https://github.com/Jefferson0k/restaurant.git
    cd restaurant
    ```

2. Copiar el archivo de configuración del entorno:

    ```bash
    cp .env.example .env
    ```

3. Instalar dependencias de PHP:

    ```bash
    composer install
    ```

4. Instalar dependencias de frontend:

    ```bash
    npm install
    ```

5. Generar la llave de la aplicación:

    ```bash
    php artisan key:generate
    ```

6. Configurar la base de datos en `.env`:

    ```
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_base_de_datos
    DB_USERNAME=usuario
    DB_PASSWORD=contraseña
    ```

7. Ejecutar las migraciones y seeders (si existen):

    ```bash
    php artisan migrate --seed
    ```

8. Compilar los assets de frontend:

    ```bash
    npm run dev
    ```

9. Levantar el servidor local (opcional):

    ```bash
    php artisan serve
    ```

---

## 🎨 UI Framework

La interfaz de usuario está construida con **Vue.js** y utiliza **PrimeVue** para componentes modernos y responsivos.

---

## 👨‍💻 Desarrolladores

- [karin27-06](https://github.com/karin27-06)  
- [PabloLupuX](https://github.com/PabloLupuX)  
- [Jefferson0k](https://github.com/Jefferson0k)

---

## 📄 Licencia

This project is licensed under the
Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)

You may study, copy, and modify this code for non-commercial purposes only.
Commercial use is prohibited without express permission from the author.

See the LICENSE file for full legal terms
