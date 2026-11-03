@echo off
REM ── Start the Meals on Wheels local dev server ──────────────────────────────
set "PHPDIR=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe"
set "ROOT=%~dp0MealsonWheels"

echo Starting Meals on Wheels on http://localhost:8090 ...
echo (Leave this window open. Press Ctrl+C to stop the server.)
echo.
"%PHPDIR%\php.exe" -c "%PHPDIR%\php.ini" -S localhost:8090 -t "%ROOT%" "%ROOT%\router.php"
