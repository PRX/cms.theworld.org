<?php
// © Microsoft Corporation. All rights reserved.

namespace microsoft_start\routes;

use microsoft_start\services\TokenService;

use microsoft_start\infrastructure\ApiController;
use microsoft_start\services\Options;
class authenticationApi extends ApiController
{
    function register_routes()
    {
        register_rest_route('microsoft/v1', '/connect', [
            'methods' => 'GET',
            'permission_callback' => function () {
                return current_user_can('activate_plugins');
            },
            'callback' => function () {
                $callbackUrl = urlencode(admin_url("admin.php?page=msn-callback"));
                $creatorCenterUrl = MSPH_MSN_BASE_URL;
                wp_redirect("{$creatorCenterUrl}/dialog/connect#callback_url=$callbackUrl");
                exit;
            }
        ]);

        register_rest_route('microsoft/v1', '/redeemCode', [
            'methods' => 'POST',
            'permission_callback' => function () {
                return current_user_can('activate_plugins');
            },
            'callback' => function ($data) {
                $parameters = $data->get_json_params();
                $msnAccountUrl = MSPH_SERVICE_URL;

                $response = wp_remote_post(
                    "{$msnAccountUrl}account/RedeemCode/{$parameters['redeemCode']}?wrapodata=false",
                    [
                        'timeout'       => 60,
                        'headers' => [
                            "accept" => "*/*",
                        ],
                        'method'      => 'GET'
                    ]
                );
                if (is_wp_error($response)) {
                    return null;
                }
                switch ($response['response']['code']) {
                    case 200:
                        $result = json_decode($response['body']);
                        TokenService::set_client($result->appId, $result->appSecret);
                        if (array_key_exists('sharePastPostsStartDate', $parameters)) {
                            Options::set_share_past_posts_start_date($parameters['sharePastPostsStartDate']);
                        }

                        break;
                    default:
                        header("HTTP/1.1 500 Internal Server Error");
                        die($response['body']);
                }
            }
        ]);

        register_rest_route('microsoft/v1', '/token', [
            'methods' => 'GET',
            'permission_callback' => function () {
                return current_user_can('activate_plugins');
            },
            'callback' => function () {
                return ['token' => TokenService::get_token()];
            }
        ]);

        register_rest_route('microsoft/v1', '/delete-token', [
            'methods' => 'POST',
            'permission_callback' => function () {
                return current_user_can('activate_plugins');
            },
            'callback' => function () {
                Options::set_red_dot(0);
                TokenService::delete_token();
            }
        ]);
    }
}
