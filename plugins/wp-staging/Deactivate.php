<?php

namespace WPStaging;

use WPStaging\Core\Cron\Cron;
use WPStaging\Framework\BackgroundProcessing\BackgroundProcessingServiceProvider;
use WPStaging\Framework\BackgroundProcessing\FeatureDetection;
use WPStaging\Framework\BackgroundProcessing\QueueProcessor;

/**
 * Actions to perform when we deactivate WP Staging Plugin
 */
class Deactivate
{
    /**
     * @var string
     */
    private $currentPluginFile;

    /**
     * @param string $currentPluginFile
     */
    public function __construct($currentPluginFile)
    {
        $this->currentPluginFile = $currentPluginFile;

        // Early bail
        // This filter hook is for internal use only
        if (apply_filters('wpstg.deactivation_hook.skip_mu_delete', false)) {
            return;
        }

        // Only delete MU plugin when no other wp staging plugin is activated
        if (!$this->isOtherWPStagingPluginActivated()) {
            $this->deleteMuPlugin();
        }

        $this->deleteBackupSchedulesFromCron();
        $this->deleteOtherCron();
    }

    /**
     * Check if any other WP Staging Plugin is activated other than current one
     *
     * @return boolean
     */
    private function isOtherWPStagingPluginActivated()
    {
        foreach (wp_get_active_and_valid_plugins() as $activePlugin) {
            if ($activePlugin === $this->currentPluginFile) {
                continue;
            }

            if (strpos($activePlugin, 'wp-staging.php') !== false || strpos($activePlugin, 'wp-staging-pro.php') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * delete MuPlugin
     */
    private function deleteMuPlugin()
    {
        $muDir = (defined('WPMU_PLUGIN_DIR')) ? WPMU_PLUGIN_DIR : trailingslashit(WP_CONTENT_DIR) . 'mu-plugins';
        $dest = trailingslashit($muDir) . 'wp-staging-optimizer.php';

        if (file_exists($dest) && !unlink($dest)) {
            return false;
        }

        return true;
    }

    protected function deleteBackupSchedulesFromCron()
    {
        if (!file_exists(__DIR__ . '/Backup/BackupScheduler.php')) {
            return;
        }

        if (!class_exists('\WPStaging\Backup\BackupScheduler')) {
            require_once __DIR__ . '/Backup/BackupScheduler.php';
        }

        \WPStaging\Backup\BackupScheduler::removeBackupSchedulesFromCron();
    }

    /**
     * delete Other Cron
     */
    private function deleteOtherCron()
    {
        $hooks = [
            FeatureDetection::ACTION_AJAX_SUPPORT_FEATURE_DETECTION,
            BackgroundProcessingServiceProvider::ACTION_QUEUE_MAINTAIN,
            QueueProcessor::ACTION_QUEUE_PROCESS,
            Cron::ACTION_WEEKLY_EVENT,
            Cron::ACTION_DAILY_EVENT
        ];

        foreach ($hooks as $hook) {
            if (wp_get_schedule($hook)) {
                wp_clear_scheduled_hook($hook);
            }
        }
    }
}
