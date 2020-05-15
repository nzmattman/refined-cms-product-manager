<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;
use RefinedDigital\ProductManager\Module\Models\ProductVariationTypeValue;

class ProductStatusRepository extends CoreRepository
{

    public function __construct()
    {
        $this->setModel('RefinedDigital\ProductManager\Module\Models\ProductVariationType');
    }
}
