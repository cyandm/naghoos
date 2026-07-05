<?php

use Cyan\Theme\Helpers\Templates;


if (function_exists('is_account_page') && is_account_page()) {
    get_header('', ['header_type' => 'dashboard']);
} else {
    get_header();
}
?>

<?php
if (function_exists('is_cart') && (is_cart() || is_checkout() || is_wc_endpoint_url('order-received'))) {
    Templates::getPart('breadcrumb');
}
?>

<main class="container">
    <?php the_content(); ?>
</main>

<?php
get_footer();
?>