# Laravel Inventory Management System

A comprehensive inventory management system built with Laravel 12, featuring role-based access control, order management, goods receipt tracking, and a complete Point of Sale (POS) system.

## Features

### 🏪 Point of Sale (POS)
- Real-time sales transactions
- Product selection with barcode scanning
- Receipt generation and printing
- Stock management integration
- Cashier-specific transaction tracking

### 📦 Inventory Management
- **Order Requests**: Admin can create purchase order requests
- **Approval Workflow**: Manager approval system for orders
- **Goods Receipt**: Track incoming inventory with duplicate prevention
- **Stock Tracking**: Real-time stock updates with sales integration

### 👥 User Management
- **Role-based Access Control**: Admin, Manager, Cashier roles
- **Permission System**: Granular access control per feature
- **User Authentication**: Secure login and session management

### 📊 Reporting & Analytics
- Dashboard statistics with fallback data logic
- Sales history and transaction tracking
- Audit trail for all operations
- Daily/monthly sales reports

## Technology Stack

- **Framework**: Laravel 12
- **Database**: SQLite (configurable)
- **Frontend**: Blade Templates + Tailwind CSS
- **Authentication**: Laravel Breeze
- **Styling**: Tailwind CSS with responsive design

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd inv-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start the application**
   ```bash
   php artisan serve
   ```

## Database Schema

### Key Tables

#### Users Table
- `user_code` (Primary Key): String-based user identifier
- `name`: User full name
- `email`: User email address
- `role`: admin, manager, cashier

#### Products Table
- `product_code` (Primary Key): String-based product identifier
- `product_name`: Product name
- `product_price`: Selling price
- `stock_quantity`: Current stock level

#### Transactions Table (Sales)
- `transaction_code` (Primary Key): String-based transaction identifier (TRX0001)
- `user_code`: Foreign key to users (cashier)
- `total_quantity`: Total items sold
- `total_price`: Total transaction amount
- `purchase_date`: Transaction date

#### Transaction Items Table
- `transaction_item_code` (Primary Key): String-based item identifier (TI0001)
- `transaction_code`: Foreign key to transactions
- `product_code`: Foreign key to products
- `quantity`: Item quantity
- `sub_total`: Item subtotal

## API Endpoints

### Sales Management
```
GET    /sales              - Sales history (with filters)
POST   /sales              - Create new sale
GET    /sales/{sale}       - View sale details
GET    /sales/{sale}/receipt - Print receipt
POST   /sales/{sale}/void  - Void transaction
```

### POS System
```
GET    /pos                - POS dashboard
GET    /pos/create         - New sale interface
POST   /pos/store          - Process sale
GET    /pos/{sale}/receipt - Receipt view
```

### Goods Management
```
GET    /goods-received     - Goods receipt list
POST   /goods-received     - Create goods receipt
GET    /api/goods-received/available-orders - Get available orders
```

## Key Features

### Model Accessor Patterns
- **Product Model**: `name` → `product_name`, `selling_price` → `product_price`
- **TransactionItem Model**: `subtotal` → `sub_total`, `unit_price` (calculated)
- **Sale Model**: Uses `transactions` table with custom primary key

### Business Logic
- **Duplicate Prevention**: Goods receipt entries prevent duplicate orders
- **Auto-generation**: Transaction codes follow patterns (TRX0001, TI0001)
- **Stock Management**: Automatic stock updates on sales/voids
- **Role Restrictions**: Cashiers see only their transactions

### Security Features
- CSRF protection on all forms
- Role-based route protection
- Input validation and sanitization
- Audit logging for critical operations

## User Roles & Permissions

### Admin
- Full system access
- Create/manage products, brands, distributors
- Create order requests
- View all transactions and reports

### Manager  
- Approve/reject order requests
- View all sales data
- Manage employee accounts
- Access to all reports

### Cashier
- Process sales transactions
- View own transaction history
- Access POS system
- Print receipts

## Development Guidelines

### Code Patterns
- Use string primary keys with auto-generation
- Implement proper model relationships
- Add accessors for database field mapping
- Include validation rules and casting

### Controller Patterns
- Check user permissions before operations
- Use database transactions for multi-step operations
- Include proper error handling
- Provide user-friendly error messages

### View Patterns
- Separate POS and Sales management interfaces
- Include dashboard statistics
- Use consistent table layouts
- Implement print-friendly designs

## Testing

Run the test suite:
```bash
php artisan test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please create an issue in the repository or contact the development team.

---

**Built with ❤️ using Laravel Framework**
