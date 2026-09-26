# Actualizar el sistema en el servidor

Guía para actualizar **Vinculación ISTAM** en el servidor del instituto después de cada cambio subido a GitHub.

## 1. Traer los cambios

```bash
cd /ruta/del/proyecto
git pull origin main
```

## 2. Instalar dependencias de PHP

```bash
composer install --no-dev --optimize-autoloader
```

> Los archivos compilados de CSS/JS (`public/build`) ya vienen en el repositorio, así que **no hace falta** ejecutar `npm`.
> Si alguna vez se ejecuta `npm run build` en el servidor, que sea con Node 20 o superior.

## 3. Actualizar la base de datos

```bash
php artisan migrate --force
```

Esto crea las tablas y columnas nuevas (por ejemplo, `permitir_nueva_actividad` en `users`).
**Sin este paso, las pantallas nuevas dan error 500.**

Solo la primera vez (datos base):

```bash
php artisan db:seed --class=CarrerasSeeder --force
php artisan db:seed --class=TiposCertificadoSeeder --force
```

## 4. Enlace de archivos subidos (fotos, certificados)

Solo la primera vez:

```bash
php artisan storage:link
```

## 5. Limpiar y regenerar cachés (siempre, al final)

```bash
php artisan optimize:clear
php artisan optimize
```

Si se omite este paso, el servidor puede seguir usando rutas y vistas **viejas**: botones que llevan a páginas que ya no existen, o errores 404/500.

## 6. Permisos (servidor Linux)

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Revisar el archivo `.env` del servidor

| Variable | Valor en producción |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | la dirección real, por ejemplo `https://vinculacion.istam.edu.ec` |
| `DB_*` | los datos de la base de datos del servidor |

`APP_URL` debe ser la dirección correcta. Si no, las imágenes y los archivos descargables pueden no cargar.

## Qué NO se sube con git

- **Los datos** (usuarios, carreras, proyectos, actividades): cada base de datos tiene los suyos.
- **Los archivos subidos** (fotos de carreras, certificados, evidencias), que están en `storage/app/public`.
- **El archivo `.env`**, que tiene contraseñas.

## Si algo falla

Revisar el registro de errores:

```bash
tail -n 50 storage/logs/laravel.log
```
