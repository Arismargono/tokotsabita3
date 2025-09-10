@echo off
REM Deployment script for Toko Tsabita Cashier Application on Windows

echo Deploying Toko Tsabita Cashier Application to Vercel...

REM Check if Vercel CLI is installed
vercel --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Vercel CLI could not be found. Installing...
    npm install -g vercel
)

REM Deploy to Vercel
echo Starting deployment...
vercel --prod

echo Deployment completed!
pause