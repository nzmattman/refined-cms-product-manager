<?php

namespace RefinedDigital\ProductManager\Module\Http\Controllers;

use Illuminate\Http\Request;
use RefinedDigital\CMS\Modules\Core\Http\Controllers\CoreController;
use RefinedDigital\ProductManager\Module\Http\Requests\VariationRequest;
use RefinedDigital\ProductManager\Module\Http\Repositories\VariationRepository;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;

class VariationController extends CoreController
{
    protected $model = 'RefinedDigital\ProductManager\Module\Models\ProductVariationType';
    protected $prefix = 'productManager::variations';
    protected $route = 'product-variations';
    protected $heading = 'Variation Types';
    protected $button = 'a Variation Type';

    protected $variationRepository;

    public function __construct(CoreRepository $coreRepository)
    {
        $this->variationRepository = new VariationRepository();
        $this->variationRepository->setModel($this->model);

        parent::__construct($coreRepository);
    }

    public function setup() {

        $table = new \stdClass();
        $table->fields = [
            (object) [ 'name' => '#', 'field' => 'id', 'sortable' => true, 'classes' => ['data-table__cell--id']],
            (object) [ 'name' => 'Name', 'field' => 'name', 'sortable' => true],
            (object) [ 'name' => 'Display Name', 'field' => 'display_name', 'sortable' => true],
        ];
        $table->routes = (object) [
            'edit'      => 'refined.product-variations.edit',
            'destroy'   => 'refined.product-variations.destroy'
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
    public function store(VariationRequest $request)
    {
        $item = $this->variationRepository->store($request);

        $this->variationRepository->syncVariations($item->id, $request->get('variations'));

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
    public function update(VariationRequest $request, $id)
    {
        $this->variationRepository->update($id, $request);

        $this->variationRepository->syncVariations($id, $request->get('variations'));

        $route = $this->getReturnRoute($id, $request->get('action'));

        return redirect($route)->with('status', 'Successfully updated');
    }


    public function getForFront(Request $request)
    {
        $data = $this->variationRepository->getForFront($request->get('perPage'));
        return parent::formatGetForFront($data, $request);
    }

}
