<?php

namespace RefinedDigital\ProductManager\Module\Models;

use RefinedDigital\CMS\Modules\Core\Models\CoreModel;

class OrderProductValue extends CoreModel
{
    protected $fillable = [
        'product_order_id',
        'product_id',
        'variation_id',
        'variation_value_id',
    ];
}
