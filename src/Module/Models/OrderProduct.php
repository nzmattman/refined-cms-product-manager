<?php

namespace RefinedDigital\ProductManager\Module\Models;

use RefinedDigital\CMS\Modules\Core\Models\CoreModel;

class OrderProduct extends CoreModel
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total',
    ];
}
