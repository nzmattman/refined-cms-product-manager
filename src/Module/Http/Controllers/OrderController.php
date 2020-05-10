<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\ProductRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\ProductRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class OrderController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\Product';
    protected $prefix = 'productManager::products';
    protected $route = 'products';
    protected $heading = 'Products';
    protected $button = 'a Product';

    protected $productRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->productRepository = new ProductRepository();
        $this->productRepository->setModel($this->model);

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => '#', 'field' => 'id', 'sortable' => true, 'classes' => ['data-table__cell--id']],
            (object) [ 'name' => 'Name', 'field' => 'name', 'sortable' => true],
            (object) [ 'name' => 'Categories', 'field' => 'categories', 'type' => 'tags', 'setType' => 'product_categories', 'sortable' => false],
            (object) [ 'name' => 'Active', 'field' => 'active', 'type'=> 'select', 'options' => [1 => 'Yes', 0 => 'No'], 'sortable' => true, 'classes' => ['data-table__cell--active']],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.products.edit',
            'destroy'   => 'refined.products.destroy'
        ];
        $table->sortable = true;

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
    public function store(ProductRequest $request)
    {
        $item = $this->productRepository->store($request);

        $this->productRepository->syncRelated($item->id, $request->get('related_products'));

        $route = $this->getReturnRoute($item->id, $request->get('action'));

        return redirect($route)->with('status', 'Successfully created');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $this->productRepository->update($id, $request);

        $this->productRepository->syncRelated($id, $request->get('related_products'));

        $route = $this->getReturnRoute($id, $request->get('action'));

        return redirect($route)->with('status', 'Successfully updated');
    }


    public function getForFront(Request $request)
    {
        $data = $this->productRepository->getForFront($request->get('perPage'));
        return parent::formatGetForFront($data, $request);
    }

}
