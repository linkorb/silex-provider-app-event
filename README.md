# linkorb/silex-provider-app-event-logger

Provides a Monolog logger configured to produce Application Event logs using
[linkorb/app-event][].

## Install

Install using composer:

```sh
$ composer require linkorb/silex-provider-app-event
```

Then register the provider in your app or bootstrap file:

```php
// app/app.php or app/bootstrap.php

use LinkORB\AppEventLogger\Provider\AppEventLoggerProvider;
...

$app->register(
    new AppEventLoggerProvider,
    [
        'linkorb_app_event.path' => 'path/to/a/logfile.ndjson',
    ]
);
```

## Usage

```php
class LoginController
{
    public function indexAction(Application $app, Request $request)
    {
        ...

        $app['linkorb_app_event.logger']->info('login.success', ['username' => 'lara']);
    }
}
```

## Configuration

```php
// app/app.php or app/bootstrap.php
$app->register(
    new AppEventLoggerProvider,
    [
        'linkorb_app_event.path' => 'path/to/a/logfile.ndjson',

        // the minimum log level can be changed from the default INFO
        'linkorb_app_event.level' => DEBUG,

        // the TagProcessor will add tags to log records
        'linkorb_app_event.tags' => ['mytag' => null, 'othertag' => 'a-value'],

        // the TokenProcessor, which adds info about the currently
        // authenticated user, can be disabled
        'linkorb_app_event.token_processor' => false,
    ]
);
```

[linkorb/app-event]: <https://github.com/linkorb/app-event>
  "linkorb/app-event at GitHub"
