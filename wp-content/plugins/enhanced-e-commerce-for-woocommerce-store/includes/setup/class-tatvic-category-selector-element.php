<?php

/**
 * TVC Category Selector Element Class.
 */
if (! defined('ABSPATH')) {
	exit;
}

if (! class_exists('Tatvic_Category_Selector_Element')) :
	class Tatvic_Category_Selector_Element
	{
		/**
		 * Returns the code for a single row meant for the category mapping table.
		 *
		 * @param object    $category           object containing data of the active category like term_id and name
		 * @param string    $category_children  a string with the children of the active category
		 * @param string    $level_indicator    current active level
		 * @param string    $mode               defines if the category mapping row should contain a description (normal) or a catgory mapping (mapping) column
		 *
		 * @return string
		 */


		public static function category_mapping_row($category, $level_indicator, $mode, $ee_prod_mapped_cats, $option)
		{
			$category_row_class = 'mapping' === $mode ? 'tvc-category-mapping-selector' : 'tvc-category-selector';
			$mode_column  = 'mapping' === $mode
				? self::category_mapping_selector('catmap', $category->term_id, true, $ee_prod_mapped_cats, $option)
				: self::category_description_data_item($category->termid);

			// Compute depth from level indicator (number of em dashes)
			$depth = 0;
			if (!empty($level_indicator)) {
				$depth = substr_count($level_indicator, '—');
			}

			// Determine if this category has children
			$has_children = false;
			$children_args = array(
				'taxonomy' => 'product_cat',
				'parent' => (int) $category->term_id,
				'hide_empty' => false,
				'number' => 1,
			);
			$children = get_terms($children_args);
			if (!is_wp_error($children) && !empty($children)) {
				$has_children = true;
			}

			// Build action buttons
			$is_sub_category = !empty(trim($level_indicator));
			$buttons_html = '';
			// New copy-to-children button: shown only when this category has children
			if ($has_children) {
				$buttons_html .= '<button type="button"
								class="btn btn-sm copy-children"
								data-term-id="' . esc_attr($category->term_id) . '"
								title="Copy this mapping only to direct and nested child categories"
								style="min-width: 32px; width: 32px; height: 32px; flex-shrink: 0;">
									<span class="dashicons dashicons-arrow-down"></span>
								</button>';
			}
			// Existing copy-down button: only on top-level to preserve prior behavior
			if (! $is_sub_category) {
				$buttons_html .= '<button type="button" 
								class="btn btn-sm copy-down" 
								data-term-id="' . esc_attr($category->term_id) . '" 
								title="Copy this mapping to all below categories"
								style="min-width: 32px; width: 32px; height: 32px; flex-shrink: 0;">
									<span class="dashicons dashicons-arrow-down-alt"></span>
								</button>';
			}

			// Wrap buttons in a fixed-width container so select retains consistent width
			$buttons_html = '<div class="mapping-actions" style="display:flex; gap:8px; flex:0 0 80px; max-width:80px; justify-content:flex-end;">' . $buttons_html . '</div>';

			return '<div class="row catTermId termId_' . esc_attr($category->term_id) . '" data-term-id="' . esc_attr($category->term_id) . '" data-depth="' . esc_attr($depth) . '">
				<div class="col-6 mt-2">
					<div class="form-group shop-category d-flex align-items-center justify-content-between">
						<label class="form-label-control font-weight-400 text-color" style="font-size:17px;">
							' . esc_html($category->name) . ' 
							<small>(' . esc_html($category->count) . ')</small> 
							' . esc_html($level_indicator) . '
						</label>
					</div>
				</div>
				<div class="col-6 mt-2">
					<div class="form-group d-flex align-items-center" style="gap: 8px;">
						<div id="feed-category-' . esc_attr($category->term_id) . '" 
							 style="flex: 1; min-width: 0;">
						' . str_replace('<select', '<select style="max-width: 100%; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"', $mode_column) . '
						</div>
						' . $buttons_html . '
					</div>
				</div>
			</div>';
		}


		/**
		 * Returns the code for a category input selector.
		 *
		 * @param string    $identifier     identifier for the selector
		 * @param string    $id             id of the selector
		 * @param boolean   $start_visible  should this selector start visible
		 *
		 * @return string
		 */
		public static function category_mapping_selector($identifier, $id, $start_visible, $ee_prod_mapped_cats, $option)
		{
			$display         = $start_visible ? 'initial' : 'none';
			$ident           = '-1' !== $id ? $identifier . '-' . $id : $identifier;
			$category_levels = apply_filters('tvc_category_selector_level', 6);
			$id = esc_attr($id);
			if (isset($ee_prod_mapped_cats[$id]['id']) && isset($ee_prod_mapped_cats[$id]['name']) && $ee_prod_mapped_cats[$id]['id'] && $ee_prod_mapped_cats[$id]['name']) {

				$cat_id = esc_attr($ee_prod_mapped_cats[$id]['id']);
				$cat_name = esc_attr($ee_prod_mapped_cats[$id]['name']);
				$html_code  = '<div id="category-selector-' . esc_attr($ident) . '" style="display:' . esc_attr($display) . '">
					<div id="selected-categories">
					<input type="hidden" name="category-' . esc_attr($id) . '" id="category-' . esc_attr($id) . '" value="' . esc_attr($cat_id) . '">
					<input type="hidden" name="category-name-' . esc_attr($id) . '" id="category-name-' . esc_attr($id) . '" value="' . esc_attr($cat_name) . '">
					</div>
					
					<select class="form-control categorySelect" id="' . esc_attr($ident) . '_0" catId="' . esc_attr($id) . '" iscategory="false" style="width:100%" onchange="selectSubCategory(this)">
						<option value="' . esc_attr($cat_id) . '">' . esc_attr($cat_name) . '</option>
					</select>';
				// for ( $i = 1; $i < $category_levels; $i ++ ) {
				// 	$html_code .= '<select class="" id="' . esc_attr($ident) . '_' . esc_attr($i) . '" value="0" catId="'.esc_attr($id).'" style="display:none;" onchange="selectSubCategory(this)"></select>';
				// }
			} else {
				$html_code  = '<div id="category-selector-' . esc_attr($ident) . '" style="display:' . esc_attr($display) . '">
					<div id="selected-categories">
					<input type="hidden" name="category-' . esc_attr($id) . '" id="category-' . esc_attr($id) . '" value="">
					<input type="hidden" name="category-name-' . esc_attr($id) . '" id="category-name-' . esc_attr($id) . '" value="">
					</div>
					<select style="width:100%" class="category_mapping_select form-control select2 categorySelect" id="' . esc_attr($ident) . '_0" catId="' . esc_attr($id) . '" onchange="selectSubCategory(this)" iscategory="false">
					' . $option . '
					</select>';

				// for ( $i = 1; $i < $category_levels; $i ++ ) {
				// 	$html_code .= '<select style="width:100%" class="" id="' . esc_attr($ident) . '_' . esc_attr($i) . '" value="0" catId="'.esc_attr($id).'" style="display:none;" onchange="selectSubCategory(this)"></select>';
				// }

			}

			$html_code .= '</div>';
			return $html_code;
		}

		/**
		 * Returns the code for the category description column.
		 *
		 * @param string    $category_id
		 *
		 * @return string
		 */
		private static function category_description_data_item($category_id)
		{
			$category_description = '' !== category_description($category_id) ? category_description($category_id) : '—';

			$html_code = '<span aria-hidden="true">' . wp_kses_post($category_description) . '</span>';

			return $html_code;
		}
	}
// end of TVC_Category_Selector_Element class
endif;
