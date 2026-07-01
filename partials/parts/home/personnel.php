<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');

$home_personnel_title = get_field('home_personnel_title', $home_page_id);
$home_personnel_select = get_field('home_personnel_select', $home_page_id);

if (!empty($home_personnel_select)) {
    $post_ids = is_array($home_personnel_select) ? $home_personnel_select : [(int) $home_personnel_select];
    $home_personnel_query = new WP_Query([
        'post_type'      => 'personnel',
        'post__in'       => array_map('intval', $post_ids),
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
} else {
    $home_personnel_query = new WP_Query([
        'post_type'      => 'personnel',
        'posts_per_page' => 12,
        'order'          => 'DESC',
    ]);
}
?>

<?php if ($home_personnel_query->have_posts()) : ?>
    <section class="flex flex-col gap-5 my-15">

        <div class="flex container">
            <p class="text-3xl font-bold text-cynBlack">
                <?php echo $home_personnel_title ?>
            </p>
        </div>

        <swiper-container class="w-full" space-between="12" slides-per-view="auto" loop="true" navigation="false">

            <?php while ($home_personnel_query->have_posts()) : $home_personnel_query->the_post(); ?>
                <swiper-slide class="max-w-[183px] h-auto flex">
                    <?php Templates::getCard('personnel', ['person' => get_the_ID()]); ?>
                </swiper-slide>
            <?php endwhile; ?>

        </swiper-container>
    </section>
<?php endif; ?>