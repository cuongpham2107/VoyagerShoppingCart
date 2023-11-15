<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $categories = \App\Models\ProductCategory::select('id','name','parent_id','order')->get();
        $brands = \App\Models\ProductBrand::where('status','published')->get();
        $categoryTree = $this->buildCategoryTree($categories);
        $categoryTreeJson = json_encode($categoryTree, JSON_PRETTY_PRINT);
        View::share('categoryTreeJson', $categoryTreeJson);
        View::share('brands', $brands);
    }
   

    public function buildCategoryTree($categories, $parent_id = null) {
        $categoryTree = [];

        foreach ($categories as $category) {
            if ($category['parent_id'] === $parent_id) {
                $children = $this->buildCategoryTree($categories, $category['id']);
                if ($children) {
                    $category['children'] = $children;
                }
                $categoryTree[] = $category;
            }
        }

        return $categoryTree;
    }
   
}
