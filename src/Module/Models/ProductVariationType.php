<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class ProductVariationType extends CoreModel implements Sortable
{
    use SoftDeletes;

    protected $order = [ 'column' => 'id', 'direction' => 'asc'];

    public $sortable = [
        'order_column_name' => 'id',
        'sort_when_creating' => false,
    ];

    protected $fillable = [
        'name',
        'display_name',
    ];

    protected $with = [
        'values'
    ];

    protected $appends = [
        'variations',
        'select_name',
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
                    [ 'label' => 'Name', 'name' => 'name', 'required' => true],
                    [ 'label' => 'Display Name', 'name' => 'display_name'],
                ],
                [
                    [ 'label' => 'Variations', 'name' => 'variations', 'type' => 'repeatable', 'required' => true, 'fields' =>
                        [
                            [ 'name' => 'Name', 'page_content_type_id' => 3, 'field' => 'name', 'hide_label' => false,],
                        ]
                    ],
                ],
            ]
        ]
    ];

    public function values()
    {
        return $this->hasMany(ProductVariationTypeValue::class)->orderBy('position', 'asc');
    }

    public function getVariationsAttribute()
    {
        $variations = collect([]);
        if ($this->values && $this->values->count()) {
            $fields = $this->formFields[0]['fields'][1][0]['fields'];
            foreach ($this->values as $value) {
                $items = new \stdClass();
                foreach ($fields as $field) {
                    $item = (object) $field;
                    $item->content = $value->{$field['field']};
                    $items->{$field['field']} = $item;
                }

                $item = new \stdClass();
                $item->name = 'Id';
                $item->page_content_type_id = 3;
                $item->field = 'id';
                $item->hide_field = true;
                $item->content = $value->id;
                $items->id = $item;

                $variations->push($items);
            }
        }

        return $variations;
    }

    public function getSelectNameAttribute()
    {
        $name = $this->name;
        if ($this->display_name) {
            $name .= ' ('.$this->display_name.')';
        }

        return $name;
    }
}
