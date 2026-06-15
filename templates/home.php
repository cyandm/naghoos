<?php /* Template Name: Home */ ?>

<?php

use Cyan\Theme\Helpers\Templates;

get_header(); ?>

<main class="relative">
    <?php Templates::getPart('home/slider'); ?>

</main>

<?php get_footer(); ?>