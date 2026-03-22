<?php

/**
 * Initialise Theme Classes
 */
$files = ( defined( 'WP_DEBUG' ) AND WP_DEBUG ) ? glob( __DIR__ . '/_functions/*.php', GLOB_ERR ) : glob( __DIR__ . '/_functions/*.php' );
foreach ( $files as $file ) : include $file; endforeach;

/**
 * Initialise Commerce Based Class Features
 */
$files = ( defined( 'WP_DEBUG' ) AND WP_DEBUG ) ? glob( __DIR__ . '/_functions/commerce/*.php', GLOB_ERR ) : glob( __DIR__ . '/_functions/commerce/*.php' );
foreach ( $files as $file ) : include $file; endforeach;