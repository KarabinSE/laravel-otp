# Changelog

All notable changes to this project will be documented in this file.

---

## [Unreleased]

### Added

- 🎉 Added `config/otp.php` configuration file to allow customization of:

  - Table name (`otp.table`)
  - OTP length (`otp.length`)
  - Validity in minutes (`otp.expiry`)
  - Max attempts (`otp.max_attempts` – reserved for future use)

- 🧱 Added stub-based migration publishing for OTP table

  - Respects the table name defined in config
  - Skips creation if the table already exists

- 🛠️ Added new Artisan command: `php artisan otp:install`
  - Installs both config and migration files with success messages
  - Helpful for new developers using the package

### Changed

- 🔧 Refactored `OtpServiceProvider` to:

  - Only publish config/migrations in console
  - Register custom commands cleanly

- 🧼 Improved code structure and modernized syntax (return types, cleaner paths)

---

## [v1.0.0] – Initial Release

- Basic OTP generation and validation
- Artisan command to clean expired OTPs (`otp:clean`)
