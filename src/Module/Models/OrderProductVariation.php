<?php

namespace RefinedDigital\ProductManager\Module\Models;

use RefinedDigital\CMS\Modules\Core\Models\CoreModel;

class OrderProductVariation extends CoreModel
{
    protected $fillable = [
        'order_product_id',
        'variation_id',
        'variation_value_id',
    ];
}
