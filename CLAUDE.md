# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 sales management system for a thesis project focused on custom product orders and manufacturing. The system handles products with complex variant relationships, custom designs, and order processing for both individual customers and businesses.

## Development Commands

### Core Laravel Commands
- `php artisan serve` - Start development server
- `php artisan migrate` - Run database migrations  
- `php artisan migrate:rollback` - Rollback migrations
- `php artisan db:seed` - Run database seeders
- `php artisan db:seed --class=MetodoPagoSeeder` - Run specific seeder

### Frontend Asset Management
- `npm run dev` - Start Vite development server
- `npm run build` - Build assets for production

### Testing
- `vendor/bin/phpunit` - Run PHPUnit tests
- `php artisan test` - Run Laravel tests

### Code Quality  
- `vendor/bin/pint` - Laravel Pint code formatting (if configured)

### Variant Migration (Critical System Change)
The system underwent a major architectural change from One-to-Many to Many-to-Many product-variant relationships:
- `migrate_variantes.bat` (Windows) or `migrate_variantes.sh` (Linux/Mac) - Automated migration scripts
- Manual migration steps documented in `MIGRACION_VARIANTES_README.md`

## Architecture & Key Concepts

### Core Business Domain
- **Products** can have multiple **Variants** (sizes, colors, styles)
- **Variants** contain **Characteristics** (specific options like "Red", "Large")
- Products can have custom **Designs** uploaded by customers
- **Orders** (Ventas) contain **Order Details** (DetalleVenta) with variant-characteristic combinations
- Support for both **Individual Customers** (ClienteNatural) and **Business Customers** (ClienteEstablecimiento)

### Complex Relationships Architecture

#### Product-Variant Relationship (Recently Migrated)
- **Before**: Products → Variants (One-to-Many)
- **Current**: Products ↔ Variants (Many-to-Many via `producto_variantes` pivot)
- Pivot table stores: `precioAdicional`, `stockVariante`, `estado`
- Enables variant reuse across multiple products

#### Variant-Characteristics Chain
```
Productos → Variantes → VarianteCaracteristicas → Caracteristicas → Opciones
```

#### Design Management
- Products can have multiple designs via `producto_disenos` pivot table
- Supports custom design uploads and design personalization
- Track pricing and personalization data per product-design combination

### Data Flow Patterns
1. **Catalog Browsing**: Products → Variants → Characteristics display
2. **Order Creation**: Product selection → Variant/Characteristic selection → Custom design upload → Order processing
3. **Inventory Management**: Stock tracking at variant level per product

### Controllers Architecture

#### PedidoController (Order Management)
- Handles the complete customer ordering workflow
- `catalogo()` - Product catalog with variant information
- `configurarProducto()` - Product customization interface  
- `personalizarDiseno()` - Custom design upload
- `nuevoPedido()` - Unified order form
- API endpoints for dynamic variant/characteristic loading

#### ProductoController
- Product CRUD with complex variant relationships
- Variant attachment/detachment for Many-to-Many relations
- Design association management

#### Configuration Controllers
- `CategoriaController`, `VarianteController`, `CaracteristicaController` - Basic CRUD
- `ConfiguracionController` - Unified configuration interface

### Database Considerations

#### Recent Migration Impact
- Legacy `idProducto` column removed from `variantes` table
- New `producto_variantes` pivot table replaces direct foreign key
- Migration preserves all existing data relationships

#### Key Tables
- `productos` - Product catalog
- `variantes` - Product variants (sizes, styles, etc.)
- `caracteristicas` - Specific options within variants  
- `producto_variantes` - Product-variant relationships with pricing/stock
- `ventas` - Orders/sales transactions
- `detalle_ventas` - Order line items
- `disenos` - Custom design templates
- `producto_disenos` - Product-design associations

#### Critical Foreign Key Relationships
- Products reference Categories via `idCategoria`
- Order details reference variants and characteristics for pricing calculation
- Complex pivot relationships require careful handling of attach/detach operations

## File Structure Notes

### Misplaced Files (Technical Debt)
Some files are incorrectly placed and should be moved:
- Controllers in `app/Models/` directory
- Blade templates in `app/Models/` and other incorrect locations
- Routes defined in `routes/PedidoController.php` instead of proper route files

### Storage Organization
- `storage/app/public/disenos/` - Design template images
- `storage/app/public/disenos_personalizados/` - Customer uploaded designs
- `storage/app/public/productos/` - Product images

## Development Guidelines

### Model Relationship Patterns
When working with the product-variant system:
- Use `belongsToMany` relationships with proper pivot data
- Always include `withPivot()` for additional pivot columns
- Handle variant pricing through pivot table `precioAdicional`

### API Development
- Follow existing pattern in PedidoController for dynamic UI APIs
- Prefix API routes with `api/` namespace
- Return consistent JSON responses for variant/characteristic lookups

### Migration Safety
- Always backup database before structural changes
- The variant migration is irreversible after `idProducto` column removal
- Test thoroughly with existing data before production deployment

## Debugging Resources

The project includes several debugging files for development:
- `debug_*.php` files for testing specific functionality
- `test_*.php` files for database connection and model testing
- Migration validation scripts for ensuring data integrity

## Recent Changes Context

The system recently completed a major architectural migration documented in `MIGRACION_VARIANTES_README.md`. This affects how products relate to variants and impacts order processing, inventory management, and API responses. When working with variant-related functionality, consider both legacy patterns and new Many-to-Many relationships.