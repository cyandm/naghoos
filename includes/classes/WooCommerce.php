<?php

/**
 * WooCommerce Customizations
 * Custom modifications for WooCommerce checkout and other features
 * @package Cyan\Theme\Classes
 */

namespace Cyan\Theme\Classes;

use Cyan\Theme\Helpers\Templates;

class WooCommerce
{
    /**
     * User meta key used to persist wishlist product IDs.
     *
     * @var string
     */
    private const WISHLIST_META_KEY = '_cyn_wishlist_product_ids';

    /**
     * Query-string action for removing a product from wishlist.
     *
     * @var string
     */
    private const WISHLIST_REMOVE_ACTION = 'cyn_wishlist_remove';

    /**
     * Nonce action used for wishlist remove links.
     *
     * @var string
     */
    private const WISHLIST_REMOVE_NONCE_ACTION = 'cyn_wishlist_remove_item';
    private const WISHLIST_TOGGLE_ACTION = 'cyn_wishlist_toggle';
    private const WISHLIST_TOGGLE_NONCE_ACTION = 'cyn_wishlist_toggle_item';

    public static function init()
    {
        // Remove specific checkout fields
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'removeCheckoutFields'], 9999);

        // Make phone field required
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'makePhoneRequired'], 9999);

        // Add placeholders to fields
        add_filter('woocommerce_checkout_fields', [__CLASS__, 'addPlaceholders'], 9999);

        // Customize coupon success messages
        add_filter('woocommerce_coupon_message', [__CLASS__, 'customizeCouponMessages'], 10, 3);

        // Customize coupon error messages
        add_filter('woocommerce_coupon_error', [__CLASS__, 'customizeCouponErrors'], 10, 3);

        // Remove default WooCommerce breadcrumb
        self::removeBreadcrumb();

        // Filter "in stock only" on shop archive
        add_action('woocommerce_product_query', [__CLASS__, 'filterByInstock']);
        // Filter by age via URL query param
        add_action('woocommerce_product_query', [__CLASS__, 'filterByTaxonomyQueryVars'], 20);
        // orderby=sale: only sale products + sort by date
        add_filter('woocommerce_get_catalog_ordering_args', [__CLASS__, 'catalogOrderBySale'], 10, 3);
        add_action('woocommerce_product_query', [__CLASS__, 'filterByOrderbySale'], 25);
        add_filter('woocommerce_catalog_orderby', [__CLASS__, 'catalogOrderbyWithSale']);
        add_filter('woocommerce_catalog_orderby', [__CLASS__, 'catalogOrderbyRemoveOptions'], 20);

        add_filter('loop_shop_per_page', [__CLASS__, 'shopPerPage'], 20);

        add_action('widgets_init', [__CLASS__, 'registerShopSidebar']);

        self::archiveProduct();

        // Cart page: custom layout (totals output in cart.php), custom proceed button
        remove_action('woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10);
        remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
        add_action('woocommerce_proceed_to_checkout', [__CLASS__, 'cartProceedButton'], 20);
        add_filter('woocommerce_product_cross_sells_products_heading', [__CLASS__, 'crossSellsHeading']);

        // Remove "Your cart is currently empty" default message
        remove_action('woocommerce_cart_is_empty', 'wc_empty_cart_message', 10);

        add_action('template_redirect', [__CLASS__, 'redirectAccountAliasToMyAccount'], 1);
        add_filter('wp_redirect', [__CLASS__, 'maybeRedirectAccountSaveToDashboard'], 10, 2);
        add_action('init', [__CLASS__, 'registerAccountWishlistEndpoint']);
        add_action('init', [__CLASS__, 'registerAccountSupportEndpoint']);
        add_action('template_redirect', [__CLASS__, 'maybeHandleWishlistToggle'], 8);
        add_action('template_redirect', [__CLASS__, 'maybeHandleWishlistRemove'], 9);
        add_action('woocommerce_account_wishlist_endpoint', [__CLASS__, 'renderWishlistEndpointContent']);
        add_action('woocommerce_account_support_endpoint', [__CLASS__, 'renderSupportEndpointContent']);

        add_filter('woocommerce_default_address_fields', [__CLASS__, 'addAddressPlaqueAndUnitFields']);
        add_filter('woocommerce_my_account_my_address_formatted_address', [__CLASS__, 'appendAddressPlaqueAndUnit'], 10, 3);
        add_filter('woocommerce_localisation_address_formats', [__CLASS__, 'localisationAddressFormats']);
        add_filter('woocommerce_formatted_address_replacements', [__CLASS__, 'formattedAddressPlaqueAndUnitReplacements'], 10, 2);
        add_filter('woocommerce_order_formatted_billing_address', [__CLASS__, 'appendOrderBillingPlaqueAndUnit'], 10, 2);
        add_filter('woocommerce_order_formatted_shipping_address', [__CLASS__, 'appendOrderShippingPlaqueAndUnit'], 10, 2);

        add_action('template_redirect', [__CLASS__, 'ensureMobileCustomerAccountSetup'], 5);
        add_filter('woocommerce_save_account_details_required_fields', [__CLASS__, 'removeEmailFromAccountRequiredFields']);
        add_filter('woocommerce_billing_fields', [__CLASS__, 'customizeAccountBillingEmailField'], 9999, 2);
        add_filter('woocommerce_process_myaccount_field_billing_email', [__CLASS__, 'fillBillingEmailForMobileCustomer']);

        // Cart AJAX: enqueue params + handlers
        add_action('wp_head', [__CLASS__, 'addCartAjaxParams']);
        add_action('wp_ajax_update_cart_quantity', [__CLASS__, 'ajaxUpdateCartQuantity']);
        add_action('wp_ajax_nopriv_update_cart_quantity', [__CLASS__, 'ajaxUpdateCartQuantity']);

        // Delete coupon AJAX
        add_action('wp_ajax_delete_coupon_code', [__CLASS__, 'ajaxDeleteCoupon']);
        add_action('wp_ajax_nopriv_delete_coupon_code', [__CLASS__, 'ajaxDeleteCoupon']);
    }

    /**
     * Enqueue AJAX parameters for cart page (inline script in <head>)
     */
    public static function addCartAjaxParams()
    {
        if (!is_cart()) {
            return;
        }
?>
        <script type="text/javascript">
            var cart_ajax_params = {
                ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
                nonce: '<?php echo wp_create_nonce('cart_update_nonce'); ?>'
            };
        </script>
    <?php
    }

    public static function getProductDiscountPercent($product)
    {
        if (!$product instanceof \WC_Product) {
            return false;
        }

        $regular = (float) $product->get_regular_price();
        $sale    = (float) $product->get_sale_price();

        if ($sale && $regular > 0 && $regular > $sale) {
            return round((($regular - $sale) / $regular) * 100);
        }

        return false;
    }

    /**
     * True when product is out of stock.
     */
    public static function isProductFullyOutOfStock(\WC_Product $product): bool
    {
        return ! $product->is_in_stock();
    }

    public static function getProductPrices(\WC_Product $product)
    {
        $data = [
            'regular_price' => 0,
            'sale_price'    => 0,
            'final_price'   => 0,
            'has_discount'  => false,
        ];

        if (! $product) {
            return $data;
        }

        $regular = (float) $product->get_regular_price();
        $sale    = (float) $product->get_sale_price();
        $price   = (float) $product->get_price();

        $data['regular_price'] = $regular;
        $data['sale_price']    = $sale;
        $data['final_price']   = $price;
        $data['has_discount']  = ($sale > 0 && $sale < $regular);

        return $data;
    }

    /**
     * AJAX handler for updating cart quantity
     */
    public static function ajaxUpdateCartQuantity()
    {
        // Security check
        if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'cart_update_nonce')) {
            wp_send_json_error(array('message' => 'خطای امنیتی'));
            return;
        }

        if (!isset($_POST['cart_key']) || !isset($_POST['quantity'])) {
            wp_send_json_error(array('message' => 'داده‌های ناقص'));
            return;
        }

        $cart_key = sanitize_text_field($_POST['cart_key']);
        $quantity = intval($_POST['quantity']);

        // Get cart
        $cart = WC()->cart;

        if ($quantity === 0) {
            // Remove item
            $cart->remove_cart_item($cart_key);

            wp_send_json_success(array(
                'removed' => true,
                'cart_subtotal' => WC()->cart->get_cart_subtotal(),
                'cart_total' => WC()->cart->get_total(),
            ));
            return;
        }

        // Update quantity
        $cart->set_quantity($cart_key, $quantity, true);
        $cart->calculate_totals();

        // Get updated values
        $cart_item = $cart->get_cart_item($cart_key);
        $_product = $cart_item ? $cart_item['data'] : null;

        $response = array(
            'cart_subtotal' => WC()->cart->get_cart_subtotal(),
            'cart_total' => WC()->cart->get_total(),
            'item_subtotal' => $_product ? WC()->cart->get_product_subtotal($_product, $quantity) : '',
            'cart_saving'   => wc_price(self::cyn_get_cart_special_price_saving()),
        );

        wp_send_json_success($response);
    }

    /**
     * Products per page on shop archive (enables pagination when more than one page).
     *
     * @return int
     */
    public static function shopPerPage()
    {
        return 20;
    }

    /**
     * Related product IDs for single product: ACF picks first, then smart fallback.
     *
     * @param list<int> $exclude_ids Additional product IDs to exclude (e.g. cart items).
     * @return list<int>
     */
    public static function getRelatedProductIds(int $product_id, int $limit = 12, array $exclude_ids = []): array
    {
        $exclude = array_values(array_unique(array_merge(
            [$product_id],
            array_map('intval', $exclude_ids)
        )));

        $acf_ids = self::getAcfRelatedProductIds($product_id);
        $acf_ids = array_values(array_filter(
            $acf_ids,
            static function (int $id) use ($exclude): bool {
                return ! in_array($id, $exclude, true);
            }
        ));

        if (count($acf_ids) >= $limit) {
            return array_slice($acf_ids, 0, $limit);
        }

        $ids = $acf_ids;
        $current_title = get_the_title($product_id);
        $product_categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
        $product_tags = wp_get_post_terms($product_id, 'product_tag', ['fields' => 'ids']);
        $not_in = static function () use ($exclude, $ids): array {
            return array_values(array_unique(array_merge($exclude, $ids)));
        };

        if (count($ids) < $limit) {
            $similar_name_query = new \WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => $limit - count($ids),
                'post__not_in'   => $not_in(),
                's'              => $current_title,
                'fields'         => 'ids',
            ]);

            if ($similar_name_query->have_posts()) {
                $ids = self::mergeRelatedProductIds($ids, $similar_name_query->posts);
            }
        }

        if (count($ids) < 4 && ! empty($product_categories)) {
            $category_query = new \WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => $limit - count($ids),
                'post__not_in'   => $not_in(),
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $product_categories,
                    ],
                ],
                'fields' => 'ids',
            ]);

            if ($category_query->have_posts()) {
                $ids = self::mergeRelatedProductIds($ids, $category_query->posts);
            }
        }

        if (count($ids) < $limit && ! empty($product_tags)) {
            $tag_query = new \WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => $limit - count($ids),
                'post__not_in'   => $not_in(),
                'tax_query'      => [
                    [
                        'taxonomy' => 'product_tag',
                        'field'    => 'term_id',
                        'terms'    => $product_tags,
                    ],
                ],
                'fields' => 'ids',
            ]);

            if ($tag_query->have_posts()) {
                $ids = self::mergeRelatedProductIds($ids, $tag_query->posts);
            }
        }

        return array_slice($ids, 0, $limit);
    }

    /**
     * WP_Query for the "شاید بپسندید" related products slider.
     */
    public static function getRelatedProductsQuery(int $product_id): \WP_Query
    {
        $ids = self::getRelatedProductIds($product_id);

        return new \WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 12,
            'post__in'       => $ids !== [] ? $ids : [0],
            'orderby'        => 'post__in',
        ]);
    }

    /**
     * Recommended product IDs for cart slider: equal share per cart item, max 12 total.
     *
     * @return list<int>
     */
    public static function getCartRecommendedProductIds(int $total_limit = 12): array
    {
        if (! function_exists('WC') || ! WC()->cart || WC()->cart->is_empty()) {
            return [];
        }

        $cart_product_ids = [];

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_id = (int) $cart_item['product_id'];

            if ($product_id > 0 && ! in_array($product_id, $cart_product_ids, true)) {
                $cart_product_ids[] = $product_id;
            }
        }

        if ($cart_product_ids === []) {
            return [];
        }

        $per_product = max(1, (int) floor($total_limit / count($cart_product_ids)));
        $merged      = [];

        foreach ($cart_product_ids as $cart_product_id) {
            if (count($merged) >= $total_limit) {
                break;
            }

            $remaining   = $total_limit - count($merged);
            $chunk_limit = min($per_product, $remaining);
            $chunk       = self::getRelatedProductIds($cart_product_id, $chunk_limit, $cart_product_ids);
            $merged      = self::mergeRelatedProductIds($merged, $chunk);
        }

        return array_slice($merged, 0, $total_limit);
    }

    /**
     * WP_Query for cart page "محصولات پیشنهادی" slider.
     */
    public static function getCartRecommendedProductsQuery(int $total_limit = 12): \WP_Query
    {
        $ids = self::getCartRecommendedProductIds($total_limit);

        if ($ids === []) {
            return new \WP_Query([
                'post_type'      => 'product',
                'posts_per_page' => $total_limit,
                'orderby'        => 'total_sales',
                'order'          => 'DESC',
            ]);
        }

        return new \WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => $total_limit,
            'post__in'       => $ids,
            'orderby'        => 'post__in',
        ]);
    }

    /**
     * @return list<int>
     */
    private static function getAcfRelatedProductIds(int $product_id): array
    {
        if (! function_exists('get_field')) {
            return [];
        }

        $selected = get_field('product_related_products', $product_id);

        if (empty($selected)) {
            return [];
        }

        $raw_ids = is_array($selected) ? $selected : [(int) $selected];
        $ids     = [];

        foreach ($raw_ids as $raw_id) {
            $id = (int) $raw_id;

            if ($id <= 0 || $id === $product_id) {
                continue;
            }

            if (get_post_type($id) !== 'product' || get_post_status($id) !== 'publish') {
                continue;
            }

            if (! in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /**
     * @param list<int> $existing
     * @param list<int|string> $new
     * @return list<int>
     */
    private static function mergeRelatedProductIds(array $existing, array $new): array
    {
        foreach ($new as $raw_id) {
            $id = (int) $raw_id;

            if ($id <= 0 || in_array($id, $existing, true)) {
                continue;
            }

            $existing[] = $id;
        }

        return $existing;
    }

    /**
     * Cart proceed to checkout button (تایید و تکمیل سفارش)
     */
    public static function cartProceedButton()
    {
        echo '<a href="' . esc_url(wc_get_checkout_url()) . '" class="primary-btn wc-forward block w-full text-center py-3 rounded-xl">' . esc_html__('تایید و تکمیل سفارش', 'taghechian') . '</a>';
    }

    /**
     * Cross-sells section heading on cart page (محصولات پیشنهادی)
     *
     * @param string $heading
     * @return string
     */
    public static function crossSellsHeading($heading)
    {
        return __('محصولات پیشنهادی', 'taghechian');
    }

    /**
     * Redirect /account alias URLs to canonical /my-account URLs.
     */
    public static function redirectAccountAliasToMyAccount()
    {
        if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
            return;
        }

        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';
        if ($request_uri === '') {
            return;
        }

        $parsed_url = wp_parse_url($request_uri);
        $request_path = isset($parsed_url['path']) ? (string) $parsed_url['path'] : '';
        if ($request_path === '') {
            return;
        }

        $normalized_request_path = trim($request_path, '/');
        if ($normalized_request_path === '' || strpos($normalized_request_path, 'account') !== 0) {
            return;
        }

        $my_account_path = preg_replace('/^account\b/', 'my-account', $normalized_request_path);
        $redirect_url = home_url('/' . ltrim((string) $my_account_path, '/') . '/');

        if (isset($parsed_url['query']) && $parsed_url['query'] !== '') {
            $redirect_url = $redirect_url . '?' . $parsed_url['query'];
        }

        wp_safe_redirect($redirect_url, 301);
        exit;
    }

    /**
     * Keep dashboard-origin account forms on the My Account page.
     *
     * WooCommerce redirects some account forms to dedicated endpoints by default.
     * This only overrides those redirects when the submit came from dashboard UI forms.
     *
     * @param string $location Redirect destination.
     * @param int    $status   Redirect status code.
     * @return string
     */
    public static function maybeRedirectAccountSaveToDashboard($location, $status)
    {
        unset($status);

        if (!is_string($location) || $location === '') {
            return $location;
        }

        if (!is_account_page() || !is_user_logged_in()) {
            return $location;
        }

        $posted_action = isset($_POST['action']) ? sanitize_text_field(wp_unslash($_POST['action'])) : '';
        $my_account_url = wc_get_page_permalink('myaccount');

        if ($posted_action === 'save_account_details') {
            $form_source = isset($_POST['cyn_account_form_source']) ? sanitize_text_field(wp_unslash($_POST['cyn_account_form_source'])) : '';
            if ($form_source !== 'dashboard_names') {
                return $location;
            }

            $edit_account_url = wc_get_endpoint_url('edit-account', '', $my_account_url);
            if (untrailingslashit($location) !== untrailingslashit($edit_account_url)) {
                return $location;
            }

            return $my_account_url;
        }

        if ($posted_action === 'edit_address') {
            $form_source = isset($_POST['cyn_address_form_source']) ? sanitize_text_field(wp_unslash($_POST['cyn_address_form_source'])) : '';
            if ($form_source !== 'dashboard_modal') {
                return $location;
            }

            $edit_address_url = wc_get_endpoint_url('edit-address', '', $my_account_url);
            if (untrailingslashit($location) !== untrailingslashit($edit_address_url)) {
                return $location;
            }

            return $my_account_url;
        }

        return $location;
    }

    /**
     * Register plaque and unit on all WooCommerce address forms (checkout, my account).
     *
     * @param array<string, array<string, mixed>> $fields
     * @return array<string, array<string, mixed>>
     */
    public static function addAddressPlaqueAndUnitFields($fields)
    {
        $fields['plaque'] = [
            'label'       => __('پلاک', 'taghechian'),
            'placeholder' => __('پلاک', 'taghechian'),
            'required'    => true,
            'class'       => ['form-row-first'],
            'priority'    => 55,
        ];

        $fields['unit'] = [
            'label'       => __('واحد', 'taghechian'),
            'placeholder' => __('واحد', 'taghechian'),
            'required'    => true,
            'class'       => ['form-row-last'],
            'priority'    => 56,
        ];

        return $fields;
    }

    /**
     * Read plaque or unit meta from an order (supports both prefixed meta keys).
     */
    private static function getOrderAddressFieldMeta(\WC_Order $order, string $address_type, string $field): string
    {
        $prefixed_key = '_' . $address_type . '_' . $field;
        $plain_key    = $address_type . '_' . $field;
        $value        = $order->get_meta($prefixed_key, true);

        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        $value = $order->get_meta($plain_key, true);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * Map {plaque} and {unit} placeholders for WooCommerce address formatting.
     *
     * @param array<string, string> $replacements
     * @param array<string, string> $args
     * @return array<string, string>
     */
    public static function formattedAddressPlaqueAndUnitReplacements($replacements, $args)
    {
        $replacements['{plaque}'] = isset($args['plaque']) ? (string) $args['plaque'] : '';
        $replacements['{unit}']   = isset($args['unit']) ? (string) $args['unit'] : '';

        return $replacements;
    }

    /**
     * Append plaque and unit lines to a formatted address array.
     *
     * @param array<string, string> $address
     * @param string                $plaque
     * @param string                $unit
     * @return array<string, string>
     */
    private static function appendPlaqueAndUnitLines(array $address, $plaque, $unit)
    {
        $plaque = is_string($plaque) ? trim($plaque) : '';
        $unit   = is_string($unit) ? trim($unit) : '';

        if ($plaque !== '') {
            $address['plaque'] = 'پلاک: ' . $plaque;
        }

        if ($unit !== '') {
            $address['unit'] = 'واحد: ' . $unit;
        }

        return $address;
    }

    /**
     * Add plaque and unit to formatted My Account addresses.
     *
     * @param array<string, string> $address
     * @param int                   $customer_id
     * @param string                $address_type
     * @return array<string, string>
     */
    public static function appendAddressPlaqueAndUnit($address, $customer_id, $address_type)
    {
        $plaque = get_user_meta($customer_id, $address_type . '_plaque', true);
        $unit   = get_user_meta($customer_id, $address_type . '_unit', true);

        return self::appendPlaqueAndUnitLines($address, (string) $plaque, (string) $unit);
    }

    /**
     * Add plaque and unit to formatted order billing address.
     *
     * @param array<string, string> $address
     * @param \WC_Order             $order
     * @return array<string, string>
     */
    public static function appendOrderBillingPlaqueAndUnit($address, $order)
    {
        if (! $order instanceof \WC_Order) {
            return $address;
        }

        return self::appendPlaqueAndUnitLines(
            $address,
            self::getOrderAddressFieldMeta($order, 'billing', 'plaque'),
            self::getOrderAddressFieldMeta($order, 'billing', 'unit')
        );
    }

    /**
     * Add plaque and unit to formatted order shipping address.
     *
     * @param array<string, string> $address
     * @param \WC_Order             $order
     * @return array<string, string>
     */
    public static function appendOrderShippingPlaqueAndUnit($address, $order)
    {
        if (! $order instanceof \WC_Order) {
            return $address;
        }

        return self::appendPlaqueAndUnitLines(
            $address,
            self::getOrderAddressFieldMeta($order, 'shipping', 'plaque'),
            self::getOrderAddressFieldMeta($order, 'shipping', 'unit')
        );
    }

    /**
     * Custom Iran address format including plaque and unit.
     *
     * @param array<string, string> $formats
     * @return array<string, string>
     */
    public static function localisationAddressFormats($formats)
    {
        $formats['IR'] =
            "{name}\n" .
            "{state}\n" .
            "{city}\n" .
            "{address_1}\n" .
            "{plaque}\n" .
            "{unit}\n" .
            "{postcode}";

        return $formats;
    }

    /**
     * Register "wishlist" endpoint under My Account.
     */
    public static function registerAccountWishlistEndpoint()
    {
        add_rewrite_endpoint('wishlist', EP_ROOT | EP_PAGES);
    }

    /**
     * Register "support" endpoint under My Account.
     */
    public static function registerAccountSupportEndpoint()
    {
        add_rewrite_endpoint('support', EP_ROOT | EP_PAGES);
    }

    /**
     * Output wishlist endpoint content.
     */
    public static function renderWishlistEndpointContent()
    {
        wc_get_template('myaccount/wishlist.php');
    }

    /**
     * Output support endpoint content.
     */
    public static function renderSupportEndpointContent()
    {
        wc_get_template('myaccount/support.php');
    }

    /**
     * Return current user's normalized wishlist product IDs.
     *
     * @return int[]
     */
    public static function getCurrentUserWishlistProductIds()
    {
        $user_id = get_current_user_id();
        if ($user_id <= 0) {
            return [];
        }

        $stored = get_user_meta($user_id, self::WISHLIST_META_KEY, true);
        if (! is_array($stored)) {
            return [];
        }

        $wishlist_product_ids = array_values(array_unique(array_filter(array_map('absint', $stored))));

        return $wishlist_product_ids;
    }

    /**
     * Remove a product from current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function removeProductFromCurrentUserWishlist($product_id)
    {
        $user_id = get_current_user_id();
        if ($user_id <= 0 || $product_id <= 0) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();
        $updated_wishlist_ids = array_values(array_filter($wishlist_product_ids, static function ($wishlist_product_id) use ($product_id) {
            return (int) $wishlist_product_id !== (int) $product_id;
        }));

        update_user_meta($user_id, self::WISHLIST_META_KEY, $updated_wishlist_ids);

        return true;
    }

    /**
     * Add a product to current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function addProductToCurrentUserWishlist($product_id)
    {
        $user_id = get_current_user_id();
        $product_id = absint($product_id);

        if ($user_id <= 0 || $product_id <= 0) {
            return false;
        }

        $product = wc_get_product($product_id);
        if (! $product || ! $product->get_id()) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();
        if (! in_array($product_id, $wishlist_product_ids, true)) {
            $wishlist_product_ids[] = $product_id;
        }

        $wishlist_product_ids = array_values(array_unique(array_filter(array_map('absint', $wishlist_product_ids))));
        update_user_meta($user_id, self::WISHLIST_META_KEY, $wishlist_product_ids);

        return true;
    }

    /**
     * Check whether a product exists in current user's wishlist.
     *
     * @param int $product_id Product ID.
     * @return bool
     */
    public static function isProductInCurrentUserWishlist($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0 || ! is_user_logged_in()) {
            return false;
        }

        $wishlist_product_ids = self::getCurrentUserWishlistProductIds();

        return in_array($product_id, $wishlist_product_ids, true);
    }

    /**
     * Handle add/remove toggle action from single product page.
     */
    public static function maybeHandleWishlistToggle()
    {
        if (! isset($_GET[self::WISHLIST_TOGGLE_ACTION])) {
            return;
        }

        $product_id = absint(wp_unslash($_GET[self::WISHLIST_TOGGLE_ACTION]));
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        $redirect_url = wp_get_referer();
        if (! is_string($redirect_url) || $redirect_url === '') {
            $redirect_url = $product_id > 0 ? get_permalink($product_id) : home_url('/');
        }
        $redirect_url = remove_query_arg([self::WISHLIST_TOGGLE_ACTION, '_wpnonce'], $redirect_url);

        if (! is_user_logged_in()) {
            wp_safe_redirect($redirect_url);
            exit;
        }

        if ($product_id <= 0 || $nonce === '' || ! wp_verify_nonce($nonce, self::WISHLIST_TOGGLE_NONCE_ACTION . '_' . $product_id)) {
            wc_add_notice(__('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.', 'taghechian'), 'error');
            wp_safe_redirect($redirect_url);
            exit;
        }

        $is_liked = self::isProductInCurrentUserWishlist($product_id);
        $result = $is_liked
            ? self::removeProductFromCurrentUserWishlist($product_id)
            : self::addProductToCurrentUserWishlist($product_id);

        if (! $result) {
            wc_add_notice(__('امکان بروزرسانی علاقه‌مندی وجود ندارد.', 'taghechian'), 'error');
            wp_safe_redirect($redirect_url);
            exit;
        }

        if ($is_liked) {
            wc_add_notice(__('محصول از علاقه‌مندی‌ها حذف شد.', 'taghechian'), 'success');
        } else {
            wc_add_notice(__('محصول به علاقه‌مندی‌ها اضافه شد.', 'taghechian'), 'success');
        }

        wp_safe_redirect($redirect_url);
        exit;
    }

    /**
     * Handle secure remove action from My Account wishlist page.
     */
    public static function maybeHandleWishlistRemove()
    {
        if (! is_user_logged_in() || ! is_account_page()) {
            return;
        }

        if (! isset($_GET[self::WISHLIST_REMOVE_ACTION])) {
            return;
        }

        $product_id = isset($_GET[self::WISHLIST_REMOVE_ACTION]) ? absint(wp_unslash($_GET[self::WISHLIST_REMOVE_ACTION])) : 0;
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        $wishlist_url = wc_get_account_endpoint_url('wishlist');

        if ($product_id <= 0 || $nonce === '' || ! wp_verify_nonce($nonce, self::WISHLIST_REMOVE_NONCE_ACTION . '_' . $product_id)) {
            wc_add_notice(__('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.', 'taghechian'), 'error');
            wp_safe_redirect($wishlist_url);
            exit;
        }

        $removed = self::removeProductFromCurrentUserWishlist($product_id);
        if (! $removed) {
            wc_add_notice(__('امکان حذف محصول از علاقه‌مندی‌ها وجود ندارد.', 'taghechian'), 'error');
            wp_safe_redirect($wishlist_url);
            exit;
        }

        wc_add_notice(__('محصول از علاقه‌مندی‌ها حذف شد.', 'taghechian'), 'success');
        wp_safe_redirect($wishlist_url);
        exit;
    }

    /**
     * Build secure remove URL for a wishlist product.
     *
     * @param int $product_id Product ID.
     * @return string
     */
    public static function getWishlistRemoveUrl($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0) {
            return '';
        }

        $url = add_query_arg(
            [
                self::WISHLIST_REMOVE_ACTION => $product_id,
            ],
            wc_get_account_endpoint_url('wishlist')
        );

        return wp_nonce_url($url, self::WISHLIST_REMOVE_NONCE_ACTION . '_' . $product_id);
    }

    /**
     * Build secure toggle URL for single-product wishlist button.
     *
     * @param int $product_id Product ID.
     * @return string
     */
    public static function getWishlistToggleUrl($product_id)
    {
        $product_id = absint($product_id);
        if ($product_id <= 0) {
            return '';
        }

        $url = add_query_arg(
            [
                self::WISHLIST_TOGGLE_ACTION => $product_id,
            ],
            get_permalink($product_id)
        );

        return wp_nonce_url($url, self::WISHLIST_TOGGLE_NONCE_ACTION . '_' . $product_id);
    }


    /**
     * Register shop sidebar for widgets (Appearance > Widgets).
     */
    public static function registerShopSidebar()
    {
        register_sidebar([
            'name'          => 'سایدبار فروشگاه',
            'id'            => 'shop',
            'description'   => 'Widgets in this area appear on the shop archive.',
            'before_widget' => '<div id="%1$s" class="widget %2$s mb-4">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title font-bold mb-2">',
            'after_title'   => '</h3>',
        ]);
    }

    /**
     * When instock=1 in URL, show only in-stock products.
     * Simple products: use own _stock_status. Variable: WooCommerce sets parent by variant availability.
     *
     * @param \WP_Query $q
     */
    public static function filterByInstock($q)
    {
        if (! $q->is_main_query() || (! is_shop() && ! is_product_taxonomy())) {
            return;
        }
        if (!isset($_GET['instock']) || $_GET['instock'] !== '1') {
            return;
        }
        $meta_query = $q->get('meta_query') ?: [];
        $meta_query[] = [
            'key'     => '_stock_status',
            'value'   => 'instock',
            'compare' => '=',
        ];
        $q->set('meta_query', $meta_query);
    }

    /**
     * Apply taxonomy filters (product_age) from URL params.
     *
     * @param \WP_Query $q
     */
    public static function filterByTaxonomyQueryVars($q)
    {
        if (! $q->is_main_query() || (! is_shop() && ! is_product_taxonomy())) {
            return;
        }
        $tax_query = is_array($q->get('tax_query')) ? $q->get('tax_query') : [];
        $filter_taxonomies = ['product_age'];
        foreach ($filter_taxonomies as $taxonomy) {
            if (! taxonomy_exists($taxonomy)) {
                continue;
            }
            if (! isset($_GET[$taxonomy]) || $_GET[$taxonomy] === '') {
                continue;
            }
            $slugs = array_map('sanitize_title', explode(',', wc_clean(wp_unslash($_GET[$taxonomy]))));
            $slugs = array_filter($slugs);
            if (empty($slugs)) {
                continue;
            }
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slugs,
                'operator' => count($slugs) > 1 ? 'IN' : '=',
            ];
        }
        if (! empty($tax_query)) {
            $q->set('tax_query', $tax_query);
        }
    }

    /**
     * When orderby=sale, return valid ordering args so WooCommerce query does not error.
     *
     * @param array  $args
     * @param string $orderby
     * @param string $order
     * @return array
     */
    public static function catalogOrderBySale($args, $orderby, $order)
    {
        if ($orderby !== 'sale') {
            return $args;
        }
        return [
            'orderby'  => 'date',
            'order'    => 'DESC',
            'meta_key' => '',
        ];
    }

    /**
     * When orderby=sale in URL, show only sale products on archive.
     *
     * @param \WP_Query $q
     */
    public static function filterByOrderbySale($q)
    {
        if (! $q->is_main_query() || (! is_shop() && ! is_product_taxonomy())) {
            return;
        }
        if (! isset($_GET['orderby']) || sanitize_text_field(wp_unslash($_GET['orderby'])) !== 'sale') {
            return;
        }
        $on_sale_ids = wc_get_product_ids_on_sale();
        $q->set('post__in', ! empty($on_sale_ids) ? $on_sale_ids : [0]);
    }

    /**
     * Add "sale products" option to shop catalog orderby dropdown.
     *
     * @param array $options
     * @return array
     */
    public static function catalogOrderbyWithSale($options)
    {
        $options['sale'] = __('محصولات تخفیف‌دار', 'taghechian');
        return $options;
    }

    /**
     * Active archive filters for display as removable tags.
     *
     * @param string $base_url Base URL (shop or term).
     * @return array List of [ 'label' => '...', 'url' => '...' ].
     */
    public static function getArchiveActiveFilters($base_url)
    {
        $filters = [];
        $get    = isset($_GET) ? wp_unslash($_GET) : [];
        unset($get['paged']);

        $orderby_labels = [
            'menu_order' => __('مرتب‌سازی پیش‌فرض', 'taghechian'),
            'popularity' => __('مرتب‌سازی بر اساس محبوبیت', 'taghechian'),
            'rating'     => __('مرتب‌سازی بر اساس امتیاز', 'taghechian'),
            'date'       => __('مرتب‌سازی بر اساس جدیدترین', 'taghechian'),
            'price'      => __('مرتب‌سازی بر اساس ارزان‌ترین', 'taghechian'),
            'price-desc' => __('مرتب‌سازی بر اساس گران‌ترین', 'taghechian'),
            'sale'       => __('محصولات تخفیف‌دار', 'taghechian'),
        ];

        // Price
        if (! empty($get['min_price']) || ! empty($get['max_price'])) {
            $parts = [];
            if (! empty($get['min_price'])) {
                $parts[] = sprintf(__('از %s', 'taghechian'), strip_tags(wc_price($get['min_price'])));
            }
            if (! empty($get['max_price'])) {
                $parts[] = sprintf(__('تا %s', 'taghechian'), strip_tags(wc_price($get['max_price'])));
            }
            $params = $get;
            unset($params['min_price'], $params['max_price']);
            $filters[] = ['label' => __('قیمت', 'taghechian') . ': ' . implode(' ', $parts), 'url' => add_query_arg($params, $base_url)];
        }

        // In stock
        if (! empty($get['instock']) && $get['instock'] === '1') {
            $params = $get;
            unset($params['instock']);
            $filters[] = ['label' => __('موجود در انبار', 'taghechian'), 'url' => add_query_arg($params, $base_url)];
        }

        // product_age
        if (! empty($get['product_age'])) {
            $slug = sanitize_title($get['product_age']);
            $t   = get_term_by('slug', $slug, 'product_age');
            $params = $get;
            unset($params['product_age']);
            $filters[] = ['label' => ($t && ! is_wp_error($t) ? $t->name : $slug), 'url' => add_query_arg($params, $base_url)];
        }

        // orderby
        if (! empty($get['orderby'])) {
            $ob   = sanitize_text_field($get['orderby']);
            $label = isset($orderby_labels[$ob]) ? $orderby_labels[$ob] : $ob;
            $params = $get;
            unset($params['orderby']);
            $filters[] = ['label' => $label, 'url' => add_query_arg($params, $base_url)];
        }

        return $filters;
    }

    /**
     * In archive-product, mobile/desktop orderby are handled separately; this applies elsewhere.
     */
    public static function catalogOrderbyRemoveOptions($options)
    {
        $remove = [
            'rating',     // sort by rating
            'popularity', // popularity
            'date',       // newest
            'menu_order', // default
            'sale',       // sale products
        ];
        foreach ($remove as $key) {
            unset($options[$key]);
        }
        return $options;
    }

    /**
     * Remove specific fields from checkout form
     * First makes them non-required, then removes them
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function removeCheckoutFields($fields)
    {
        // $fields_to_remove = [
        //     'billing_address_2',
        // ];

        // foreach ($fields_to_remove as $field_key) {
        //     if (isset($fields['billing'][$field_key])) {
        //         $fields['billing'][$field_key]['required'] = false;
        //         unset($fields['billing'][$field_key]);
        //     }
        // }

        if (isset($fields['billing']['billing_email'])) {
            $fields['billing']['billing_email']['required'] = false;

            $fields['billing']['billing_email']['class'][] = 'hidden';

            $fields['billing']['billing_email']['label'] = '';
            $fields['billing']['billing_email']['placeholder'] = '';
        }

        add_filter('woocommerce_checkout_fields', function ($fields) {

            $fields['billing']['billing_country']['default'] = 'IR';
            $fields['billing']['billing_country']['class'][] = 'hidden';

            return $fields;
        });

        remove_action(
            'woocommerce_before_checkout_form',
            'woocommerce_checkout_coupon_form',
            10
        );

        return $fields;
    }


    /**
     * Make phone field required
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function makePhoneRequired($fields)
    {
        if (isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['required'] = true;
        }

        return $fields;
    }

    /**
     * Add placeholders to checkout fields
     * 
     * @param array $fields Checkout fields
     * @return array Modified checkout fields
     */
    public static function addPlaceholders($fields)
    {
        if (isset($fields['billing']['billing_first_name'])) {
            $fields['billing']['billing_first_name']['placeholder'] = __('نام', 'taghechian');
        }

        if (isset($fields['billing']['billing_last_name'])) {
            $fields['billing']['billing_last_name']['placeholder'] = __('نام خانوادگی', 'taghechian');
        }

        if (isset($fields['billing']['billing_city'])) {
            $fields['billing']['billing_city']['placeholder'] = __('نام شهر خود را وارد کنید', 'taghechian');
        }

        if (isset($fields['billing']['billing_postcode'])) {
            $fields['billing']['billing_postcode']['placeholder'] = __('کد پستی ده رقمی را وارد کنید', 'taghechian');
        }

        if (isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone']['placeholder'] = __('09xxxxxxxxxx', 'taghechian');
        }

        if (isset($fields['billing']['billing_plaque'])) {
            $fields['billing']['billing_plaque']['placeholder'] = __('پلاک', 'taghechian');
            $fields['billing']['billing_plaque']['required']    = true;
        }

        if (isset($fields['billing']['billing_unit'])) {
            $fields['billing']['billing_unit']['placeholder'] = __('واحد', 'taghechian');
            $fields['billing']['billing_unit']['required']    = true;
        }

        $fields['order']['order_comments']['custom_attributes'] = [
            'rows' => 4,
        ];

        return $fields;
    }

    /**
     * Customize WooCommerce coupon success messages
     * Localizes coupon applied/removed messages
     * 
     * @param string $msg Message text
     * @param int $msg_code Message code
     * @param object $coupon Coupon object
     * @return string Modified message
     */
    public static function customizeCouponMessages($msg, $msg_code, $coupon)
    {
        switch ($msg_code) {
            case \WC_Coupon::WC_COUPON_SUCCESS:
                $msg = '🎉 تبریک! کد تخفیف "' . esc_html($coupon->get_code()) . '" با موفقیت اعمال شد.';
                break;
            case \WC_Coupon::WC_COUPON_REMOVED:
                $msg = 'کد تخفیف حذف شد.';
                break;
        }
        return $msg;
    }

    /**
     * Customize WooCommerce coupon error messages
     * Localizes all coupon validation error messages
     * 
     * @param string $err Error message
     * @param int $err_code Error code
     * @param object $coupon Coupon object
     * @return string Modified error message
     */
    public static function customizeCouponErrors($err, $err_code, $coupon)
    {
        switch ($err_code) {
            case 100:
                $err = '⚠️ کد تخفیف وارد شده معتبر نیست.';
                break;
            case 101:
                $err = '❌ کد تخفیف معتبر نیست و حذف شد.';
                break;
            case 102:
                $err = '🚫 این کد به حساب کاربری شما تعلق ندارد.';
                break;
            case 103:
                $err = '🔁 این کد تخفیف قبلاً اعمال شده است.';
                break;
            case 104:
                $err = '⚠️ این کد فقط به صورت انفرادی قابل استفاده است. ابتدا سایر کدها را حذف کنید.';
                break;
            case 105:
                $err = '❌ کد تخفیفی که وارد کردید وجود ندارد.';
                break;
            case 106:
                $err = '🚫 سقف استفاده از این کد تخفیف پر شده است.';
                break;
            case 107:
                $err = '⏰ این کد تخفیف منقضی شده است.';
                break;
            case 108:
                $min_spend = $coupon->get_minimum_amount();
                $err = '💰 برای استفاده از این کد باید حداقل ' . \wc_price($min_spend) . ' خرید کنید.';
                break;
            case 109:
                $err = '⚠️ این کد برای محصولات انتخاب‌شده قابل استفاده نیست.';
                break;
            case 110:
                $err = '❗ این کد روی کالاهای دارای تخفیف قابل استفاده نیست.';
                break;
            case 111:
                $err = '🔤 لطفاً یک کد تخفیف وارد کنید.';
                break;
            case 112:
                $err = '🔤 لطفاً یک کد تخفیف معتبر وارد کنید.';
                break;
        }
        return $err;
    }

    public static function ajaxDeleteCoupon()
    {
        // اعتبارسنجی nonce (اختیاری)
        if (isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'delete_coupon_nonce')) {
            wp_send_json_error(['message' => 'خطای امنیتی']);
            return;
        }

        // حذف همه کوپن‌ها
        foreach (WC()->cart->get_applied_coupons() as $code) {
            WC()->cart->remove_coupon($code);
        }

        WC()->cart->calculate_totals();

        wp_send_json_success([
            'message' => __('کد تخفیف با موفقیت حذف شد.', 'taghechian')
        ]);
    }


    /**
     * Remove WooCommerce breadcrumb
     */
    public static function removeBreadcrumb()
    {
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    }

    /**
     * Product loop hooks
     */
    public static function archiveProduct()
    {
        remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

        add_filter('rank_math/admin/disable_rich_editor', [__CLASS__, 'disableRankMathCategoryDescriptionEditor'], 10, 2);
        add_action('admin_init', [__CLASS__, 'allowRichProductCategoryDescription']);
        add_action('admin_head-edit-product_cat', [__CLASS__, 'hideDefaultCategoryDescriptionField']);
        add_action('admin_head-edit-tags', [__CLASS__, 'hideDefaultCategoryDescriptionField']);
        add_action('admin_footer-edit-product_cat', [__CLASS__, 'removeDefaultCategoryDescriptionField']);
        add_action('admin_footer-edit-tags', [__CLASS__, 'removeDefaultCategoryDescriptionField']);
        add_action('product_cat_edit_form_fields', [__CLASS__, 'renderProductCategoryDescriptionEditor'], 1, 2);
        add_action('product_cat_add_form_fields', [__CLASS__, 'renderProductCategoryDescriptionEditorAdd']);
    }

    /**
     * Disable Rank Math duplicate WYSIWYG on product_cat (theme provides a single editor).
     *
     * @param bool   $disabled Whether the rich editor is disabled.
     * @param string $taxonomy Taxonomy slug.
     */
    public static function disableRankMathCategoryDescriptionEditor($disabled, $taxonomy): bool
    {
        return $taxonomy === 'product_cat' ? true : (bool) $disabled;
    }

    /**
     * Allow post-like HTML in product category descriptions (WYSIWYG).
     */
    public static function allowRichProductCategoryDescription(): void
    {
        remove_filter('pre_term_description', 'wp_filter_kses');
        remove_filter('term_description', 'wp_kses_data');
        add_filter('pre_term_description', 'wp_kses_post');
        add_filter('term_description', 'wp_kses_post');
    }

    /**
     * Hide the default plain textarea for category description (keep theme WYSIWYG visible).
     */
    public static function hideDefaultCategoryDescriptionField(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (! $screen || $screen->taxonomy !== 'product_cat') {
            return;
        }

        echo '<style>
            tr.term-description-wrap:not(.cyn-term-description-wrap),
            div.form-field.term-description-wrap:not(.cyn-term-description-wrap) {
                display: none;
            }
        </style>';
    }

    /**
     * Remove default description textarea from DOM (backup for CSS hide).
     */
    public static function removeDefaultCategoryDescriptionField(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (! $screen || $screen->taxonomy !== 'product_cat') {
            return;
        }

    ?>
        <script>
            jQuery(function($) {
                $('tr.term-description-wrap:not(.cyn-term-description-wrap), div.form-field.term-description-wrap:not(.cyn-term-description-wrap)').remove();
            });
        </script>
    <?php
    }

    /**
     * WYSIWYG editor for product category description (edit form).
     *
     * @param \WP_Term $term Current term.
     */
    public static function renderProductCategoryDescriptionEditor($term): void
    {
        $description = term_description($term->term_id, 'product_cat');

        if (is_string($description)) {
            $description = html_entity_decode($description, ENT_QUOTES, get_bloginfo('charset'));
        } else {
            $description = '';
        }
    ?>
        <tr class="form-field term-description-wrap cyn-term-description-wrap">
            <th scope="row">
                <label for="cyn_product_cat_description"><?php esc_html_e('توضیحات دسته بندی', 'taghechian'); ?></label>
            </th>
            <td>
                <?php
                wp_editor(
                    $description,
                    'cyn_product_cat_description',
                    self::getProductCategoryDescriptionEditorSettings()
                );
                ?>
                <p class="description"><?php esc_html_e('توضیحات دسته‌بندی در انتهای صفحه آرشیو نمایش داده می‌شود.', 'taghechian'); ?></p>
            </td>
        </tr>
    <?php
    }

    /**
     * WYSIWYG editor for product category description (add form).
     */
    public static function renderProductCategoryDescriptionEditorAdd(): void
    {
    ?>
        <div class="form-field term-description-wrap cyn-term-description-wrap">
            <label for="cyn_product_cat_description"><?php esc_html_e('توضیح', 'taghechian'); ?></label>
            <?php
            wp_editor(
                '',
                'cyn_product_cat_description',
                self::getProductCategoryDescriptionEditorSettings()
            );
            ?>
            <p class="description"><?php esc_html_e('توضیحات دسته‌بندی در انتهای صفحه آرشیو نمایش داده می‌شود.', 'taghechian'); ?></p>
        </div>
<?php
    }

    /**
     * Shared wp_editor settings for product category description.
     *
     * @return array<string, mixed>
     */
    private static function getProductCategoryDescriptionEditorSettings(): array
    {
        return [
            'textarea_name' => 'description',
            'textarea_rows' => 10,
            'media_buttons' => true,
            'teeny'         => false,
            'quicktags'     => true,
        ];
    }

    public static function cyn_get_cart_special_price_saving()
    {
        if (! WC()->cart) {
            return 0;
        }

        $total_saving = 0;

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product  = $cart_item['data'];
            $quantity = $cart_item['quantity'];

            if (! $product || $quantity < 1) {
                continue;
            }

            $regular_price = (float) $product->get_regular_price();
            $sale_price    = (float) $product->get_sale_price();

            if ($sale_price > 0 && $regular_price > $sale_price) {
                $saving_per_item = $regular_price - $sale_price;
                $total_saving += $saving_per_item * $quantity;
            }
        }

        return $total_saving;
    }

    /**
     * Normalize stored mobile number digits for mobile-login customers.
     */
    private static function getCustomerMobileNumber(int $user_id): string
    {
        $mobile = get_user_meta($user_id, 'smartlogin_mobile', true);

        if (! is_string($mobile) || $mobile === '') {
            return '';
        }

        return preg_replace('/\D/', '', $mobile) ?? '';
    }

    /**
     * Whether the customer registered via mobile OTP (smartlogin).
     */
    private static function isMobileOnlyCustomer(int $user_id): bool
    {
        return self::getCustomerMobileNumber($user_id) !== '';
    }

    /**
     * Domain used for synthetic emails required by WordPress/WooCommerce internals.
     */
    private static function getInternalEmailDomain(): string
    {
        $host = wp_parse_url(home_url(), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return 'phone.local';
        }

        return 'phone.' . $host;
    }

    /**
     * Return a valid email for mobile-only customers (existing or synthetic from mobile).
     */
    public static function getCustomerInternalEmail(int $user_id): string
    {
        $user = get_userdata($user_id);

        if ($user instanceof \WP_User && is_email($user->user_email)) {
            return $user->user_email;
        }

        $mobile = self::getCustomerMobileNumber($user_id);

        if ($mobile !== '') {
            return sprintf('%s@%s', $mobile, self::getInternalEmailDomain());
        }

        return sprintf('user-%d@%s', $user_id, self::getInternalEmailDomain());
    }

    /**
     * Sync internal email and clear password-nag notices for mobile-login customers.
     */
    public static function ensureMobileCustomerAccountSetup(): void
    {
        if (! is_user_logged_in()) {
            return;
        }

        $user_id = get_current_user_id();

        if (! self::isMobileOnlyCustomer($user_id)) {
            return;
        }

        delete_user_option($user_id, 'default_password_nag', true);

        $user = get_userdata($user_id);

        if (! $user instanceof \WP_User) {
            return;
        }

        $internal_email = self::getCustomerInternalEmail($user_id);

        if (! is_email($user->user_email)) {
            wp_update_user([
                'ID'         => $user_id,
                'user_email' => $internal_email,
            ]);
        }

        if (! function_exists('WC') || ! WC()->customer) {
            return;
        }

        $customer = new \WC_Customer($user_id);
        $billing_email = $customer->get_billing_email();

        if (! is_email($billing_email)) {
            $customer->set_billing_email($internal_email);
            $customer->save();
        }
    }

    /**
     * Mobile-login customers do not manage email on the account dashboard.
     *
     * @param array<string, string> $fields
     * @return array<string, string>
     */
    public static function removeEmailFromAccountRequiredFields(array $fields): array
    {
        if (! is_user_logged_in() || ! self::isMobileOnlyCustomer(get_current_user_id())) {
            return $fields;
        }

        unset($fields['account_email']);

        return $fields;
    }

    /**
     * Hide billing email on My Account address forms for mobile-login customers.
     *
     * @param array<string, array<string, mixed>> $fields
     * @return array<string, array<string, mixed>>
     */
    public static function customizeAccountBillingEmailField(array $fields, string $country): array
    {
        unset($country);

        if (! is_account_page() || ! is_user_logged_in() || ! self::isMobileOnlyCustomer(get_current_user_id())) {
            return $fields;
        }

        if (! isset($fields['billing_email'])) {
            return $fields;
        }

        $fields['billing_email']['required'] = false;
        $fields['billing_email']['label']    = '';
        $fields['billing_email']['placeholder'] = '';
        $field_classes = isset($fields['billing_email']['class']) && is_array($fields['billing_email']['class'])
            ? $fields['billing_email']['class']
            : [];
        $field_classes[] = 'hidden';
        $fields['billing_email']['class'] = array_values(array_unique($field_classes));

        return $fields;
    }

    /**
     * Auto-fill billing email when saving addresses from the dashboard modal.
     */
    public static function fillBillingEmailForMobileCustomer(string $value): string
    {
        if (! is_user_logged_in() || ! self::isMobileOnlyCustomer(get_current_user_id())) {
            return $value;
        }

        if ($value !== '' && is_email($value)) {
            return $value;
        }

        return self::getCustomerInternalEmail(get_current_user_id());
    }
}
