<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   System.JtRegisterGroups
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    2018 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

namespace Joomla\CMS\Form\Rule;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

/**
 * Form Rule class for the Joomla Platform.
 *
 * @since  1.0.1
 */
class AllowedgroupsRule extends FormRule
{
	/**
	 * Method to test the username for uniqueness.
	 *
	 * @param   \SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed              $value    The form field value to validate.
	 * @param   string             $group    The field name group control value. This acts as an array container for the field.
	 *                                       For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                       full field name would end up being "bar[foo]".
	 * @param   Registry           $input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   Form               $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   1.0.1
	 */
	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
	{
		// Get the database object and a new query object.
		$pluginRegisterglobals = PluginHelper::getPlugin('system', 'jtregistergroups');
		$pluginParams          = new Registry($pluginRegisterglobals->params);
		$allowedGroups         = (array) $pluginParams->get('set_allowed_usertypes', '');

		if ($value === '0' || in_array($value, $allowedGroups, true))
		{
			return true;
		}

		return false;
	}
}
