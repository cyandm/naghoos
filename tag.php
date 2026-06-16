<?php

/**
 * Blog tag archive.
 *
 * @package CyanTheme
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Templates;

get_header();

Templates::getPart('blog/archive-layout');

get_footer();
