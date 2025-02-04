<?php

namespace ShortLinkManager;

class SupportFunctions
{

    public function __construct()
    {
        add_action('init', [$this, 'redirect_with_statistics']);
    }


    function redirect_with_statistics(){
        $request = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $short_links = get_option('my_shortlink');
        $request_url = home_url('/' . esc_html($request));


        if (isset($short_links[$request_url])) {
            $short_links[$request_url]['ip'] = $_SERVER['REMOTE_ADDR'];
            $short_links[$request_url]['referer'] = $_SERVER['HTTP_REFERER'] ?? '';
            $short_links[$request_url]['date'] = current_time('mysql');
            $short_links[$request_url]['clicks'] += 1 ;

            update_option('my_shortlink', $short_links);

            wp_redirect($short_links[$request_url]['long_url'], 301);
            exit;
        }
    }

}