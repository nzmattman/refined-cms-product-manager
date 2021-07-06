<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;
use RefinedDigital\ProductManager\Module\Models\Discount;

class DiscountRepository extends CoreRepository
{

    public function __construct()
    {
        $this->setModel('RefinedDigital\ProductManager\Module\Models\Discount');
    }

    public function findByCode($coupon)
    {
        return Discount::whereCode($coupon)->first();
    }
}
