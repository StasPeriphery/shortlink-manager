<?php

namespace ShortLinkManager\Admin;

class AdminAccess
{

    /**
     * filter of access
     * @return mixed|null
     */
    public static function get_allowed_roles()
    {
        return apply_filters('wpsm_allowed_roles', ['administrator']);
    }

    /**
     * check access
     * @return void
     */
    public static function check_access() :void
    {
        $allowed_roles = static::get_allowed_roles();
        $user = wp_get_current_user();
        if (!array_intersect($allowed_roles, $user->roles)) {
            wp_die(__('You do not have permission to access this page.'));
        }
    }

}