#Twig Gravatar
An extension for Twig that provides simple filters for Gravatar.

##Installation
`composer require ry167/twig-gravatar`

##Usage
```
require("vendor/autoload.php");

//Define your Twig Environment and Twig Loader
$twig->addExtension(new \TwigGravatar());
```

##Filters
This is Extension is designed so that you chain together the filters that you need on top of each other. You must however always start with the grAvatar filter.

###grAvatar
Create a Gravatar URL, This returns just the URL for the persons avatar image without the `<img>` tag
```
{{example@example.com|grAvatar}}
```

###grHttps
Change a Gravatar URL to its secure counterpart.
```
{example@example.com|grAvatar|grHttps}
```

###grSize
Change the Size of the Gravatar Image to the specified size in pixels.
```
{{example@example.com|grAvatar|grSize(1024)}}
```

Gravatar does not serve images greater than `2048px`, and they are all squares.

###grDefault
Specify a default image for if the User does not have one defined
```
{{example@example.com|grAvatar|grDefault('http://example.com/default.png')}}
```

You can also use any of Gravatar's built in default images, [you can see them here](http://en.gravatar.com/site/implement/images/#default-image). Just use the code assigned to them such as `mm` instead of your Image URL.

###grRating
Specify a maximum rating that the image can be.
Valid values are `g,pg,r` and `x`.
```
{{example@example.com|grAvatar|grRating('pg')}}
```

##Prefix
You can change the filter prefix from `gr` to something else by editing its public variable.
```
//Define your Twig Environment and Twig Loader

$TwigGravatar = new \TwigGravatar();
$TwigGravatar->filterPrefix = 'foo';

$twig->addExtension($TwigGravatar);
//Filters are now 'fooAvatar' etc
```
