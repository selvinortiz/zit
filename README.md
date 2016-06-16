![Zit](Zit.png)

[![Build Status](https://travis-ci.org/selvinortiz/zit.png)](https://travis-ci.org/selvinortiz/zit)
[![Total Downloads](https://poser.pugx.org/selvinortiz/zit/d/total.png)](https://packagist.org/packages/selvinortiz/zit)
[![Latest Stable Version](https://poser.pugx.org/selvinortiz/zit/v/stable.png)](https://packagist.org/packages/selvinortiz/zit)

### Description
>**Zit** is a tiny dependency injection library inspired by [Pimple](https://github.com/fabpot/Pimple) and written by [Selvin Ortiz](https://selvinortiz.com)

### Requirements
- PHP 5.4+
- [Composer](http://getcomposer.org) and [selvinortiz/zit](https://packagist.org/packages/selvinortiz/zit)

### Install
```bash
composer require selvinortiz/zit
```

### Test
```bash
sh spec.sh
```

### Usage
> Dependency management is a difficult topic to explain but essentially, **Zit** lets you stuff things into it that you can pop out later throughout your application.

```php
// Make an instance
$app = Zit::make();

// Stash a config object
$app->stash('config', new Config(['usr' => 'root', 'pwd' => 'secret']));

// Bind a session generator
$app->bind('session', function() {
    return new Session();
});

// Bind a database generator
$app->bind('db', function($app) {
    return new Db($app->config->usr, $app->config->pwd);
});

// Extend your $app with new functionality
$app->extend('end', function($app) {
    $app->db->close();
    $app->session->destroy();
});

// Finish
$app->end();
```

> You can also use **Zit** without making a new instance, the _static way_ if you will.

```php
// Stash a config object
Zit::stash('config', new Config(['usr' => 'root', 'pwd' => 'secret']));

// Bind a session generator
Zit::bind('session', function() {
    return new Session();
});

// Bind a database generator
Zit::bind('db', function($zit) {
    return new Db($zit->config->usr, $zit->config->pwd);
});

// Extend Zit with new functionality
Zit::extend('end', function($zit) {
    $zit->db->close();
    $zit->session->destroy();
});

// Finish
Zit::end();
```

> Which approach should you use? Whatever you prefer. If you aren't sure, you can use the _static way_ unless you need to create your own instance that extends **Zit**

#### Note
> Whether you **bind()**, **stash()**, or **extend()** it. You can pop it out using:

```php
Zit::pop('db');  // Formal
Zit::db();       // Via __callStatic()
Zit::db          // Via __callStatic() property sniffing

// If you had done $app = Zit::make()
$app->pop('db'); // Formal
$app->db();      // Via __call()
$app->db;        // Via __get()
```

### API

#### `pop($id, $args = array())`
> **pop()** lets you pop a dependency out of the container

```php
Zit::pop('db');
// See the note above on popping stuff out
```

#### `bind($id, callable $serviceGenerator)`
> **bind()** lets you register a service generator.
> Useful when you need to instantiate a service that depends on a config object for example. Services get generated _only once_ on first call.

```php
Zit::make()->bind('db', function($zit) {
   return new Db($zit->config->usr, $zit->config->pwd); 
});
```

#### `stash($id, $service)`
> **stash()** lets you stash anything inside the container. You get out what you put in, which is useful if you need to stash a bunch of stuff that needs to be accessible throughout your whole application.

```php
Zit::make()->stash('session', new Session())
```

#### `extend($id, $callback)`
> **extend()** gives you the ability to add new functionality to the container

```php
Zit::make()->extend('logout', function($zit) {
    $zit->session->logout();
});
```

---

### Contribute
> **Zit** wants to be friendly to _first time contributors_. Just follow the steps below and if you have questions along the way, please reach out.

1. Fork it!
1. Create your bugfix or feature branch
1. Commit and push your changes
1. Submit a pull request

### License
**Zit** is open source software licensed under the [MIT License](LICENSE.txt)
