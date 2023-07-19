<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd8c3e811ca4318a1e990ee18d894b463
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DrewM\\MailChimp\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DrewM\\MailChimp\\' => 
        array (
            0 => __DIR__ . '/..' . '/drewm/mailchimp-api/src',
        ),
    );

    public static $classMap = array (
        'CS_REST_Administrators' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_administrators.php',
        'CS_REST_Campaigns' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_campaigns.php',
        'CS_REST_Clients' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_clients.php',
        'CS_REST_Events' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_events.php',
        'CS_REST_General' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_general.php',
        'CS_REST_JourneyEmails' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_journey_emails.php',
        'CS_REST_Journeys' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_journeys.php',
        'CS_REST_Lists' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_lists.php',
        'CS_REST_People' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_people.php',
        'CS_REST_Segments' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_segments.php',
        'CS_REST_Subscribers' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_subscribers.php',
        'CS_REST_Templates' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_templates.php',
        'CS_REST_Transactional_ClassicEmail' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_transactional_classicemail.php',
        'CS_REST_Transactional_SmartEmail' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_transactional_smartemail.php',
        'CS_REST_Transactional_Timeline' => __DIR__ . '/..' . '/campaignmonitor/createsend-php/csrest_transactional_timeline.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Newspack\\Newsletters\\Subscription_List' => __DIR__ . '/../..' . '/includes/class-subscription-list.php',
        'Newspack\\Newsletters\\Subscription_Lists' => __DIR__ . '/../..' . '/includes/class-subscription-lists.php',
        'Newspack_Newsletters' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters.php',
        'Newspack_Newsletters_Active_Campaign' => __DIR__ . '/../..' . '/includes/service-providers/active_campaign/class-newspack-newsletters-active-campaign.php',
        'Newspack_Newsletters_Active_Campaign_Controller' => __DIR__ . '/../..' . '/includes/service-providers/active_campaign/class-newspack-newsletters-active-campaign-controller.php',
        'Newspack_Newsletters_Ads' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-ads.php',
        'Newspack_Newsletters_Blocks' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-blocks.php',
        'Newspack_Newsletters_Bulk_Actions' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-bulk-actions.php',
        'Newspack_Newsletters_Campaign_Monitor' => __DIR__ . '/../..' . '/includes/service-providers/campaign_monitor/class-newspack-newsletters-campaign-monitor.php',
        'Newspack_Newsletters_Campaign_Monitor_Controller' => __DIR__ . '/../..' . '/includes/service-providers/campaign_monitor/class-newspack-newsletters-campaign-monitor-controller.php',
        'Newspack_Newsletters_Constant_Contact' => __DIR__ . '/../..' . '/includes/service-providers/constant_contact/class-newspack-newsletters-constant-contact.php',
        'Newspack_Newsletters_Constant_Contact_Controller' => __DIR__ . '/../..' . '/includes/service-providers/constant_contact/class-newspack-newsletters-constant-contact-controller.php',
        'Newspack_Newsletters_Constant_Contact_SDK' => __DIR__ . '/../..' . '/includes/service-providers/constant_contact/class-newspack-newsletters-constant-contact-sdk.php',
        'Newspack_Newsletters_ESP_API_Interface' => __DIR__ . '/../..' . '/includes/service-providers/interface-newspack-newsletters-esp-service.php',
        'Newspack_Newsletters_Editor' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-editor.php',
        'Newspack_Newsletters_Embed' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-embed.php',
        'Newspack_Newsletters_Layouts' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-layouts.php',
        'Newspack_Newsletters_Letterhead' => __DIR__ . '/../..' . '/includes/service-providers/letterhead/class-newspack-newsletters-letterhead.php',
        'Newspack_Newsletters_Letterhead_Promotion' => __DIR__ . '/../..' . '/includes/service-providers/letterhead/models/class-newspack-newsletters-letterhead-promotion.php',
        'Newspack_Newsletters_Letterhead_Promotion_Dto' => __DIR__ . '/../..' . '/includes/service-providers/letterhead/dtos/class-newspack-newsletters-letterhead-promotion-dto.php',
        'Newspack_Newsletters_Logger' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-logger.php',
        'Newspack_Newsletters_Mailchimp' => __DIR__ . '/../..' . '/includes/service-providers/mailchimp/class-newspack-newsletters-mailchimp.php',
        'Newspack_Newsletters_Mailchimp_Cached_Data' => __DIR__ . '/../..' . '/includes/service-providers/mailchimp/class-newspack-newsletters-mailchimp-cached-data.php',
        'Newspack_Newsletters_Mailchimp_Controller' => __DIR__ . '/../..' . '/includes/service-providers/mailchimp/class-newspack-newsletters-mailchimp-controller.php',
        'Newspack_Newsletters_Mailchimp_Groups' => __DIR__ . '/../..' . '/includes/service-providers/mailchimp/class-newspack-newsletters-mailchimp-groups.php',
        'Newspack_Newsletters_Quick_Edit' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-quick-edit.php',
        'Newspack_Newsletters_Renderer' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-renderer.php',
        'Newspack_Newsletters_Service_Provider' => __DIR__ . '/../..' . '/includes/service-providers/class-newspack-newsletters-service-provider.php',
        'Newspack_Newsletters_Service_Provider_Controller' => __DIR__ . '/../..' . '/includes/service-providers/class-newspack-newsletters-service-provider-controller.php',
        'Newspack_Newsletters_Settings' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-settings.php',
        'Newspack_Newsletters_Subscription' => __DIR__ . '/../..' . '/includes/class-newspack-newsletters-subscription.php',
        'Newspack_Newsletters_WP_Hookable_Interface' => __DIR__ . '/../..' . '/includes/service-providers/interface-newspack-newsletters-wp-hookable.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd8c3e811ca4318a1e990ee18d894b463::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd8c3e811ca4318a1e990ee18d894b463::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd8c3e811ca4318a1e990ee18d894b463::$classMap;

        }, null, ClassLoader::class);
    }
}
