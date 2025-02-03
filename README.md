# Laravel Sonar - Product Analytics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mafrasil/laravel-sonar.svg?style=flat-square)](https://packagist.org/packages/mafrasil/laravel-sonar)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mafrasil/laravel-sonar/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mafrasil/laravel-sonar/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mafrasil/laravel-sonar.svg?style=flat-square)](https://packagist.org/packages/mafrasil/laravel-sonar)

Laravel Sonar is a powerful product analytics package that makes it easy to track user interactions in your Laravel application. It provides automatic tracking for clicks, hovers, and impressions, with support for custom events.

## Features

-   🚀 Automatic event tracking (clicks, hovers, impressions)
-   🎯 Custom event tracking
-   ⚡ Efficient batch processing of events
-   🛠️ Optional React components and hooks for easy integration
-   📱 Responsive design support with screen size tracking
-   🔄 Compatible with Inertia.js
-   ⚙️ Configurable tracking behavior

## Installation

You can install the package via composer:

```bash
composer require mafrasil/laravel-sonar
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="laravel-sonar-migrations"
php artisan migrate
```

Publish the config file:

```bash
php artisan vendor:publish --tag="laravel-sonar-config"
```

Publish the JavaScript assets:

```bash
php artisan sonar:publish-assets
```

### Optional: Export React Components

If you're using React, you can export the React components and TypeScript types:

```bash
php artisan sonar:export-react
```

This will create React components and hooks in your application's JavaScript directory.

## Usage

### Data Attributes

The simplest way to track elements is using data attributes:

```html
<button data-sonar="signup-button" data-sonar-metadata='{"variant": "blue"}'>
    Sign Up
</button>
```

### React Component (Optional)

If you've exported the React components, you can use the `SonarTracker` component:

```jsx
import { SonarTracker } from "@/components/SonarTracker";

function SignupButton() {
    return (
        <SonarTracker
            name="signup-button"
            metadata={{ variant: "blue" }}
            trackAllHovers={true} // Optional: track repeated hovers
        >
            <button>Sign Up</button>
        </SonarTracker>
    );
}
```

### Custom Events

Use the `useSonar` hook for custom event tracking and configuration:

```jsx
import { useSonar } from "@/hooks/useSonar";

function CheckoutForm() {
    const { track, configure } = useSonar();

    // Optional: Configure tracking behavior
    useEffect(() => {
        configure({ trackAllHovers: true });
    }, []);

    const handleSubmit = () => {
        track("checkout-complete", "custom", {
            amount: 99.99,
            currency: "USD",
        });
    };

    return <form onSubmit={handleSubmit}>{/* form fields */}</form>;
}
```

### Server-Side Tracking

You can also track events from your PHP code:

```php
use Mafrasil\LaravelSonar\Facades\LaravelSonar;

LaravelSonar::track('order-processed', 'custom', [
    'orderId' => $order->id,
    'amount' => $order->total
]);
```

## Event Types

The package supports the following event types out of the box:

-   `click`: User clicks on tracked elements
-   `hover`: User hovers over tracked elements (configurable for repeated tracking)
-   `impression`: Element becomes visible in the viewport
-   `custom`: Any custom event you want to track

## Configuration

You can customize the package behavior in the `config/sonar.php` file:

```php
return [
    'route' => [
        'prefix' => 'api',
        'middleware' => ['api'],
    ],
    'queue' => [
        'batch_size' => 10,
        'flush_interval' => 1000,
    ],
    // ...
];
```

### JavaScript Configuration

You can configure the JavaScript behavior globally:

```javascript
import { configureSonar } from "laravel-sonar";

configureSonar({
    trackAllHovers: true, // Enable tracking of repeated hovers
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [mafrasil](https://github.com/mafrasil)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
