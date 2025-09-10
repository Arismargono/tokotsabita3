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
vercel --prod

echo "Deployment completed!"