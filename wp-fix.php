<?php
/**
 * WordPress compatibility fixes for PHP 8.0+
 */

// Fix for get_magic_quotes_gpc()
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        return false;
    }
}

// Fix for nav-menu.php array to string conversion error
add_action('init', function() {
    if (function_exists('wp_get_nav_menu_items')) {
        $original_function = 'wp_get_nav_menu_items';
        $function_code = '
            function wp_get_nav_menu_items($menu, $args = array()) {
                $menu = wp_get_nav_menu_object($menu);
                
                if (!$menu) {
                    return false;
                }
                
                static $fetched = array();
                
                $items = get_objects_in_term($menu->term_id, "nav_menu");
                
                if (is_wp_error($items)) {
                    return false;
                }
                
                $defaults = array(
                    "order" => "ASC",
                    "orderby" => "menu_order",
                    "post_type" => "nav_menu_item",
                    "post_status" => "publish",
                    "output" => ARRAY_A,
                    "output_key" => "menu_order",
                    "nopaging" => true,
                );
                
                $args = wp_parse_args($args, $defaults);
                
                if (count($items) > 0) {
                    $args = array_merge(
                        $args,
                        array(
                            "include" => $items,
                            "post_type" => "nav_menu_item",
                        )
                    );
                    
                    $items = get_posts($args);
                    
                    if (is_array($items)) {
                        foreach ($items as $key => $item) {
                            $items[$key]->db_id = $item->ID;
                            $items[$key]->menu_item_parent = get_post_meta(
                                $item->ID,
                                "_menu_item_menu_item_parent",
                                true
                            );
                            $items[$key]->object_id = get_post_meta(
                                $item->ID,
                                "_menu_item_object_id",
                                true
                            );
                            $items[$key]->object = get_post_meta(
                                $item->ID,
                                "_menu_item_object",
                                true
                            );
                            $items[$key]->type = get_post_meta(
                                $item->ID,
                                "_menu_item_type",
                                true
                            );
                            
                            if ("post_type" === $items[$key]->type) {
                                $object = get_post_type_object($items[$key]->object);
                                if ($object) {
                                    $items[$key]->url = get_permalink($items[$key]->object_id);
                                    $items[$key]->title = $item->post_title;
                                }
                            } elseif ("taxonomy" === $items[$key]->type) {
                                $object = get_taxonomy($items[$key]->object);
                                if ($object) {
                                    $items[$key]->url = get_term_link(
                                        (int) $items[$key]->object_id,
                                        $items[$key]->object
                                    );
                                    $items[$key]->title = $item->post_title;
                                }
                            } elseif ("custom" === $items[$key]->type) {
                                $items[$key]->url = get_post_meta(
                                    $item->ID,
                                    "_menu_item_url",
                                    true
                                );
                                $items[$key]->title = $item->post_title;
                            }
                            
                            $items[$key]->target = get_post_meta(
                                $item->ID,
                                "_menu_item_target",
                                true
                            );
                            
                            $items[$key]->classes = array();
                            $classes = get_post_meta(
                                $item->ID,
                                "_menu_item_classes",
                                true
                            );
                            if (is_array($classes)) {
                                $items[$key]->classes = $classes;
                            }
                            
                            $items[$key]->xfn = get_post_meta(
                                $item->ID,
                                "_menu_item_xfn",
                                true
                            );
                            
                            $items[$key]->description = $item->post_content;
                            
                            $items[$key]->attr_title = $item->post_excerpt;
                        }
                    }
                }
                
                return apply_filters("wp_get_nav_menu_items", $items, $menu, $args);
            }
        ';
        
        // Only replace the function if we're on PHP 8.0+
        if (version_compare(PHP_VERSION, "8.0.0", ">=")) {
            runkit_function_remove($original_function);
            eval($function_code);
        }
    }
}, 0);
