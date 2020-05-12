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

    public function getVariationOptions($product)
    {
        $lookup = $this->getTypeLookup($product);
        $typeLookup = $lookup->typeLookup;
        $typeNameLookup = $lookup->typeNameLookup;

        $initialKey = key($typeLookup);

        $notAType = ['price','sale_price'];
        $dotArray = [];

        $rows = $product->variations->items;
        if ($initialKey && sizeof($rows)) {
            foreach ($rows as $row) {
                $key = '';
                foreach ($row as $type => $value) {
                    if (!in_array($type, $notAType)) {
                        $key .= '.' .str_slug($typeLookup[$type]->name);
                        $key .= '.'.str_slug($typeLookup[$type]->options[$value->content]);
                    }
                }

                $item = new \stdClass();
                $item->price = $row['price']->content;
                $item->sale_price = $row['sale_price']->content;
                $dotArray[substr($key, 1)] = $item;
            }

            $data = [];
            foreach ($dotArray as $key => $value) {
              array_set($data, $key, $value);
            }

            return $this->formatRow($data, $typeNameLookup);
        }

        return [];
    }

    public function getVariationTypeValues($productId)
    {
        $data = \DB::table('product_product_variation_type_value')
            ->select('product_variation_type_value_ids AS key', 'price', 'sale_price')
            ->whereProductId($productId)
            ->orderBy('id')
            ->get();

        $values = [];
        if ($data->count()) {
            foreach ($data as $d) {
                $prices = new \stdClass();
                $prices->price = $d->price;
                $prices->sale_price = $d->sale_price;
                $values[$d->key] = $prices;
            }
        }

        return $values;
    }

    private function formatRow($array, $typeNameLookup)
    {
        foreach ($array as $key => $values) {
            if (isset($typeNameLookup[$key])) {
                $type = $typeNameLookup[$key];
                $item = new \stdClass();
                $item->name = $type->name;
                $item->id = $type->id;
                $item->options = $this->formatChildRow($values, $type, $typeNameLookup);

                $array[] = $item;
                unset($array[$key]);
            }
        }

        return $array;
    }

    private function formatChildRow($array, $type, $typeNameLookup)
    {
        // help()->trace($array);
        foreach ($array as $key => $values) {
            if (isset($type->optionLookup[$key])) {
                $option = $type->optionLookup[$key];
                $item = new \stdClass();
                $item->name = $option->name;
                $item->id = $option->id;
                if (isset($values->price)) {
                    $item->options = [];
                    $item->price = $values->price;
                    $item->sale_price = $values->sale_price;
                } else {
                    $item->options = $this->formatRow($values, $typeNameLookup);
                }

                $array[] = $item;
                unset($array[$key]);
            }
        }

        return $array;
    }



    public function findVariationsByKeys($product, $variationKeys)
    {
        $keys = explode(',', $variationKeys);
        $lookup = $this->getTypeLookup($product);
        $typeLookup = $lookup->typeLookup;
        $variations = [];

        foreach ($typeLookup as $type => $options) {
            $key = array_shift($keys);
            $variation = new \stdClass();
            $variation->id = $options->id;
            $variation->name = $options->name;
            $variation->value = isset($options->options[$key]) ? $options->options[$key] : null;
            $variation->value_id = $key;
            $variations[] = $variation;
        }

        return $variations;
    }

    private function getTypeLookup($product)
    {
        $typeLookup = [];
        $typeNameLookup = [];

        if ($product->variation_types->count()) {
            foreach ($product->variation_types as $type) {
                $name = $type->display_name ?: $type->name;
                $option = new \stdClass();
                $option->name = $name;
                $option->id = $type->id;
                $option->options = [];
                $option->optionLookup = [];
                foreach($type->values as $v) {
                    $option->options[$v->id] = $v->name;
                    $item = new \stdClass();
                    $item->name = $v->name;
                    $item->id = $v->id;
                    $option->optionLookup[str_slug($v->name)] = $item;
                }
                $typeLookup['type_'.$type->id] = $option;
                $typeNameLookup[str_slug($name)] = $option;
            }
        }

        $lookup = new \stdClass();
        $lookup->typeLookup = $typeLookup;
        $lookup->typeNameLookup = $typeNameLookup;

        return $lookup;
    }
}
