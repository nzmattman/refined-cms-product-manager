<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\ProductStatusRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\ProductStatusRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class ProductStatusController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\ProductStatus';
    protected $prefix = 'products::productStatus';
    protected $route = 'product-statuses';
    protected $heading = 'Product Status ';
    protected $button = 'a Status';

    protected $productStatusRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->productStatusRepository = new ProductStatusRepository();

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => 'Name', 'field' => 'name', 'sortable' => true],
            (object) [ 'name' => 'Active', 'field' => 'active', 'type'=> 'select', 'options' => [1 => 'Yes', 0 => 'No'], 'sortable' => true, 'classes' => ['data-table__cell--active']],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.product-statuses.edit',
            'destroy'   => 'refined.product-statuses.destroy'
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
    public function store(ProductStatusRequest $request)
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
    public function update(ProductStatusRequest $request, $id)
    {
        return parent::updateRecord($request, $id);
    }
}
