# User Agent for WP Bones

<p align="center">

  <a href="https://packagist.org/packages/wpbones/useragent">
  <img src="https://poser.pugx.org/wpbones/useragent/v/stable?style=for-the-badge" alt="Latest Stable Version" />
  </a>

  <a href="https://packagist.org/packages/wpbones/useragent">
   <img src="https://poser.pugx.org/wpbones/useragent/v/unstable?style=for-the-badge" alt="Latest Unstable Version" />
  </a>

  <a href="https://packagist.org/packages/wpbones/useragent">
   <img src="https://poser.pugx.org/wpbones/useragent/downloads?style=for-the-badge" alt="Total Downloads" />
  </a>

  <a href="https://packagist.org/packages/wpbones/useragent">
   <img src="https://poser.pugx.org/wpbones/useragent/license?style=for-the-badge" alt="License" />
  </a>

  <a href="https://packagist.org/packages/wpbones/useragent">
   <img src="https://poser.pugx.org/wpbones/useragent/d/monthly?style=for-the-badge" alt="Monthly Downloads" />
  </a>

</p>

An useful method to detect the useragent

## Requirements

This package works with a WordPress plugin written with [WP Bones framework library](https://github.com/wpbones/WPBones).

## Installation

You can install third party packages by using:

```sh copy
php bones require wpbones/useragent
```

I advise to use this command instead of `composer require` because doing this an automatic renaming will done.

You can use composer to install this package:

```sh copy
composer require wpbones/useragent
```

You may also to add `"wpbones/useragent": "^1.0"` in the `composer.json` file of your plugin:

```json copy filename="composer.json" {4}
  "require": {
    "php": ">=7.4",
    "wpbones/wpbones": "~0.8",
    "wpbones/useragent": "~1.0"
  },
```

and run

```sh copy
composer install
```

## How to

You will be able to use `wpbones_user_agent()` function to get an instance of Mobile Detect.

```php copy
if(wpbones_user_agent()->isMobile()) {
  echo "You're by Mobile";
} else {
  echo "You're by Desktop";
}
```

### Sample

```php copy
// Basic detection.
wpbones_user_agent()->isMobile();
wpbones_user_agent()->isTablet();

// Magic methods.
wpbones_user_agent()->isIphone();
wpbones_user_agent()->isSamsung();
// [...]

// Alternative to magic methods.
wpbones_user_agent()->is('iphone');

// Find the version of component.
wpbones_user_agent()->version('Android');
```

You may also

```php copy
// Any mobile device (phones or tablets).
if ( wpbones_user_agent()->isMobile() ) {

}

// Any tablet device.
if( wpbones_user_agent()->isTablet() ){

}

// Exclude tablets.
if( wpbones_user_agent()->isMobile() && !wpbones_user_agent()->isTablet() ){

}

// Check for a specific platform with the help of the magic methods:
if( wpbones_user_agent()->isiOS() ){

}

if( wpbones_user_agent()->isAndroidOS() ){

}

// Alternative method is() for checking specific properties.
// WARNING: this method is in BETA, some keyword properties will change in the future.
wpbones_user_agent()->is('Chrome')
wpbones_user_agent()->is('iOS')
wpbones_user_agent()->is('UCBrowser')
wpbones_user_agent()->is('Opera')
// [...]
```