@echo off
title Candour Auto Launcher
echo Starting your digital Platform....
timeout /t 2 >nul

:: starting PhpStroem
start "" "C:\Program Files\JetBrains\PhpStorm 2025.2.5\bin\phpstorm64.exe"



node C:\xampp1\htdocs\okaya\.temp\Automate.js

"C:\Program Files\nodejs\node.exe" C:\xampp1\htdocs\okaya\.temp\Automate.js

:: Start XAMPP
start "" "C:\xampp1\xampp-control.exe"

:: Open StarPlus
start "" "http://localhost/Okaya/index.php"

:: Open StarPlus
start "" "http://localhost/phpmyadmin/sql.php?db=task1&table=users&token=cdf065c1c73d78f26c77a4d96cbdce12&pos=0"

exit
