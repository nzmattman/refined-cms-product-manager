<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\OrderNotificationRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderNotificationRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class OrderNotificationController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\OrderStatus';
    protected $prefix = 'products::order-notification';
    protected $route = 'order-notifications';
    protected $heading = 'Order Notifications';
    protected $button = '';

    protected $orderNotificationRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->orderNotificationRepository = new OrderNotificationRepository();

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => 'Name', 'field' => 'name', ],
            (object) [ 'name' => 'Send Email', 'field' => 'send_email', 'type'=> 'select', 'options' => [1 => 'Yes', 0 => 'No'], 'classes' => ['data-table__cell--right']],
        ];

        if (config('products.orders.sms.active')) {
            $table->fields[] = (object) [ 'name' => 'Send SMS', 'field' => 'send_sms', 'type'=> 'select', 'options' => [1 => 'Yes', 0 => 'No'], 'classes' => ['data-table__cell--right']];
        }

        $table->routes = (object) [
            'edit'      => 'refined.order-notifications.edit',
            'destroy'   => 'refined.order-notifications.destroy'
        ];
        $table->sortable = false;

        $this->table = $table;

        $this->setCanCreate(false);
        $this->setCanDelete(false);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($item)
    {
        // get the instance
        $data = $this->model::findOrFail($item);

        return parent::edit($data);
    }

    /**
     * Store the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(OrderNotificationRequest $request)
    {
        return parent::storeRecord($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderNotificationRequest $request, $id)
    {
        return parent::updateRecord($request, $id);
    }
}
