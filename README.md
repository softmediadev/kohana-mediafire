## MediaFire for Kohana

#### Requirements

You must first register and create a new application in the *My Account* &gt; *Developers* section on MediaFire's website in order to get an Application ID and an API Key to use this class.

Also look at the class source and MediaFire website for documentation of the API : [REST API - MediaFire](http://developers.mediafire.com/index.php/REST_API)

#### Installation

Place the files in your modules directory.

Copy `MODPATH/config/mediafire.php` into `APPPATH/config/mediafire.php` and customize.

Activate the module in `bootstrap.php`.

```php
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

#### Methods for Files

---

**$mfinst->fileCollaborate()**: Generate link(s) for multiple people to edit files

**$mfinst->fileCopy()**: Copy a file to a specified folder

**$mfinst->fileDelete()**: Deletes a single file or multiple files

**$mfinst->fileGetInfo()**: Returns details of a single file or multiple files

**$mfinst->fileGetLinks()**: Return the view link, normal download link, and if possible the direct download link of a file.

**$mfinst->fileMove()**: Moves a single file or multiple files to a folder

**$mfinst->fileOneTimeDownload()**: Create a one-time download link. This method can also be used to configure/update an existing one-time download link

**$mfinst->fileUpdateInfo()**: Updates a file's information

**$mfinst->fileUpdate()**: Updates a file's quickkey with another file's quickkey. Note: Only files with the same file extension can be used with this operation

**$mfinst->fileUpdatePassword()**: Updates a file's password

**$mfinst->fileUpload()**: Uploads a file

**$mfinst->filePollUpload()**: Check for the status of a current Upload

---

#### ABOUT AND LICENSE

Copyright (c) 2013, Soft Media Development. All right reserved. Website: www.smd.com.pa

This project is using the class [mflib](https://github.com/windylea/mediafire-api-php-library) created by [WindyLea](https://github.com/windylea).

This project is made under BSD license. See LICENSE file for more information.

MediaFire® is a registered trademark of the MediaFire, LLC.
