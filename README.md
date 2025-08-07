# TaskManagement

Sistema de gestión de tareas con frontend y backend.

## Demo en línea

Para ver una demo responsive y funcional en línea , puedes acceder a:  
https://frontend-task-management-ten.vercel.app/

## Estructura del Proyecto

- `backend-task-management/`: API RESTful construida con Laravel (PHP).
- `frontend-task-management/`: Interfaz de usuario construida con (especifica framework: React, Vue, Angular, etc.).

## Requisitos

- Node.js >= 16.x (para el frontend)
- PHP >= 8.x (para el backend)
- Composer
- MySQL o MariaDB

## Instalación

### Backend
1. Ve al directorio del backend:
   ```sh
   cd backend-task-management
   ```
2. Instala dependencias:
   ```sh
   composer install
   ```
3. Copia el archivo de entorno y configura tus variables:
   ```sh
   cp .env.example .env
   ```
4. Genera la clave de la aplicación:
   ```sh
   php artisan key:generate
   ```
5. Configura la base de datos en `.env` y ejecuta migraciones:
   ```sh
   php artisan migrate --seed
   ```
6. Inicia el servidor:
   ```sh
   php artisan serve
   ```

### Frontend
1. Ve al directorio del frontend:
   ```sh
   cd frontend-task-management
   ```
2. Instala dependencias:
   ```sh
   npm install
   ```
3. Inicia la aplicación:
   ```sh
   npm run dev
   ```

## Uso

- Accede al frontend en `http://localhost:3000` (o el puerto configurado).
- El backend estará disponible en `http://localhost:8000`.

## Endpoints principales (Backend)

- `GET /api/keywords`: Listar palabras clave
- `POST /api/keywords`: Crear palabra clave
- `PUT /api/keywords/{id}`: Actualizar palabra clave
- `DELETE /api/keywords/{id}`: Eliminar palabra clave

## Documentación

- Importa el archivo `postman_keywords_collection.json` en Postman para probar los endpoints del backend.

## Tests automatizados

### Backend

- El backend incluye pruebas automatizadas con PHPUnit.
- Las pruebas están ubicadas en la carpeta `backend-task-management/tests/` (subcarpetas `Feature` y `Unit`).
- Para ejecutarlas:
  ```sh
  cd backend-task-management
  php artisan test
  ```
- Se prueban funcionalidades como:
  - Acceso a endpoints principales
  - Creación, actualización y eliminación de palabras clave
  - Validaciones y respuestas de error
