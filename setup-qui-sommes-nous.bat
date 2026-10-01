@echo off
echo Setting up Qui Sommes-Nous backend...

echo.
echo 1. Running migrations...
php artisan migrate

echo.
echo 2. Running seeders...
php artisan db:seed --class=QuiSommesNousSeeder

echo.
echo 3. Creating storage link (if not exists)...
php artisan storage:link

echo.
echo Setup completed successfully!
echo.
echo You can now:
echo - Access the API at: /api/qui-sommes-nous
echo - Manage content from the backoffice at: /admin/nosagences/qui-sommes-nous
echo.

pause