<?php

namespace RefinedDigital\ProductManager\Module\Models;

use RefinedDigital\CMS\Modules\Core\Models\CoreModel;

class OrderEmail extends CoreModel
{
    protected $fillable = [
        'active',
        'name',
        'content',
    ];
}
