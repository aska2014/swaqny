# Eloquent extension
These set of classes extend laravel eloquent orm to give more functionalities

## Usage example

```php

// 1. Create product and validate it

class Product extends Kareem3d\Eloquent\Model {

    protected $rules = array(
        'title' => 'required', .....);

    protected $customMessages = array(
        'title.required' => 'Please enter title');
}

$product = new Product( $attributes );
if($product->isValid()) ....
$messages = $product->getValidatorMessages();

if(Product::validateAttributes($attributes)) ...
//-------------------------------------------------------------------------------------//



// 2. Specification table
// You have to set specsTable attribute and specs array before using this functionality..

Model::setLanguages(array('en', 'fr', 'ru'));

Model::$defaultLanguage = 'fr';

$product = new Product();

$product->update(array('title' => 'me', 'language' => 'en'));
$product->update(array('title' => 'moi', 'language' => 'fr'));
$product->update(array('title' => 'мне', 'language' => 'ru'));


$product = Product::find(1);

echo $product->en('title'); // me

echo $product->fr('title'); // moi

echo $product->ru('title'); // мне

echo $product->title; // moi
//-------------------------------------------------------------------------------------//



// 3. Dont duplicate
// Dont duplicate title
array $dontDuplicate = array('title');

// Dont duplicate title or model
array $dontDuplicate = array('title', 'model');

// Done duplicate title and model
array $dontDuplicate = array(array('title', 'model'));
//-------------------------------------------------------------------------------------//



// 4. Before validate and before save
// Copy all directory contents to another directory
// If returned false won't reach the validation step or save step..
function beforeSave(){ ... }
function beforeValidate(){ ... }
//-------------------------------------------------------------------------------------//
```