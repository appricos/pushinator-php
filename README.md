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

### Managing Channels

All channel methods return an array with the decoded JSON response from the API. On failure, a `RuntimeException` is thrown.

#### List channels

```php
$channels = $client->listChannels();
// $channels['data'] — array of channel objects
```

#### Create a channel

```php
$channel = $client->createChannel(
    name: 'My Channel',
    description: 'Optional description',  // optional
    acknowledgmentEnabled: true            // optional, default false
);
// $channel['data'] — created channel object
```

#### Get a channel

```php
$channel = $client->getChannel('PUSHINATOR_CHANNEL_ID');
// $channel['data'] — channel object
```

#### Update a channel

```php
$channel = $client->updateChannel(
    channelId: 'PUSHINATOR_CHANNEL_ID',
    name: 'Updated Name',
    description: 'Updated description',   // optional
    acknowledgmentEnabled: false          // optional
);
// $channel['data'] — updated channel object
```

#### Delete a channel

```php
$channel = $client->deleteChannel('PUSHINATOR_CHANNEL_ID');
// $channel['data'] — deleted channel object
```

#### Channel object structure

```json
{
  "data": {
    "id": "channel-id",
    "name": "My Channel",
    "description": "Optional description",
    "acknowledgment_enabled": false,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```