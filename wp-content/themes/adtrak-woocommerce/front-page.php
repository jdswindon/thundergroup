<?php

use Timber\Timber;

$context = Timber::context();

// Get the current post
$timber_post = Timber::get_post();
$context['post'] = $timber_post;

Timber::render(['front-page.twig', 'page.twig'],$context);