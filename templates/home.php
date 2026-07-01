<?php /* Template Name: Home */ ?>

<?php

use Cyan\Theme\Helpers\Templates;

get_header(); ?>

<main class="relative">
    <?php Templates::getPart('home/slider'); ?>
    <?php Templates::getPart('home/best-selling'); ?>
    <?php Templates::getPart('home/discounted'); ?>
    <?php Templates::getPart('home/show-categories'); ?>
    <?php Templates::getPart('home/show-product-with-cat'); ?>
    <?php Templates::getPart('home/faq'); ?>
    <?php Templates::getPart('home/personnel'); ?>
    <?php Templates::getPart('home/testimonials'); ?>
    <?php Templates::getPart('home/history'); ?>
    <?php Templates::getPart('home/attributes'); ?>
</main>

<?php get_footer(); ?>