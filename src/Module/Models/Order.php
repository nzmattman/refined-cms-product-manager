<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use Spatie\EloquentSortable\Sortable;

class Order extends CoreModel
{
    use SoftDeletes;

    protected $order = [ 'column' => 'id', 'direction' => 'desc'];

    protected $fillable = [
        'order_status_id',
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
        'data',
    ];

    protected $casts = [
        'data' => 'object',
    ];

    protected $appends = [
        'full_name',
        'full_address',
        'name', // using order number
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
                            'name' => 'Billing Details',
                            'fields' => [
                                [
                                    [ 'label' => 'Customer Details', 'name' => 'customer_details', 'type' => 'order-billing-details', 'hideLabel' => true ],
                                ],
                            ]
                        ],
                        [
                            'name' => 'Order Details',
                            'fields' => [
                                [
                                    [ 'label' => 'Order Details', 'name' => 'order_details', 'type' => 'order-details', 'hideLabel' => true ],
                                ],
                            ]
                        ]
                    ]
                ],
                'right' => [
                    'blocks' => [
                        [
                            'name' => 'Order Status',
                            'fields' => [
                                [
                                    [ 'label' => 'Order Status', 'name' => 'order_status_id', 'hideLabel' => true, 'type' => 'select', 'options' => [] ],
                                ],
                            ]
                        ],
                    ]
                ]
            ]
        ],
    ];


    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getFullAddressAttribute()
    {
        $address = $this->address();

        return implode('<br/>', $address);
    }

    public function getFullAddressInlineAttribute()
    {
        $address = $this->address();

        return implode(',', $address);
    }

    public function getNameAttribute()
    {
        return str_pad($this->id, 4, 0, STR_PAD_LEFT);
    }

    private function address()
    {
        $address = [];
        if ($this->address) {
            $address[] = $this->address;
        }
        if ($this->address_2) {
            $address[] = $this->address_2;
        }
        if ($this->suburb) {
            $address[] = $this->suburb;
        }
        if ($this->state) {
            $address[] = $this->state;
        }
        if ($this->postcode) {
            $address[] = $this->postcode;
        }

        return $address;
    }

	public function setFormFields()
    {
        $fields = $this->formFields;
        $fields[0]['sections']['right']['blocks'][0]['fields'][0][0]['options'] = orders()->getStatuses();

        return $fields;
    }

}
