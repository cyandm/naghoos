<?php

use Cyan\Theme\Helpers\Icon;

$args     = get_query_var('args', []);
$mode     = sanitize_key((string) ($args['mode'] ?? 'blog'));
$total    = max(0, (int) ($args['total'] ?? 0));
$current  = max(1, (int) ($args['current'] ?? 1));
$tab      = sanitize_key((string) ($args['tab'] ?? 'all'));
$base_url = ! empty($args['base_url']) ? (string) $args['base_url'] : get_permalink();

if ($total <= 1) {
    return;
}

if ($mode === 'archive') {
    $link_for = static function (int $page): string {
        $link = get_pagenum_link($page, false);

        if (! empty($_GET['orderby'])) {
            $link = add_query_arg('orderby', sanitize_key(wp_unslash((string) $_GET['orderby'])), $link);
        }

        return esc_url($link);
    };
} else {
    $link_for = static function (int $page) use ($tab, $base_url): string {
        return esc_url(
            add_query_arg(
                [
                    'blog_tab'   => $tab,
                    'blog_paged' => $page,
                ],
                $base_url
            ) . '#blog-categories'
        );
    };
}
?>

<nav class="blog-pagination col-span-full flex items-center justify-center gap-2 mt-3" aria-label="<?php esc_attr_e('صفحه‌بندی بلاگ', 'naghoos'); ?>">

    <?php if ($current > 1) : ?>
        <a href="<?php echo $link_for($current - 1); ?>" class="size-11 rounded-full bg-cynRed hover:opacity-90 text-white flex items-center justify-center shrink-0 transition-opacity no-underline" aria-label="<?php esc_attr_e('صفحهٔ قبل', 'naghoos'); ?>">
            <i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
        </a>
    <?php else : ?>
        <span class="size-11 rounded-full bg-cynRed/50 text-white/70 flex items-center justify-center shrink-0 cursor-not-allowed" aria-hidden="true">
            <i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-19'); ?></i>
        </span>
    <?php endif; ?>

    <?php
    $show_pages  = 2;
    $dots_before = false;
    $dots_after  = false;

    for ($i = 1; $i <= $total; $i++) :
        $show = ($i === 1 || $i === $total || ($i >= $current - $show_pages && $i <= $current + $show_pages));

        if ($show) :
            if ($i > $current) {
                $dots_after = false;
            }

            if ($i < $current) {
                $dots_before = false;
            }

            if ($i === $current) : ?>
                <span class="size-11 rounded-full bg-cynBlack text-white flex items-center justify-center shrink-0 font-medium shadow-md min-w-[2.75rem]"><?php echo (int) $i; ?></span>
            <?php else : ?>
                <a href="<?php echo $link_for($i); ?>" class="size-11 rounded-full bg-white border border-[#E5E5E5] text-cynBlack hover:border-cynBlack/30 flex items-center justify-center shrink-0 font-medium transition-colors no-underline min-w-[2.75rem]"><?php echo (int) $i; ?></a>
    <?php endif;
        else :
            if ($i < $current && ! $dots_before) {
                $dots_before = true;
                echo '<span class="text-cynBlack/50 px-1">…</span>';
            } elseif ($i > $current && ! $dots_after) {
                $dots_after = true;
                echo '<span class="text-cynBlack/50 px-1">…</span>';
            }
        endif;
    endfor;
    ?>

    <?php if ($current < $total) : ?>
        <a href="<?php echo $link_for($current + 1); ?>" class="size-11 rounded-full bg-cynRed hover:opacity-90 text-white flex items-center justify-center shrink-0 transition-opacity no-underline" aria-label="<?php esc_attr_e('صفحهٔ بعد', 'naghoos'); ?>">
            <i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
        </a>
    <?php else : ?>
        <span class="size-11 rounded-full bg-cynRed/50 text-white/70 flex items-center justify-center shrink-0 cursor-not-allowed" aria-hidden="true">
            <i class="size-6 stroke-[1.5]"><?php Icon::print('Arrow-27'); ?></i>
        </span>
    <?php endif; ?>

</nav>