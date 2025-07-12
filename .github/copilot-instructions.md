# Copilot Instructions for Laravel Inventory Management System

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Overview
This is a Laravel 12 inventory management system with the following features:
- Order Management (Admin role)
- Approval Workflow (Manager role)
- Goods Input and Tracking (Admin role)
- Point of Sale (POS) System (Cashier role)
- Sales Transaction Management
- Role-based authentication and authorization
- Audit trail for all operations

## Architecture Guidelines
- Follow Laravel conventions and best practices
- Use Eloquent ORM for database operations
- Implement proper validation using Form Request classes
- Use middleware for authorization and role-based access
- Implement proper exception handling
- Use Laravel's built-in features like migrations, seeders, and factories

## Database Schema
### Key Tables and Field Mappings:
- **transactions**: Main sales table
  - Primary key: `transaction_code` (string, non-incrementing)
  - Fields: `user_code`, `total_quantity`, `total_price`, `purchase_date`
- **transaction_items**: Sales items
  - Primary key: `transaction_item_code` (string, auto-generated)
  - Fields: `transaction_code`, `product_code`, `quantity`, `sub_total`
- **products**: Product catalog
  - Primary key: `product_code` (string)
  - Fields: `product_name`, `product_price`, `stock_quantity`
- **users**: System users
  - Primary key: `user_code` (string)
  - Roles: admin, manager, cashier

### Model Accessor Patterns:
- Product model: `name` accessor maps to `product_name`, `selling_price` maps to `product_price`
- TransactionItem model: `subtotal` maps to `sub_total`, `unit_price` calculated from sub_total/quantity
- Sale model: Uses `transactions` table with custom primary key

## Security Requirements
- All operations must be secured with proper authentication
- Role-based access control (Admin, Manager, and Cashier roles)
- Cashiers can only view/manage their own transactions
- Input validation on all forms
- CSRF protection on all forms
- Audit logging for all critical operations

## Sales System Guidelines
### Business Logic:
- Prevent duplicate goods receipt entries for same order
- Auto-generate codes for transactions and items (TRX0001, TI0001 patterns)
- Stock management: decrement on sale, increment on void
- Role restrictions: cashiers see own sales, managers see all

### Data Validation:
- Validate product availability and stock before sale
- Ensure positive quantities and prices
- Check user permissions before transaction operations

## Database Design
- Use proper foreign key relationships with string keys (product_code, user_code, etc.)
- Implement soft deletes where appropriate
- Add audit fields (created_at, updated_at, created_by, updated_by)
- Use proper indexing for performance on code fields

## UI/UX Guidelines
- Use Laravel Blade templates
- Implement responsive design with Tailwind CSS
- Provide clear user feedback for all operations
- Include confirmation dialogs for destructive actions
- Show proper error and success messages
- Dashboard statistics should fall back to recent data if today is empty

## Code Quality
- Use meaningful variable and method names
- Add proper PHPDoc comments
- Follow PSR standards
- Use Laravel's built-in helpers and facades
- Implement proper error handling
- Use model accessors for field name mapping between database and application layer

## Common Patterns
### Controllers:
- Always check user permissions before operations
- Use database transactions for multi-step operations
- Provide both API and view responses where needed
- Include proper error handling with user-friendly messages

### Views:
- Separate POS and Sales management interfaces
- Include dashboard statistics on relevant pages
- Use consistent table layouts for data display
- Implement print-friendly receipt layouts

### Models:
- Use string primary keys with auto-generation patterns
- Implement proper relationships with foreign key constraints
- Add accessors for database field name mapping
- Include validation rules and casting
