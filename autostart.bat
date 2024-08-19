@echo off


cd /d C:\Users\Tecnologia\OneDrive\Documentos\Proyectos\msp

start /min "" php artisan serve --host=10.0.0.88 --port=9000

start /min "" php artisan schedule:work

pause
