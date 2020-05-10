<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;
use RefinedDigital\ProductManager\Module\Models\ProductVariationTypeValue;

class VariationRepository extends CoreRepository
{

    public function __construct()
    {
        $this->setModel('RefinedDigital\ProductManager\Module\Models\ProductVariationType');
    }

    public function syncVariations($type, $variations)
    {
        // format the variations correctly
        if ($variations) {
            $variations = json_decode($variations);
            $toInsert = [];
            $toUpdate = [];
            $toCheckForDelete = [];
            if (sizeof($variations)) {
                foreach ($variations as $variation) {
                    $name = trim($variation->name->content);
                    if ($name) {
                        $item = [
                            'name' => $name
                        ];
                        if (isset($variation->id)) {
                            $toUpdate[$variation->id->content] = $item;
                            $toCheckForDelete[] = $variation->id->content;
                        } else {
                            $toInsert[] = $item;
                        }
                    }
                }
            }

            if (sizeof($toInsert)) {
                foreach($toInsert as $insert) {
                    $vType = new ProductVariationTypeValue();
                    $vType->name = $insert['name'];
                    $vType->product_variation_type_id = $type;
                    $vType->save();
                    $toCheckForDelete[] = $vType->id;
                }
            }

            if (sizeof($toUpdate)) {
                foreach ($toUpdate as $id => $update) {
                    $vType = ProductVariationTypeValue::find($id);
                    $vType->name = $update['name'];
                    $vType->save();
                }
            }

            $vTypes = ProductVariationTypeValue::whereProductVariationTypeId($type)->get();
            $vTypeIds = $vTypes->pluck('id');
            $toDelete = [];
            if ($vTypeIds->count()) {
                foreach ($vTypeIds as $id) {
                    if (!in_array($id, $toCheckForDelete)) {
                        $toDelete[] = $id;
                    }
                }

                if (sizeof($toDelete)) {
                    ProductVariationTypeValue::whereIn('id', $toDelete)->delete();
                }
            }
        }
    }


    public function getForFront()
    {
        return $this->model::order()->get();
    }

    public function getForSelect()
    {
        $items = $this->model::orderBy('name', 'asc')->get();
        $data = [];
        if ($items->count()) {
            foreach ($items as $item) {
                $name = $item->name;
                if ($item->display_name) {
                    $name .= ' ('.$item->display_name.')';
                }
                $data[] = [
                    'id' => $item->id,
                    'name' => $name,
                ];
            }
        }

        return $data;
    }
}
