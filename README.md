# Configurator

## What is it
A simple & common configurator for PHP

## Logger是什么
一个简洁的、通用的PHP应用配置工具

## Installation
```
composer require blogdaren/configurator
```

## Usage

```php
<?php
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';
use Configurator\Configurator;

$config = array(
    'k1' => 'v1',
    'k2' => array(
        'k3' => 'v3',
        'k4' => 'v4',
    ),
);

$config1 = array(
    'k1' => 'v1',
    'k2' => '100',
);

$config2 = array(
    'k1' => 'v1',
    'k2' => array(
        'k5' => 'v5',
    ),
);

$config3 = array(
    'k1' => 'v1',
    'k2' => array(
        'k6' => 'v6',
    ),
);

//output: 
/*Array
(
    [appConfig] => Array
        (
            [k1] => v1
            [k2] => Array
                (
                    [k3] => v3
                    [k4] => v4
                )

        )

)*/
Configurator::set('appConfig', $config);
print_r(Configurator::get('/'));


//output:
/*Array
(
    [appConfig] => Array
        (
            [k1] => v1
            [k2] => Array
                (
                    [k3] => v3
                    [k4] => v4
                    [k5] => v5
                )

        )

)*/
Configurator::set('appConfig', $config2);
print_r(Configurator::get('/'));

//output:
/*Array
(
    [appConfig] => Array
        (
            [k1] => v1
            [k2] => Array
                (
                    [k6] => v6
                )

        )

)*/
Configurator::reset('appConfig', $config3);
print_r(Configurator::get('/'));

//output:
/*Array
(
    [k1] => v1
    [k2] => Array
        (
            [k6] => v6
        )

)*/
print_r(Configurator::get('appConfig'));

//output: 100
Configurator::set('appConfig', $config1);
print_r(Configurator::get('appConfig/k2'));


//output: 200
Configurator::set('appConfig/k2', '200');
print_r(Configurator::get('appConfig/k2'));

//ouput:
/*Array
(
    [appConfig] => Array
        (
            [k1] => v1
        )

)*/
Configurator::remove('appConfig/k2');
print_r(Configurator::get('/'));
```

## Related links and thanks

* [http://www.blogdaren.com](http://www.blogdaren.com)

