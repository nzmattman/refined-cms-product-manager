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
        'notes'
    ];

    protected $casts = [
        'available_days' => 'object'
    ];

    protected $appends = [
        'available',
        'notes_as_html'
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
                        ]
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
        ]
    ];

    public function getNotesAsHtmlAttribute()
    {
        return nl2br($this->attributes['notes']);
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
