<?php
// © Microsoft Corporation. All rights reserved.

namespace microsoft_start\infrastructure;

use microsoft_start\services\MSNClient;
use microsoft_start\services\Options;

abstract class Page extends Registration
{
    function __construct()
    {
        add_action('admin_menu', function () {
            
            if (empty($GLOBALS['admin_page_hooks']['microsoft'])) {
                $current_timestamp = time();
                $red_dot = Options::get_red_dot();
                $red_dot_count = $red_dot['count'];
                if ($red_dot['timestamp'] + HOUR_IN_SECONDS * 6 < $current_timestamp) {
                    MSNClient::get_notification('sidebar');
                    $red_dot_count = Options::get_red_dot()['count'];
                }
                //https://wordpress.stackexchange.com/questions/311412/how-can-i-make-my-admin-icon-svg-color-correctly
                $image = base64_encode("<svg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <path fill-rule='evenodd' clip-rule='evenodd' d='M4 4H11V11H4V4Z' fill=\"white\"/>
                <path fill-rule='evenodd' clip-rule='evenodd' d='M4 13H11V20H4V13Z' fill=\"white\"/>
                <path fill-rule='evenodd' clip-rule='evenodd' d='M13 4H20V11H13V4Z' fill=\"white\"/>
                <path fill-rule='evenodd' clip-rule='evenodd' d='M13 13H20V20H13V13Z' fill=\"white\"/>
                </svg>");

                add_menu_page(
                    __('General', "microsoft-start"),
                    /* translators: Name of plugin, appears in menu bar and edit bar, or in connection page when user tries to connect*/
                    __(
                        'Microsoft Start',
                        "microsoft-start"
                    ) . ($red_dot_count ? '<span class="awaiting-mod">'. $red_dot_count .'</span>' : ''),
                    'manage_options',
                    'microsoft',
                    '',
                    'data:image/svg+xml;base64,' . $image,
                    0
                );
            }
            remove_submenu_page('microsoft', 'microsoft');

            $this->admin_menu();
        });
    }

    abstract protected function admin_menu();
    abstract protected function render();

    function add_submenu_page(string $id, string $title, callable $enqueueScripts)
    {
        Util::add_submenu_page($id, $title, [$this, 'render'], $enqueueScripts);
    }
}
