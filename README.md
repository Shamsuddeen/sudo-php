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

### Create Customer

```php
<?php
require_once 'vendor/autoload.php';
use SudoAfrica\Sudo;

$sudo = new Sudo\Sudo('13337b87ee76gew87fg87gfweugf87w7ge78f229c', true); // true for Sandbox; false for Live
$client_data = [
    "type" => "individual",
    "status" => "active",
    "name" => "Shamsuddeen Omacy",
    "phoneNumber" => "07012345678",
    "emailAddress" => "omacy@mail.ng",
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
        ]
    ],
    "billingAddress" => [
        "line1" => "4 Barnawa Close",
        "line2" => "Off Challawa Crescent",
        "city" => "Barnawa",
        "state" => "Kaduna",
        "country" => "NG",
        "postalCode" => "800001"
    ]
];

$sudo->addCustomer($client_data);
?>
```

### Get Customers

```php
<?php
    $sudo->getCustomers();
?>
```

### Create Card

Create card and set spending limits, if you did not set the spendingLimits, default will be applied.

```php
<?php
    $data = [
        "customerId" => "64771cdfce4b094addfcba4a",
        "fundingSourceId" => "61e5655b1e32bc4c04dea28b",
        "debitAccountId" => "61e5747a1e32bc4c04dea85c",
        "brand" => "MasterCard",
        "type" => "virtual",
        "currency" => "USD",
        "issuerCountry" => "USA",
        "status" => "active",
        "spendingControls" => [
            "allowedCategories" => [],
            "blockedCategories" => [],
            "channels" => [
                "atm" => true,
                "pos" => true,
                "web" => true,
                "mobile" => true
            ],
            "spendingLimits" => [
                [
                    "interval" => "daily",
                    "amount" => 20000
                ],
                [
                    "interval" => "weekly",
                    "amount" => 1000000
                ],
                [
                    "interval" => "monthly",
                    "amount" => 100000000
                ]
            ]
        ],
        "sendPINSMS" => false
    ];
    $result = $sudo->createCard($data);
?>
```
