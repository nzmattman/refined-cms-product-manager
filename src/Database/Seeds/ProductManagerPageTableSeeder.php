<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use DB;
use RefinedDigital\CMS\Modules\Core\Models\Uri;
use RefinedDigital\CMS\Modules\Pages\Models\Page;
use RefinedDigital\CMS\Modules\Pages\Models\PageHolder;
use RefinedDigital\FormBuilder\Module\Models\Form;

class ProductManagerPageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $pageHolderId = PageHolder::insertGetId([
            'active' => 1,
            'position' => PageHolder::count() + 1,
            'name' => 'Cart',
        ]);

        $templates = DB::table('templates')
            ->whereIn('name', ['Cart','Checkout'])
            ->get();

        $templateLookup = [];
        if ($templates->count()) {
            foreach ($templates as $template) {
                $templateLookup[$template->name] = $template->id;
            }
        }

        $productForm = Form::whereName('Checkout')->first();

        $pages = [
            [
                'page_holder_id' => $pageHolderId,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Cart'
            ],
            [
                'page_holder_id' => $pageHolderId,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Checkout',
                'form_id' => $productForm->id ?? null
            ],
            [
                'page_holder_id' => $pageHolderId,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Thank You',
                'content' => '[{"name": "Content", "fields": [{"name": "Heading", "content": "Order Received"}, {"name": "Content", "content": "<p>Thank you. Your order has been received.</p>\n <p>[[order_details]]</p> \n <p>[[billing_details]]</p>"}]}]'
            ]
        ];

        $pageId = 0;

        foreach($pages as $pos => $u) {
            $u['parent_id'] = $pageId;
            $u['created_at'] = Carbon::now();
            $u['updated_at'] = Carbon::now();

            $pageId = DB::table('pages')->insertGetId($u);

            $uriData = [
                'title'         => $u['name'],
                'name'          => $u['name'],
                'description'   => null,
                'template_id'   => $templateLookup[$u['name']] ?? 1,
                'uriable_id'    => $pageId,
                'uriable_type'  => 'RefinedDigital\CMS\Modules\Pages\Models\Page',
            ];

            Uri::create($uriData);

        }
    }
}
