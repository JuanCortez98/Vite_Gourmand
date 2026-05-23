@echo off
REM Script pour démarrer XAMPP (Apache + MySQL) facilement

echo =========================================
echo   INICIANDO XAMPP - VITE GOURMAND
echo =========================================
echo.

REM Vérifier si le script est lancé en administrateur
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERREUR : Ce script doit être exécuté en tant qu'administrateur
    echo Cliquez droit sur le fichier et sélectionnez "Exécuter en tant qu'administrateur"
    pause
    exit /b 1
)

REM Iniciar Apache
echo [1/2] Iniciando Apache...
start /B cmd /c "C:\xampp\apache\bin\httpd.exe" >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ Apache iniciado en puerto 80
) else (
    echo ✗ Error al iniciar Apache (puede estar en uso)
)

REM Iniciar MySQL
echo [2/2] Iniciando MySQL/MariaDB...
start cmd /c "C:\xampp\mysql\bin\mysqld.exe --console" >nul 2>&1
if %errorLevel% equ 0 (
    echo ✓ MySQL iniciado en puerto 3306
)

echo.
echo =========================================
echo   SERVICIOS INICIADOS
echo =========================================
echo.
echo Acceso rápido:
echo - http://localhost/
echo - http://localhost/vite-gourmand/public/index.php
echo - http://localhost/phpmyadmin/
echo.
echo.
pause
