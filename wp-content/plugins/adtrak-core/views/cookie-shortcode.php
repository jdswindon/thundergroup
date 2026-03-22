<?php

$url = '/privacy-policy';

$CPquery = new \WP_Query(
	array(
		'post_type'              => 'page',
		'title'                  => 'Cookie Policy',
		'post_status'            => 'all',
		'posts_per_page'         => 1,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'orderby'                => 'post_date ID',
		'order'                  => 'ASC',
	)
);
	
if ( ! empty( $CPquery->post ) ) {
	$cookiePage = $CPquery->post;
} else {
	$cookiePage = null;
}

if($cookiePage != null) {
	$cookieStatus = get_post_status($cookiePage->ID);

	if($cookieStatus == 'publish') {
		$url = '/cookie-policy';
	}
}
?>
<div id="wp-notification" class="closed">
	<div class="wp-notification-container">
		<p>This website uses cookies to enhance your browsing experience and deliver personalised ads. By clicking “Accept All Cookies”, you agree to the storing of cookies on your device to enhance site navigation, analyse site usage, and assist in our marketing efforts.</p>
		<div>
			<a href="<?= site_url($url) ?>/">More Information</a>
			<span id="wp-notification-toggle">Accept All Cookies</span>
		</div>
	</div>
</div>