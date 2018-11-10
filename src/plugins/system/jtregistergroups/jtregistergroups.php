<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * An example custom profile plugin.
 *
 * @since  1.6
 */
class PlgSystemJtregistergroups extends JPlugin
{
	/**
	 * Global application object
	 *
	 * @var     JApplication
	 * @since   3.0.0
	 */
	protected $app = null;
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Runs on content preparation
	 *
	 * @param   string  $context  The context for the data
	 * @param   object  $data     An object containing the data for the form.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onContentPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_users.profile', 'com_users.user', 'com_users.registration', 'com_admin.profile')))
		{
			return true;
		}

		if (in_array($context, array('com_users.profile', 'com_users.user', 'com_admin.profile')))
		{

		}

		if ($context == 'com_users.registration')
		{
			$menuItem = (int) $this->app->getMenu()->getActive()->id;

			if ($menuItem)
			{
				$newUserGroup = $this->app->getMenu()->getItem($menuItem)->getParams()->get('new_usertype');

				$data->groups[0] = $newUserGroup;

				return true;
			}
		}

		return false;
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

		if ($name =='com_menus.item' && $data->link == 'index.php?option=com_users&view=registration')
		{
			$this->setGroupToMenuItem($form);
		}

		return true;
	}

	/**
	 * Method is called before user data is stored in the database
	 *
	 * @param   array    $user   Holds the old user data.
	 * @param   boolean  $isnew  True if a new user is stored.
	 * @param   array    $data   Holds the new user data.
	 *
	 * @return  boolean
	 *
	 * @since   3.1
	 * @throws  InvalidArgumentException on invalid date.
	 */
	public function onUserBeforeSave($user, $isnew, $data)
	{
/*		$menuItem = (int) $this->app->getMenu()->getActive()->id;

		if ($menuItem && $isnew)
		{
			$newUserGroup = $this->app->getMenu()->getItem($menuItem)->getParams()->get('new_usertype');

			$data['groups'][0] = $newUserGroup;

			return true;
		}

		return false;
*/
	}

	/**
	 * Add groups field to menu item
	 *
	 * @param   \JForm  $form
	 *
	 * @since   1.0.0
	 */
	private function setGroupToMenuItem(JForm $form): void
	{
		JForm::addFormPath(__DIR__ . '/groups');
		$form->loadFile('groups');
	}
}
