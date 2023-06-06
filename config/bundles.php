<?php

use CustomerManagementFrameworkBundle\PimcoreCustomerManagementFrameworkBundle;
use Pimcore\Bundle\ObjectMergerBundle\ObjectMergerBundle;
// ...
return [
    // ...
    PimcoreCustomerManagementFrameworkBundle::class => ['all' => true],
    ObjectMergerBundle::class => ['all' => true],
    // ...
];