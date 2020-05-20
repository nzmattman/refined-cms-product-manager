<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class Discount extends CoreModel implements Sortable
{
    use SoftDeletes;

    protected $order = [ 'column' => 'position', 'direction' => 'asc'];

    protected $fillable = [
        'active',
        'name',
        'code',
        'user_group_id',
        'price',
        'percent',
        'position',
        'type_id'
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
                    [ 'count' => 3 ],
                    [ 'label' => 'Name', 'name' => 'name', 'required' => true],
                    [ 'label' => 'Discount Type', 'name' => 'type_id', 'required' => true, 'type' => 'select', 'options' => [], 'v-model' => 'form.typeId' ],
                    [ 'label' => 'Code', 'name' => 'code', 'required' => true, 'row' => ['attrs' => ['v-if' => "form.typeId == '1'"]]],
                    [ 'label' => 'User Group', 'name' => 'user_group_id', 'required' => true, 'row' => ['attrs' => ['v-if' => "form.typeId == '2'"]], 'type' => 'select', 'options' => [] ],
                ],
                [
                    [ 'count' => 3 ],
                    [ 'label' => 'Price', 'name' => 'price', 'required' => true, 'type' => 'price'],
                    [ 'label' => 'Percent', 'name' => 'percent', 'required' => true, 'type' => 'number'],
                ],
            ]
        ]
    ];


	public function setFormFields()
    {
        $fields = $this->formFields;
        $fields[0]['fields'][1][2]['options'] = config('discounts.types');
        $userGroups = users()->getUserGroupsForSelect();
        $fields[0]['fields'][1][4]['options'] = $userGroups;

        return $fields;
    }
}
