# Pushinator PHP Client

A PHP Composer package that enables developers to send push notifications seamlessly through the Pushinator API.

## Installation

Install the package using Composer:

```bash
composer require appricos/pushinator-php
```

Make sure Composer's autoloader is included in your project:

```php
require 'vendor/autoload.php';
```
## Usage

### Initializing the Client

To start using the PushinatorClient, create an instance by passing your API token:

```php
use Pushinator\PushinatorClient;

$client = new PushinatorClient('PUSHINATOR_API_TOKEN');
```

### Sending Notifications

To send a notification to a specific channel, use the `sendNotification` method. Provide your channel ID and the notification content as arguments:

```php
use Pushinator\PushinatorClient;

$client = new PushinatorClient('PUSHINATOR_API_TOKEN');

try {
    $client->sendNotification('PUSHINATOR_CHANNEL_ID', 'Hello from PHP!');
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```