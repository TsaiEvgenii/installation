# BelVG OrderUpgrader

## Overview
BelVG OrderUpgrader is a Magento 2 module that allows customers to upgrade their orders by changing product options such as materials and energy class.

## Features
- Allow customers to upgrade their quote items by changing material type and energy class
- Admin configuration panel for managing available upgrade options
- Integration with cart and checkout processes
- Support for both guest and registered customers

## Configuration

### Admin Configuration
1. Navigate to **Stores > Configuration > BelVG > Order Upgrader**
2. Enable the module in the **General** section
3. Configure upgrade options in the **Options Configuration** section

### Options Configuration
The module allows configuring two types of upgrade options:

1. **Option Types**: Define option codes and display labels (e.g., energy_class, material_type)
    - Option Code: Unique identifier for the option type
    - Display Label: Label displayed to users

2. **Option Values**: Define values for each option type
    - Option Type: Select the option type this value belongs to (dropdown populated from created Option Types)
    - Display Label: Label displayed to users
    - System Value: Value used internally by the system

**Important**: The Option Type dropdown in the Option Values section depends on Option Types that you have created. You must first create Option Types before you can assign values to them. When you delete an Option Type, all related values will be automatically removed from the Option Type dropdown in the Option Values section.

## How It Works

### For Customers
1. When viewing the shopping cart, customers can see available upgrade options for their products
2. Customers can select different materials or energy classes to upgrade their products
3. Upon selection, the original product is replaced with the upgraded version in the cart

### For Admins
1. Admins can configure available option types and values through the admin interface
2. The module automatically syncs the available option types with option values
3. When an option type is deleted, related values in dropdown lists are automatically removed
