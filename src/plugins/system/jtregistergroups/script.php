<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.JtRegisterGroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;

/**
 * Script file of Joomla CMS
 *
 * @since  1.0.0
 */
class PlgSystemJtregistergroupsInstallerScript
{
    /**
     * Extension script constructor.
     *
     * @since  1.0.0
     */
    public function __construct()
    {
        // Define the minumum versions to be supported.
        $this->minimumJoomla = '3.9';
        $this->minimumPhp    = '7.2';
    }

    /**
     * Function to act prior to installation process begins
     *
     * @param   string     $action     Which action is happening (install|uninstall|discover_install|update)
     * @param   Installer  $installer  The class calling this method
     *
     * @return  boolean  True on success
     * @since   1.0.0
     */
    public function preflight($action, $installer)
    {
        $app = Factory::getApplication();
        Factory::getLanguage()->load('plg_content_jteasylaw', dirname(__FILE__));

        if (version_compare(PHP_VERSION, $this->minimumPhp, 'lt')) {
            $app->enqueueMessage(Text::_('PLG_SYSTEM_JTREGISTERGROUPS_MINPHPVERSION'), 'error');

            return false;
        }

        if (version_compare(JVERSION, $this->minimumJoomla, 'lt')) {
            $app->enqueueMessage(Text::_('PLG_SYSTEM_JTREGISTERGROUPS_MINJVERSION'), 'error');

            return false;
        }

        return true;
    }
}
