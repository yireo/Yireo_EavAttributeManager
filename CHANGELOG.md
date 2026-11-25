# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 25 November 2026
### Added
- Attribute form validates the frontend, source, backend and attribute model classes and the backend table before saving
- Attribute form shows the "required", "user defined" and "unique" flags as switches
- Support for PHP 8.5

### Changed
- Requires `loki/magento2-admin-components` 0.9.2 or newer (needed for `ItemValidatorInterface`)

### Removed
- Support for PHP 8.1

### Security
- Separate ACL resources for viewing, editing and deleting EAV attributes under Stores > Attributes, now enforced on the grid and form actions
- Model fields only accept existing classes of the matching EAV type, and the backend table only the entity's own value table, so arbitrary classes or tables can no longer be configured
- System attributes can no longer be deleted, nor can their code, entity type, backend type, backend table or user-defined flag be changed

## [1.0.0] - 21 October 2025
### Added
- Initial release
