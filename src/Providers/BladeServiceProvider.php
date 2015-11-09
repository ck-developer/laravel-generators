<?php

namespace Mwc\Generators\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider {


    /**
     * Booting
     */
    public function boot()
    {
        Blade::directive('php', function() {
            return '<?php echo \'<?php\'.PHP_EOL; ?>';
        });

        Blade::directive('endphp', function() {
            return "<?php echo '?>'; ?>";
        });

        Blade::directive('spaceless', function(){
            return '<?php ob_start(); ?>';
        });

        Blade::directive('endspaceless', function(){
            return '<?php echo trim(preg_replace(\'/>\s+</\', \'><\', ob_get_clean())); ?>';
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}