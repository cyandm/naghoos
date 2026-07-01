<?php

use Cyan\Theme\Helpers\Templates;

$home_page_id = get_option('page_on_front');

$normalize_term_ids = static function ($value): array {
    if (empty($value)) {
        return [];
    }

    return is_array($value) ? array_map('intval', $value) : [(int) $value];
};

$row_one = $normalize_term_ids(get_field('home_products_categories_row_one', $home_page_id));
$row_two = $normalize_term_ids(get_field('home_products_categories_row_two', $home_page_id));

if (empty($row_one) && empty($row_two)) {
    return;
}

$get_card_config = static function (int $index, int $row_number): array {
    $is_first = $index === 0;

    if (! $is_first) {
        return [
            'layout' => 'compact',
            'span'   => 'lg:col-span-1',
        ];
    }

    if ($row_number === 1) {
        return [
            'layout' => 'featured',
            'span'   => 'lg:col-span-2',
        ];
    }

    return [
        'layout' => 'featured',
        'span'   => 'lg:col-span-3',
    ];
};

$build_row_cards = static function (array $term_ids, int $row_number) use ($get_card_config): array {
    $cards = [];

    foreach ($term_ids as $index => $term_id) {
        $term = get_term((int) $term_id, 'product_cat');

        if (! $term || is_wp_error($term)) {
            continue;
        }

        $config = $get_card_config($index, $row_number);

        $cards[] = [
            'term_id'           => (int) $term_id,
            'layout'            => $config['layout'],
            'span'              => $config['span'],
            'mobile_horizontal' => $row_number === 2 && $index > 0,
        ];
    }

    return $cards;
};

$row_one_cards = $build_row_cards($row_one, 1);
$row_two_cards = $build_row_cards($row_two, 2);

if (empty($row_one_cards) && empty($row_two_cards)) {
    return;
}

$render_category_card = static function (array $card): void {
    Templates::getCard('category', [
        'term_id'           => $card['term_id'],
        'layout'            => $card['layout'],
        'mobile_horizontal' => ! empty($card['mobile_horizontal']),
    ]);
};
?>

<section class="mt-6 mb-15 container">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
        <?php if (! empty($row_one_cards)) : ?>
            <?php
            $row_one_featured = $row_one_cards[0];
            $row_one_compacts = array_slice($row_one_cards, 1);
            ?>
            <div class="h-full <?php echo esc_attr($row_one_featured['span']); ?>">
                <?php $render_category_card($row_one_featured); ?>
            </div>

            <?php if (! empty($row_one_compacts)) : ?>
                <div class="grid grid-cols-2 gap-3 lg:contents">
                    <?php foreach ($row_one_compacts as $card) : ?>
                        <div class="h-full <?php echo esc_attr($card['span']); ?>">
                            <?php $render_category_card($card); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (! empty($row_two_cards)) : ?>
            <?php
            $row_two_featured = $row_two_cards[0];
            $row_two_compacts = array_slice($row_two_cards, 1);
            ?>
            <div class="h-full <?php echo esc_attr($row_two_featured['span']); ?>">
                <?php $render_category_card($row_two_featured); ?>
            </div>

            <?php foreach ($row_two_compacts as $card) : ?>
                <div class="h-full <?php echo esc_attr($card['span']); ?>">
                    <?php $render_category_card($card); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>