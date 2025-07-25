<?php

/**
 * This namespace is wrong! Is namespacing here even required?
 * Test carefully if consider removing it. Our code should generally always be namespaced to prevent name conflicts.
 */
namespace WPStaging\Backend;

use WPStaging\Core\Cron\Cron;

/**
 * Uninstall WP-Staging
 *
 * @package     WPSTG
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.0
 */
// No direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Avoid using const here, we don't want to load the whole plugin
 */
class uninstall
{
    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $options = json_decode(json_encode(get_option("wpstg_settings", [])));

        /**
         * @todo Write a query that delete all the options from option table where option_name like (wpstg_* or wpstgpro_*)
         *       but not in (wpstg_existing_clones, wpstg_existing_clones_beta, wpstg_staging_sites, wpstg_connection)
         *       but there will be not need of this condition once we add a routine that deletes staging sites
         */
        if (isset($options->unInstallOnDelete) && $options->unInstallOnDelete === '1') {
            // Options
            delete_option("wpstg_version_upgraded_from");
            delete_option("wpstg_version");
            delete_transient("wpstg_login_link_settings");
            delete_option("wpstgpro_version_upgraded_from");
            delete_option("wpstgpro_version");
            // @see \WPStaging\Backend\Pro\Upgrade\Upgrade::OPTION_UPGRADE_DATE
            delete_option("wpstgpro_upgrade_date");
            // @see \WPStaging\Backend\Upgrade\Upgrade::OPTION_UPGRADE_DATE
            delete_option("wpstg_free_upgrade_date");
            delete_option("wpstg_installDate"); // @deprecated
            // @see \WPStaging\Backend\Upgrade\Upgrade::OPTION_INSTALL_DATE
            delete_option("wpstg_free_install_date");
            // @see \WPStaging\Backend\Pro\Upgrade\Upgrade::OPTION_INSTALL_DATE
            delete_option("wpstgpro_install_date");
            delete_option("wpstg_firsttime"); // @deprecated
            delete_option("wpstg_is_staging_site");
            delete_option("wpstg_settings");
            delete_option("wpstg_rmpermalinks_executed");
            delete_option("wpstg_activation_redirect");
            delete_option("wpstg_disabled_items_notice"); // @deprecated
            delete_option("wpstg_clone_settings");
            delete_option("wpstg_clone_excluded_files_list");
            delete_option("wpstg_different_prefix_backup_notice");
            /* @see \WPStaging\Pro\Notices\EntireNetworkCloneServerConfigNotice::OPTION_NAME */
            delete_option("wpstg_entire_network_clone_notice");
            // Old notice used for display cache on staging site.
            delete_option("wpstg_disabled_cache_notice"); // @deprecated
            // Old option, now moved inside wpstg_clone_settings
            delete_option("wpstg_emails_disabled"); // @deprecated
            delete_option("wpstg_disabled_mail_notice"); // @deprecated
            // Option related to staging sites shifting from one db option to another
            delete_option("wpstg_structure_updated"); // @deprecated
            /**
             * Option releted to the latest WP STAGING PRO version
             * @see WPStaging\Pro\License::OPTION_PRO_LATEST_VERSION
             */
            delete_option("wpstg_pro_latest_version");
            // Option that hold the old snapshots
            delete_option("wpstg_snapshots"); // @deprecated
            delete_option("wpstg_access_token");
            delete_option("wpstg_backups");
            delete_option("wpstg_old_staging_sites_backup"); // @deprecated
            delete_option("wpstg_staging_sites_backup");
            delete_option("wpstg_missing_cloneName_routine_executed");
            delete_option('wpstg_googledrive');
            delete_option('wpstg_amazons3');
            delete_option('wpstg_sftp');
            delete_option('wpstg_digitalocean');
            delete_option('wpstg_wasabi');
            delete_option('wpstg_dropbox');
            delete_option('wpstg_one-drive');
            delete_option('wpstg_pcloud');
            delete_option('wpstg_free_backup_notice_dismissed');
            delete_option('wpstg_first_backup_speed_index');
            delete_option('wpstg_backup_speed_index');
            delete_option('wpstg_backup_speed_modal_shown');
            delete_option('wpstg_backup_notice_is_closed');
            delete_option('wpstg_backup_notice_remind_me');
            delete_option('wpstg_resave_permalinks_executed');

            // @see \WPStaging\Backup\BackupScheduler::OPTION_BACKUP_SCHEDULES
            delete_option('wpstg_backup_schedules');

            /**
             * @see \WPStaging\Framework\Security\UniqueIdentifier::IDENTIFIER_OPTION_NAME;
             */
            delete_option("wpstg_unique_identifier");


            /* Do not delete these fields without actually deleting the staging site
             * @create a delete routine which deletes the staging sites first
             */
            //delete_option( "wpstg_existing_clones" );
            //delete_option( "wpstg_existing_clones_beta" );
            //delete_option( "wpstg_staging_sites" );
            //delete_option( "wpstg_connection" );

            // Old wpstg 1.3 options for admin notices
            delete_option("wpstg_start_poll");
            delete_option("wpstg_hide_beta");
            delete_option("wpstg_RatingDiv");

            // New 2.x options for admin notices
            delete_option("wpstg_poll");
            delete_option("wpstg_rating");
            delete_option("wpstg_beta");

            /* @see \WPStaging\Staging\FirstRun::FIRST_RUN_KEY */
            delete_option('wpstg_execute');

            /* @see \WPStaging\Framework\BackgroundProcessing\Queue::QUEUE_TABLE_VERSION_KEY */
            delete_option('wpstg_queue_table_version');

            /** @see \WPStaging\Framework\BackgroundProcessing\Queue::QUEUE_TABLE_STRUCTURE_VERSION_KEY */
            delete_option('wpstg_queue_table_structure_version');

            /** @see \WPStaging\Framework\Notices\WarningsNotice::OPTION_NAME */
            delete_option('wpstg_warnings_notice');

            // Delete events
            wp_clear_scheduled_hook(Cron::ACTION_WEEKLY_EVENT);

            // Transients
            delete_transient("wpstg_issue_report_submitted");

            /** @see \WPStaging\Framework\Assets::TRANSIENT_REST_URL */
            delete_transient('wpstg_rest_url');
        }
    }
}

new uninstall();
