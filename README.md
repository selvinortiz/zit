![Zit](Zit.png)

[![Build Status](https://travis-ci.org/selvinortiz/zit.png)](https://travis-ci.org/selvinortiz/zit)
[![Total Downloads](https://poser.pugx.org/selvinortiz/zit/d/total.png)](https://packagist.org/packages/selvinortiz/zit)
[![Latest Stable Version](https://poser.pugx.org/selvinortiz/zit/v/stable.png)](https://packagist.org/packages/selvinortiz/zit)

### Description
>**Zit** is a tiny dependency injection container similar to and inspired by [Pimple](https://github.com/fabpot/Pimple)

### Requirements
- PHP 5.4
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
```php
// 1. Make a new instance of Zit
// 2. Start injecting stuff
Zit::make();
```

### Reference

#### `bind($id, callable $serviceGenerator)`
> Binds the service generator function to be _popped_ later

```php
Zit::make()->bind('db', function($zit) {
   return new Db(); 
});
// Db will only be generated once
```

#### `stash($id, $service)`
> Stashes away a service to be _popped_ later

```php
Zit::make()->stash('session', new Session())

// You can now access the session statically
Zit::session();
```

#### `extend($id, $callback)`
> Extends _Zit_ with your own functions

```php
Zit::make()->extend('logout', function($zit) {
    $zit->session->logout();
});

Zit::logout();
// Would log the user out via the hypothetical session service
```

---

### License
**Zit** is open source software licensed under the **MIT License**
