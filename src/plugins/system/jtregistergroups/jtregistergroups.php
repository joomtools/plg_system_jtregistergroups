<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Factory;

/**
 * An example custom profile plugin.
 *
 * @since  1.6
 */
class PlgSystemJtregistergroups extends JPlugin
{
	/**
	 * Formname
	 *
	 * @var     string
	 * @since   1.0.0
	 */
	private $formName = null;
	/**
	 * Global application object
	 *
	 * @var     JApplication
	 * @since   1.0.0
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
	 * @return  boolean
	 * @since   1.0.0
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			return true;
		}

		if ($context == 'com_users.registration')
		{
			$newUserGroup = (array) $this->getMenuGroup();

			if (!empty($newUserGroup))
			{
				$data->groups = $newUserGroup;
			}

			return true;
		}

		return true;
	}

	/**
	 * Get group set in menuitem
	 *
	 * @return   string|null  Selected group
	 * @since    1.0.0
	 */
	private function getMenuGroup()
	{
		$menuItem = (int) $this->app->getMenu()->getActive()->id;

		if ($menuItem)
		{
			return $this->app->getMenu()->getItem($menuItem)->getParams()->get('register_group');
		}

		return null;
	}

	/**
	 * Get group set in menuitem
	 *
	 * @param   string  $context  The context for the data
	 *
	 * @return   array  Selected group
	 * @since    1.0.0
	 */
	private function getUserGroups($context)
	{
		$userId = (int) $this->app->input->get('id');

		if ($context == 'com_users.profile')
		{
			$userId = (int) Factory::getUser()->id;
		}

		return (array) UserHelper::getUserGroups($userId);
	}
	/**
	 * Adds additional fields to the user editing form
	 *
	 * @param   JForm  $form  The form to be altered.
	 * @param   mixed  $data  The associated data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareForm(JForm $form, $data)
	{
		$name = $form->getName();

		if ($name =='com_menus.item'
			&& (!empty($data->link) && $data->link == 'index.php?option=com_users&view=registration'))
		{
			JForm::addFormPath(__DIR__ . '/xml');
			$form->loadFile('register_group');
//			$this->loadAllowedGroupsToMenuItem($form);
		}

		if ($name == 'com_fields.field.com_users.user')
		{
			JForm::addFormPath(__DIR__ . '/xml');
			$form->loadFile('fields_groups');
//			$this->loadAllowedGroupsToMenuItem($form);
		}

		if (in_array($name, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			$this->formName = $name;
		}

		return true;
	}

	/**
	 * Remove not needed custom fields
	 *
	 * @return   void
	 * @since    1.0.0
	 */
	public function onCustomFieldsPrepareDom($field, $fieldset, $form)
	{
		$name = $form->getName();
		$groupFound = false;

		if (in_array($name, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			$fieldGroups = (array) $field->params->get('fields_groups');

			if (empty($fieldGroups))
			{
				return;
			}

			if ($name == 'com_users.registration')
			{
				$newUserGroup = $this->getMenuGroup();

				if (in_array($newUserGroup, $fieldGroups))
				{
					$groupFound = true;
				}
			}
			else
			{
				$userGroups = (array) $this->getUserGroups($name);

				foreach ($userGroups as $userGroup)
				{
					if (in_array($userGroup, $fieldGroups))
					{
						$groupFound = true;
					}
				}
			}

			if ($groupFound)
			{
				return;
			}

			$field->type = '';
		}

		return;
	}


	/**
	 * Remove not needed custom fields
	 *
	 * @return   void
	 * @since    1.0.0
	 */
	public function onAfterDispatch()
	{
		return;
		$name = $this->formName;
		$form = JForm::getInstance($name);

		$test = null;
		if (in_array($name, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			$fields = $this->getCustomFields();

			foreach ($fields as $key => $field)
			{
				if ($name == 'com_users.registration')
				{
					$newUserGroup = $this->getMenuGroup();
				}

				if ($field->params->get('new_usertype') != $newUserGroup)
				{
					$form->removeField($field->name, 'com_fields');
//					$field = new stdClass;
				}

				/*				$groupModel = JModelLegacy::getInstance('Group', 'FieldsModel', array('ignore_request' => true));
								$groupItem = $groupModel->getItem($field->group_id);

								if ($groupItem->state == 1)
								{
									$fieldGroups[$key]['id'] = $groupItem->id;
									$fieldGroups[$key]['title'] = $groupItem->title;
								}*/
			}
		}
	}

	/**
	 * Add allowed groups field to menu item
	 *
	 * @param   \JForm  $form
	 *
	 * @since   1.0.0
	 */
	private function loadAllowedGroupsToMenuItem(JForm $form): void
	{
		JForm::addFormPath(__DIR__ . '/xml');
		$form->loadFile('register_group');
	}

	/**
	 * @return array
	 */
	private function getCustomFields(): array
	{
		jimport('fields', JPATH_ADMINISTRATOR . '/components/com_fields/helpers');

		return FieldsHelper::getFields('com_users.user');
	}
}
