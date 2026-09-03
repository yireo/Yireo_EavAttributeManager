# Yireo EAV Attribute Manager

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-eav-attribute-manager)
<!-- badges.specs.end -->


**Manage EAV attributes from the Magento Admin Panel**

## Installation
Install this package via composer:
```bash
composer require yireo/magento2-eav-attribute-manager
```

Next, enable this module:
```bash
bin/magento module:enable Yireo_EavAttributeManager Loki_AdminComponents Loki_Components 
```

## Usage
Navigate to **Stores > Attributes > EAV Attribute Manager** to use this module.

![Screenshot of grid overview](doc/images/screenshot-grid.png?raw=true "Screenshow of grid overview")

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EavAttributeManager/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EavAttributeManager/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EavAttributeManager/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EavAttributeManager/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EavAttributeManager/compile.yml?label=compile)
<!-- badges.test.end -->
