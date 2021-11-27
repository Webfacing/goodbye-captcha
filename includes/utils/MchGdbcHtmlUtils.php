<?php

/* 
 * Copyright (C) 2014 Mihai Chelaru
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 *
 * @author Mihai Chelaru
 */
final class MchGdbcHtmlUtils
{
	const FORM_ELEMENT_INPUT_HIDDEN   = 'hidden';
	const FORM_ELEMENT_INPUT_TEXT     = 'text';	
	const FORM_ELEMENT_INPUT_CHECKBOX = 'checkbox';
	const FORM_ELEMENT_SELECT         = 'select';


	public static function createSelectElement(array $arrAttributes)
	{
		$optionsCode = '';
		if(isset($arrAttributes['options']) && is_array($arrAttributes['options']))
		foreach($arrAttributes['options'] as $key => $value)
		{
			$selected = isset($arrAttributes['value']) && $arrAttributes['value'] == $value ? 'selected = "selected"' : '';
			$optionsCode .=  '<option value="' . esc_attr($value) . '" '.$selected.'>' . esc_html($key) . '</option>';
		}
		unset($arrAttributes['value'], $arrAttributes['options'], $arrAttributes['type']);

		empty($arrAttributes['id']) && !empty($arrAttributes['name']) ? $arrAttributes['id'] = MchGdbcUtils::replaceNonAlphaCharacters($arrAttributes['name'], '-') : null;

		$code  = '<select';
		foreach ($arrAttributes as $key => $value)
		{
			$value = esc_attr($value);
			$code .= " {$key}=\"{$value}\"";
		}

		$code .= '>' . $optionsCode . '</select>';
		return $code;


	}


	public static function createInputElement(array $arrAttributes)
	{
		$code  = '<input';
		
		$code .= isset($arrAttributes['type'])   ? " type=\"{$arrAttributes['type']}\"" : " type=\"text\"";
		
		unset($arrAttributes['type']);

		empty($arrAttributes['id']) && !empty($arrAttributes['name']) ? $arrAttributes['id'] = MchGdbcUtils::replaceNonAlphaCharacters($arrAttributes['name'], '-') : null;

		foreach ($arrAttributes as $key => $value)
		{
			$value = esc_attr($value);
			$code .= " {$key}=\"{$value}\"";
		}
		
		$code .= ' />';

		return $code;
	}		

	public static function createLabelElement($innerText, $forInputId)
	{
		return '<label>' . esc_html($innerText) . '<label>';
	}
	
	public static function createAnchorElement($innerText, array $arrAttributes)
	{
		$code  = '<a';
		
		foreach ($arrAttributes as $key => $value)
		{
			$value = esc_attr($value);
			$code .= " {$key}=\"{$value}\"";
		}
		
		$code .= '>' . esc_html($innerText) . '</a>';
		
		return $code;
	}		
	
	
	private function __construct()
	{}
}
