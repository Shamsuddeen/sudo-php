# Sudo-php

PHP Library for Sudo Africa

## How to Use

Require the package from Packagist 

``` bash
composer require Shamsuddeen/sudo-php
```

## Function Naming Convention

Functions are named based on the documentation [located here](https://docs.sudo.africa)

`Add Customer` from the documentation becomes 

``` php
$sudo->addCustomer()
```

As in the sample code below:

## Sample code
  
```php
<?php
require_once 'vendor/autoload.php';
use SudoAfrica\Sudo;

$sudo = new Sudo\Sudo('13337b87ee76gew87fg87gfweugf87w7ge78f229c');
$client_data = [
	"type" => "individual",
  	"individual" => [
		"firstName" => "Shamsuddeen",
		"lastName" => "Omacy",
		"otherNames" => "",
		"dob" => "1988/12/23",
		"identity" => [
			"type" => "BVN",
			"number" => "12345678901"
		],
		"documents" => [
			"idFrontUrl" => "link",
			"idBackUrl" => "link",
			"addressVerificationUrl" => "link"
		],
	],
  	"status" => "active",
  	"name" => "Shamsuddeen Omacy",
  	"phoneNumber" => "07012345678",
  	"emailAddress" => "omacy@mail.ng"
];

$sudo->addCustomer($client_data);
?>
```
