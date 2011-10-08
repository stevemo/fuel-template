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

	private $_module = NULL;

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
     * Shortcode to self::forge().
     *
     * @deprecated  1.1.0
     * @static
     * @access  public
     * @param   array  $custom  array of config
     * @return  self::forge()
     */
    public static function factory($custom = array())
    {
        \Log::warning('This method is deprecated. Please use a forge() instead.', __METHOD__);

        return static::forge($custom);
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

        //grab the active request
        $active = \Request::active();

        $this->_module = $active->module;
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

    /**
     * Build function
     *
     **/
    public function build($file, $data = array())
    {
        // Want this file wrapped with a layout file?
        if($this->_layout)
        {
            if($this->_theme_path !== null)
            {
                $this->view->set_filename(self::_find_view_folder().'layouts/'.$this->_layout.$this->_ext($this->_layout));
                $this->body = $this->_find_view($file, $data);
            }
            else
            {
                throw new \Fuel_Exception('Did you forget to set the theme?');
            }
        }
        else
        {
            $view = $this->_find_view($file, $data, false);
            $this->view->set_filename($view)->set($data);
        }

        /*TODO
        * add partials views
        */

        $this->view->set('template', $this->_get_fields(), false);

        return $this->view;
    }

    // --------------------------------------------------------------------

    /**
     * _find_view_folder function
     *
     * @access  private
     * @return  string folder
     */
    private function _find_view_folder()
    {

        // Base view folder
        $view_folder = APPPATH.'views/';

        // Using a theme? Put the theme path in before the view folder
        if ( ! empty($this->_theme))
        {
            $view_folder = $this->_theme_path.'views/';
        }

        // Things like views/admin/web/view admin = subdir
        if ($this->_layout_subdir)
        {
            $view_folder .= $this->_layout_subdir.'/';
        }

        return $view_folder;
    }

    // --------------------------------------------------------------------

    /**
     * A module view file can be overriden in a theme
     *
     * @access  private
     * @return  void
     */
    private function _find_view($view, array $data, $parse = true)
    {
        // Only bother looking in themes if there is a theme
        if ( ! empty($this->_theme))
        {
            foreach ($this->_theme_locations as $location)
            {
                $theme_views = array(
                    $this->_theme.'/views/modules/'.$this->_module .'/'.$view,
                    $this->_theme.'/views/'.$view
                );

                foreach ($theme_views as $theme_view)
                {
                    if (file_exists($location.$theme_view.$this->_ext($view)))
                    {
                        return self::_load_view($theme_view, $data, $location, $parse);
                    }
                }
            }
        }

        // Not found it yet? Just load, its either in the module or root view
        return self::_load_view($view, $data, null, $parse);
    }

    // --------------------------------------------------------------------

    /**
     * load the view
     *
     * @access  private
     * @return  object view
     */
    private function _load_view($view, array $data, $override_view_path = NULL, $parse = true)
    {

        if($parse)
        {
            if ($override_view_path !== NULL)
            {
                return \View::forge($override_view_path.$view.$this->_ext($view), $data);
            }

            // Can just run as usual
            else
            {
                return \View::forge($view, $data);
            }
        }
        else
        {
            if ($override_view_path !== NULL)
            {
                return $override_view_path.$view.$this->_ext($view);
            }

            // Can just run as usual
            else
            {
                return $view;
            }
        }

    }

    /**
    * function get_fields
    * will return only public properties
    * @access private
    * @return object
    */
    private function _get_fields()
    {
        $getFields = function($obj) { return get_object_vars($obj); };
        return $getFields($this);
    }

    private function _ext($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) ? '' : '.php';
    }
}

/* End of file template.php */
