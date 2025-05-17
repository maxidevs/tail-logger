# 🪵 maxidev/tail-logger

**Dynamic, contextual, and tailable logging package for Laravel.**

Easily log messages into daily log files organized by custom folders, with full support for structured context data and a built-in Artisan command for colored tailing—even on Windows.

---

## 🚀 Features

✅ Dynamic directory-based log storage  
✅ Daily rotating log files  
✅ Structured array/object context logging  
✅ Clean output format  
✅ Artisan command `log:tail` for real-time log viewing  
✅ Colorized output by log level (info, warning, error, success)  
✅ Cross-platform tailing (Windows & Unix)

---

## 📦 Installation

```bash
composer require maxidev/tail-logger
```

If installing from local or VCS:

```bash
composer config repositories.max-logger path ../path/to/package
composer require maxidev/tail-logger:@dev
```

Laravel will auto-discover the service provider.

---

## 🧰 Usage

### 📄 Log to a custom path with optional level and context

```php
use Maxidev\Logger\TailLogger;

// Basic log
TailLogger::saveLog('User login successful', 'auth/login');

// With level
TailLogger::saveLog('Invalid credentials', 'auth/login', 'warning');

// With context (array or object)
TailLogger::saveLog('Payment processed', 'billing/invoices', 'success', [
    'user_id' => 123,
    'amount' => 49.99,
    'currency' => 'USD'
]);
```

📁 Log files are saved like:

```
storage/logs/auth/login/2025-05-17.log
storage/logs/billing/invoices/2025-05-17.log
```

---

## 🔍 Real-time log tailing (with colors!)

```bash
php artisan log:tail auth/login
php artisan log:tail billing/invoices --date=2025-05-15
php artisan log:tail api/events --live
```

✅ Fully compatible with Windows and Linux  
🎨 Colorized output: green = success, yellow = warning, red = error

---

## ⚙️ Customization

You can fully control:

- Path/folder structure
- Log level (`info`, `warning`, `error`, `success`)
- Context arrays
- Date formatting in logs

Internally based on [Monolog v3](https://github.com/Seldaek/monolog) and Laravel's logging conventions.

---

## 🧪 Requirements

- PHP 8.1+
- Laravel 11 or 12
- Composer

---

## 📄 License

MIT © Max
