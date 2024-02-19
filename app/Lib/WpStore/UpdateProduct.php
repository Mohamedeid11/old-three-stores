<?php
use App\Product;
use App\Category;
if(!function_exists('update_woocommerce_product'))
{
    function update_woocommerce_product ($id)
    {
        $product_system_info = Product::findorfail($id);
        if($product_system_info->woocommerce > 0)
        {
            $ProductData = DB::connection('mysql2')->table("wp_thposts")->where("ID", $product_system_info->woocommerce)
            ->where("post_type", "product")->where('post_status', 'publish')->first();
            if($ProductData !== NULL)
            {
                if($ProductData->post_title != $product_system_info->title)
                {
                    $product_title = $product_system_info->title;
                    $lower_title = strtolower($product_title);
                    $someModel = DB::connection('mysql2')->table("wp_thposts")->whereRaw("lower(`post_title`) = '$lower_title' AND `ID` != $product_system_info->woocommerce")->count();
                    if($someModel == 0)
                    {
                        $post_name = str_replace(' ', '-', strtolower($product_title));
                    }
                    else
                    {
                        $dd = $someModel + 1;
                        $post_name = str_replace(' ', '-', strtolower($product_title))."-".$dd;
                    }
                    $date = date('Y-m-d H:i:s');
                    $gmt_date = date('Y-m-d H:i:s', strtotime('now') - 7200);
            
                    $url = "http://three-store.com/site1/product/".$post_name."/";
                    DB::connection('mysql2')->unprepared("UPDATE `wp_thposts` SET `post_title` = '$product_title', 
                    `post_name` = '$post_name', `post_modified` = '$date', `post_modified_gmt` = '$gmt_date', `guid` = '$url' 
                    WHERE `ID` = $ProductData->ID");
                }
                $product_price_egp = $product_system_info->price;
                $product_id = $ProductData->ID;
                $product_cat = $product_system_info->cat_info->title;
                $product_cat_slug = str_replace(' ', '-', strtolower($product_cat));

                $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
                if($product_cat_selector === NULL)
                {
                    $insert_product_cat = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
                    VALUES ('$product_cat', '$product_cat_slug')");
                    $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
                    $product_cat_id = $product_cat_selector->term_id;
                    $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`,
                    `term_id`, `taxonomy`, `description`, 
                    `parent`, `count`) VALUES ($product_cat_id, $product_cat_id, 'product_cat', '', 0, 0)");
                }
                $product_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                `meta_value` = '$product_price_egp' WHERE `post_id`=$ProductData->ID AND  `meta_key`='_price'");
                
                $product_cat_id = $product_cat_selector->term_id;
                $woo_cats =  DB::connection('mysql2')->table('wp_thterm_taxonomy')->
                where('taxonomy', '=', 'product_cat')->where('term_id', '!=', $product_cat_id)->get();
                // dd($woo_cats);
                foreach($woo_cats as $cat)
                {
                    $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)
                    ->where('term_taxonomy_id', $cat->term_id)->first();
                    if($term_checker !== NULL)
                    {
                        // dd('DELETE FROM `wp_thterm_relationships` WHERE `object_id` = '.$product_id.' AND `term_taxonomy_id` = '.$cat->term_id);
                        $term_checker = DB::connection('mysql2')->unprepared('DELETE FROM `wp_thterm_relationships` 
                        WHERE `object_id` ='.$product_id.' AND `term_taxonomy_id` = '.$cat->term_id);
                    }

                }
                $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_cat_id)->first();
                if($term_checker === NULL)
                {
                    $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
                    VALUES ($product_id, $product_cat_id)");
                }
                DB::connection('mysql2')->unprepared("DELETE FROM `wp_thwc_product_meta_lookup` WHERE `product_id` = $product_id");
                $stock_qty = 0;
                $stock_status = 'outofstock';
                $productmetalookup = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thwc_product_meta_lookup` (`product_id`, `sku`, `virtual`, 
                `downloadable`, `min_price`, `max_price`, `onsale`, `stock_quantity`, `stock_status`, `rating_count`, `average_rating`, 
                `total_sales`, `tax_status`, `tax_class`) VALUES ($product_id, '', 0, 0, $product_price_egp, $product_price_egp, 0, $stock_qty, '$stock_status',
                0, 0, 0, 'taxable', '')");
            }
            else
            {
                add_product_to_wp ($id);
            }
        }
        else
        {
            add_product_to_wp ($id);
        }

        if(count($product_system_info->colors) > 0 || count($product_system_info->size) > 0)
        {
            update_product_variations($id);
        }
        else
        {
            update_product_meta_without_variations($id);
        }

    }
}

if(!function_exists('update_product_meta_without_variations'))
{
    function update_product_meta_without_variations ($id)
    {
        $product_system_info = Product::findorfail($id);
        $product_id = $product_system_info->woocommerce;
        DB::connection('mysql2')->unprepared("DELETE FROM `wp_thpostmeta` WHERE `post_id` = $product_id AND `meta_key` = '_product_attributes'");
        $variatons = DB::connection('mysql2')->table("wp_thposts")->where("post_parent", $product_id)->where("post_type", 'product_variation')->update(['post_status'=>'trash']);
    }
}


if(!function_exists('update_product_variations'))
{
    function update_product_variations ($id)
    {
        $product_system_info = Product::findorfail($id);
        $product_id = $product_system_info->woocommerce;
        $colors = array();
        foreach ($product_system_info->colors as $color)
        {
            if($color->color_info->name != '')
            {
                $colors[] = $color->color_info->name;
            }
            else
            {
                $colors[] = $color->color_info->title;
            }
        }
        $sizes = array();
        foreach ($product_system_info->sizes as $size)
        {
            $sizes[] = $size->size_info->title;
        }
        if(count($colors) > 0 && count($sizes) == 0)
        {
            $attributes = array("color"=>array("name"=>"Color", "value"=>implode(" | ", $colors), "position"=>1, "is_visible"=>1, 
            "is_variation"=>1, "is_taxonomy"=>0));
        }
        else if(count($colors) == 0 && count($sizes) > 0)
        {
            $attributes = array("size"=>array("name"=>"Size", "value"=>implode(" | ", $sizes), "position"=>2, "is_visible"=>1, 
            "is_variation"=>1, "is_taxonomy"=>0));
        }
        else
        {
            $attributes = array("color"=>array("name"=>"Color", "value"=>implode(" | ", $colors), "position"=>1, "is_visible"=>1, 
            "is_variation"=>1, "is_taxonomy"=>0), "size"=>array("name"=>"Size", "value"=>implode(" | ", $sizes), "position"=>2, 
            "is_visible"=>1, "is_variation"=>1, "is_taxonomy"=>0));
        }
        $product_attributes = serialize ($attributes);
        $nmkaldsm = DB::connection('mysql2')->table("wp_thpostmeta")->whereRaw("`post_id` = $product_id AND `meta_key` = '_product_attributes'")->first();
        if($nmkaldsm !== NULL)
        {
            DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET `meta_value` = '$product_attributes'
            WHERE `post_id` = $product_id AND `meta_key` = '_product_attributes'");    
        }
        else
        {
            $product_attrs = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
            VALUES ($product_id, '_product_attributes', '$product_attributes')");    
        }
        $variations = array();
        $product_title = $product_system_info->title;
        $lower_title = strtolower($product_title);
        $someModel = DB::connection('mysql2')->table("wp_thposts")->whereRaw("lower(`post_title`) = '$lower_title' AND `ID` != $product_system_info->woocommerce")->count();
        if($someModel == 0)
        {
            $post_name = str_replace(' ', '-', strtolower($product_title));
        }
        else
        {
            $dd = $someModel + 1;
            $post_name = str_replace(' ', '-', strtolower($product_title))."-".$dd;
        }
        $url = "http://three-store.com/site1/product/".$post_name."/";
        $date = date('Y-m-d H:i:s');
        $gmt_date = date('Y-m-d H:i:s', strtotime('now') - 7200);
        $product_price_egp = $product_system_info->price;
        $stock_qty = 0;
        $stock_status = 'outofstock';

        if(count($colors) > 0 && count($sizes) == 0)
        {
            for ($i = 0; $i < count($colors); $i++)
            {
                $product_color = $colors[$i];
                $product_color_slug = str_replace(' ', '-', strtolower($product_color));
                $product_color_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_color_slug)->first();
                if($product_color_selector === NULL)
                {
                    $insert_product_color = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
                    VALUES ('$product_color', '$product_color_slug')");
                    $product_color_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_color_slug)->first();
                    $product_color_id = $product_color_selector->term_id;
                    $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
                    `parent`, `count`) VALUES ($product_color_id, $product_color_id, 'pa_color', '', 0, 0)");
                }
                else
                {
                    $product_color_id = $product_color_selector->term_id;
                }
                $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_color_id)->first();
                if($term_checker === NULL)
                {
                    $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
                    VALUES ($product_id, $product_color_id)");
                }
                // Step 5.1 Add Colors To Product
                $product_title_color = $product_title." - ".$product_color;
                $post_xcerpt = "Color: ".$product_color;
                $post_name_color = $post_name."-".$product_color_slug;
                $color_url = "http://three-store.com/site1/?post_type=product_variation&p=".$product_id;
                
                $hhnm = DB::connection('mysql2')->table('wp_thposts')->where('post_parent', $product_id)->where('post_excerpt', $post_xcerpt)->first();
                if($hhnm === NULL)
                {
                    $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                    `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                    `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                    `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', 
                    '$product_title_color', '$post_xcerpt', 'publish', 'open', 
                    'closed', '', '$post_name_color', '', '', '$date', '$gmt_date', 0, $product_id, '$color_url', 0, 'product_variation', '', 0)"); 
                    
                    $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_color)->where('post_type', 'product_variation')->first();

                    // dd($selectProduct);
                    $product_id_color = $selectProduct->ID;
                    $variations[] = $selectProduct->ID;
                    /// Step 5.2 Add Meta To Variation
                    $product_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_price', $product_price_egp)");
                    $product_regular_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_regular_price', $product_price_egp)");
                    $product_stock_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_stock_status', '$stock_status')");
                    $product_manage_stock = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_manage_stock', 'yes')");
                    $product_stock_qty = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_stock', '$stock_qty')");
                    $product_average_rating = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_wc_average_rating', 0)");
                    $product_wc_review_count = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_wc_review_count', 0)");
                    $product_total_sales = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, 'total_sales', 0)");      
                    $product_attribute_color = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, 'attribute_color', '$product_color')");    
                    $product_variation_description = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_variation_description', '')");
                    $product_tax_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_tax_status', 'taxable')");
                    $product_tax_class = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_tax_class', 'parent')");
                    $produc_thumbnail_id = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_thumbnail_id', '0')");
                    $produc_virtual = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_virtual', 'no')");
                    $produc_sold_individually = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_sold_individually', 'no')");
                    $produc_backorders = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_backorders', 'yes')");
                    $produc__download_limit = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_limit', '-1')");
                    $produc_download_expiry = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_expiry', '-1')");
                    $produc_downloadable = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_expiry', 'no')");    
                    $product_version = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_product_version', '4.6.0')");
                }
                else
                {
                    $product_id_color =  $hhnm->ID;
                    $variations[] = $hhnm->ID;
                    $product_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_price'");
                    $product_regular_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_regular_price'");
                    $product_stock_status = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$stock_status' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock_status'");
                    $product_stock_qty = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                   `meta_value` = '$stock_qty' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock'");
                }
            }
        }
        else if(count($colors) == 0 && count($sizes) > 0)
        {
            for ($i = 0; $i < count($sizes); $i++)
            {
                $product_size = $sizes[$i];
                $product_size_slug = str_replace(' ', '-', strtolower($product_size));
                $product_size_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_size_slug)->first();
                if($product_size_selector === NULL)
                {
                    $insert_product_size = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
                    VALUES ('$product_size', '$product_size_slug')");
                    $product_size_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_size_slug)->first();
                    $product_size_id = $product_size_selector->term_id;
                    $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
                    `parent`, `count`) VALUES ($product_size_id, $product_size_id, 'pa_size', '', 0, 0)");
                }
                else
                {
                    $product_size_id = $product_size_selector->term_id;
                }
                $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_size_id)->first();
                if($term_checker === NULL)
                {
                    $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
                    VALUES ($product_id, $product_size_id)");
                }
                // Step 5.1 Add Sizes To Product
                $product_title_size = $product_title." - ".$product_size;
                $post_xcerpt = "Size: ".$product_size;
                $post_name_size = $post_name."-".$product_size_slug;
                $size_url = "http://three-store.com/site1/?post_type=product_variation&p=".$product_id;
                $hhnm = DB::connection('mysql2')->table('wp_thposts')->where('post_parent', $product_id)->where('post_excerpt', $post_xcerpt)->first();
                if($hhnm === NULL)
                {
                    $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                    `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                    `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                    `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title_size', '$post_xcerpt', 'publish', 'open', 
                    'closed', '', '$post_name_size', '', '', '$date', '$gmt_date', 0, $product_id, '$size_url', 0, 'product_variation', '', 0)"); 
                    $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_size)->where('post_type', 'product_variation')->first();
                    // dd($selectProduct);
                    $product_id_color = $selectProduct->ID;
                    $variations[] = $selectProduct->ID;
                    /// Step 5.2 Add Meta To Variation
                    $product_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_price', $product_price_egp)");
                    $product_regular_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_regular_price', $product_price_egp)");
                    $product_stock_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_stock_status', '$stock_status')");
                    $product_manage_stock = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_manage_stock', 'yes')");
                    $product_stock_qty = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_stock', '$stock_qty')");
                    $product_average_rating = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_wc_average_rating', 0)");
                    $product_wc_review_count = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_wc_review_count', 0)");
                    $product_total_sales = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, 'total_sales', 0)");      
                    $product_attribute_size = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, 'attribute_size', '$product_size')");    
                    $product_variation_description = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_variation_description', '')");
                    $product_tax_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_tax_status', 'taxable')");
                    $product_tax_class = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_tax_class', 'parent')");
                    $produc_thumbnail_id = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_thumbnail_id', '0')");
                    $produc_virtual = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_virtual', 'no')");
                    $produc_sold_individually = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_sold_individually', 'no')");
                    $produc_backorders = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_backorders', 'yes')");
                    $produc__download_limit = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_limit', '-1')");
                    $produc_download_expiry = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_expiry', '-1')");
                    $produc_downloadable = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_download_expiry', 'no')");
                    $product_version = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                    VALUES ($product_id_color, '_product_version', '4.6.0')");
                    
                }
                else
                {
                    $product_id_color =  $hhnm->ID;
                    $variations[] = $hhnm->ID;
                    $product_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_price'");
                    $product_regular_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_regular_price'");
                    $product_stock_status = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                    `meta_value` = '$stock_status' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock_status'");
                    $product_stock_qty = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                   `meta_value` = '$stock_qty' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock'");
                }
            }
        }
        else if(count($colors) > 0 && count($sizes) > 0)
        {
            for ($i = 0; $i < count($colors); $i++)
            {
                $product_color = $colors[$i];
                $product_color_slug = str_replace(' ', '-', strtolower($product_color));
                $product_color_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_color_slug)->first();
                if($product_color_selector === NULL)
                {
                    $insert_product_color = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
                    VALUES ('$product_color', '$product_color_slug')");
                    $product_color_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_color_slug)->first();
                    $product_color_id = $product_color_selector->term_id;
                    $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
                    `parent`, `count`) VALUES ($product_color_id, $product_color_id, 'pa_color', '', 0, 0)");
                }
                else
                {
                    $product_color_id = $product_color_selector->term_id;
                }
                $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_color_id)->first();
                if($term_checker === NULL)
                {
                    $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
                    VALUES ($product_id, $product_color_id)");
                }
                for ($y = 0; $y < count($sizes); $y++)
                {
                    $product_size = $sizes[$y];
                    $product_size_slug = str_replace(' ', '-', strtolower($product_size));
                    $product_size_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_size_slug)->first();
                    if($product_size_selector === NULL)
                    {
                        $insert_product_size = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
                        VALUES ('$product_size', '$product_size_slug')");
                        $product_size_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_size_slug)->first();
                        $product_size_id = $product_size_selector->term_id;
                        $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
                        `parent`, `count`) VALUES ($product_size_id, $product_size_id, 'pa_size', '', 0, 0)");
                    }
                    else
                    {
                        $product_size_id = $product_size_selector->term_id;
                    }
                    $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_size_id)->first();
                    if($term_checker === NULL)
                    {
                        $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
                        VALUES ($product_id, $product_size_id)");
                    }
                    // Step 5.1 Add Colors To Product
                    $product_title_color = $product_title." - ".$product_color.", ".$product_size;
                    $post_xcerpt = "Color: ".$product_color.", Size: ".$product_size;
                    $post_name_color = $post_name."-".$product_color_slug."-".$product_size_slug;
                    $color_url = "http://three-store.com/site1/?post_type=product_variation&p=".$product_id;
                    $hhnm = DB::connection('mysql2')->table('wp_thposts')->where('post_parent', $product_id)->where('post_excerpt', $post_xcerpt)->first();
                    if($hhnm === NULL)
                    {
                        $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                        `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                        `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                        `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title_color', '$post_xcerpt', 'publish', 'open', 
                        'closed', '', '$post_name_color', '', '', '$date', '$gmt_date', 0, $product_id, '$color_url', 0, 'product_variation', '', 0)"); 
                        $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_color)->where('post_type', 'product_variation')
                        ->where('post_parent', $product_id)->first();
                        // dd($selectProduct);
                        $product_id_color = $selectProduct->ID;
                        $variations[] = $selectProduct->ID;
                        /// Step 5.2 Add Meta To Variation
                        $product_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_price', $product_price_egp)");
                        $product_regular_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_regular_price', $product_price_egp)");
                        $product_stock_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_stock_status', '$stock_status')");
                        $product_manage_stock = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_manage_stock', 'yes')");
                        $product_stock_qty = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_stock', '$stock_qty')");
                        $product_average_rating = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_wc_average_rating', 0)");
                        $product_wc_review_count = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_wc_review_count', 0)");
                        $product_total_sales = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, 'total_sales', 0)");      
                        $product_attribute_color = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, 'attribute_color', '$product_color')");  
                        $product_attribute_size = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, 'attribute_size', '$product_size')");      
                        $product_variation_description = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_variation_description', '')");
                        $product_tax_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_tax_status', 'taxable')");
                        $product_tax_class = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_tax_class', 'parent')");
                        $produc_thumbnail_id = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_thumbnail_id', '0')");
                        $produc_virtual = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_virtual', 'no')");
                        $produc_sold_individually = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_sold_individually', 'no')");
                        $produc_backorders = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_backorders', 'yes')");
                        $produc__download_limit = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_download_limit', '-1')");
                        $produc_download_expiry = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_download_expiry', '-1')");
                        $produc_downloadable = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_download_expiry', 'no')");
                        $product_version = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
                        VALUES ($product_id_color, '_product_version', '4.6.0')");
                    }
                    else
                    {
                        $product_id_color =  $hhnm->ID;
                        $variations[] = $hhnm->ID;
                        
                        $selectProduct = DB::connection('mysql2')->unprepared("UPDATE `wp_thposts` SET `post_name` = '$post_name_color'
                        WHERE `ID` = $product_id_color");
                        $product_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                        `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_price'");
                        $product_regular_price = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                        `meta_value` = '$product_price_egp' WHERE `post_id`=$product_id_color AND  `meta_key`='_regular_price'");
                        $product_stock_status = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                        `meta_value` = '$stock_status' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock_status'");
                        $product_stock_qty = DB::connection('mysql2')->unprepared("UPDATE `wp_thpostmeta` SET 
                       `meta_value` = '$stock_qty' WHERE `post_id`=$product_id_color AND  `meta_key`='_stock'");
                       
                        $xasd = DB::connection('mysql2')->table('wp_thwc_product_meta_lookup')->where('product_id', $product_id_color)->first();
                        if($xasd === NULL)
                        {
                            $productmetalookup = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thwc_product_meta_lookup` (`product_id`, `sku`, `virtual`, 
                            `downloadable`, `min_price`, `max_price`, `onsale`, `stock_quantity`, `stock_status`, `rating_count`, `average_rating`, 
                            `total_sales`, `tax_status`, `tax_class`) VALUES ($product_id_color, '', 0, 0, $product_price_egp, $product_price_egp, 
                            0, $stock_qty, '$stock_status', 0, 0, 0, 'taxable', 'parent')");
                        }
                        else
                        {
                            $productmetalookup = DB::connection('mysql2')->unprepared("UPDATE `wp_thwc_product_meta_lookup` SET `min_price`=$product_price_egp, 
                            `max_price`=$product_price_egp, `stock_quantity`=$stock_qty, `stock_status`='$stock_status' WHERE `product_id` = $product_id_color");
                        }
                    }
                }
            }
        }
        // for ($i = 0; $i < count($variations); $i++)
        // {
        //     $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('ID', $variations[$i])->first();
        //     print_r($selectProduct);
        //     echo '<hr />';
        // }
        DB::connection('mysql2')->table("wp_thposts")->whereNotIn('ID', $variations)->where("post_parent", $product_id)->
        where("post_type", 'product_variation')->update(['post_status'=>'trash']);
        //dd('END');
        return true;
        
    }
}