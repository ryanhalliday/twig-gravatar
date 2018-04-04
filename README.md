# Twig Gravatar
[![Latest Version](https://img.shields.io/github/release/ry167/twig-gravatar.svg?style=flat-square)](https://github.com/ry167/twig-gravatar/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/ry167/twig-gravatar/master.svg?style=flat-square)](https://travis-ci.org/ry167/twig-gravatar)
[![Quality Score](https://img.shields.io/scrutinizer/g/ry167/twig-gravatar.svg?style=flat-square)](https://scrutinizer-ci.com/g/ry167/twig-gravatar)
[![Total Downloads](https://img.shields.io/packagist/dt/ry167/twig-gravatar.svg?style=flat-square)](https://packagist.org/packages/ry167/twig-gravatar)

An extension for Twig that provides simple filters for Gravatar.

## Installation
Use `composer` to install this extension:
```Shell
composer require ry167/twig-gravatar 2.0.2
```

## Usage
```PHP
require("vendor/autoload.php");

//Define your Twig Environment and Twig Loader
$twig->addExtension(new \TwigGravatar());

//create customized Twig Gravatar
new \TwigGravatar($default = null, $size = null, $filterPrefix = 'gr', $rating = null, $useHttps = true);
```

## Usage in Symfony
```YAML
    twig.extension.gravatar:
        class: \TwigGravatar
        arguments:
            $default: ~         e.g. 'monsterid'
            $size: ~            e.g. 50
            $filterPrefix: ~    e.g. 'foo'
            $rating: ~          e.g. 'x'
            $useHttps: true
        tags:
            - { name: twig.extension }
```
You can also remove arguments section and the default values shown will be used.

## Filters
This is Extension is designed so that you chain together the filters that you need on top of each other. You must however always start with the grAvatar filter.

### grAvatar
Create a Gravatar URL, This returns just the URL for the persons avatar image without the `<img>` tag
```Twig
{{ 'example@example.com'|grAvatar }}
```

### grHttps
Change a Gravatar URL to its secure counterpart.
```Twig
{{ 'example@example.com'|grAvatar|grHttps }}
```

### grSize
Change the Size of the Gravatar Image to the specified size in pixels.
```Twig
{{ 'example@example.com'|grAvatar|grSize(1024) }}
```

Gravatar does not serve images greater than `2048px`, and they are all squares.

### grDefault
Specify a default image for if the User does not have one defined
```
{{ 'example@example.com'|grAvatar|grDefault('http://example.com/default.png') }}
```

You can also use any of Gravatar's built in default images, [you can see them here](http://en.gravatar.com/site/implement/images/#default-image). Just use the code assigned to them such as `mm` instead of your Image URL.

### grRating
Specify a maximum rating that the image can be.
Valid values are `g`, `pg`, `r` and `x`.
```
{{ 'example@example.com'|grAvatar|grRating('pg') }}
```

## Prefix
You can change the filter prefix from `gr` to something else by changing its value in constructor.
```PHP
//Define your Twig Environment and Twig Loader

$TwigGravatar = new \TwigGravatar(null, null, 'foo');

$twig->addExtension($TwigGravatar);
//Filters are now 'fooAvatar' etc
```
