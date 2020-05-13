<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class Order extends CoreModel implements Sortable
{
    use SoftDeletes;

    protected $fillable = [
        'paid_at',
        'payment_method',
        'first_name',
        'last_name',
        'company_name',
        'address',
        'address_2',
        'suburb',
        'state',
        'postcode',
        'phone',
        'email',
        'additional_information',
        'delivery_zone_id',
        'gst_active',
        'gst_method',
        'discount',
        'sub_total',
        'delivery',
        'gst',
        'total',
    ];
}
