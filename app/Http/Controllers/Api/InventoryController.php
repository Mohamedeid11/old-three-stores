<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPShopify\ShopifySDK;

class InventoryController extends Controller
{

public function update_inventory_api(Request $request)
{
    try {
        // Replace 'https://three-store3.myshopify.com' and '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'
        // with your actual Shopify store details
        $config = [
            'ShopUrl' => 'https://three-store3.myshopify.com',
            'AccessToken' => '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',
        ];

        $sku = "Three004542";
        $newQuantity = 100; // Update with the desired quantity

        // Initialize the Shopify SDK
        ShopifySDK::config($config);

        // Create an instance of the Shopify SDK
        $shopify = new ShopifySDK($config);

        // Get the product by SKU
        $products = $shopify->Product->get(['sku' => $sku, 'limit' => 1]);

        // Check if the product with the given SKU exists
        if (!empty($products)) {
            // Find the product with the correct SKU
            $product = null;
            foreach ($products as $p) {
                if ($p['variants'][0]['sku'] === $sku) {
                    $product = $p;
                    break;
                }
            }

            // Check if the product with the correct SKU was found
            if ($product !== null) {
                // Get the variant ID
                $variantId = $product['variants'][0]['id'];

                // Update the inventory quantity of the product
                $updatedVariant = $shopify->Variant->update($variantId, [
                    'variant' => [
                        // 'inventory_quantity' => $newQuantity,
                    ],
                ]);   
                return 1;

                // Check if the inventory quantity was updated successfully
                if (!empty($updatedVariant)) {
                    return response()->json(['message' => 'Inventory quantity updated successfully']);
                } else {
                    return response()->json(['error' => 'Failed to update inventory quantity'], 500);
                }
            } else {
                // Product with the specified SKU not found
                return response()->json(['error' => 'Product not found for the specified SKU'], 404);
            }
        } else {
            // No products found for the specified SKU
            return response()->json(['error' => 'No products found for the specified SKU'], 404);
        }
    } catch (\PHPShopify\Exception\ApiException $e) {
        // Handle API errors
        return response()->json(['error' => "API Error: " . $e->getMessage()], 500);
    } catch (\PHPShopify\Exception\CurlException $e) {
        // Handle cURL errors
        return response()->json(['error' => "cURL Error: " . $e->getMessage()], 500);
    } catch (\Exception $e) {
        // Handle other unexpected errors
        return response()->json(['error' => "Error: " . $e->getMessage()], 500);
    }
}

}
