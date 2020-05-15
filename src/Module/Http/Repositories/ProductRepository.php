<?php

namespace RefinedDigital\ProductManager\Module\Http\Repositories;

use RefinedDigital\ProductManager\Module\Models\DeliveryZone;
use RefinedDigital\ProductManager\Module\Models\Product;
use RefinedDigital\CMS\Modules\Core\Http\Repositories\CoreRepository;
use RefinedDigital\ProductManager\Module\Models\ProductStatus;
use RefinedDigital\ProductManager\Module\Models\ProductVariation;

class ProductRepository extends CoreRepository
{

    public function __construct()
    {
        $this->setModel('RefinedDigital\ProductManager\Module\Models\Product');
    }

    public function getForFront($perPage = 5)
    {
        return $this->model::whereActive(1)
            ->search(['name','content'])
            ->paging($perPage);
    }

    public function getAllForFront()
    {
        return $this->model::whereActive(1)->get();
    }

    public function getForHomePage($limit = 6)
    {
        return $this->model::whereActive(1)
            ->limit($limit)
            ->get();
    }

    public function getForSelect()
    {
        $posts = $this->model::active()->orderBy('name', 'asc')->get();
        $data = [];
        if ($posts->count()) {
            foreach ($posts as $post) {
                $data[] = [
                    'id' => $post->id,
                    'name' => $post->name,
                ];
            }
        }

        return $data;
    }

    public function syncRelated($productId, $relatedProducts = '')
    {
        // first delete all relations
        \DB::table('related_products')
            ->whereProductId($productId)
            ->delete();

        $products = array_filter(explode(',', trim($relatedProducts)));

        // now add in the products
        if (is_array($products) && sizeof($products)) {
            foreach ($products as $product) {
                \DB::table('related_products')
                    ->insert([
                        'product_id' => $productId,
                        'related_product_id' => $product
                    ]);
            }
        }
    }

    public function syncVariations($productId, $productVariations = '')
    {
        $variations = json_decode($productVariations);

        $types = array_map(function($type) use($productId) {
            return [
                'product_id' => $productId,
                'product_variation_type_id' => $type->id
            ];
        }, $variations->variationTypes);

        // first delete all relations
        \DB::table('product_product_variation_type')
            ->whereProductId($productId)
            ->delete();
        \DB::table('product_product_variation_type_value')
            ->whereProductId($productId)
            ->delete();

        $notType = ['price','sale_price', 'product_status_id'];
        $values = array_map(function($item) use ($productId, $notType) {
            $value = [
                'product_id' => $productId,
                'product_variation_type_value_ids' => null,
            ];

            $valueIds = [];
            foreach($item as $key => $values) {
                if (in_array($key, $notType)) {
                    if ($values->content) {
                        $value[$key] = $values->content;
                    }
                } else {
                    $valueIds[] = $values->content;
                }
            }

            $value['product_variation_type_value_ids'] = implode(',', $valueIds);

            return $value;
        }, $variations->items);

        if (sizeof($types)) {
            foreach ($types as $type) {
                \DB::table('product_product_variation_type')->insert($type);
            }
        }

        if (sizeof($values)) {
            foreach ($values as $value) {
                \DB::table('product_product_variation_type_value')->insert($value);
            }
        }
    }

    public function getVariationsAsSelect($product)
    {
        $options = [
            null => 'Please Select'
        ];

        if (isset($product->variations) && sizeof($product->variations)) {
            foreach ($product->variations as $index => $variation) {
                $options[$index] = $variation->name->content;
            }
        }

        return html()
            ->select('variation', $options)
            ->attribute('class', 'add-to-cart__control')
            ->attribute('required', 'required')
        ;
    }

    public function getItemsForRepeatable($product)
    {
        $variationTypeValues = $this->getVariationTypeValues($product->id);

        $rows = [];
        if ($variationTypeValues->count()) {
            foreach ($variationTypeValues as $typeValue) {
                $fields = [];

                $valueIds = explode(',', $typeValue->product_variation_type_value_ids);
                $price = $this->newField('Price', 'price', $typeValue->price, 8);
                $salePrice = $this->newField('Sale Price', 'sale_price', $typeValue->sale_price, 8);
                $status = $this->newField(
                    'Status',
                    'product_status_id',
                    isset($typeValue->product_status_id) ? $typeValue->product_status_id : null,
                    6,
                    products()->getStatusesForView()
                );

                if ($product->variation_types->count()) {
                    foreach ($product->variation_types as $type) {
                        $key = 'type_'.$type->id;
                        $field = $this->newField($type->select_name, $key, null, 6);
                        // todo: should this auto be done on the model?
                        $field->options = $type->values->map(function($value) {
                            $option = new \stdClass();
                            $option->value = $value->id;
                            $option->label = $value->name;
                            return $option;
                        });
                        $optionIds = $field->options->pluck('value');
                        foreach ($valueIds as $id) {
                            if ($optionIds->contains($id)) {
                                $field->content = $id;
                            }
                        }
                        $fields[$key] = $field;
                    }
                }

                $fields['price'] = $price;
                $fields['sale_price'] = $salePrice;
                $fields['product_status_id'] = $status;

                $rows[] = $fields;
            }
        }

        return $rows;
    }

    private function newField ($name, $fieldName, $content, $type, $options = [])
    {
        $field = new \stdClass();
        $field->content = $content;
        $field->field = $fieldName;
        $field->name = $name;
        $field->page_content_type_id = $type;

        if ($type === 6) {
            $field->options = $options;
        }

        return $field;
    }

    private function getVariationTypeValues($productId)
    {
        return \DB::table('product_product_variation_type_value')
            ->whereProductId($productId)
            ->orderBy('id')
            ->get();
    }

    public function getDeliveryZones()
    {
        return DeliveryZone::whereActive(1)
            ->orderBy('position')
            ->get();
    }

    public function getStatuses()
    {
        $statuses = [];
        $data = ProductStatus::orderBy('id', 'asc')->get();
        if ($data->count()) {
            foreach ($data as $d) {
                $statuses[$d->id] = $d->name;
            }
        }

        return $statuses;

    }

    public function getStatusesForView()
    {
        $statuses = [];
        $data = ProductStatus::orderBy('id', 'asc')->get();
        if ($data->count()) {
            foreach ($data as $d) {
                $statuses[] = [
                    'label' => $d->name,
                    'value' => $d->id
                ];
            }
        }

        return $statuses;

    }
}
