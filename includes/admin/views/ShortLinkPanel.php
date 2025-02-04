<?php

namespace ShortLinkManager\Admin\views;

use ShortLinkManager\Admin\AdminAccess;

class ShortLinkPanel
{
    public function __construct()
    {
    }


    private function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['wpsm_add_link'])) {

                check_admin_referer('shortlink_key');

                $short = esc_url_raw($_POST['short']);
                $long = esc_url_raw($_POST['long']);

                if (!empty($short) && !empty($long)) {

                    $db_data_link = get_option('my_shortlink');
                    $db_data_link[$short] = ['long_url' => $long, 'clicks' => 0, 'ip' => '', 'referer' => '', 'date' => ''];
                    update_option('my_shortlink', $db_data_link);
                }
            }
        }

    }


    private function delete()
    {
        if (!empty($_POST['shortlinks']) && isset($_POST['bulk_action']) && $_POST['bulk_action'] === 'delete') {

            check_admin_referer('shortlink_key');

            $db_data_link = get_option('my_shortlink');

            foreach ($_POST['shortlinks'] as $short) {
                unset($db_data_link[$short]);
            }

            update_option('my_shortlink', $db_data_link);
        }
    }

    private function search(&$links)
    {
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        if (!empty($search)) {

            check_admin_referer('shortlink_key');

            $links = array_filter($links, function ($data, $key) use ($search) {
                return stripos($key, $search) !== false || stripos($data['url'], $search) !== false;
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $search;
    }


    function sm_admin_page()
    {
        AdminAccess::check_access();

        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $links_per_page = 20;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->update();
            $this->delete();

            if (isset($_POST['wpsm_add_link'])) {

                check_admin_referer('shortlink_key');

                $short = esc_url_raw($_POST['short']);
                $long = esc_url_raw($_POST['long']);

                if (!empty($short) && !empty($long)) {

                    $db_data_link = get_option('my_shortlink');
                    $db_data_link[$short] = ['long_url' => $long, 'clicks' => 0, 'ip' => '', 'referer' => '', 'date' => ''];
                    update_option('my_shortlink', $db_data_link);
                }
            }


        }

        $links = [];

        $res_shortlink = get_option('my_shortlink');


        if (!empty($res_shortlink) && isset($res_shortlink)) {
            foreach ($res_shortlink as $key => $value) {
                $short = str_replace('wpsm_shortlink_', '', $key);
                $links[$short] = [
                    'url' => $key,
                    'long_url' => $value['long_url'],
                    'clicks' => $value['clicks'],
                    'referer' => $value['referer'] ?? '',
                    'date' => $value['date'] ?? '',
                    'ip' => $value['ip'] ?? '',
                ];
            }
        }

        $search = $this->search($links);

        $total_links = count($links);
        $total_pages = ceil($total_links / $links_per_page);
        $links = array_slice($links, ($current_page - 1) * $links_per_page, $links_per_page, true);
        ?>

        <div class="wrap">
            <h1><?php _e('Shortlink Manager', 'shortlink'); ?></h1>

            <h2><?php _e('Add new link
', 'shortlink'); ?></h2>
            <form method="post">
                <?php wp_nonce_field('shortlink_key'); ?>
                <table class="form-table">
                    <tr>
                        <th><label for="short"><?php _e('Short Link', 'shortlink'); ?></label></th>
                        <td><input type="text" name="short" id="short" required></td>
                    </tr>
                    <tr>
                        <th><label for="long"><?php _e('Long link', 'shortlink'); ?></label></th>
                        <td><input type="url" name="long" id="long" required></td>
                    </tr>
                </table>
                <button type="submit" name="wpsm_add_link" class="button button-primary"><?php _e('Add', 'shortlink'); ?>

                </button>
            </form>


            <h2><?php _e('Search', 'shortlink'); ?></h2>
            <form method="get">
                <?php wp_nonce_field('shortlink_key'); ?>

                <input type="hidden" name="page" value="sm_shortlinks">
                <input type="text" name="search" value="<?php echo esc_attr($search); ?>"
                       placeholder="<?php _e('Search by link...', 'shortlink'); ?>">
                <button type="submit" class="button"><?php _e('Search', 'shortlink'); ?></button>
            </form>

            <form method="post">
                <?php wp_nonce_field('shortlink_key'); ?>
                <div class="tablenav top">
                    <div class="alignleft actions">
                        <select name="bulk_action">
                            <option value="bulk_action"><?php _e('Bulk actions', 'shortlink'); ?></option>
                            <option value="delete"><?php _e('Remove', 'shortlink'); ?></option>
                        </select>
                        <button type="submit" class="button action"><?php _e('Apply', 'shortlink'); ?>
                        </button>
                    </div>
                    <br class="clear">
                </div>

                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <td class="manage-column check-column">
                            <input type="checkbox" id="select-all">
                        </td>
                        <th><?php _e('Короткий URL', 'shortlink'); ?></th>
                        <th><?php _e('Redirect', 'shortlink'); ?></th>
                        <th><?php _e('Transitions', 'shortlink'); ?></th>
                        <th><?php _e('Last action', 'shortlink'); ?></th>
                        <th><?php _e('ip', 'shortlink'); ?></th>
                        <th><?php _e('Referrer', 'shortlink'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($links as $short => $data): ?>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" name="shortlinks[]" value="<?php echo esc_attr($short); ?>">
                            </th>
                            <td><?php echo esc_html($short); ?></td>
                            <td><a href="<?php echo esc_url($data['long_url']); ?>"
                                   target="_blank"><?php echo esc_html($data['long_url']); ?></a></td>
                            <td><?php echo esc_html($data['clicks']); ?></td>
                            <td><?php echo esc_html($data['date']); ?></td>
                            <td><?php echo esc_html($data['ip']); ?></td>
                            <td><?php echo esc_html($data['referer']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="tablenav">
            <div class="tablenav-pages">
                <?php if ($total_pages > 1): ?>
                    <?php if ($current_page > 1): ?>
                        <a class="button"
                           href="?page=sm_shortlinks&paged=<?php echo($current_page - 1); ?>&search=<?php echo esc_attr($search); ?>"><?php _e('', 'shortlink'); ?>
                            «
                            <?php _e('Back', 'shortlink'); ?></a>
                    <?php endif; ?>
                    <span><?php _e('Page', 'shortlink'); ?> <?php echo $current_page; ?> <?php _e('of', 'shortlink'); ?> <?php echo $total_pages; ?></span>
                    <?php if ($current_page < $total_pages): ?>
                        <a class="button"
                           href="?page=sm_shortlinks&paged=<?php echo($current_page + 1); ?>&search=<?php echo esc_attr($search); ?>"><?php _e('', 'shortlink'); ?>
                            <?php _e('Next', 'shortlink'); ?>
                            »</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <script>
            document.getElementById('select-all').addEventListener('click', function () {
                let checkboxes = document.querySelectorAll('input[name="shortlinks[]"]');
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });
        </script>

        <?php
    }

}