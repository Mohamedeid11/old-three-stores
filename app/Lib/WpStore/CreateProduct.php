<?php
use App\Product;
use App\Category;

/// Add Product To WP
if(!function_exists('add_product_to_wp'))
{
    function add_product_to_wp ($product_id)
    {
        $product = Product::findorfail($product_id);
        if(count($product->colors) == 0 && count($product->sizes) == 0)
        {
            add_simple_product_to_wp($product_id);
        }
        else
        {
            add_variable_product_to_wp($product_id);
        }
        return true;
    }
}

/// Add Variable Product
if(!function_exists('add_variable_product_to_wp'))
{
    function add_variable_product_to_wp ($product_id)
    {
        $product_system_info = Product::findorfail($product_id);
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
        $date = date('Y-m-d H:i:s');
        $gmt_date = date('Y-m-d H:i:s', strtotime('now') - 7200);
        $product_title = $product_system_info->title;
        $lower_title = strtolower($product_title);
        $someModel = DB::connection('mysql2')->table("wp_thposts")->whereRaw("lower(`post_title`) = '$lower_title'")->count();
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
        /// Step 1. Add Product
        $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
        `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
        `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
        `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title', '', 'publish', 'open', 
        'closed', '', '$post_name', '', '', '$date', '$gmt_date', 0, 0, '$url', 0, 'product', '', 0)");
        $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name)->first();
        // dd($selectProduct);
        $product_id = $selectProduct->ID;
        $product_system_info->woocommerce = $product_id;
        $product_system_info->save();
        $product_url = $selectProduct->guid;
        $product_price_egp = $product_system_info->price;
        $stock_qty = 0;
        $stock_status = 'outofstock';
        $product_cat = $product_system_info->cat_info->title;
        $product_cat_slug = str_replace(' ', '-', strtolower($product_cat));
        /// Step 2. Add Product Meta
        $product_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_price', $product_price_egp)");
        $product_stock_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_stock_status', '$stock_status')");
        $product_manage_stock = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_manage_stock', 'yes')");
        $product_stock_qty = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_stock', '$stock_qty')");
        $product_average_rating = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_wc_average_rating', 0)");
        $product_wc_review_count = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_wc_review_count', 0)");
        $product_total_sales = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, 'total_sales', 0)");
        $product_version = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_product_version', '4.6.0')");
        /// Product Attributes
        //a:2:{s:5:"color";a:6:{s:4:"name";s:5:"Color";s:5:"value";s:42:"Black | gray | Havan | Pink | Ultra yellow";s:8:"position";i:1;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:0;}s:4:"size";a:6:{s:4:"name";s:4:"Size";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:0;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:0;}}
        $product_attrs = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_product_attributes', '$product_attributes')");
        /// Step 3. Add Product Category
        $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
        if($product_cat_selector === NULL)
        {
            $insert_product_cat = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
            VALUES ('$product_cat', '$product_cat_slug')");
            $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
            $product_cat_id = $product_cat_selector->term_id;
            $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
            `parent`, `count`) VALUES ($product_cat_id, $product_cat_id, 'product_cat', '', 0, 0)");
        }
        // dd($product_cat_selector);
        $product_cat_id = $product_cat_selector->term_id;
        $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_cat_id)->first();
        if($term_checker === NULL)
        {
            $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
            VALUES ($product_id, $product_cat_id)");
        }
        /// Step 3.1 Make Product Variable
        $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
        VALUES ($product_id, 7)");
        /// Step 4. Add Product Meta Lookup
        $productmetalookup = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thwc_product_meta_lookup` (`product_id`, `sku`, `virtual`, 
        `downloadable`, `min_price`, `max_price`, `onsale`, `stock_quantity`, `stock_status`, `rating_count`, `average_rating`, 
        `total_sales`, `tax_status`, `tax_class`) VALUES ($product_id, '', 0, 0, $product_price_egp, $product_price_egp, 0, $stock_qty, '$stock_status',
        0, 0, 0, 'taxable', '')");

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
                $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title_color', '$post_xcerpt', 'publish', 'open', 
                'closed', '', '$post_name_color', '', '', '$date', '$gmt_date', 0, $product_id, '$color_url', 0, 'product_variation', '', 0)"); 
                $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_color)->where('post_type', 'product_variation')->first();
                // dd($selectProduct);
                $product_id_color = $selectProduct->ID;
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
                $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title_size', '$post_xcerpt', 'publish', 'open', 
                'closed', '', '$post_name_size', '', '', '$date', '$gmt_date', 0, $product_id, '$size_url', 0, 'product_variation', '', 0)"); 
                $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_size)->where('post_type', 'product_variation')->first();
                // dd($selectProduct);
                $product_id_color = $selectProduct->ID;
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
                    $product_title_color = $product_title." - ".$product_color." - ".$product_size;
                    $post_xcerpt = "Color: ".$product_color.", Size: ".$product_size;
                    $post_name_color = $post_name."-".$product_color_slug."-".$product_size_slug;
                    $color_url = "http://three-store.com/site1/?post_type=product_variation&p=".$product_id;
                    $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
                    `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
                    `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
                    `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title_color', '$post_xcerpt', 'publish', 'open', 
                    'closed', '', '$post_name_color', '', '', '$date', '$gmt_date', 0, $product_id, '$color_url', 0, 'product_variation', '', 0)"); 
                    $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name_color)->where('post_type', 'product_variation')->first();
                    // dd($selectProduct);
                    $product_id_color = $selectProduct->ID;
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
                }
            }
        }
    }
}

/// Add Simple Product
if(!function_exists('add_simple_product_to_wp'))
{
    function add_simple_product_to_wp ($product_id)
    {
        $product_system_info = Product::findorfail($product_id);
        $date = date('Y-m-d H:i:s');
        $gmt_date = date('Y-m-d H:i:s', strtotime('now') - 7200);
        $product_title = $product_system_info->title;
        $lower_title = strtolower($product_title);
        $someModel = DB::connection('mysql2')->table("wp_thposts")->whereRaw("lower(`post_title`) = '$lower_title'")->count();
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
        /// Step 1. Add Product
        $insertProduct = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thposts`(`post_author`, `post_date`, `post_date_gmt`, 
        `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, 
        `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, 
        `post_type`, `post_mime_type`, `comment_count`) VALUES (1, '$date', '$gmt_date', '', '$product_title', '', 'publish', 'open', 
        'closed', '', '$post_name', '', '', '$date', '$gmt_date', 0, 0, '$url', 0, 'product', '', 0)");
        $selectProduct = DB::connection('mysql2')->table('wp_thposts')->where('post_name', $post_name)->first();
        // dd($selectProduct);
        $product_id = $selectProduct->ID;
        $product_system_info->woocommerce = $product_id;
        $product_system_info->save();
        $product_url = $selectProduct->guid;
        $product_price = $product_system_info->price;
        $stock_qty = 0;
        $stock_status = 'outofstock';
        $product_cat = $product_system_info->cat_info->title;
        $product_cat_slug = str_replace(' ', '-', strtolower($product_cat));
        /// Step 2. Add Product Meta
        $product_price = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_price', $product_price)");
        $product_stock_status = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_stock_status', '$stock_status')");
        $product_manage_stock = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_manage_stock', 'yes')");
        $product_stock_qty = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_stock', '$stock_qty')");
        $product_average_rating = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_wc_average_rating', 0)");
        $product_wc_review_count = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, '_wc_review_count', 0)");
        $product_total_sales = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thpostmeta` (`post_id`, `meta_key`, `meta_value`) 
        VALUES ($product_id, 'total_sales', 0)");
        /// Step 3. Add Product Category
        $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
        if($product_cat_selector === NULL)
        {
            $insert_product_cat = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterms` (`name`, `slug`) 
            VALUES ('$product_cat', '$product_cat_slug')");
            $product_cat_selector =  DB::connection('mysql2')->table('wp_thterms')->where('slug', $product_cat_slug)->first();
            $product_cat_id = $product_cat_selector->term_id;
            $aan = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_taxonomy`(`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, 
            `parent`, `count`) VALUES ($product_cat_id, $product_cat_id, 'product_cat', '', 0, 0)");
        }
        // dd($product_cat_selector);
        $product_cat_id = $product_cat_selector->term_id;
        $term_checker = DB::connection('mysql2')->table('wp_thterm_relationships')->where('object_id', $product_id)->where('term_taxonomy_id', $product_cat_id)->first();
        if($term_checker === NULL)
        {
            $term_taxonomy = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thterm_relationships` (`object_id`, `term_taxonomy_id`) 
            VALUES ($product_id, $product_cat_id)");
        }
        /// Step 4. Add Product Meta Lookup
        $productmetalookup = DB::connection('mysql2')->unprepared("INSERT INTO `wp_thwc_product_meta_lookup` (`product_id`, `sku`, `virtual`, 
        `downloadable`, `min_price`, `max_price`, `onsale`, `stock_quantity`, `stock_status`, `rating_count`, `average_rating`, 
        `total_sales`, `tax_status`, `tax_class`) VALUES ($product_id, '', 0, 0, $product_price, $product_price, 0, $stock_qty, '$stock_status',
        0, 0, 0, 'taxable', '')");
        return true;
    }
}
?>