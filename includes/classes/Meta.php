<?php

namespace Cyan\Theme\Classes;

class Meta
{
    /**
     * Post types that show an unread count badge in admin menu.
     *
     * @var array<string, array{read_meta: string}>
     */
    protected static $unread_badge_config = [
        'contact_form'       => ['read_meta' => '_read'],
        'customer_club_form' => ['read_meta' => '_read'],
        'support_form'       => ['read_meta' => '_read'],
    ];

    public static function init()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_form_meta_box']);
        add_filter('manage_contact_form_posts_columns', [__CLASS__, 'contact_form_table_head']);
        add_action('manage_contact_form_posts_custom_column', [__CLASS__, 'contact_form_table_column'], 10, 2);
        add_filter('manage_customer_club_form_posts_columns', [__CLASS__, 'customer_club_form_table_head']);
        add_action('manage_customer_club_form_posts_custom_column', [__CLASS__, 'customer_club_form_table_column'], 10, 2);
        add_filter('manage_support_form_posts_columns', [__CLASS__, 'support_form_table_head']);
        add_action('manage_support_form_posts_custom_column', [__CLASS__, 'support_form_table_column'], 10, 2);
        add_action('load-post.php', [__CLASS__, 'markCurrentPostAsRead']);
        add_action('admin_menu', [__CLASS__, 'addUnreadCountToMenu'], 999);
        add_action('manage_posts_extra_tablenav', [__CLASS__, 'renderCustomerClubExportButton'], 10, 1);
        add_action('admin_init', [__CLASS__, 'exportCustomerClubCsv']);
    }

    public static function add_form_meta_box()
    {
        global $post;

        if (!$post) {
            return;
        }

        if ($post->post_type === 'contact_form') {
            add_meta_box('form_information', 'اطلاعات فرم', function () {
                $meta_group = [
                    ['name' => '_name', 'label' => 'نام:'],
                    ['name' => '_email', 'label' => 'ایمیل:'],
                    ['name' => '_phone', 'label' => 'تلفن همراه:'],
                    ['name' => '_message', 'label' => 'سوال:'],
                ];

                include get_template_directory() . '/partials/parts/metabox.php';
            }, null, 'advanced', 'high');

            return;
        }

        if ($post->post_type === 'customer_club_form') {
            add_meta_box('customer_club_information', 'اطلاعات عضویت', function () {
                $meta_group = [
                    ['name' => '_phone', 'label' => 'تلفن همراه:'],
                ];

                include get_template_directory() . '/partials/parts/metabox.php';
            }, null, 'advanced', 'high');

            return;
        }

        if ($post->post_type === 'support_form') {
            add_meta_box('support_form_information', 'اطلاعات پشتیبانی', function () {
                $meta_group = [
                    ['name' => '_name', 'label' => 'نام:'],
                    ['name' => '_phone', 'label' => 'تلفن همراه:'],
                    ['name' => '_message', 'label' => 'پیام:'],
                    ['name' => '_user_id', 'label' => 'شناسه کاربر:'],
                ];

                include get_template_directory() . '/partials/parts/metabox.php';
            }, null, 'advanced', 'high');
        }
    }

    public static function contact_form_table_head($columns)
    {
        $columns['name']    = __('نام', 'cyn-dm');
        $columns['phone']   = __('تلفن همراه', 'cyn-dm');
        $columns['email']   = __('ایمیل', 'cyn-dm');
        $columns['message'] = __('سوال', 'cyn-dm');

        return $columns;
    }

    public static function contact_form_table_column($column_name, $post_id)
    {
        if ($column_name === 'name') {
            echo esc_html(get_post_meta($post_id, '_name', true));
        }

        if ($column_name === 'phone') {
            echo esc_html(get_post_meta($post_id, '_phone', true));
        }

        if ($column_name === 'email') {
            echo esc_html(get_post_meta($post_id, '_email', true));
        }

        if ($column_name === 'message') {
            echo esc_html(get_post_meta($post_id, '_message', true));
        }
    }

    public static function customer_club_form_table_head($columns)
    {
        $columns['phone']     = __('تلفن همراه', 'cyn-dm');
        $columns['club_date'] = __('تاریخ ثبت', 'cyn-dm');

        return $columns;
    }

    public static function customer_club_form_table_column($column_name, $post_id)
    {
        if ($column_name === 'phone') {
            echo esc_html(get_post_meta($post_id, '_phone', true));
        }

        if ($column_name === 'club_date') {
            echo esc_html(get_the_date('Y/m/d H:i', $post_id));
        }
    }

    public static function support_form_table_head($columns)
    {
        $columns['name']         = __('نام', 'naghoos');
        $columns['phone']        = __('تلفن همراه', 'naghoos');
        $columns['message']      = __('پیام', 'naghoos');
        $columns['support_date'] = __('تاریخ ثبت', 'naghoos');

        return $columns;
    }

    public static function support_form_table_column($column_name, $post_id)
    {
        if ($column_name === 'name') {
            echo esc_html(get_post_meta($post_id, '_name', true));
        }

        if ($column_name === 'phone') {
            echo esc_html(get_post_meta($post_id, '_phone', true));
        }

        if ($column_name === 'message') {
            echo esc_html(get_post_meta($post_id, '_message', true));
        }

        if ($column_name === 'support_date') {
            echo esc_html(get_the_date('Y/m/d H:i', $post_id));
        }
    }

    public static function renderCustomerClubExportButton($which)
    {
        if ($which !== 'top') {
            return;
        }

        global $typenow;

        if ($typenow !== 'customer_club_form') {
            return;
        }

        $export_url = wp_nonce_url(
            admin_url('edit.php?post_type=customer_club_form&cyn_export_customer_club_csv=1'),
            'cyn_export_customer_club_csv'
        );
?>
        <div class="alignleft actions">
            <a href="<?php echo esc_url($export_url); ?>" class="button button-secondary">
                <?php esc_html_e('خروجی CSV', 'naghoos'); ?>
            </a>
        </div>
<?php
    }

    public static function exportCustomerClubCsv()
    {
        if (!isset($_GET['cyn_export_customer_club_csv'])) {
            return;
        }

        if (!current_user_can('edit_posts')) {
            wp_die(esc_html__('شما اجازه انجام این کار را ندارید.', 'naghoos'));
        }

        check_admin_referer('cyn_export_customer_club_csv');

        $query = new \WP_Query([
            'post_type'      => 'customer_club_form',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);

        $filename = 'customer-club-phones-' . gmdate('Y-m-d') . '.csv';

        nocache_headers();
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        if ($output === false) {
            wp_die(esc_html__('خطا در ایجاد فایل خروجی.', 'naghoos'));
        }

        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, ['ردیف', 'شماره تلفن', 'تاریخ ثبت']);

        $row = 1;

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $phone = get_post_meta(get_the_ID(), '_phone', true);

                fputcsv($output, [
                    $row,
                    $phone,
                    get_the_date('Y/m/d H:i'),
                ]);

                $row++;
            }
        }

        wp_reset_postdata();
        fclose($output);
        exit;
    }

    public static function markCurrentPostAsRead()
    {
        if (!isset($_GET['post']) || !current_user_can('edit_posts')) {
            return;
        }

        $post_id = (int) $_GET['post'];
        $post    = get_post($post_id);

        if (!$post || !isset(self::$unread_badge_config[$post->post_type])) {
            return;
        }

        $read_meta = self::$unread_badge_config[$post->post_type]['read_meta'];
        update_post_meta($post_id, $read_meta, '1');
    }

    public static function getUnreadCount($post_type, $read_meta_key = '_read')
    {
        $query = new \WP_Query([
            'post_type'      => $post_type,
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                'relation' => 'OR',
                ['key' => $read_meta_key, 'compare' => 'NOT EXISTS'],
                ['key' => $read_meta_key, 'value' => '1', 'compare' => '!='],
            ],
        ]);

        return $query->found_posts;
    }

    public static function addUnreadCountToMenu()
    {
        global $menu;

        foreach (self::$unread_badge_config as $post_type => $config) {
            $unread = self::getUnreadCount($post_type, $config['read_meta']);

            if ($unread <= 0) {
                continue;
            }

            $menu_slug = 'edit.php?post_type=' . $post_type;

            foreach ($menu as $i => $item) {
                if (isset($item[2]) && $item[2] === $menu_slug) {
                    $menu[$i][0] .= ' <span class="awaiting-mod count-' . esc_attr($unread) . '"><span class="count">' . (int) $unread . '</span></span>';
                    break;
                }
            }
        }
    }

    public static function registerUnreadBadge($post_type, $read_meta_key = '_read')
    {
        self::$unread_badge_config[$post_type] = ['read_meta' => $read_meta_key];
    }
}
