# Toko Tsabita Cashier Application

A simple PHP-based cashier application for Toko Tsabita.

## Features
- Product management
- Transaction processing
- Sales reporting
- User authentication (admin and cashier roles)

## Deployment to Vercel

### Prerequisites
1. Create an account at [Vercel](https://vercel.com/)
2. Install Node.js and npm: https://nodejs.org/
3. Install Vercel CLI: `npm install -g vercel`

### Deployment Steps
1. Clone or download this repository
2. Open terminal/command prompt in the project directory
3. Run `vercel` to deploy to a preview URL
4. Run `vercel --prod` to deploy to production

### Alternative Deployment Method
1. Go to https://vercel.com/dashboard
2. Click "New Project"
3. Import this repository or upload the project files
4. Vercel will automatically detect it's a PHP project
5. Click "Deploy"

## Troubleshooting Deployment Issues

If you encounter issues with PHP runtime detection:

1. Make sure you have a `composer.json` file in your project root
2. Try specifying the PHP runtime explicitly in `vercel.json`:
   ```json
   {
     "version": 2,
     "builds": [
       {
         "src": "**/*.php",
         "use": "@vercel/php"
       }
     ]
   }
   ```

3. Test PHP functionality with these API endpoints after deployment:
   - `/api/health` - Basic health check
   - `/api/test` - PHP functionality test
   - `/api/version` - PHP version information

## Default Login
- Username: admin
- Password: admin123

## Local Development

To run locally, you can use PHP's built-in server:
```bash
php -S localhost:8000
```

Or use XAMPP/WAMP by placing the project folder in the htdocs/www directory.

## File Structure
- `index.php`: Main cashier interface
- `login.php`: User login page
- `products.php`: Product management (admin only)
- `transactions.php`: Transaction history
- `sales_report.php`: Sales reporting (admin only)
- `users.php`: User management (admin only)
- `receipt.php`: Receipt printing
- `includes/db_config.php`: Database configuration
- `includes/auth.php`: Authentication functions
- `db/tsabita_cashier.json`: Data storage file

## Technology Stack
- PHP (no database required - uses JSON file for storage)
- HTML/CSS
- JavaScript (minimal)

## Notes
This is a file-based system using JSON for data storage, making it easy to deploy without a database.

The application uses PHP sessions for authentication, which will work on Vercel with the free tier.