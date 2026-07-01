<?php
$args = get_query_var('args', []);
$postId = $args['post-id'] ?? get_the_ID();
$mobile_slider = get_field('mobile_slider', $postId);
$desktop_slider = get_field('desktop_slider', $postId);
$raw_wide = get_field('slider_wide', $postId);
// ACF default is on; unset meta = wide. Explicit off = 0 / false / '0'.
$is_wide = ($raw_wide !== false && $raw_wide !== 0 && $raw_wide !== '0');

$mobile_classes = 'w-full h-auto object-cover md:hidden';

if ($is_wide) {
    $desktop_classes = 'w-full h-auto max-md:hidden mx-auto';
} else {
    $desktop_classes = 'w-full object-cover mx-auto max-md:hidden max-w-[1200px]';
}

$slider_url = get_field('url', $postId);
$slider_url = is_string($slider_url) && $slider_url !== '' ? $slider_url : '#';
?>

<a href="<?php echo esc_url($slider_url); ?>" class="relative block w-full">
    <?php
    if ($desktop_slider && $mobile_slider) :
        echo wp_get_attachment_image($mobile_slider, 'full', false, ['class' => $mobile_classes]);
        echo wp_get_attachment_image($desktop_slider, 'full', false, ['class' => $desktop_classes]);
    else :
        echo 'لطفا تصویر انتخاب نمایید!';
    endif;
    ?>
</a>