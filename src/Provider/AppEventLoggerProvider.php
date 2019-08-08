<?php

namespace LinkORB\AppEventLogger\Provider;

use LinkORB\AppEvent\Formatter\AppEventFormatter;
use LinkORB\AppEventLogger\Processor\TokenProcessor;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\TagProcessor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AppEventLoggerProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        // this is the only mandatory config
        $container['linkorb_app_event.path'] = function () {
            return null;
        };

        // set the default log level
        $container['linkorb_app_event.level'] = function () {
            return Logger::INFO;
        };
        // set the default log channel
        $container['linkorb_app_event.channel_name'] = function () {
            return 'app_event';
        };
        // tag processor is disabled; set an array of tags to enable
        $container['linkorb_app_event.tags'] = function () {
            return [];
        };
        // token processor is enabled by default; set to false to disable
        $container['linkorb_app_event.token_processor'] = function () {
            return true;
        };

        $container['linkorb_app_event.logger'] = function ($container) {
            $logger = new Logger($container['linkorb_app_event.channel_name']);

            $loggingHandler = $container['linkorb_app_event.app_event_handler'];
            $loggingHandler->setFormatter($container['linkorb_app_event.app_event_formatter']);
            $logger->pushHandler($loggingHandler);

            foreach ($container['linkorb_app_event.processors'] as $processor) {
                $logger->pushProcessor($processor);
            }

            return $logger;
        };

        $container['linkorb_app_event.app_event_handler'] = function ($container) {
            if (null === $container['linkorb_app_event.path']) {
                throw new \RuntimeException(
                    'Unable to configure App Event Logger: missing "linkorb_app_event.path" configuration.'
                );
            }

            return new StreamHandler($container['linkorb_app_event.path'], $container['linkorb_app_event.level']);
        };

        $container['linkorb_app_event.app_event_formatter'] = function () {
            return new AppEventFormatter(new JsonFormatter());
        };

        $container['linkorb_app_event.processors'] = function ($container) {
            $processors = [];
            if (!empty($container['linkorb_app_event.tags'])) {
                $processors[] = new TagProcessor($container['linkorb_app_event.tags']);
            }
            if (false !== $container['linkorb_app_event.token_processor']) {
                $processors[] = new TokenProcessor($container['security.token_storage']);
            }

            return $processors;
        };
    }
}
