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

        $pageHolder = new PageHolder();
        $pageHolder->active = 1;
        $pageHolder->position = PageHolder::count() + 1;
        $pageHolder->name = 'Cart';
        $pageHolder->save();

        $templates = DB::table('templates')
            ->whereIn('name', ['Cart','Checkout'])
            ->get();

        $templateLookup = [];
        if ($templates->count()) {
            foreach ($templates as $template) {
                $templateLookup[$template->name] = $template->id;
            }
        }

        $productForm = Form::whereName('Checkout')->get();

        $pages = [
            [
                'page_holder_id' => $pageHolder->id,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Cart'
            ],
            [
                'page_holder_id' => $pageHolder->id,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Checkout',
                'form_id' => isset($productForm->id) ? $productForm->id : null
            ],
            [
                'page_holder_id' => $pageHolder->id,
                'parent_id' => 0,
                'active' => 1,
                'hide_from_menu' => 0,
                'protected' => 0,
                'page_type' => 1,
                'position' => 0,
                'name' => 'Thank You',
                'content' => '
                    <h2>Order Received</h2>
                    <p>Thank you. Your order has been received.</p>
                    <p>[[order_summary]]</p>                    
                    <p>[[order_details]]</p>
                '
            ]
        ];

        $pageId = 0;

        foreach($pages as $pos => $u) {
            $u['parent_id'] = $pageId;
            $u['created_at'] = Carbon::now();
            $u['updated_at'] = Carbon::now();
            $content = '';
            if (isset($u['content'])) {
                $content = $u['content'];
                unset($u['content']);
            }

            $pageId = DB::table('pages')->insertGetId($u);

            $uriData = [
                'title'         => $u['name'],
                'name'          => $u['name'],
                'description'   => null,
                'template_id'   => isset($templateLookup[$u['name']]) ? $templateLookup[$u['name']] : 1,
                'uriable_id'    => $pageId,
                'uriable_type'  => 'RefinedDigital\CMS\Modules\Pages\Models\Page',
            ];

            Uri::create($uriData);

            if ($content) {
                DB::table('page_contents')->insert([
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'page_id' => $pageId,
                    'page_content_type_id' => 1,
                    'position' => 0,
                    'name' => 'Content',
                    'source' => 'content',
                    'content' => $content
                ]);
            }

        }
    }
}
