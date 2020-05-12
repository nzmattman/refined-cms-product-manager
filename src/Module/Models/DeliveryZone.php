<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class DeliveryZone extends CoreModel implements Sortable
{
    use SoftDeletes;

    protected $order = [ 'column' => 'position', 'direction' => 'asc'];

    protected $fillable = [
        'active',
        'position',
        'name',
        'price',
        'postcodes',
    ];

    /**
     * The fields to be displayed for creating / editing
     *
     * @var array
     */
    public $formFields = [
        [
            'name' => 'Content',
            'fields' => [
                [
                    [ 'label' => 'Active', 'name' => 'active', 'required' => true, 'type' => 'select', 'options' => [1 => 'Yes', 0 => 'No'] ],
                ],
                [
                    [ 'label' => 'Name', 'name' => 'name', 'required' => true],
                    [ 'label' => 'Price', 'name' => 'price', 'required' => true, 'type' => 'price'],
                ],
                [
                    [ 'label' => 'Postcodes', 'name' => 'postcodes', 'required' => false, 'type' => 'textarea', 'note' => 'Comma separated values'],
                ],
            ]
        ]
    ];
}
