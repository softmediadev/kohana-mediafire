## MediaFire for Kohana

#### Requirements

You must first register and create a new application in the *My Account* &gt; *Developers* section on MediaFire's website in order to get an Application ID and an API Key to use this class.

Also look at the class source and MediaFire website for documentation of the API : [REST API - MediaFire](http://developers.mediafire.com/index.php/REST_API)

#### Installation

Place the files in your modules directory.

Copy `MODPATH.menu/config/mediafire.php` into `APPPATH/config/mediafire.php` and customize.

Activate the module in `bootstrap.php`.

```php
<?php
Kohana::modules(array(
	...
	'mediafire' => MODPATH.'mediafire',
));
```
We create an instance of the class
```php
$mfinst = MediaFire::instance();
```

##### Upload a file to MediaFire.
We must first upload the file to your server, then, upload it to MediaFire with the following script:
```php
$file = 'path/filename.ext';
	
$mfinst->fileUpload($file);
```

#### Methods for files

**fileCollaborate**: Generate link(s) for multiple people to edit files

#### ABOUT AND LICENSE

Copyright (c) 2013, Soft Media Development. All right reserved. Website: www.smd.com.pa

This project is using the class [mflib](https://github.com/windylea/mediafire-api-php-library) created by [WindyLea](https://github.com/windylea).

This project is made under BSD license. See LICENSE file for more information.

MediaFire® is a registered trademark of the MediaFire, LLC.
