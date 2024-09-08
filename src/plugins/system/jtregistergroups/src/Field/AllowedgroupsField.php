<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.JtRegisterGroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

namespace JoomTools\Plugin\System\JtRegisterGroups\Field;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class to show allowed groups in menu item
 * selected in plugin params.
 *
 * @since  1.0.0
 */
class AllowedgroupsField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'Allowedgroups';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0.0
     */
    protected function getOptions()
    : array
    {
        $pluginRegisterglobals = PluginHelper::getPlugin('system', 'jtregistergroups');
        $pluginParams          = new Registry($pluginRegisterglobals->params);
        $allowedGroups         = (array) $pluginParams->get('set_allowed_usertypes', '');
        $groups                = UserGroupsHelper::getInstance();
        $options               = [];

        // Set Global
        $userParams       = ComponentHelper::getParams('com_users');
        $globalGroup      = (int) $userParams->get('new_usertype');
        $globalGroupTitle = $groups->get($globalGroup)->title;
        $options[]        = (object) [
            'text'  => Text::_('JGLOBAL_USE_GLOBAL') . ' (' . $globalGroupTitle . ')',
            'value' => 0,
        ];

        foreach ($allowedGroups as $allowed) {
            if (!$group = $groups->get($allowed)) {
                continue;
            }

            $options[] = (object) [
                'text'  => $group->title,
                'value' => $group->id,
            ];
        }

        return $options;
    }
}
