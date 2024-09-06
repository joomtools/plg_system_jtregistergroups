<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.JtRegisterGroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

namespace JoomTools\Plugin\System\JtRegisterGroups\Extension;

use Joomla\CMS\Event\CustomFields;
use Joomla\CMS\Event\Model;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserHelper;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Class to register user to selected group
 * using individual custom field for each group
 *
 * @since  1.0.0
 */
final class JtRegisterGroups extends CMSPlugin implements SubscriberInterface
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since   2.0.0
     */
    public static function getSubscribedEvents()
    : array
    {
        return [
            'onContentPrepareData'     => 'onContentPrepareData',
            'onContentPrepareForm'     => 'onContentPrepareForm',
            'onCustomFieldsPrepareDom' => 'onCustomFieldsPrepareDom',
        ];
    }

    /**
     * Runs on content preparation
     *
     * @param   Model\PrepareDataEvent  $event  Event instance
     *
     * @return  void
     * @since   1.0.0
     */
    public function onContentPrepareData(Model\PrepareDataEvent $event)
    : void
    {
        /**
         * @var   string $context The form to be altered.
         * @var   mixed  $data    The associated data for the form.
         */
        [$context, $data] = array_values($event->getArguments());

        if (!in_array($context, ['com_users.registration', 'com_admin.profile'])) {
            return;
        }

        if ($context == 'com_admin.profile') {
            $user   = $this->getApplication()->getIdentity();
            $userId = (int) $user->id;

            $this->getApplication()->setUserState('com_users.edit.user.id', $userId);
            $this->getApplication()->redirect('index.php?option=com_users&view=user&layout=edit&id=' . $userId);
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
     * @param   Model\PrepareFormEvent  $event  Event instance
     *
     * @return  void
     * @since   1.0.0
     */
    public function onContentPrepareForm(Model\PrepareFormEvent $event)
    : void
    {
        /**
         * @var   Form  $form The form to be altered.
         * @var   mixed $data The associated data for the form.
         */
        [$form, $data] = array_values($event->getArguments());

        $name    = $form->getName();
        $plgPath = JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name;

        FormHelper::addFormPath($plgPath . '/forms');

        if ($name == 'com_menus.item'
            && (!empty($data->link) && $data->link == 'index.php?option=com_users&view=registration')
        ) {
            FormHelper::addFieldPrefix('JoomTools\\Plugin\\System\\JtRegisterGroups\\Field');
            FormHelper::addRulePrefix('JoomTools\\Plugin\\System\\JtRegisterGroups\\Rule');

            $form->loadFile('register_group');
        }

        if ($name == 'com_fields.field.com_users.user') {
            $form->loadFile('fields_groups');
        }
    }

    /**
     * Remove not needed custom fields
     *
     * @param   CustomFields\PrepareDomEvent  $event  Event instance
     *
     * @return  void
     * @since   1.0.0
     */
    public function onCustomFieldsPrepareDom(CustomFields\PrepareDomEvent $event)
    : void
    {
        /**
         * @var   \stdClass $field The field.
         * @var   Form      $form  The form.
         */
        [$field, $_, $form] = array_values($event->getArguments());

        $name       = $form->getName();
        $groupFound = false;

        if (!in_array($name, ['com_users.profile', 'com_users.user', 'com_users.registration'])) {
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
            $userGroups = $this->getUserGroups($name);

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
     * @return  null|string  Selected group.
     * @since   1.0.0
     */
    private function getMenuGroup()
    : ?string
    {
        $menuItem = (int) $this->getApplication()->getMenu()->getActive()->id;

        if ($menuItem) {
            return $this->getApplication()->getMenu()->getItem($menuItem)->getParams()->get('register_group');
        }

        return null;
    }

    /**
     * Get groups set to users
     *
     * @param   string  $context  The context for the data.
     *
     * @return  array  Selected groups.
     * @since   1.0.0
     */
    private function getUserGroups($context)
    : array
    {
        $userId = (int) $this->getApplication()->getInput()->get('id');

        if ($context == 'com_users.profile') {
            $userId = (int) $this->getApplication()->getIdentity()->id;
        }

        return UserHelper::getUserGroups($userId);
    }
}
