<?php

namespace RefinedDigital\ProductManager\Commands;

use Illuminate\Console\Command;
use Validator;
use Artisan;
use DB;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refinedCMS:install-product-manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the product manager files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->migrate();
        $this->seed();
        $this->publish();
        $this->createSymLink();
        $this->copyTemplates();
        $this->info('Product Manager has been successfully installed');
    }


    protected function migrate()
    {
        $this->output->writeln('<info>Migrating the database</info>');
        Artisan::call('migrate', [
            '--path' => 'vendor/refineddigital/cms-product-manager/src/Database/Migrations',
            '--force' => 1,
        ]);
    }

    protected function seed()
    {
        $this->output->writeln('<info>Seeding the database</info>');
        Artisan::call('db:seed', [
            '--class' => '\\RefinedDigital\\ProductManager\\Database\\Seeds\\ProductManagerDatabaseSeeder',
            '--force' => 1
        ]);
    }

    protected function publish()
    {
        Artisan::call('vendor:publish', [
            '--tag' => 'product-manager',
        ]);

        // grab the team details template id
        $productTemplate = \DB::table('templates')
                        ->whereName('Product Details')
                        ->first();
        $this->replaceIds($productTemplate, '__PRODUCT_ID__');
    }

    private function replaceIds($template, $key)
    {
        if (isset($template->id)) {
            // override the template id of the team details
            $configFile = config_path('products.php');
            $file = file_get_contents($configFile);
            $search = [ "'".$key."'" ];
            $replace = [ $template->id ];
            file_put_contents($configFile, str_replace($search, $replace, $file));
        }
    }

    protected function createSymLink()
    {
        $this->output->writeln('<info>Creating Symlink</info>');
        try {
            $link = public_path('vendor/');
            $target = '../../../vendor/refineddigital/cms-product-manager/assets/';

            // create the directories
            if (!is_dir($link)) {
                mkdir($link);
            }
            $link .= 'refined/';
            if (!is_dir($link)) {
                mkdir($link);
            }
            $link .= 'product-manager';

            if (! windows_os()) {
                return symlink($target, $link);
            }

            $mode = is_dir($target) ? 'J' : 'H';

            exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
        } catch(\Exception $e) {
            $this->output->writeln('<error>Failed to install symlink</error>');
        }
    }

    private function copyTemplates()
    {
        $this->output->writeln('<info>Copying Templates</info>');
        $dir = __DIR__.'/defaults/views/templates/';
        $templates = scandir($dir);
        array_shift($templates);array_shift($templates);

        if (!is_dir(resource_path('views/templates'))) {
            mkdir(resource_path('views/templates'));
        }
        if (sizeof($templates)) {
            try {
                foreach ($templates as $template) {
                    $contents = file_get_contents($dir.$template);
                    file_put_contents(resource_path('views/templates/'.$template), $contents);
                }
            } catch(\Exception $e) {
                $this->output->writeln('<error>Failed to copy all templates</error>');
            }
        }
    }

}
