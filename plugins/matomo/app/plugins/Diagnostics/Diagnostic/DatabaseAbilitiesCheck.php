<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Diagnostics\Diagnostic;

use Piwik\Common;
use Piwik\Config;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\SettingsPiwik;
use Piwik\Translation\Translator;
use Piwik\Url;
/**
 * Check if Piwik can use LOAD DATA INFILE.
 */
class DatabaseAbilitiesCheck implements \Piwik\Plugins\Diagnostics\Diagnostic\Diagnostic
{
    /**
     * @var Translator
     */
    private $translator;
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    public function execute()
    {
        if (!SettingsPiwik::isMatomoInstalled()) {
            // Skip the diagnostic if Matomo is being installed
            return [];
        }
        $result = new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult($this->translator->translate('Installation_DatabaseAbilities'));
        $result->addItem($this->checkUtf8mb4Charset());
        $result->addItem($this->checkCollation());
        if (Config::getInstance()->General['enable_load_data_infile']) {
            $result->addItem($this->checkLoadDataInfile());
        }
        $result->addItem($this->checkTemporaryTables());
        $result->addItem($this->checkTransactionLevel());
        $databaseVersion = Db::fetchOne('SELECT VERSION();');
        if (strpos(strtolower($databaseVersion), 'mariadb') !== \false && Config\DatabaseConfig::getConfigValue('schema') !== 'Mariadb') {
            $comment = $this->translator->translate('Diagnostics_MariaDbNotConfigured');
            $result->addItem(new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_INFORMATIONAL, $comment));
        }
        return [$result];
    }
    protected function checkUtf8mb4Charset()
    {
        $dbSettings = new Db\Settings();
        $charset = $dbSettings->getUsedCharset();
        if (DbHelper::getDefaultCharset() === 'utf8mb4' && $charset === 'utf8mb4') {
            return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK, 'UTF8mb4 charset');
        }
        if (DbHelper::getDefaultCharset() === 'utf8mb4') {
            return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING, 'UTF8mb4 charset<br/><br/>' . $this->translator->translate('Diagnostics_DatabaseUtf8mb4CharsetAvailableButNotUsed', '<code>' . PIWIK_INCLUDE_PATH . '/console core:convert-to-utf8mb4</code>') . '<br/><br/>' . $this->translator->translate('Diagnostics_DatabaseUtf8Requirement', ['�', '<a href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/how-to-update/how-to-convert-the-database-to-utf8mb4-charset/') . '" rel="noreferrer noopener" target="_blank">', '</a>']) . '<br/>');
        }
        return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING, 'UTF8mb4 charset<br/><br/>' . $this->translator->translate('Diagnostics_DatabaseUtf8mb4CharsetRecommended') . '<br/><br/>' . $this->translator->translate('Diagnostics_DatabaseUtf8Requirement', ['�', '<a href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/how-to-update/how-to-convert-the-database-to-utf8mb4-charset/') . '" rel="noreferrer noopener" target="_blank">', '</a>']) . '<br/>');
    }
    protected function checkCollation() : \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem
    {
        $dbSettings = new Db\Settings();
        $collation = $dbSettings->getUsedCollation();
        if ('' !== $collation) {
            return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK, 'Connection collation');
        }
        $collationConnection = Db::get()->fetchOne('SELECT @@collation_connection');
        $collationCharset = DbHelper::getDefaultCollationForCharset($dbSettings->getUsedCharset());
        $message = sprintf('Connection collation<br/><br/>%s<br/><br/>%s<br/>', $this->translator->translate('Diagnostics_DatabaseCollationNotConfigured'), $this->translator->translate('Diagnostics_DatabaseCollationConnection', [$collationConnection]));
        if ('' !== $collationCharset) {
            $message .= $this->translator->translate('Diagnostics_DatabaseCollationCharset', [$collationCharset]) . '<br/>';
        }
        return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING, $message);
    }
    protected function checkLoadDataInfile()
    {
        $optionTable = Common::prefixTable('option');
        $testOptionNames = array('test_system_check1', 'test_system_check2');
        $loadDataInfile = \false;
        $errorMessage = null;
        try {
            $loadDataInfile = Db\BatchInsert::tableInsertBatch($optionTable, array('option_name', 'option_value'), array(array($testOptionNames[0], '1'), array($testOptionNames[1], '2')), $throwException = \true, $charset = 'latin1');
        } catch (\Exception $ex) {
            $errorMessage = str_replace("\n", "<br/>", $ex->getMessage());
        }
        // delete the temporary rows that were created
        Db::exec("DELETE FROM `{$optionTable}` WHERE option_name IN ('" . implode("','", $testOptionNames) . "')");
        if ($loadDataInfile) {
            return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK, 'LOAD DATA INFILE');
        }
        $comment = sprintf('LOAD DATA INFILE<br/>%s<br/>%s', $this->translator->translate('Installation_LoadDataInfileUnavailableHelp', array('LOAD DATA INFILE', 'FILE')), $this->translator->translate('Installation_LoadDataInfileRecommended'));
        if ($errorMessage) {
            $comment .= sprintf('<br/><strong>%s:</strong> %s<br/>%s', $this->translator->translate('General_Error'), $errorMessage, 'Troubleshooting: <a target="_blank" rel="noreferrer noopener" href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/troubleshooting/faq_194') . '">FAQ on matomo.org</a>');
        }
        return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem(\Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING, $comment);
    }
    protected function checkTemporaryTables()
    {
        $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK;
        $comment = 'CREATE TEMPORARY TABLES';
        try {
            // create a temporary table
            Db::exec("CREATE TEMPORARY TABLE `piwik_test_table_temp` (\n                                        id INT,\n                                        val VARCHAR(5) NULL,\n                                        PRIMARY KEY (id)\n                                     )");
            // insert an entry into the new temporary table
            Db::exec('INSERT INTO `piwik_test_table_temp` (`id`, `val`) VALUES ("1", "val1");');
            for ($i = 0; $i < 5; $i++) {
                // try reading the entry a few times to ensure it doesn't fail, which might be possible when using load balanced databases
                $result = Db::fetchRow('SELECT * FROM `piwik_test_table_temp` WHERE `id` = 1');
                if (empty($result)) {
                    throw new \Exception('read failed');
                }
            }
        } catch (\Exception $e) {
            $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_ERROR;
            $comment .= '<br/>' . $this->translator->translate('Diagnostics_MysqlTemporaryTablesWarning');
            $comment .= '<br/>Troubleshooting: <a target="_blank" rel="noreferrer noopener" href="' . Url::addCampaignParametersToMatomoLink('https://matomo.org/faq/how-to-install/faq_23484/') . '">FAQ on matomo.org</a>';
        }
        return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem($status, $comment);
    }
    protected function checkTransactionLevel()
    {
        $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_OK;
        $comment = 'Changing transaction isolation level';
        $level = new Db\TransactionLevel(Db::getReader());
        if (!$level->setTransactionLevelForNonLockingReads()) {
            $status = \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResult::STATUS_WARNING;
            $comment .= '<br/>' . $this->translator->translate('Diagnostics_MysqlTransactionLevel');
        } else {
            $level->restorePreviousStatus();
        }
        return new \Piwik\Plugins\Diagnostics\Diagnostic\DiagnosticResultItem($status, $comment);
    }
}
