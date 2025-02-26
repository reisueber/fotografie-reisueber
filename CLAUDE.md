# CLAUDE.md - Agent Guidelines for fotografie-reisueber

## Build/Test/Lint Commands
- **Run all tests**: `composer test`
- **Run single test**: `php bin/phpunit tests/path/to/TestFile.php`
- **Run specific test method**: `php bin/phpunit --filter methodName tests/path/to/TestFile.php`
- **Lint code**: `composer lint` (runs all linters)
- **Fix code style**: `composer fix`
- **Build CSS**: `npm run build:css`
- **Watch CSS changes**: `npm run watch:css`
- **Build assets**: `npm run build`
- **Start dev server**: `npm run start` or `composer serve`

## Code Style Guidelines
- **PHP**: Strict types declaration required (`declare(strict_types=1);`)
- **Imports**: Ordered imports, no global imports
- **Formatting**: Symfony standards, short array syntax, single space concatenation
- **Indentation**: 4 spaces, array indentation consistent
- **Documentation**: Left-aligned PHPDoc, ordered PHPDoc sections
- **Error Handling**: Strict comparison (`===`), strict parameter typing 
- **Types**: Nullable types for default null values, explicit return types
- **Naming**: Follow Symfony/Sulu conventions for class/method/property names
- **Spacing**: Ensure multiline arguments are fully multiline
- **Method arguments**: One per line when multiline
- **Classes**: One extension per line for multi-inheritance
- **PHP version compatibility**: Target PHP 8.2+