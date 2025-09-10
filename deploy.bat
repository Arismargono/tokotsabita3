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
echo Note: Using community PHP runtime (vercel-php@0.6.0)
vercel --prod

echo Deployment completed!
echo After deployment, test these endpoints:
echo - /api/health
echo - /api/test
echo - /api/version
pause