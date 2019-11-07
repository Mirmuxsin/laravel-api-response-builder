<?php
/**
 * Disable return type hint inspection as we do not have it specified in that
 * class for a purpose. The base class is also not having return type hints.
 *
 * @noinspection ReturnTypeCanBeDeclaredInspection
 */

declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2019 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */

use Illuminate\Support\ServiceProvider;

class ResponseBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/response_builder.php', 'response_builder'
        );
    }

    /**
     * Sets up package resources
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'response-builder');

        $source = __DIR__ . '/../config/response_builder.php';
        $this->publishes([
            $source => config_path('response_builder.php'),
        ]);
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param string $path
     * @param string $key
     *
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $defaults = require $path;
        $config = $this->app['config']->get($key, []);

        $merged_config = Util::mergeConfig($defaults, $config);

        // we now need to sort 'classes' node by priority
        uasort($merged_config['classes'], function($array_a, $array_b) {
            $pri_a = $array_a['pri'] ?? 0;
            $pri_b = $array_b['pri'] ?? 0;

            return $pri_b <=> $pri_a;
        });

        $this->app['config']->set($key, $merged_config);
    }

}
