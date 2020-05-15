<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class ProductVariationTypeValue extends CoreModel implements Sortable
{
    use SoftDeletes;

    protected $order = [ 'column' => 'position', 'direction' => 'asc'];

    protected $fillable = [
        'name',
        'product_variation_type_id',
        'product_status_id'
    ];

    public function type()
    {
        return $this->hasOne(ProductVariationType::class);
    }
}
