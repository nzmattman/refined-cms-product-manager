<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderRepository;

class OrderStatus extends CoreModel
{
    use SoftDeletes;

    protected $order = [ 'column' => 'id', 'direction' => 'asc'];

    public $sortable = [
        'order_column_name' => 'id',
        'sort_when_creating' => false,
    ];

    protected $fillable = [
        'name',
        'active',
        'send_sms',
        'send_email',
        'position',
        'email_subject',
        'email_content',
        'sms_content',
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
                    [ 'label' => 'Name', 'name' => 'name', 'type' => 'readonly'],
                    [ 'label' => 'Send Email', 'name' => 'send_email', 'required' => true, 'type' => 'select', 'options' => [1 => 'Yes', 0 => 'No'] ],
                ],
                [
                    [ 'label' => 'Email Subject', 'name' => 'email_subject', 'required' => true, ],
                ],
                [
                    [ 'label' => 'Email Content', 'name' => 'email_content', 'required' => true, 'type' => 'richtext' ],
                ],
            ]
        ]
    ];

	public function setFormFields()
    {
        $config = config('products.orders.sms');
        $fields = $this->formFields;

        $smsContent = [
            ['count' => 2],
            [ 'label' => 'SMS Content', 'name' => 'sms_content', 'required' => false, 'type' => 'textarea' ],
        ];
        $smsToggle = [ 'label' => 'Send SMS', 'name' => 'send_sms', 'required' => false, 'type' => 'select', 'options' => [0 => 'No', 1 => 'Yes'] ];
        if ($config['active']) {
            $fields[0]['fields'][0][] = $smsToggle;
            $fields[0]['fields'][] = $smsContent;
        }

        return $fields;
    }
}
