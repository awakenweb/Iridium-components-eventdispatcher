Iridium-components-eventdispatcher
==================================

About
-----
Event dispatcher implementing the mediator pattern. Can work independantly but best used with Iridium Framework

The Dispatcher class keeps a list of registred handlers for specific events. Handler can be any valid callback : closures, object/method array, etc. as long as they accept the Event object as parameter.
The Event class allows you to propagate objects related to the events throug all registered handlers linked to this event.

The class is unit tested using [Atoum](https://github.com/atoum/atoum).

Installation
------------
### Prerequisites

***Iridium requires at least PHP 5.4+ to work.***

Some of Iridium components may work on PHP5.3 but no support will be provided for this version.

### Using Composer
First, install [Composer](http://getcomposer.org/ "Composer").
Create a composer.json file at the root of your project. This file must at least contain :
```json
{
    "require": {
        "awakenweb/iridium-components-eventdispatcher": "dev-master"
        }
}
```
and then run
```bash
~$ composer install
```
---
Usage
-----

### Dispatcher
```php
<?php
include('path/to/vendor/autoload.php');
use Iridium\Components\EventDispatcher\Dispatcher,
    Iridium\Components\EventDispatcher\Event;    

$handler = function($event){
                foreach($event as $ev) {
                    echo $ev;
                }};

$dispatcher = new Dispatcher\Dispatcher();
$dispatcher->subscribeToEvent('event.name', $handler);
```

You can either provide a Event object or a string. The string will be automatically converted into an Event object using the string as name.

```php
$dispatcher->dispatch('event.name');
```
is totally identical to
```php
$dispatcher->dispatch(new Event\Event('event.name'));
```
----
### Event

Event constructor accepts 2 parameters :
- the name of the event: **Event names MUST be only letters, digits, hyphens or dots. They are case insensitive**.
- an array of attachments you want to make available to handlers that will be receive the event.

Attachments can be anything you want, as long as your handlers are able to use it.

You can also change the name of the event at any time, and add attachments.
When using the attach() method, you must pass the values directly.

```php
<?php
include('path/to/vendor/autoload.php');
use Iridium\Components\EventDispatcher\Event; 

$event = new Event\Event('event.name', array('a string', 123456, new \stdClass());
$event->setName('anothername.that-i-want')
      ->attach('string')
      ->attach(567890)
      ->attach(new \stdClass());
```
Event implements the `\IteratorAggregate` interface, that gives you access to all the attachements in a `foreach` loop.

```php
<?php
foreach($event as $attachments) {
    var_dump($attachment);
}
```
