# Soapbox Backend API Challenge

## Installation

### General Requirements
- PHP 7.4
- Composer

### General Installation
- Run `composer install`;
- Copy `.env.example` to `.env`;
- Run `php artisan key:generate`.
- Run `php artisan optimize:clear`
- Update the following environment variable to use the database connection of your choice
```dotenv
DB_CONNECTION=****
DB_HOST=****
DB_PORT=****
DB_DATABASE=****
DB_USERNAME=****
DB_PASSWORD=****
```
- Run `php artisan migrate`.
- Run `php artisan passport:install`.
- Update the following environment variable to use mailer settings of choice
```dotenv
MAIL_MAILER=****
MAIL_HOST=****
MAIL_PORT=****
MAIL_USERNAME=****
MAIL_PASSWORD=****
MAIL_ENCRYPTION=****
MAIL_FROM_ADDRESS=****
MAIL_FROM_NAME=****
```
- Run `php artisan serve`.

###Testing 

To test this API, please run either of this commands in the root folder of the project
```bash
php artisan test
```
or

```bash
vendor\bin\phpunit
```

### API Documentation
The API documentation can be found at [https://documenter.getpostman.com/view/3172372/Tz5ncJbP](https://documenter.getpostman.com/view/3172372/Tz5ncJbP)
