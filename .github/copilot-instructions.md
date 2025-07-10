# Copilot Instructions for Laravel Inventory Management System

<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

## Project Overview
This is a Laravel 12 inventory management system with the following features:
- Order Management (Admin role)
- Approval Workflow (Manager role)
- Goods Input and Tracking (Admin role)
- Role-based authentication and authorization
- Audit trail for all operations

## Architecture Guidelines
- Follow Laravel conventions and best practices
- Use Eloquent ORM for database operations
- Implement proper validation using Form Request classes
- Use middleware for authorization and role-based access
- Implement proper exception handling
- Use Laravel's built-in features like migrations, seeders, and factories

## Security Requirements
- All operations must be secured with proper authentication
- Role-based access control (Admin and Manager roles)
- Input validation on all forms
- CSRF protection on all forms
- Audit logging for all critical operations

## Database Design
- Use proper foreign key relationships
- Implement soft deletes where appropriate
- Add audit fields (created_at, updated_at, created_by, updated_by)
- Use proper indexing for performance

## UI/UX Guidelines
- Use Laravel Blade templates
- Implement responsive design with Tailwind CSS
- Provide clear user feedback for all operations
- Include confirmation dialogs for destructive actions
- Show proper error and success messages

## Code Quality
- Use meaningful variable and method names
- Add proper PHPDoc comments
- Follow PSR standards
- Use Laravel's built-in helpers and facades
- Implement proper error handling
