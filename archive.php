<?php

/**
 * Fallback archive template for post archives.
 *
 * @package CyanTheme
 * @see https://developer.wordpress.org/themes/templates/template-hierarchy/#archive-hierarchy
 */

defined('ABSPATH') || exit;

use Cyan\Theme\Helpers\Templates;

get_header();

Templates::getPart('blog/archive-layout');

get_footer();
