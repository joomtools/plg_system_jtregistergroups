<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.JtRegisterGroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Installer script file.
 *
 * @since  2.0.0
 */
return new class () implements InstallerScriptInterface {
    /**
     * Minimum Joomla version to install.
     *
     * @var    string
     * @since  2.0.0
     */
    private string $minimumJoomla = '5';

    /**
     * Minimum PHP version to install.
     *
     * @var    string
     * @since  2.0.0
     */
    private string $minimumPhp = '8.1';

    /**
     * Function called after the extension is installed.
     *
     * @param   InstallerAdapter  $adapter  The adapter calling this method.
     *
     * @return  bool  True on success.
     * @since   2.0.0
     */
    public function install(InstallerAdapter $adapter)
    : bool
    {
        return true;
    }

    /**
     * Function called after the extension is updated.
     *
     * @param   InstallerAdapter  $adapter  The adapter calling this method.
     *
     * @return  bool  True on success.
     * @since   2.0.0
     */
    public function update(InstallerAdapter $adapter)
    : bool
    {
        return true;
    }

    /**
     * Function called after the extension is uninstalled.
     *
     * @param   InstallerAdapter  $adapter  The adapter calling this method.
     *
     * @return  bool  True on success.
     * @since   2.0.0
     */
    public function uninstall(InstallerAdapter $adapter)
    : bool
    {
        return true;
    }

    /**
     * Function called before extension installation/update/removal procedure commences.
     *
     * @param   string            $type     The type of change (install or discover_install, update, uninstall).
     * @param   InstallerAdapter  $adapter  The adapter calling this method.
     *
     * @return  bool  True on success.
     * @since   2.0.0
     */
    public function preflight(string $type, InstallerAdapter $adapter)
    : bool
    {
        if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
            Factory::getApplication()->enqueueMessage(sprintf(Text::_('JLIB_INSTALLER_MINIMUM_PHP'), $this->minimumPhp), 'error');

            return false;
        }

        if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Factory::getApplication()->enqueueMessage(sprintf(Text::_('JLIB_INSTALLER_MINIMUM_JOOMLA'), $this->minimumJoomla), 'error');

            return false;
        }

        if ($type === 'update') {
            $extensionId = $adapter->currentExtensionId;

            if (!empty($extensionId) && version_compare($extensionId, '2.0.1', '<')) {
                $deletes    = array();
                $pluginPath = JPATH_PLUGINS . '/system/jtregistergroups';

                $deletes['folder'] = [
                    // Before 2.0.1
                    $pluginPath . '/fields',
                    $pluginPath . '/rules',
                    $pluginPath . '/xml',
                ];

                $deletes['file'] = [
                    // Before 2.0.1
                    $pluginPath . '/jtregistergroups.php',
                    $pluginPath . '/language/de-DE/de-DE.plg_system_jtregistergroups.ini',
                    $pluginPath . '/language/de-DE/de-DE.plg_system_jtregistergroups.sys.ini',
                    $pluginPath . '/language/en-GB/en-GB.plg_system_jtregistergroups.ini',
                    $pluginPath . '/language/en-GB/en-GB.plg_system_jtregistergroups.sys.ini',
                ];

                foreach ($deletes as $key => $orphans) {
                    $this->deleteOrphans($key, $orphans);
                }
            }

            sleep(1);
        }

        return true;
    }

    /**
     * Function called after extension installation/update/removal procedure commences.
     *
     * @param   string            $type     The type of change (install or discover_install, update, uninstall)
     * @param   InstallerAdapter  $adapter  The adapter calling this method
     *
     * @return  bool  True on success
     * @since   2.0.0
     */
    public function postflight(string $type, InstallerAdapter $adapter)
    : bool
    {
        return true;
    }

    /**
     * Delete files and folders
     *
     * @param   string  $type     Which type are orphans of (file or folder)
     * @param   array   $orphans  Array of files or folders to delete
     *
     * @return  void
     * @since   2.0.1
     */
    private function deleteOrphans($type, array $orphans)
    : void
    {
        $app = Factory::getApplication();

        foreach ($orphans as $item) {
            if ($type == 'folder' && (is_dir($item) && Folder::delete($item) === false)) {
                $app->enqueueMessage(Text::sprintf('PLG_CONTENT_JTF_NOT_DELETED', $item), 'warning');
            }

            if ($type == 'file' && (is_file($item) && File::delete($item) === false)) {
                $app->enqueueMessage(Text::sprintf('PLG_CONTENT_JTF_NOT_DELETED', $item), 'warning');
            }
        }
    }
};
