<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\OrderRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\OrderRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;
use RefinedDigital\ProductManager\Module\Events\OrderStatusUpdatedEvent;

class OrderController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\Order';
    protected $prefix = 'products::orders';
    protected $route = 'orders';
    protected $heading = 'Orders';
    protected $button = '';

    protected $orderRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->orderRepository = new OrderRepository();

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => '#', 'field' => 'id', 'sortable' => true, 'type' => 'orderId', 'classes' => ['data-table__cell--id']],
            (object) [ 'name' => 'Name', 'field' => 'full_name', ],
            (object) [ 'name' => 'Date', 'field' => 'created_at', 'type'=> 'orderDate', ],
            (object) [ 'name' => 'Status', 'field' => 'order_status_id', 'type'=> 'orderStatus', ],
            (object) [ 'name' => 'Method', 'field' => 'delivery_zone_id', 'type'=> 'deliveryZone', ],
            (object) [ 'name' => 'Total', 'field' => 'total', 'type'=> 'price', ],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.orders.edit',
            'destroy'   => 'refined.orders.destroy'
        ];
        $table->sortable = false;

        $this->setCanDelete(false);
        $this->setCanCreate(false);

        $this->table = $table;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($item)
    {
        $buttons = $this->getButtons();
        if (sizeof($buttons)) {
            foreach ($buttons as $index => $button) {
                if ($button->name === 'Save & New') {
                    unset($buttons[$index]);
                }
            }

            $this->setButtons($buttons);
        }
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
    public function store(DeliveryRequest $request)
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
    public function update(OrderRequest $request, $id)
    {
        $repo = new CoreRepository();
        $repo->setModel($this->model);
        $order = $repo->update($id, $request);

        // send the notifications
        event(new OrderStatusUpdatedEvent($order, $request->get('order_status_id')));

        $route = $this->getReturnRoute($order->id, $request->get('action'));

        return redirect($route)->with('status', 'Successfully updated');
    }

}
