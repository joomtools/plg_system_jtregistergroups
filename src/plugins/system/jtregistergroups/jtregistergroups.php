<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.Jtregistergroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserHelper;

/**
 * Class to register user to selected group
 * using individual custom field for each group
 *
 * @since  1.0.0
 */
class PlgSystemJtregistergroups extends CMSPlugin
{
    /**
     * Global application object
     *
     * @var    CMSApplication
     * @since  1.0.0
     */
    protected $app = null;

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Runs on content preparation
     *
     * @param   string  $context  The context for the data
     * @param   object  $data     An object containing the data for the form.
     *
     * @return  void
     * @since   1.0.0
     */
    public function onContentPrepareData($context, $data)
    {
        if (!in_array($context, array('com_users.registration', 'com_admin.profile'))) {
            return;
        }

        if ($context == 'com_admin.profile') {
            $userId = (int) Factory::getUser()->id;
            $this->app->setUserState('com_users.edit.user.id', $userId);
            $this->app->redirect('index.php?option=com_users&view=user&layout=edit&id=' . $userId);
        }

        if ($context == 'com_users.registration') {
            $newUserGroup = $this->getMenuGroup();

            if (!empty($newUserGroup)) {
                $data->groups = (array) $newUserGroup;
            }
        }
    }

    /**
     * Adds additional fields to the user editing form
     *
     * @param   Form   $form  The form to be altered.
     * @param   mixed  $data  The associated data for the form.
     *
     * @return  void
     * @since   1.0.0
     */
    public function onContentPrepareForm(Form $form, $data)
    {
        $name = $form->getName();

        Form::addFormPath(__DIR__ . '/xml');

        if ($name == 'com_menus.item'
            && (!empty($data->link) && $data->link == 'index.php?option=com_users&view=registration')
        ) {
            Form::addFieldPath(__DIR__ . '/fields');
            Form::addRulePath(__DIR__ . '/rules');

            \JLoader::registerNamespace('Joomla\\CMS\\Form\\Field', __DIR__ . '/fields', false, true, 'psr4');
            \JLoader::registerNamespace('Joomla\\CMS\\Form\\Rule', __DIR__ . '/rules', false, true, 'psr4');

            $form->loadFile('register_group');
        }

        if ($name == 'com_fields.field.com_users.user') {
            $form->loadFile('fields_groups');
        }
    }

    /**
     * Remove not needed custom fields
     *
     * @return  void
     * @since   1.0.0
     */
    public function onCustomFieldsPrepareDom($field, $fieldset, $form)
    {
        $name       = $form->getName();
        $groupFound = false;

        if (!in_array($name, array('com_users.profile', 'com_users.user', 'com_users.registration'))) {
            return;
        }

        $fieldGroups = (array) $field->params->get('fields_groups');

        if (empty($fieldGroups)) {
            return;
        }

        if ($name == 'com_users.registration') {
            $newUserGroup = $this->getMenuGroup();

            if (in_array($newUserGroup, $fieldGroups)) {
                $groupFound = true;
            }
        } else {
            $userGroups = (array) $this->getUserGroups($name);

            foreach ($userGroups as $userGroup) {
                if (in_array($userGroup, $fieldGroups)) {
                    $groupFound = true;
                }
            }
        }

        if ($groupFound) {
            return;
        }

        $field->type = '';
    }

    /**
     * Get group set in menuitem
     *
     * @return  null|string  Selected group
     * @since   1.0.0
     */
    private function getMenuGroup()
    {
        $menuItem = (int) $this->app->getMenu()->getActive()->id;

        if ($menuItem) {
            return $this->app->getMenu()->getItem($menuItem)->getParams()->get('register_group');
        }

        return null;
    }

    /**
     * Get groups set to users
     *
     * @param   string  $context  The context for the data
     *
     * @return  array  Selected groups
     * @since   1.0.0
     */
    private function getUserGroups($context)
    {
        $userId = (int) $this->app->input->get('id');

        if ($context == 'com_users.profile') {
            $userId = (int) Factory::getUser()->id;
        }

        return UserHelper::getUserGroups($userId);
    }
}
