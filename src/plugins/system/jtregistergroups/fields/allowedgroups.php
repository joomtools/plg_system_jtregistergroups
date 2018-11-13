<?php
/**
 * @package      Joomla.Plugin
 * @subpackage   Content.Jtf
 *
 * @author       Guido De Gobbis <support@joomtools.de>
 * @copyright    (c) 2017 JoomTools.de - All rights reserved.
 * @license      GNU General Public License version 3 or later
 */

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @link   http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since  11.1
 */
class JFormFieldAllowedgroups extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Allowedgroups';


	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.7.0
	 */
	protected function getOptions()
	{
		$pluginRegisterglobals = JPluginHelper::getPlugin('system', 'jtregistergroups');
		$pluginParams          = new JRegistry($pluginRegisterglobals->params);
		$allowedGroups         = (array) $pluginParams->get('set_allowed_usertypes', '');
		$groups                = UserGroupsHelper::getInstance();
		$options               = array();

		// Set Global
		$options               = array(
			(object) array(
				'text'  => Text::_('JGLOBAL'),
				'value' => 0,
			),
		);

		foreach ($allowedGroups as $allowed)
		{
			$group     = $groups->get($allowed);
			$options[] = (object) array(
				'text'  => $group->title,
				'value' => $group->id,
			);
		}

		return $options;


		foreach ($this->element->xpath('option') as $option)
		{
			// Filter requirements
			if ($requires = explode(',', (string) $option['requires']))
			{
				// Requires multilanguage
				if (in_array('multilanguage', $requires) && !JLanguageMultilang::isEnabled())
				{
					continue;
				}

				// Requires associations
				if (in_array('associations', $requires) && !JLanguageAssociations::isEnabled())
				{
					continue;
				}

				// Requires adminlanguage
				if (in_array('adminlanguage', $requires) && !JModuleHelper::isAdminMultilang())
				{
					continue;
				}

				// Requires vote plugin
				if (in_array('vote', $requires) && !JPluginHelper::isEnabled('content', 'vote'))
				{
					continue;
				}
			}

			$value = (string) $option['value'];
			$text  = trim((string) $option) != '' ? trim((string) $option) : $value;

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');
			$disabled = $disabled || ($this->readonly && $value != $this->value);

			$checked = (string) $option['checked'];
			$checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

			$selected = (string) $option['selected'];
			$selected = ($selected == 'true' || $selected == 'selected' || $selected == '1');

			$tmp = array(
				'value'    => $value,
				'text'     => JText::alt($text, $fieldname),
				'disable'  => $disabled,
				'class'    => (string) $option['class'],
				'selected' => ($checked || $selected),
				'checked'  => ($checked || $selected),
			);

			// Set some event handler attributes. But really, should be using unobtrusive js.
			$tmp['onclick']  = (string) $option['onclick'];
			$tmp['onchange'] = (string) $option['onchange'];

			if ((string) $option['showon'])
			{
				$tmp['optionattr'] = " data-showon='" .
					json_encode(
						JFormHelper::parseShowOnConditions((string) $option['showon'], $this->formControl, $this->group)
					)
					. "'";
			}
			// Add the option object to the result set.
			$options[] = (object) $tmp;
		}

		if ($this->element['useglobal'])
		{
			$tmp        = new stdClass;
			$tmp->value = '';
			$tmp->text  = JText::_('JGLOBAL_USE_GLOBAL');
			$component  = JFactory::getApplication()->input->getCmd('option');

			// Get correct component for menu items
			if ($component == 'com_menus')
			{
				$link      = $this->form->getData()->get('link');
				$uri       = new JUri($link);
				$component = $uri->getVar('option', 'com_menus');
			}

			$params = JComponentHelper::getParams($component);
			$value  = $params->get($this->fieldname);

			// Try with global configuration
			if (is_null($value))
			{
				$value = JFactory::getConfig()->get($this->fieldname);
			}

			// Try with menu configuration
			if (is_null($value) && JFactory::getApplication()->input->getCmd('option') == 'com_menus')
			{
				$value = JComponentHelper::getParams('com_menus')->get($this->fieldname);
			}

			if (!is_null($value))
			{
				$value = (string) $value;

				foreach ($options as $option)
				{
					if ($option->value === $value)
					{
						$value = $option->text;

						break;
					}
				}

				$tmp->text = JText::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
			}

			array_unshift($options, $tmp);
		}

		reset($options);

		return $options;
	}
}
