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

    private $_module = '';
    private $_controller = '';
    private $_method = '';

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
        $this->_method = $active->action;
        $this->_controller = str_replace('Controller_','',\Inflector::denamespace($active->controller));

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

    // --------------------------------------------------------------------

    /**
     * Set a view partial
     *
     * @access  public
     * @param   string
     * @param   string
     * @param   boolean
     * @return  void
     */
    public function set_partial($name, $view, $data = array())
    {
        $this->_partials[$name] = array('view' => $view, 'data' => $data);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set a view partial
     *
     * @access  public
     * @param   string  $name
     * @param   string  $string
     * @param   array   $data
     * @return  object  $this
     */
    public function inject_partial($name, $string, $data = array())
    {
        $this->_partials[$name] = array('string' => $string, 'data' => $data);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Helps build custom breadcrumb trails
     *
     * @access  public
     * @param   string  $name   What will appear as the link text
     * @param   string  $uri    The URL segment
     * @return  object  $this
     */
    public function set_breadcrumb($name, $uri = '')
    {
        $this->_breadcrumbs[] = array('name' => $name, 'uri' => $uri );
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set the title of the page
     *
     * @access  public
     * @return  object  $this
     */
    public function title()
    {
        // If we have some segments passed
        if ($title_segments =& func_get_args())
        {
            $this->_title = implode($this->_title_separator, $title_segments);
        }

        return $this;
    }

    /**
     * Put extra javascipt, css, meta tags, etc before all other head data
     *
     * @access  public
     * @param   string  $line   The line being added to head
     * @return  object  $this
     */
    public function prepend_metadata($line)
    {
        array_unshift($this->_metadata, $line);
        return $this;
    }


    /**
     * Put extra javascipt, css, meta tags, etc after other head data
     *
     * @access  public
     * @param   string  $line   The line being added to head
     * @return  object  $this
     */
    public function append_metadata($line)
    {
        $this->_metadata[] = $line;
        return $this;
    }


    /**
     * Set metadata for output later
     *
     * @access  public
     * @param   string  $name       keywords, description, etc
     * @param   string  $content    The content of meta data
     * @param   string  $type       Meta-data comes in a few types, links for example
     * @return  object  $this
     */
    public function set_metadata($name, $content, $type = 'meta')
    {
        $name = htmlspecialchars(strip_tags($name));
        $content = htmlspecialchars(strip_tags($content));

        // Keywords with no comments? ARG! comment them
        if ($name == 'keywords' AND ! strpos($content, ','))
        {
            $content = preg_replace('/[\s]+/', ', ', trim($content));
        }

        switch($type)
        {
            case 'meta':
                $this->_metadata[$name] = '<meta name="'.$name.'" content="'.$content.'" />';
            break;

            case 'link':
                $this->_metadata[$content] = '<link rel="'.$name.'" href="'.$content.'" />';
            break;
        }

        return $this;
    }

    // --------------------------------------------------------------------

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

        if (empty($this->_title))
        {
            $this->title = $this->_guess_title();
        }
        else
        {
            $this->title = $this->_title;
        }

        //add partials view
        foreach ($this->_partials as $name => $partial)
        {
            // We can only work with data arrays
            is_array($partial['data']) OR $partial['data'] = (array) $partial['data'];

            // If it uses a view, load it
            if (isset($partial['view']))
            {
                $this->partials[$name] = $this->_find_view($partial['view'], $partial['data']);
            }
            else // Otherwise the partial must be a string
            {
                $this->partials[$name] = $partial['string'];
            }
        }

        //add the breadcrumb trails
        $this->breadcrumbs = $this->_breadcrumbs;

        $this->view->set_global('template', $this->_get_fields(), false);

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

    /**
    * function _guess_title
    *
    * @access private
    * @return string
    */
    private function _guess_title()
    {

        // Obviously no title, lets get making one
        $title_parts = array();

        // If the method is something other than index, use that
        if ($this->_method != 'index')
        {
            $title_parts[] = $this->_method;
        }

        // Make sure controller name is not the same as the method name
        if ( ! in_array($this->_controller, $title_parts))
        {
            $title_parts[] = $this->_controller;
        }

        // Is there a module? Make sure it is not named the same as the method or controller
        if ( ! empty($this->_module) AND !in_array($this->_module, $title_parts))
        {
            $title_parts[] = $this->_module;
        }

        // Glue the title pieces together using the title separator setting
        $title = \Inflector::humanize(implode($this->_title_separator, $title_parts));

        return $title;
    }

    private function _ext($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION) ? '' : '.php';
    }
}

/* End of file template.php */
