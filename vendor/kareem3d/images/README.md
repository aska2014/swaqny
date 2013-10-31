# Image Management System
A new approach developed for managing images

## Usage example

```php

// 1. Create Image specifications (Only one time)

$group = Group::createByName('User.Profile');

$group->createSpecs(array(
    'directory' => 'albums/users/profile/150x150',
    'code'      => '$image->crop(150, 150);'
));

$group->createSpecs(array(
    'directory' => 'albums/users/profile/550x400',
    'code'      => '$image->crop(550, 400);'
));
//-------------------------------------------------------------------------------------//



// 2. Create images

// ImageFacade::versions($groupName, $imageName, $file, $override = true, $interventionImage = null)
$versions = ImageFacade::versions('User.Profile', 'profile-image', Input::file('image'));

$image = Image::create(array(
    'title' => 'Image title tag',
    'alt'   => 'Image alt tag',
))->add($versions);

// First you have to add the Images extension in the user class to use the bellow methods

Auth::user()->replaceImage($image, 'profile-image');
//-------------------------------------------------------------------------------------//


// 3. Last step getting images

Auth::user()->getImage('profile')->html(600, 450); // Will print img tag with the image version with width 550, 400
//-------------------------------------------------------------------------------------//
```