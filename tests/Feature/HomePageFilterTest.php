<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\School;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\ProductStockInventory;
use App\Models\AppSlider;

/**
 * Feature tests for the Home Page filter and sort functionality.
 *
 * Covers Requirement 1: Home Page Filter Fix
 */
class HomePageFilterTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    //  Helpers
    // ---------------------------------------------------------------

    private function createSchool(array $overrides = []): School
    {
        // START: imohitmehto | 2026-08-25 | FIX: Added required columns sch_email, sch_address, sch_city
        // tbl_schools schema requires these fields; tests were failing with NOT NULL constraint violations
        return School::create(array_merge([
            'sch_name'    => 'Test School',
            'sch_mobile'  => '9000000000',
            'sch_email'   => 'test@school.com',
            'sch_address' => '123 Test Street',
            'sch_city'    => 'Test City',
            'sch_status'  => 1,
        ], $overrides));
        // END: imohitmehto | FIX: Required columns for test factory
    }

    private function createClass(array $overrides = []): Classes
    {
        return Classes::create(array_merge([
            'class_name'   => 'Class 5',
            'class_status' => 1,
        ], $overrides));
    }

    private function createSubject(array $overrides = []): Subject
    {
        return Subject::create(array_merge([
            'subject_name'   => 'Mathematics',
            'subject_status' => 1,
        ], $overrides));
    }

    private function createCategory(array $overrides = []): ProductCategory
    {
        return ProductCategory::create(array_merge([
            'cat_name'   => 'Notebooks',
            'cat_status' => 1,
        ], $overrides));
    }

    private function createProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'p_name'            => 'Test Product',
            'p_status'          => 1,
            'p_gender'          => 'Unisex',
            'is_all_schools'    => 0,
            'is_all_classes'    => 0,
            'is_all_subjects'   => 0,
        ], $overrides));
    }

    private function createStock(Product $product, array $overrides = []): ProductStockInventory
    {
        return ProductStockInventory::create(array_merge([
            'prod_sku'                => 'SKU-' . $product->p_id . '-001',
            'prod_id'                 => $product->p_id,
            'prod_variant_id'         => null,
            'available_stock'         => 100,
            'unit_price'              => 199.00,
            'discounted_unit_price'   => 179.00,
            'status'                  => 1,
        ], $overrides));
    }

    // ---------------------------------------------------------------
    //  Homepage Tests
    // ---------------------------------------------------------------

    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_passes_schools_to_view()
    {
        $school = $this->createSchool(['sch_name' => 'Delhi Public School']);
        $this->createSchool(['sch_name' => 'Inactive School', 'sch_status' => 0]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('schools', function ($schools) use ($school) {
            return $schools->contains('sch_id', $school->sch_id)
                && $schools->count() === 1; // Only active schools
        });
    }

    public function test_homepage_passes_classes_to_view()
    {
        $class = $this->createClass(['class_name' => 'Class 10']);
        $this->createClass(['class_name' => 'Inactive Class', 'class_status' => 0]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('classes', function ($classes) use ($class) {
            return $classes->contains('class_id', $class->class_id)
                && $classes->count() === 1;
        });
    }

    public function test_homepage_passes_categories_with_product_count()
    {
        $category = $this->createCategory(['cat_name' => 'Books']);
        $product = $this->createProduct(['p_cat_id' => $category->cat_id]);
        $this->createStock($product);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('categories', function ($categories) use ($category) {
            $cat = $categories->firstWhere('cat_id', $category->cat_id);
            return $cat && $cat->products_count >= 1;
        });
    }

    public function test_homepage_passes_new_arrivals()
    {
        $category = $this->createCategory();
        $product = $this->createProduct([
            'p_cat_id'     => $category->cat_id,
            'p_name'       => 'Arrival Product',
            'p_created_at' => now(),
        ]);
        $this->createStock($product);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('newArrivals', function ($arrivals) {
            return $arrivals->contains('p_name', 'Arrival Product');
        });
    }

    // ---------------------------------------------------------------
    //  School-Shop Page Tests
    // ---------------------------------------------------------------

    public function test_school_shop_page_loads()
    {
        $response = $this->get('/school-shop');

        $response->assertStatus(200);
    }

    public function test_school_shop_passes_filter_data_to_view()
    {
        $school  = $this->createSchool();
        $class   = $this->createClass();
        $subject = $this->createSubject();

        $response = $this->get('/school-shop');

        $response->assertStatus(200);
        $response->assertViewHas('schools');
        $response->assertViewHas('classes');
        $response->assertViewHas('subjects');
        $response->assertViewHas('categories');
        $response->assertViewHas('products');
        $response->assertViewHas('bundles');
    }

    public function test_school_shop_accepts_sort_by_param()
    {
        $category = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Cheap', 'p_cat_id' => $category->cat_id, 'p_created_at' => now()->subDay()]);
        $p2 = $this->createProduct(['p_name' => 'Expensive', 'p_cat_id' => $category->cat_id, 'p_created_at' => now()]);
        $this->createStock($p1, ['unit_price' => 50.00, 'discounted_unit_price' => 50.00]);
        $this->createStock($p2, ['unit_price' => 500.00, 'discounted_unit_price' => 500.00]);

        $response = $this->get('/school-shop?sort_by=price_low_high');

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_school_shop_filters_by_school()
    {
        $school1 = $this->createSchool(['sch_name' => 'School A']);
        $school2 = $this->createSchool(['sch_name' => 'School B']);
        $cat     = $this->createCategory();

        $p1 = $this->createProduct([
            'p_name'        => 'Product A',
            'p_cat_id'      => $cat->cat_id,
            'is_all_schools' => 0,
        ]);
        $this->createStock($p1);
        $p1->schools()->attach($school1->sch_id);

        $p2 = $this->createProduct([
            'p_name'        => 'Product B',
            'p_cat_id'      => $cat->cat_id,
            'is_all_schools' => 0,
        ]);
        $this->createStock($p2);
        $p2->schools()->attach($school2->sch_id);

        $response = $this->get("/school-shop?school={$school1->sch_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_school_shop_includes_products_marked_all_schools()
    {
        $school = $this->createSchool();
        $cat    = $this->createCategory();

        $p1 = $this->createProduct([
            'p_name'         => 'Universal Product',
            'p_cat_id'       => $cat->cat_id,
            'is_all_schools' => 1,
        ]);
        $this->createStock($p1);

        $response = $this->get("/school-shop?school={$school->sch_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1) {
            return $products->contains('p_id', $p1->p_id);
        });
    }

    public function test_school_shop_filters_by_category()
    {
        $cat1 = $this->createCategory(['cat_name' => 'Notebooks']);
        $cat2 = $this->createCategory(['cat_name' => 'Pens']);

        $p1 = $this->createProduct(['p_name' => 'Notebook', 'p_cat_id' => $cat1->cat_id]);
        $p2 = $this->createProduct(['p_name' => 'Pen', 'p_cat_id' => $cat2->cat_id]);
        $this->createStock($p1);
        $this->createStock($p2);

        $response = $this->get("/school-shop?category={$cat1->cat_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_school_shop_filters_by_search()
    {
        $cat = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Math Notebook', 'p_cat_id' => $cat->cat_id]);
        $p2 = $this->createProduct(['p_name' => 'Science Pen', 'p_cat_id' => $cat->cat_id]);
        $this->createStock($p1);
        $this->createStock($p2);

        $response = $this->get('/school-shop?search=Notebook');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_school_shop_filters_by_gender()
    {
        $cat = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Boys Shirt', 'p_gender' => 'Boys', 'p_cat_id' => $cat->cat_id]);
        $p2 = $this->createProduct(['p_name' => 'Girls Dress', 'p_gender' => 'Girls', 'p_cat_id' => $cat->cat_id]);
        $this->createStock($p1);
        $this->createStock($p2);

        $response = $this->get('/school-shop?gender=Boys');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    // ---------------------------------------------------------------
    //  Shop Page Tests
    // ---------------------------------------------------------------

    public function test_shop_page_loads()
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
    }

    public function test_shop_page_passes_filter_data()
    {
        $this->createSchool();
        $this->createClass();
        $this->createSubject();
        $this->createCategory();

        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertViewHas('schools');
        $response->assertViewHas('classes');
        $response->assertViewHas('subjects');
        $response->assertViewHas('categories');
        $response->assertViewHas('products');
    }

    public function test_shop_accepts_sort_by_price_low_to_high()
    {
        $cat = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Cheap', 'p_cat_id' => $cat->cat_id, 'p_created_at' => now()->subDay()]);
        $p2 = $this->createProduct(['p_name' => 'Expensive', 'p_cat_id' => $cat->cat_id, 'p_created_at' => now()]);
        $this->createStock($p1, ['unit_price' => 50.00, 'discounted_unit_price' => 50.00]);
        $this->createStock($p2, ['unit_price' => 500.00, 'discounted_unit_price' => 500.00]);

        $response = $this->get('/shop?sort_by=price_low_high');

        $response->assertStatus(200);
    }

    public function test_shop_accepts_sort_by_price_high_to_low()
    {
        $cat = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Cheap', 'p_cat_id' => $cat->cat_id, 'p_created_at' => now()->subDay()]);
        $p2 = $this->createProduct(['p_name' => 'Expensive', 'p_cat_id' => $cat->cat_id, 'p_created_at' => now()]);
        $this->createStock($p1, ['unit_price' => 50.00, 'discounted_unit_price' => 50.00]);
        $this->createStock($p2, ['unit_price' => 500.00, 'discounted_unit_price' => 500.00]);

        $response = $this->get('/shop?sort_by=price_high_low');

        $response->assertStatus(200);
    }

    public function test_shop_filters_by_school()
    {
        $school = $this->createSchool();
        $cat    = $this->createCategory();

        $p1 = $this->createProduct([
            'p_name'         => 'Matched',
            'p_cat_id'       => $cat->cat_id,
            'is_all_schools' => 0,
        ]);
        $this->createStock($p1);
        $p1->schools()->attach($school->sch_id);

        $p2 = $this->createProduct([
            'p_name'         => 'Not Matched',
            'p_cat_id'       => $cat->cat_id,
            'is_all_schools' => 0,
        ]);
        $this->createStock($p2);

        $response = $this->get("/shop?school={$school->sch_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_shop_filters_by_class()
    {
        $class = $this->createClass();
        $cat   = $this->createCategory();

        $p1 = $this->createProduct([
            'p_name'         => 'Class Match',
            'p_cat_id'       => $cat->cat_id,
            'is_all_classes' => 0,
        ]);
        $this->createStock($p1);
        $p1->classes()->attach($class->class_id);

        $p2 = $this->createProduct([
            'p_name'         => 'No Class Match',
            'p_cat_id'       => $cat->cat_id,
            'is_all_classes' => 0,
        ]);
        $this->createStock($p2);

        $response = $this->get("/shop?class={$class->class_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_shop_filters_by_subject()
    {
        $subject = $this->createSubject();
        $cat     = $this->createCategory();

        $p1 = $this->createProduct([
            'p_name'           => 'Subject Match',
            'p_cat_id'         => $cat->cat_id,
            'is_all_subjects'  => 0,
        ]);
        $this->createStock($p1);
        $p1->subjects()->attach($subject->subject_id);

        $p2 = $this->createProduct([
            'p_name'           => 'No Subject Match',
            'p_cat_id'         => $cat->cat_id,
            'is_all_subjects'  => 0,
        ]);
        $this->createStock($p2);

        $response = $this->get("/shop?subject={$subject->subject_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_only_published_products_are_shown()
    {
        $cat = $this->createCategory();
        $p1 = $this->createProduct(['p_name' => 'Published', 'p_status' => 1, 'p_cat_id' => $cat->cat_id]);
        $p2 = $this->createProduct(['p_name' => 'Draft', 'p_status' => 0, 'p_cat_id' => $cat->cat_id]);
        $this->createStock($p1);
        $this->createStock($p2);

        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }

    public function test_filter_params_are_combined_correctly()
    {
        $school  = $this->createSchool();
        $class   = $this->createClass();
        $cat     = $this->createCategory();

        // Product matching all filters
        $p1 = $this->createProduct([
            'p_name'         => 'All Match',
            'p_cat_id'       => $cat->cat_id,
            'is_all_schools' => 0,
            'is_all_classes' => 0,
        ]);
        $this->createStock($p1);
        $p1->schools()->attach($school->sch_id);
        $p1->classes()->attach($class->class_id);

        // Product matching only school
        $p2 = $this->createProduct([
            'p_name'         => 'School Only',
            'p_cat_id'       => $cat->cat_id,
            'is_all_schools' => 0,
            'is_all_classes' => 0,
        ]);
        $this->createStock($p2);
        $p2->schools()->attach($school->sch_id);

        $response = $this->get("/shop?school={$school->sch_id}&class={$class->class_id}&category={$cat->cat_id}");

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($p1, $p2) {
            $ids = $products->pluck('p_id')->toArray();
            return in_array($p1->p_id, $ids) && !in_array($p2->p_id, $ids);
        });
    }
}
