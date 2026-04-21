# QueueMaster Reporter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/queuemaster/reporter.svg?style=flat-square)](https://packagist.org/packages/queuemaster/reporter)
[![Total Downloads](https://img.shields.io/packagist/dt/queuemaster/reporter.svg?style=flat-square)](https://packagist.org/packages/queuemaster/reporter)

QueueMaster Reporter is a lightweight Laravel package that automatically monitors and reports your Queue job statuses (Pending, Processing, Completed, Failed) to the QueueMaster SaaS dashboard.

## Installation

You can install the package via composer:

```bash
composer require queuemaster/reporter
```

The service provider will automatically register itself.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="QueueMaster\Reporter\QueueMasterReporterServiceProvider" --tag="config"
```

Then, add these variables to your `.env` file:

```env
QUEUEMASTER_SERVER_URL=https://queuemaster.io
QUEUEMASTER_API_TOKEN=your_organization_api_token
```

## Features

- **Automatic Monitoring**: Hooks into Laravel Queue events.
- **Job Retries**: Supports remote retries directly from the QueueMaster dashboard.
- **Real-time Updates**: Lightweight jobs report status changes immediately.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
