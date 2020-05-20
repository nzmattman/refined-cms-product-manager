<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\DiscountRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\DiscountRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class DiscountController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\Discount';
    protected $prefix = 'products::discount';
    protected $route = 'discounts';
    protected $heading = 'Discounts';
    protected $button = 'a Discount';

    protected $discountRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->discountRepository = new DiscountRepository();

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => 'Name', 'field' => 'name', 'sortable' => true],
            (object) [ 'name' => 'Price', 'field' => 'price', 'type' => 'price', 'sortable' => true],
            (object) [ 'name' => 'Percent', 'field' => 'percent', 'sortable' => true],
            (object) [ 'name' => 'Code', 'field' => 'code', 'sortable' => true],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.discounts.edit',
            'destroy'   => 'refined.discounts.destroy'
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
    public function store(DiscountRequest $request)
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
    public function update(DiscountRequest $request, $id)
    {
        return parent::updateRecord($request, $id);
    }
}
