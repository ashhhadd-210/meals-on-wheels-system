@echo off
REM ── Open Meals on Wheels in a CLEAN Chrome window (extensions disabled) ──────
REM Use this if your normal Chrome shows the page source as text.
set "CHROME=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if not exist "%CHROME%" set "CHROME=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"

start "" "%CHROME%" --user-data-dir="C:\Users\Public\MowData\cleanchrome" --no-first-run --no-default-browser-check --disable-extensions --new-window "http://localhost:8090/login"
