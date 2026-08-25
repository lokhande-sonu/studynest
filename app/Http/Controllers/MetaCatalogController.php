<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockInventory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MetaCatalogController extends Controller
{
    /**
     * Generate a product catalog feed in XML format for Meta (Facebook) Commerce Manager.
     * 
     * URL: /feed/meta-product-catalog.xml
     * This feed should be added as a "Scheduled Feed" data source in Meta Commerce Manager.
     * Meta will auto-fetch this URL periodically (hourly/daily) to keep the catalog in sync.
     *
     * Feed follows Meta Product Feed Specification:
     * @see https://developers.facebook.com/docs/marketing-api/catalog/reference/
     */
    public function productFeed()
    {
        $products = Product::where('p_status', 1)
            ->with(['category', 'stockInventories', 'variants', 'images', 'schools', 'classes'])
            ->get();

        $baseUrl = rtrim(config('app.url'), '/');
        $photoPath = env('PRODUCT_PHOTO', 'uploads/product-photo');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= '<title>Study Nest Product Catalog</title>' . "\n";
        $xml .= '<link>' . $this->escapeXml($baseUrl) . '</link>' . "\n";
        $xml .= '<description>Product catalog feed for Meta Ads - Study Nest School Supplies</description>' . "\n";

        foreach ($products as $product) {
            $inventories = $product->stockInventories;

            // If product has variants, create one feed entry per variant
            if ($product->variants->count() > 0) {
                foreach ($product->variants as $variant) {
                    $inventory = $inventories->where('prod_variant_id', $variant->prod_variant_id)->first();
                    if (!$inventory) continue;

                    $itemId = $product->p_id . '_v' . $variant->prod_variant_id;
                    $itemTitle = $product->p_name . ' - ' . $variant->prod_variant;

                    $xml .= $this->buildItemXml($product, $inventory, $itemId, $itemTitle, $baseUrl, $photoPath);
                }
            } else {
                // No variants — single product entry
                $inventory = $inventories->whereNull('prod_variant_id')->first()
                    ?? $inventories->first();
                
                if (!$inventory) {
                    // Even without inventory, add as out-of-stock so Meta knows about it
                    $xml .= $this->buildItemXmlNoInventory($product, $baseUrl, $photoPath);
                    continue;
                }

                $xml .= $this->buildItemXml($product, $inventory, (string)$product->p_id, $product->p_name, $baseUrl, $photoPath);
            }
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Build XML for a single product item with inventory data.
     */
    private function buildItemXml(Product $product, ProductStockInventory $inventory, string $itemId, string $title, string $baseUrl, string $photoPath): string
    {
        $productUrl = $baseUrl . '/product-details/' . $product->p_id;
        $imageUrl = $this->getProductImageUrl($product, $baseUrl, $photoPath);
        $availability = ($inventory->available_stock > 0) ? 'in stock' : 'out of stock';
        $price = number_format($inventory->unit_price, 2, '.', '') . ' INR';
        $salePrice = null;

        if ($inventory->discounted_unit_price && $inventory->discounted_unit_price > 0 && $inventory->discounted_unit_price < $inventory->unit_price) {
            $salePrice = number_format($inventory->discounted_unit_price, 2, '.', '') . ' INR';
        }

        $description = $this->getProductDescription($product);
        $brand = $product->p_brand ?: 'Study Nest';
        $category = $product->category ? $product->category->cat_name : 'School Supplies';
        $sku = $inventory->prod_sku ?: ('SN-' . $product->p_id);

        // Gender mapping for Meta
        $gender = '';
        if ($product->p_gender && $product->p_gender !== 'Not Applicable') {
            $genderMap = [
                'Male' => 'male',
                'Female' => 'female',
                'Boy' => 'male',
                'Girl' => 'female',
                'Unisex' => 'unisex',
            ];
            $gender = $genderMap[$product->p_gender] ?? '';
        }

        $xml = '<item>' . "\n";
        $xml .= '  <g:id>' . $this->escapeXml($itemId) . '</g:id>' . "\n";
        $xml .= '  <g:title>' . $this->escapeXml($title) . '</g:title>' . "\n";
        $xml .= '  <g:description>' . $this->escapeXml($description) . '</g:description>' . "\n";
        $xml .= '  <g:link>' . $this->escapeXml($productUrl) . '</g:link>' . "\n";
        $xml .= '  <g:image_link>' . $this->escapeXml($imageUrl) . '</g:image_link>' . "\n";

        // Additional product images
        $additionalImages = $this->getAdditionalImageUrls($product, $baseUrl, $photoPath);
        foreach ($additionalImages as $addImg) {
            $xml .= '  <g:additional_image_link>' . $this->escapeXml($addImg) . '</g:additional_image_link>' . "\n";
        }

        $xml .= '  <g:availability>' . $availability . '</g:availability>' . "\n";
        $xml .= '  <g:price>' . $price . '</g:price>' . "\n";

        if ($salePrice) {
            $xml .= '  <g:sale_price>' . $salePrice . '</g:sale_price>' . "\n";
        }

        $xml .= '  <g:condition>new</g:condition>' . "\n";
        $xml .= '  <g:brand>' . $this->escapeXml($brand) . '</g:brand>' . "\n";
        $xml .= '  <g:product_type>' . $this->escapeXml($category) . '</g:product_type>' . "\n";
        $xml .= '  <g:google_product_category>Office Products &gt; Office &amp; School Supplies</g:google_product_category>' . "\n";
        $xml .= '  <g:mpn>' . $this->escapeXml($sku) . '</g:mpn>' . "\n";

        if ($gender) {
            $xml .= '  <g:gender>' . $gender . '</g:gender>' . "\n";
        }

        // Item group ID for variants — helps Meta understand they're the same product
        if (str_contains($itemId, '_v')) {
            $xml .= '  <g:item_group_id>' . $product->p_id . '</g:item_group_id>' . "\n";
        }

        // School and class info as custom labels (useful for ad targeting)
        $schoolNames = $product->schools->pluck('sch_name')->implode(', ');
        $classNames = $product->classes->pluck('class_name')->implode(', ');
        
        if ($schoolNames) {
            $xml .= '  <g:custom_label_0>' . $this->escapeXml($schoolNames) . '</g:custom_label_0>' . "\n";
        }
        if ($classNames) {
            $xml .= '  <g:custom_label_1>' . $this->escapeXml($classNames) . '</g:custom_label_1>' . "\n";
        }
        if ($category) {
            $xml .= '  <g:custom_label_2>' . $this->escapeXml($category) . '</g:custom_label_2>' . "\n";
        }

        $xml .= '</item>' . "\n";

        return $xml;
    }

    /**
     * Build XML for a product without inventory — always marked out of stock.
     */
    private function buildItemXmlNoInventory(Product $product, string $baseUrl, string $photoPath): string
    {
        $productUrl = $baseUrl . '/product-details/' . $product->p_id;
        $imageUrl = $this->getProductImageUrl($product, $baseUrl, $photoPath);
        $description = $this->getProductDescription($product);
        $brand = $product->p_brand ?: 'Study Nest';
        $category = $product->category ? $product->category->cat_name : 'School Supplies';

        $xml = '<item>' . "\n";
        $xml .= '  <g:id>' . $product->p_id . '</g:id>' . "\n";
        $xml .= '  <g:title>' . $this->escapeXml($product->p_name) . '</g:title>' . "\n";
        $xml .= '  <g:description>' . $this->escapeXml($description) . '</g:description>' . "\n";
        $xml .= '  <g:link>' . $this->escapeXml($productUrl) . '</g:link>' . "\n";
        $xml .= '  <g:image_link>' . $this->escapeXml($imageUrl) . '</g:image_link>' . "\n";
        $xml .= '  <g:availability>out of stock</g:availability>' . "\n";
        $xml .= '  <g:price>0.00 INR</g:price>' . "\n";
        $xml .= '  <g:condition>new</g:condition>' . "\n";
        $xml .= '  <g:brand>' . $this->escapeXml($brand) . '</g:brand>' . "\n";
        $xml .= '  <g:product_type>' . $this->escapeXml($category) . '</g:product_type>' . "\n";
        $xml .= '  <g:google_product_category>Office Products &gt; Office &amp; School Supplies</g:google_product_category>' . "\n";
        $xml .= '</item>' . "\n";

        return $xml;
    }

    /**
     * Get primary product image URL.
     */
    private function getProductImageUrl(Product $product, string $baseUrl, string $photoPath): string
    {
        // Prefer first image from product_images table
        $firstImage = $product->images->first();
        if ($firstImage && $firstImage->img_path) {
            return $baseUrl . '/' . $photoPath . '/' . $firstImage->img_path;
        }

        // Fallback to main product photo
        if ($product->p_photo) {
            return $baseUrl . '/' . $photoPath . '/' . $product->p_photo;
        }

        // Fallback placeholder
        return $baseUrl . '/customer-web-assets/images/logo/logo.png';
    }

    /**
     * Get additional image URLs (max 10 for Meta).
     */
    private function getAdditionalImageUrls(Product $product, string $baseUrl, string $photoPath): array
    {
        $urls = [];
        $images = $product->images->skip(1)->take(9); // Skip first (already primary), max 9 more

        foreach ($images as $img) {
            if ($img->img_path) {
                $urls[] = $baseUrl . '/' . $photoPath . '/' . $img->img_path;
            }
        }

        return $urls;
    }

    /**
     * Get a clean text description for the product.
     */
    private function getProductDescription(Product $product): string
    {
        // Prefer short desc, fallback to full desc stripped of HTML
        $desc = $product->p_short_desc;
        
        if (!$desc || trim($desc) === '') {
            $desc = strip_tags($product->p_full_desc ?? '');
        }

        // Meta requires at least some description
        if (!$desc || trim($desc) === '') {
            $desc = $product->p_name . ' - Available at Study Nest';
        }

        // Trim to 5000 chars (Meta limit)
        return mb_substr(trim($desc), 0, 5000);
    }

    /**
     * Escape special XML characters.
     */
    private function escapeXml(?string $value): string
    {
        if ($value === null) return '';
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
