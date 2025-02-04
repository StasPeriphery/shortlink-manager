<?php

namespace ShortLinkManager\Admin;

use ShortLinkManager\Admin\views\ShortLinkPanel;

class AdminMenu
{

    private $short_link_panel = null;


    public function __construct()
    {
        $this->short_link_panel = new ShortLinkPanel();
    }


    function add_shortlink_manager_menu() : void
    {
        add_menu_page(
            'Shortlink Manager',
            'Shortlink Manager',
            'manage_options',
            'sm_shortlinks',
            [$this->short_link_panel, 'sm_admin_page'],
            'dashicons-admin-links',
            25
        );
    }

}