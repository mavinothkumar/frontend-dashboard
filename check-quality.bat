@echo off
setlocal enabledelayedexpansion

:: Wrapper to invoke main quality checker
call "%~dp0..\..\..\..\check-quality.bat" frontend-dashboard %*
