<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStockInventory;
use App\Models\ProductVariant;
use App\Models\School;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class WebsiteFiltersTest extends TestCase
{
    private ProductCategory $category;
    private Classes $classA;
    private Classes $classB;
    private School $schoolS;
    private School $schoolT;
    private Product $pAllClasses;
    private Product $pLinkedA;
    private Product $pLinkedB;
    private Product $pOrphan;
    private Product $pSchoolS;
    private Product $pAllSchools;
    private Product $pOtherSchool;
    private $originalCategoryOrder;

    use \Tests\CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        // The shop page orders by CATEGORY_DISPLAY_ORDER (FIELD) first, which
        // would push fixtures (category not in that list) far past page 1.
        // Disable it for these tests so p_created_at desc applies.
        $this->originalCategoryOrder = env('CATEGORY_DISPLAY_ORDER');
        $this->overrideCategoryOrder(false);

        $this->category = ProductCategory::create([
            'cat_name' => 'ZZZFilterCat_' . uniqid(),
            'cat_status' => 1,
            'cat_created_at' => now(),
        ]);

        $this->classA = Classes::create(['class_name' => 'ZZZClassA_' . uniqid(), 'class_status' => 1]);
        $this->classB = Classes::create(['class_name' => 'ZZZClassB_' . uniqid(), 'class_status' => 1]);

        $this->schoolS = School::create([
            'sch_name' => 'ZZZSchoolS_' . uniqid(),
            'sch_mobile' => '9000000000',
            'sch_email' => 's' . uniqid() . '@example.com',
            'sch_address' => 'Address S',
            'sch_city' => 'City S',
            'sch_status' => 1,
        ]);
        $this->schoolT = School::create([
            'sch_name' => 'ZZZSchoolT_' . uniqid(),
            'sch_mobile' => '9000000001',
            'sch_email' => 't' . uniqid() . '@example.com',
            'sch_address' => 'Address T',
            'sch_city' => 'City T',
            'sch_status' => 1,
        ]);

        $this->pAllClasses = $this->makeProduct('ZZZFTP_AllClasses_' . uniqid(), 1);
        $this->pLinkedA = $this->makeProduct('ZZZFTP_LinkedA_' . uniqid(), 0);
        $this->pLinkedB = $this->makeProduct('ZZZFTP_LinkedB_' . uniqid(), 0);
        $this->pOrphan = $this->makeProduct('ZZZFTP_Orphan_' . uniqid(), 0);
        $this->pSchoolS = $this->makeProduct('ZZZFTP_SchoolS_' . uniqid(), 1);
        $this->pAllSchools = $this->makeProduct('ZZZFTP_AllSchools_' . uniqid(), 1);
        $this->pOtherSchool = $this->makeProduct('ZZZFTP_OtherSchool_' . uniqid(), 1, 0);

        // Class links
        DB::table('tbl_product_classes')->insert(['product_id' => $this->pLinkedA->p_id, 'class_id' => $this->classA->class_id]);
        DB::table('tbl_product_classes')->insert(['product_id' => $this->pLinkedB->p_id, 'class_id' => $this->classB->class_id]);
        // pAllClasses has is_all_classes=1, pOrphan has NO class links.

        // School links
        DB::table('tbl_product_schools')->insert(['product_id' => $this->pSchoolS->p_id, 'school_id' => $this->schoolS->sch_id]);
        DB::table('tbl_product_schools')->insert(['product_id' => $this->pOtherSchool->p_id, 'school_id' => $this->schoolT->sch_id]);
        // pAllSchools has is_all_schools=1.
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        $this->overrideCategoryOrder($this->originalCategoryOrder);
        parent::tearDown();
    }

    private function overrideCategoryOrder($value): void
    {
        $_SERVER['CATEGORY_DISPLAY_ORDER'] = $value;
        $_ENV['CATEGORY_DISPLAY_ORDER'] = $value;
        if ($value === false) {
            putenv('CATEGORY_DISPLAY_ORDER');
        } else {
            putenv('CATEGORY_DISPLAY_ORDER=' . $value);
        }
    }

    private function makeProduct(string $name, int $allClasses, int $allSchools = 1): Product
    {
        $product = Product::create([
            'p_cat_id' => $this->category->cat_id,
            'p_name' => $name,
            'p_tag' => 'tag_' . $name,
            'p_short_desc' => 'short description ' . $name,
            'p_full_desc' => 'full description ' . $name,
            'is_all_classes' => $allClasses,
            'is_all_schools' => $allSchools,
            'p_status' => 1,
            // Future date so fixtures sort first on the paginated shop page
            // regardless of the category FIELD ordering.
            'p_created_at' => now()->addDays(30),
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->p_id,
            'prod_variant' => 'Default',
        ]);

        ProductStockInventory::create([
            'prod_sku' => 'SKU_' . uniqid(),
            'prod_id' => $product->p_id,
            'prod_variant_id' => $variant->prod_variant_id,
            'available_stock' => 10,
            'unit_price' => 100,
            'discounted_unit_price' => 100,
            'gst_rate' => 18,
            'gst_type' => 'inclusive',
            'cgst_rate' => 9,
            'sgst_rate' => 9,
            'status' => 1,
        ]);

        return $product;
    }

    public function test_class_filter_shows_linked_allclasses_and_orphan_products(): void
    {
        $response = $this->get('/shop?class=' . $this->classA->class_id);

        $response->assertStatus(200);
        $response->assertSee($this->pAllClasses->p_name);
        $response->assertSee($this->pLinkedA->p_name);
        $response->assertSee($this->pOrphan->p_name);
        $response->assertDontSee($this->pLinkedB->p_name);
    }

    public function test_class_filter_other_class_excludes_linked_products(): void
    {
        $response = $this->get('/shop?class=' . $this->classB->class_id);

        $response->assertStatus(200);
        $response->assertSee($this->pAllClasses->p_name);
        $response->assertSee($this->pLinkedB->p_name);
        $response->assertSee($this->pOrphan->p_name);
        $response->assertDontSee($this->pLinkedA->p_name);
    }

    public function test_school_filter_includes_all_schools_products(): void
    {
        $response = $this->get('/shop?school=' . $this->schoolS->sch_id);

        $response->assertStatus(200);
        $response->assertSee($this->pSchoolS->p_name);
        $response->assertSee($this->pAllSchools->p_name);
        $response->assertDontSee($this->pOtherSchool->p_name);
    }

    public function test_search_matches_product_name(): void
    {
        $needle = substr($this->pLinkedA->p_name, 7); // unique suffix after prefix

        $response = $this->get('/shop?search=' . urlencode($needle));

        $response->assertStatus(200);
        $response->assertSee($this->pLinkedA->p_name);
        $response->assertDontSee($this->pLinkedB->p_name);
    }

    public function test_search_matches_tag(): void
    {
        $tag = $this->pLinkedB->p_tag;

        $response = $this->get('/shop?search=' . urlencode($tag));

        $response->assertStatus(200);
        $response->assertSee($this->pLinkedB->p_name);
        $response->assertDontSee($this->pLinkedA->p_name);
    }

    public function test_search_matches_short_description(): void
    {
        $fragment = 'short description ' . $this->pOrphan->p_name;

        $response = $this->get('/shop?search=' . urlencode($fragment));

        $response->assertStatus(200);
        $response->assertSee($this->pOrphan->p_name);
    }

    public function test_search_matches_category_name(): void
    {
        $response = $this->get('/shop?search=' . urlencode($this->category->cat_name));

        $response->assertStatus(200);
        $response->assertSee($this->pAllClasses->p_name);
        $response->assertSee($this->pLinkedA->p_name);
    }

    public function test_combined_class_and_search_filter(): void
    {
        $needle = substr($this->pLinkedA->p_name, 7);

        $response = $this->get('/shop?class=' . $this->classA->class_id . '&search=' . urlencode($needle));

        $response->assertStatus(200);
        $response->assertSee($this->pLinkedA->p_name);
        $response->assertDontSee($this->pLinkedB->p_name);
        $response->assertDontSee($this->pOrphan->p_name);
    }

    public function test_school_shop_route_renders(): void
    {
        $response = $this->get('/school-shop?school=' . $this->schoolS->sch_id);

        $response->assertStatus(200);
    }

    public function test_no_filters_returns_all_products(): void
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
        $response->assertSee($this->pAllClasses->p_name);
        $response->assertSee($this->pLinkedA->p_name);
        $response->assertSee($this->pOrphan->p_name);
        $response->assertSee($this->pOtherSchool->p_name);
    }
}
