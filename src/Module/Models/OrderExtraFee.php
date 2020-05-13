<?php

namespace RefinedDigital\ProductManager\Module\Models;

use RefinedDigital\CMS\Modules\Core\Models\CoreModel;

class OrderExtraFee extends CoreModel
{
    protected $fillable = [
        'order_id',
        'name',
        'amount',
        'percent',
        'total',
    ];
}
