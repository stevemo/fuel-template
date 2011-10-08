<?php
/**
 * CodeIgniter Template Class
 *
 * Build your CodeIgniter pages much easier with partials, breadcrumbs, layouts and themes
 *
 * @package			CodeIgniter
 * @subpackage		Libraries
 * @category		Libraries
 * @author			Philip Sturgeon
 * @license			http://philsturgeon.co.uk/code/dbad-license
 * @link			http://philsturgeon.co.uk/code/codeigniter-template
 */
/**
 * Fuel Template Class
 *
 *
 * @package			Fuel
 * @author			Steve Montambeault
 * @link			http://stevemo.ca
 */

namespace Template;

class Template {

	private $_theme = NULL;
	private $_theme_path = NULL;
	private $_layout = FALSE; // By default, dont wrap the view with anything
	private $_layout_subdir = ''; // Layouts and partials will exist in views/layouts
	// but can be set to views/foo/layouts with a subdirectory

	private $_title = '';
	private $_metadata = array();

	private $_partials = array();

	private $_breadcrumbs = array();

	private $_title_separator = ' | ';

	private $_theme_locations = array();


    /**
     * Only load the configuration once
     *
     * @static
     * @access  public
     */
    public static function _init()
    {
        \Config::load('template', 'template');
    }

    /**
     * Initiate a new Template instance
     *
     * @static
     * @access  public
     * @param 	array 	$custom  config array
     * @return  Template
     */
    public static function forge($custom = array())
    {
    	return new static($custom);
    }

    /**
	 * Constructor - Sets Preferences
	 *
	 * The constructor can be passed an array of config values
	 */
    function __construct($custom = array())
    {
    	$config = \Config::get('template', array());
    	$config = \Arr::merge($config, $custom);
        $this->initialize($config);
        $this->view = \View::forge();
        return $this;
    }

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array 	$config
	 * @return	void
	 */
    public function initialize($config)
    {
        foreach ($config as $key => $val)
        {
            if ($key == 'theme' AND $val != '')
            {
                $this->set_theme($val);
                continue;
            }

            $this->{'_'.$key} = $val;

        }

        // No locations set in config?
        if ($this->_theme_locations === array())
        {
            // Let's use this obvious default
            $this->_theme_locations = array(APPPATH . 'themes/');
        }

        // Theme was set
		if ($this->_theme)
		{
			$this->set_theme($this->_theme);
		}
    }

    /**
     * Which theme are we using here?
     *
     * @access  public
     * @param   string  $theme  Set a theme for the template library to use
     * @return  object  $this
     */
    public function set_theme($theme = NULL)
    {
        $this->_theme = $theme;
        foreach ($this->_theme_locations as $location)
        {
            if ($this->_theme AND file_exists($location.$this->_theme))
            {
                $this->_theme_path = rtrim($location.$this->_theme.'/');
                break;
            }
        }

        return $this;
    }

	/**
	 * Which theme layout should we using here?
	 *
	 * @access	public
	 * @param	string	$view
	 * @param 	string 	$_layout_subdir
	 * @return	void
	 */
	public function set_layout($view, $_layout_subdir = '')
	{
		$this->_layout = $view;

		$_layout_subdir AND $this->_layout_subdir = $_layout_subdir;

		return $this;
	}

    // --------------------------------------------------------------------

    /**
     * set_global
     *
     * @access  public
     * @param   string  variable name or an array of variables
     * @param   mixed   value
     * @param   bool    whether to filter the data or not
     * @return void
     * @author Steve Montambeault
     **/
    public function set_global($key, $value = null, $filter = null)
    {
        $this->view->set_global($key, $value, $filter);
        return $this;
    }

    /**
     * Assigns a variable by name. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This value can be accessed as $foo within the view
     *     $view->set('foo', 'my value');
     *
     * You can also use an array to set several values at once:
     *
     *     // Create the values $food and $beverage in the view
     *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
     *
     * @param   string   variable name or an array of variables
     * @param   mixed    value
     * @param   bool     whether to filter the data or not
     * @return  $this
     */
    public function set($key, $value = null, $filter = null)
    {
        $this->view->set($key, $value, $filter);
        return $this;
    }

    /**
     * The same as set(), except this defaults to not-encoding the variable
     * on output.
     *
     *     $view->set_safe('foo', 'bar');
     *
     * @param   string   variable name or an array of variables
     * @param   mixed    value
     * @return  $this
     */
    public function set_safe($key, $value = null)
    {
        $this->view->set_safe($key, $value);
        return $this;
    }

    /**
     * Assigns a value by reference. The benefit of binding is that values can
     * be altered without re-setting them. It is also possible to bind variables
     * before they have values. Assigned values will be available as a
     * variable within the view file:
     *
     *     // This reference can be accessed as $ref within the view
     *     $view->bind('ref', $bar);
     *
     * @param   string   variable name
     * @param   mixed    referenced variable
     * @param   bool     Whether to filter the var on output
     * @return  $this
     */
    public function bind($key, &$value, $filter = null)
    {
        $this->view->bind($key, &$value, $filter);
        return $this;
    }

    /**
     * Assigns a global variable by reference, similar to [static::bind], except
     * that the variable will be accessible to all views.
     *
     *     View::bind_global($key, $value);
     *
     * @param   string  variable name
     * @param   mixed   referenced variable
     * @param   bool    whether to filter the data or not
     * @return  void
     */
    public static function bind_global($key, &$value, $filter = null)
    {
        $this->view->bind_global($key, &$value, $filter);
        return $this;
    }
}

/* End of file template.php */
