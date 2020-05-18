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
        'available_days',
        'notes',
        'conditions',
    ];

    protected $casts = [
        'available_days' => 'object',
        'conditions' => 'object',
    ];

    protected $appends = [
        'available',
        'delivery_conditions',
        'notes_as_html',
        'label',
    ];

    /**
     * The fields to be displayed for creating / editing
     *
     * @var array
     */
    public $formFields = [
        [
            'name' => 'Content',
            'sections' => [
                'left' => [
                    'blocks' => [
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
                        ],
                        [
                            'name' => 'Conditions',
                            'fields' => [
                                [
                                    [ 'label' => 'Conditions', 'name' => 'conditions', 'type' => 'repeatable', 'required' => false, 'hideLabel' => true, 'fields' =>
                                        [
                                            [ 'name' => 'Option', 'page_content_type_id' => 6, 'field' => 'option', 'options' =>
                                                [
                                                    [ 'value' => 'price', 'label' => 'Price'],
                                                    [ 'value' => 'quantity', 'label' => 'Quantity'],
                                                ]
                                            ],
                                            [ 'name' => 'Is', 'page_content_type_id' => 6, 'field' => 'is', 'options' =>
                                                [
                                                    ['value' => '>', 'label' => 'Greater than'],
                                                    ['value' => '<', 'label' => 'Less than'],
                                                    ['value' => '==', 'label' => 'Equal to'],
                                                ],
                                            ],
                                            [ 'name' => 'Value', 'page_content_type_id' => 3, 'field' => 'value'],
                                            [ 'name' => 'Price', 'page_content_type_id' => 3, 'field' => 'price'],
                                        ]
                                    ],
                                ]
                            ],
                        ],
                    ]
                ],
                'right' => [
                    'blocks' => [
                        [
                            'name' => 'Info',
                            'fields' => [
                                [
                                    [ 'label' => 'Available Days', 'name' => 'available_days', 'type' => 'days' ],
                                    [ 'label' => 'Notes', 'name' => 'notes', 'required' => false, 'type' => 'textarea'],
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ],
    ];

    public function getNotesAsHtmlAttribute()
    {
        return nl2br($this->attributes['notes']);
    }

    public function getLabelAttribute()
    {
        return $this->price;
    }

    public function getDeliveryConditionsAttribute()
    {
        $conditions = json_decode($this->conditions);
        if (is_array($conditions) && sizeof($conditions)) {
            $deliveryConditions = [];
            foreach ($conditions as $cond) {
                $data = new \stdClass();
                foreach ($cond as $key => $value) {
                    $content = $value->content;
                    if ($key === 'price' || $key === 'value') {
                        $content = (float)$value->content;
                    }
                    $data->{$key} = $content;
                }

                $deliveryConditions[] = $data;
            }
            return $deliveryConditions;
        }

        return [];
    }

    public function getAvailableAttribute()
    {
        $days = help()->getDaysOfWeek();

        $size = sizeof($this->available_days);
        $cut = 4;

        if ($size === sizeof($days) || $size < 1) {
            return null;
        }

        $string = '';

        $available = [];
        // if >= cut available show NOT days
        if ($size >= $cut) {
            foreach ($days as $key => $day) {
                if (!in_array($key, $this->available_days)) {
                    $available[] = $days[$key];
                }
            }

            $string = '<strong><em>NOT</em></strong> available on ';

        // if < cut show active days
        } else if ($size < $cut) {
            foreach ($this->available_days as $key) {
                $available[] = $days[$key];
            }

            $string = '<strong><em>ONLY</em></strong> Available on ';
        }

        $last = array_pop($available);
        $string .= implode(', ', $available);
        if (sizeof($available)) {
            $string .= ' and';
        }
        $string .= ' '.$last;

        return $string;
    }
}
