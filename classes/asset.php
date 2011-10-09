<?php
/**
 * Fuel Asset Class
 *
 *
 * @package			Fuel
 * @author			Steve Montambeault
 * @link			http://stevemo.ca
 */
namespace Template;

class Asset extends \Fuel\Core\Asset {


	/**
	 * theme class variable
	 *
	 * @var string
	 **/
	private static $_theme = null;

	// --------------------------------------------------------------------

	/**
	 * set_theme
	 *
	 * @access  public
	 * @param
	 * @return void
	 * @author Steve Montambeault
	 **/
	public static function set_theme($theme)
	{
		static::$_theme = $theme;
	}

	// --------------------------------------------------------------------

	/**
	 * THEME CSS
	 *
	 * Either adds the stylesheet to the group, or returns the CSS tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 **/
	public static function theme_css($stylesheets = array(), $attr = array(), $group = NULL, $raw = false)
	{
		if(self::$_theme == null)
		{
			throw new \Fuel_Exception('Did you forget to set the theme? Or the theme do not exist!');
		}

		return static::_set_assets('themes/'.static::$_theme.DS, 'css', $stylesheets, $attr, $group, $raw);
	}

	/**
	 * Module CSS
	 *
	 * Either adds the stylesheet to the group, or returns the CSS tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 **/
	public static function module_css($stylesheets = array(), $attr = array(), $group = NULL, $raw = false)
	{
		//grab the active request
        $active = \Request::active();
        return static::_set_assets('modules/'.$active->module.DS, 'css', $stylesheets, $attr, $group, $raw);
	}

	// --------------------------------------------------------------------

	/**
	 * Theme JS
	 *
	 * Either adds the javascript to the group, or returns the script tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 */
	public static function theme_js($scripts = array(), $attr = array(), $group = NULL, $raw = false)
	{
		if(self::$_theme == null)
		{
			throw new \Fuel_Exception('Did you forget to set the theme? Or the theme do not exist!');
		}

		return static::_set_assets('themes/'.static::$_theme.DS, 'js', $scripts, $attr, $group, $raw);
	}

	// --------------------------------------------------------------------

	/**
	 * Module JS
	 *
	 * Either adds the javascript to the group, or returns the script tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 */
	public static function module_js($scripts = array(), $attr = array(), $group = NULL, $raw = false)
	{
		//grab the active request
        $active = \Request::active();
        return static::_set_assets('modules/'.$active->module.DS, 'js', $scripts, $attr, $group, $raw);
	}

	// --------------------------------------------------------------------

	/**
	 * Theme Img
	 *
	 * Either adds the image to the group, or returns the image tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 */
	public static function theme_img($images = array(), $attr = array(), $group = NULL)
	{
		if(self::$_theme == null)
		{
			throw new \Fuel_Exception('Did you forget to set the theme? Or the theme do not exist!');
		}

		return static::_set_img('themes/'.static::$_theme.DS, $images, $attr, $group);
	}

	// --------------------------------------------------------------------

	/**
	 * Module Img
	 *
	 * Either adds the image to the group, or returns the image tag.
	 *
	 * @access	public
	 * @param	mixed	The file name, or an array files.
	 * @param	array	An array of extra attributes
	 * @param	string	The asset group name
	 * @return	string
	 */
	public static function module_img($images = array(), $attr = array(), $group = NULL)
	{
		//grab the active request
        $active = \Request::active();
		return static::_set_img('modules/'.$active->module.DS, $images, $attr, $group);
	}


	// --------------------------------------------------------------------

	/**
	 * _set_assets
	 *
	 * @access  private
	 * @param
	 * @return void
	 * @author Steve Montambeault
	 **/
	private static function _set_assets($location, $type, $files = array(), $attr = array(), $group = NULL, $raw = false)
	{
		static::add_path($location);
		$rtn = static::$type($files, $attr, $group, $raw);
		static::remove_path($location);
		return $rtn;
	}

	// --------------------------------------------------------------------

	/**
	 * _set_img
	 *
	 * @access  private
	 * @param
	 * @return void
	 * @author Steve Montambeault
	 **/
	private static function _set_img($location, $files = array(), $attr = array(), $group = NULL)
	{
		static::add_path($location);
		$rtn = static::img($files, $attr, $group);
		static::remove_path($location);
		return $rtn;
	}
}
/* end of asset.php */
