# Pure CSS Switch Button for WP Bones

<p align="center">

  <a href="https://packagist.org/packages/wpbones/pure-css-switch">
  <img src="https://poser.pugx.org/wpbones/pure-css-switch/v/stable?style=for-the-badge" alt="Latest Stable Version" />
  </a>

  <a href="https://packagist.org/packages/wpbones/pure-css-switch">
   <img src="https://poser.pugx.org/wpbones/pure-css-switch/v/unstable?style=for-the-badge" alt="Latest Unstable Version" />
  </a>

  <a href="https://packagist.org/packages/wpbones/pure-css-switch">
   <img src="https://poser.pugx.org/wpbones/pure-css-switch/downloads?style=for-the-badge" alt="Total Downloads" />
  </a>

  <a href="https://packagist.org/packages/wpbones/pure-css-switch">
   <img src="https://poser.pugx.org/wpbones/pure-css-switch/license?style=for-the-badge" alt="License" />
  </a>

  <a href="https://packagist.org/packages/wpbones/pure-css-switch">
   <img src="https://poser.pugx.org/wpbones/pure-css-switch/d/monthly?style=for-the-badge" alt="Monthly Downloads" />
  </a>

</p>

Pure CSS Switch Button for WordPress/WP Bones

![Pure CSS Switch Button for WP Bones](https://github.com/user-attachments/assets/29822083-a547-4da6-a0e8-e1a048134e26)

## Requirements

This package works with a WordPress plugin written with [WP Bones framework library](https://github.com/wpbones/WPBones).

## Installation

You can install third party packages by using:

```sh copy
php bones require  wpbones/pure-css-switch
```

I advise to use this command instead of `composer require` because doing this an automatic renaming will done.

You can use composer to install this package:

```sh copy
composer require  wpbones/pure-css-switch
```

You may also to add `" wpbones/pure-css-switch": "~0.7"` in the `composer.json` file of your plugin:

```json copy filename="composer.json" {4}
  "require": {
    "php": ">=7.4",
    "wpbones/wpbones": "~1.5",
    " wpbones/pure-css-tabs": "~0.7"
  },
```

and run

```sh copy
composer install
```

## Development installation

Use `yarn` to install the development tools. Next, use `gulp --production` to compile the resources.

## Enqueue for Controller

You can use the provider to enqueue the styles.

```php
public function index()
{
  // enqueue the minified version
  PureCSSSwitchProvider::enqueueStyles();

  // ...

}
```

## PureCSSSwitchProvider

This is a static class autoloaded by composer. You can use it to enqueue or get the styles path:

```php
// enqueue the minified version
PureCSSSwitchProvider::enqueueStyles();

// enqueue the flat version
PureCSSSwitchProvider::enqueueStyles( false );

// return the absolute path of the minified css
PureCSSSwitchProvider::css();

// return the absolute path of the flat css
PureCSSSwitchProvider::css();
```

## Mode

To default the switch works as on/off button. You can change the mode by setting `mode` property,

```php
<?php echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-7' )
            ->mode( 'select' ); ?>
```

In the above example, you can use it as selector instead of on/off button.


## Theme

Of course, you can switch theme by using `theme` property ot its fluent version.
Currently, we support two theme:

* `flat-round` - default
* `flat-square`

You should use something look like:

```php
<?php echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-7' )
            ->theme( 'flat-square' ); ?>
```


## Examples

In your view you can use the `WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton` class

```php copy filename="Simple Usage"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-1' );
```

```php copy filename="Left Label"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-2' )
  ->left_label( 'Swipe me' );
```

```php copy filename="Right Label"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-3' )
  ->right_label( 'Swipe me' );
```

```php copy filename="Both Labels"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-4' )
  ->left_label( 'Swipe me' )
  ->right_label( 'Swipe me' );
```

```php copy filename="Checked"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-5' )
  ->left_label( 'Swipe me' )
  ->checked( true );
```

```php copy filename="Disabled"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-6' )
  ->left_label( 'Swipe me' )
  ->disabled( true );
```

```php copy filename="Theme"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-7' )
  ->theme( 'flat-square' );
```

```php copy filename="Mode"
echo WPBannerize\PureCSSSwitch\Html\HtmlTagSwitchButton::name( 'test-switch-8' )
  ->left_label( 'Turn left' )
  ->right_label( 'Turn right' )
  ->mode( 'select' );
```