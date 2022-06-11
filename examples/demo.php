<?php
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';
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


