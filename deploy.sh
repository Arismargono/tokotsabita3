#!/bin/bash
# Deployment script for Toko Tsabita Cashier Application

echo "Deploying Toko Tsabita Cashier Application to Vercel..."

# Check if Vercel CLI is installed
if ! command -v vercel &> /dev/null
then
    echo "Vercel CLI could not be found. Installing..."
    npm install -g vercel
fi

# Deploy to Vercel
echo "Starting deployment..."
echo "Note: Using community PHP runtime (vercel-php@0.6.0)"
vercel --prod

echo "Deployment completed!"
echo "After deployment, test these endpoints:"
echo "- /api/health"
echo "- /api/test"
echo "- /api/version"