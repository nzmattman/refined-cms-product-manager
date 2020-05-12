<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\DeliveryRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\DeliveryRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class DeliveryController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\DeliveryZone';
    protected $prefix = 'products::delivery';
    protected $route = 'delivery-zones';
    protected $heading = 'Delivery Zones';
    protected $button = 'a Zone';

    protected $deliveryRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->deliveryRepository = new DeliveryRepository();

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => '#', 'field' => 'id', 'sortable' => true, 'classes' => ['data-table__cell--id']],
            (object) [ 'name' => 'Name', 'field' => 'name', 'sortable' => true],
            (object) [ 'name' => 'Price', 'field' => 'price', 'type' => 'price', 'sortable' => true],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.delivery-zones.edit',
            'destroy'   => 'refined.delivery-zones.destroy'
        ];
        $table->sortable = false;

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
    public function update(DeliveryRequest $request, $id)
    {
        return parent::updateRecord($request, $id);
    }
}
