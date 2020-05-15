<?php

namespace RefinedDigital\ProductManager\Module\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use RefinedDigital\CMS\Modules\Core\Models\CoreModel;
use RefinedDigital\CMS\Modules\Core\Traits\IsArticle;
use RefinedDigital\CMS\Modules\Pages\Traits\IsPage;
use RefinedDigital\CMS\Modules\Tags\Traits\Taggable;
use RefinedDigital\ProductManager\Module\Http\Repositories\ProductRepository;
use RefinedDigital\ProductManager\Module\Http\Repositories\VariationRepository;
use Spatie\EloquentSortable\Sortable;

class Product extends CoreModel implements Sortable
{
    use SoftDeletes, IsPage, IsArticle, Taggable;

    protected $order = [ 'column' => 'position', 'direction' => 'asc'];

    protected $fillable = [
        'active',
        'featured_product',
        'new',
        'position',
        'name',
        'code',
        'image',
        'images',
        'file',
        'files',
        'content',
        'price',
        'sale_price',
        'hide_from_menu',
        'for_sale',
        'product_status_id',
    ];

    protected $casts = [
        'images' => 'object',
        'files' => 'object',
    ];

    protected $with = [
        'related_products',
        'variation_types',
    ];

    protected $appends = [
        'variations',
        'excerpt',
        'variation_options',
        'variation_type_values',
    ];

    /**
     * The fields to be displayed for creating / editing
     *
     * @var array
     */
    public $formFields = [
        [
            'name' => 'Content',
            'sections' => [
                'left' => [
                    'blocks' => [
                        [
                            'name' => 'Content',
                            'fields' => [
                                [
                                    [ 'label' => 'Active', 'name' => 'active', 'required' => true, 'type' => 'select', 'options' => [1 => 'Yes', 0 => 'No'] ],
                                    [ 'label' => 'New', 'name' => 'new', 'required' => true, 'type' => 'select', 'options' => [0 => 'No', 1 => 'Yes'] ],
                                    [ 'label' => 'Featured', 'name' => 'featured_product', 'required' => true, 'type' => 'select', 'options' => [0 => 'No', 1 => 'Yes'] ],
                                ],
                                [
                                    [ 'label' => 'For Sale', 'name' => 'for_sale', 'required' => true, 'type' => 'select', 'options' => [1 => 'Yes', 0 => 'No'] ],
                                    [ 'label' => 'Hide on Product Page', 'name' => 'hide_from_menu', 'required' => true, 'type' => 'select', 'options' => [0 => 'No', 1 => 'Yes'] ],
                                    [ 'label' => 'Status', 'name' => 'product_status_id', 'required' => true, 'type' => 'select', 'options' => [] ],
                                ],
                                [
                                    [ 'label' => 'Name', 'name' => 'name', 'required' => true, 'attrs' => ['v-model' => 'content.name', '@keyup' => 'updateSlug' ] ],
                                    [ 'label' => 'Code', 'name' => 'code', 'required' => false ],
                                ],
                                [
                                    [ 'label' => 'Price', 'name' => 'price', 'required' => false, 'type' => 'price'],
                                    [ 'label' => 'Sale Price', 'name' => 'sale_price', 'required' => false, 'type' => 'price'],
                                ],
                                [
                                    [ 'label' => 'Content', 'name' => 'content', 'required' => true, 'type' => 'richtext' ],
                                ],
                            ]
                        ]
                    ]
                ],
                'right' => [
                    'blocks' => [
                        [
                            'fields' => [
                                [
                                    [ 'label' => 'Image', 'name' => 'image', 'required' => true, 'type' => 'image', 'imageNote' => 'Images here will be resized to fit within <strong>800px wide x 800px tall</strong>' ],
                                    [ 'label' => 'File', 'name' => 'file', 'required' => false, 'type' => 'file' ],
                                ],
                            ]
                        ],
                        [
                            'name' => 'Categories',
                            'fields' => [
                                [
                                    [ 'label' => 'Categories', 'name' => 'product_categories', 'type' => 'tags', 'hideLabel' => true, 'tagType'=> 'product_categories'],
                                ]
                            ]
                        ],
                        [
                            'name' => 'Related Products',
                            'fields' => [
                                [
                                    [ 'label' => 'Related Products', 'name' => 'related_products', 'type' => 'related-products', 'hideLabel' => true, ],
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ],
        [
            'name' => 'Images',
            'fields' => [
                [
                    [ 'label' => 'Images', 'name' => 'images', 'type' => 'repeatable', 'required' => false, 'hideLabel' => true, 'fields' =>
                        [
                            [ 'name' => 'Image', 'page_content_type_id' => 4, 'field' => 'image', 'hide_label' => false, 'note' => 'Images here will be resized to fit within <strong>800px wide x 800px tall</strong>'],
                        ]
                    ],
                ],
            ]
        ],
        [
            'name' => 'Files',
            'fields' => [
                [
                    [ 'label' => 'Files', 'name' => 'files', 'type' => 'repeatable', 'required' => false, 'hideLabel' => true, 'fields' =>
                        [
                            [ 'name' => 'File', 'page_content_type_id' => 5, 'field' => 'file'],
                            [ 'name' => 'Title', 'page_content_type_id' => 3, 'field' => 'file_title'],
                        ]
                    ],
                ],
            ]
        ],
        [
            'name' => 'Variations',
            'fields' => [
                [
                    // todo: custom form types should be complied in automatically with vue
                    [ 'label' => 'Variations', 'name' => 'variations', 'type' => 'variations', 'required' => false, 'hideLabel' => true]
                ],
            ]
        ],
    ];

    public function getExcerptAttribute()
    {
        $content = strip_tags($this->content);

        $excerpt = substr($content, 0, 200);
        if (strlen($content) > $excerpt) {
            $excerpt .= '...';
        }

        return $excerpt;

    }

    public function getVariationsAttribute()
    {
        $repo = new ProductRepository();

        $data = new \stdClass();
        $data->variationTypes = $this->variation_types->toArray();
        $data->items = $repo->getItemsForRepeatable($this);

        return $data;
    }

    public function getVariationOptionsAttribute()
    {
        $repo = new VariationRepository();

        return $repo->getVariationOptions($this);
    }

    public function getVariationTypeValuesAttribute()
    {
        $repo = new VariationRepository();

        return $repo->getVariationTypeValues($this->id);
    }

    public function related_products()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function variation_types()
    {
        return $this->belongsToMany(ProductVariationType::class);
    }

	public function setFormFields()
    {
        $fields = $this->formFields;
        $fields[0]['sections']['left']['blocks'][0]['fields'][1][2]['options'] = products()->getStatuses();

        $config = config('products');
        if (!$config['variations']['active']) {
            unset($fields[3]);
        }

        return $fields;
    }
}
