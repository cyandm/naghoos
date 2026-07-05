<?php

namespace Cyan\Theme\Helpers;

class StarRating
{
    public const FILL_COLOR   = '#F5CF5D';
    public const STROKE_COLOR = '#FFFFFF';
    public const STROKE_WIDTH = '0.2';

    private const VIEWBOX = '0 0 11 11';
    private const VIEWBOX_SIZE = 11;

    private static ?string $path = null;

    private static function getPath(): string
    {
        if (self::$path !== null) {
            return self::$path;
        }

        $svg_path = THEME_IMAGES_DIR . '/star.svg';
        $svg      = is_readable($svg_path) ? file_get_contents($svg_path) : '';

        if ($svg !== '' && preg_match('/<path[^>]+d="([^"]+)"/', $svg, $matches)) {
            self::$path = $matches[1];
            return self::$path;
        }

        self::$path = 'M5.25586 0.166016C5.28776 0.0782042 5.41146 0.0782044 5.44336 0.166016L6.66211 3.52148C6.70391 3.63656 6.81221 3.71456 6.93457 3.71875L10.502 3.84082C10.5952 3.84402 10.6337 3.96196 10.5605 4.01953L7.74609 6.21582C7.6497 6.29103 7.60803 6.41762 7.6416 6.53516L8.62793 9.9668C8.65374 10.0566 8.55301 10.1294 8.47559 10.0771L5.51758 8.0791C5.41612 8.01057 5.2831 8.01057 5.18164 8.0791L2.22363 10.0771C2.14621 10.1294 2.04548 10.0566 2.07129 9.9668L3.05762 6.53516C3.09119 6.41762 3.04952 6.29103 2.95312 6.21582L0.138672 4.01953C0.065509 3.96196 0.104051 3.84402 0.197266 3.84082L3.76465 3.71875C3.88701 3.71456 3.99531 3.63656 4.03711 3.52148L5.25586 0.166016Z';

        return self::$path;
    }

    public static function render(float $rating, array $args = []): string
    {
        $rating       = max(0, min(5, $rating));
        $size         = (int) ($args['size'] ?? 16);
        $id_prefix    = $args['id_prefix'] ?? 'starRating' . wp_unique_id();
        $class        = $args['class'] ?? 'flex items-center gap-0.5';
        $aria_label   = $args['aria_label'] ?? null;
        $stroke_color = $args['stroke_color'] ?? self::STROKE_COLOR;

        $full_stars = (int) floor($rating);
        $half_star  = ($rating - $full_stars) * 100;

        $attrs = 'class="' . esc_attr($class) . '"';

        if ($aria_label) {
            $attrs .= ' aria-label="' . esc_attr($aria_label) . '" role="img"';
        } else {
            $attrs .= ' aria-hidden="true"';
        }

        $html = '<span ' . $attrs . '>';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $full_stars) {
                $fill_percentage = 100;
            } elseif ($i === $full_stars + 1) {
                $fill_percentage = (int) round($half_star);
            } else {
                $fill_percentage = 0;
            }

            $html .= self::renderStarSvg($fill_percentage, $size, $id_prefix . $i, $stroke_color);
        }

        $html .= '</span>';

        return $html;
    }

    public static function echo(float $rating, array $args = []): void
    {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo self::render($rating, $args);
    }

    private static function renderOutlinePath(string $path, string $stroke_color): string
    {
        return sprintf(
            '<path d="%1$s" fill="none" stroke="%2$s" stroke-width="%3$s"/>',
            $path,
            esc_attr($stroke_color),
            self::STROKE_WIDTH
        );
    }

    private static function renderStarSvg(int $fill_percentage, int $size, string $id, string $stroke_color): string
    {
        $path    = esc_attr(self::getPath());
        $outline = self::renderOutlinePath($path, $stroke_color);

        if ($fill_percentage >= 100) {
            return sprintf(
                '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="%2$s" fill="none" aria-hidden="true"><path d="%3$s" fill="%4$s"/></svg>',
                $size,
                self::VIEWBOX,
                $path,
                esc_attr(self::FILL_COLOR)
            );
        }

        if ($fill_percentage <= 0) {
            return sprintf(
                '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="%2$s" fill="none" aria-hidden="true">%3$s</svg>',
                $size,
                self::VIEWBOX,
                $outline
            );
        }

        $clip_id = esc_attr($id . 'Clip');
        $clip_x  = self::VIEWBOX_SIZE * (1 - ($fill_percentage / 100));
        $clip_w  = self::VIEWBOX_SIZE * ($fill_percentage / 100);

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="%2$s" fill="none" aria-hidden="true"><defs><clipPath id="%5$s"><rect x="%6$s" y="0" width="%7$s" height="%8$d"/></clipPath></defs><path d="%3$s" fill="%4$s" clip-path="url(#%5$s)"/>%9$s</svg>',
            $size,
            self::VIEWBOX,
            $path,
            esc_attr(self::FILL_COLOR),
            $clip_id,
            $clip_x,
            $clip_w,
            self::VIEWBOX_SIZE,
            $outline
        );
    }

    public static function renderInteractive(array $args = []): string
    {
        $size         = (int) ($args['size'] ?? 24);
        $stroke_color = $args['stroke_color'] ?? '#1E1311';
        $html         = '<div class="star-rating star-rating--interactive flex gap-1" data-rating="0">';

        for ($i = 1; $i <= 5; $i++) {
            $path = esc_attr(self::getPath());

            $html .= '<span class="star cursor-pointer inline-flex" data-value="' . esc_attr((string) $i) . '" role="button" tabindex="0">';
            $html .= sprintf(
                '<svg xmlns="http://www.w3.org/2000/svg" width="%1$d" height="%1$d" viewBox="%2$s" fill="none" aria-hidden="true"><path class="cyn-star-fill" d="%3$s" fill="none"/><path class="cyn-star-outline" d="%3$s" fill="none" stroke="%4$s" stroke-width="%5$s"/></svg>',
                $size,
                self::VIEWBOX,
                $path,
                esc_attr($stroke_color),
                self::STROKE_WIDTH
            );
            $html .= '</span>';
        }

        $html .= '</div>';

        return $html;
    }
}
