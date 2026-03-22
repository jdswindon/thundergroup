<?php
/**
 * TVC Taxonomies Class.
 *
 * @package TVC Product Feed Manager/Data/Classes
 * @version 5.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'TVC_Taxonomies' ) ) :

    /**
     * Taxonomies Class
     */
    class TVC_Taxonomies {
        public static function get_shop_categories_list() {
            $args = array(
                'hide_empty'   => 1,
                'taxonomy'     => 'product_cat',
                'hierarchical' => 1,
                'orderby'      => 'name',
                'order'        => 'ASC',
                'exclude'      => apply_filters( 'tvc_category_mapping_exclude', array() ),
                'exclude_tree' => apply_filters( 'tvc_category_mapping_exclude_tree', array() ),
                'number'       => absint( apply_filters( 'tvc_category_mapping_max_categories', 0 ) ),
                'child_of'     => 0,
            );

            $args = apply_filters( 'tvc_category_mapping_args', $args );

            return self::get_cat_hierarchy( 0, $args );
        }

        private static function get_cat_hierarchy( $parent, $args ) {
            $cats = get_categories( $args );
            $ret  = new stdClass;

            foreach ( $cats as $cat ) {
                if ( $cat->parent == $parent ) {
                    $id                 = $cat->cat_ID;
                    $ret->$id           = $cat;
                    $ret->$id->children = self::get_cat_hierarchy( $id, $args );
                }
            }
            return $ret;
        }
    }
    // end of TVC_Taxonomies_Class

endif;