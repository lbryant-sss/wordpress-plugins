<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => '1.1.10.x-dev',
        'version' => '1.1.10.9999999-dev',
        'reference' => 'f14883a037c485147bf5c417aee663d351f3b99b',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => false,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.1.10.x-dev',
            'version' => '1.1.10.9999999-dev',
            'reference' => 'f14883a037c485147bf5c417aee663d351f3b99b',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'paymentplugins/paypal-php-sdk' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => '7368222a13293c6aab8a93fb69d4b39c70e711f4',
            'type' => 'library',
            'install_path' => __DIR__ . '/../paymentplugins/paypal-php-sdk',
            'aliases' => array(
                0 => '9999999-dev',
            ),
            'dev_requirement' => false,
        ),
    ),
);
