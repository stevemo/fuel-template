<?php
/**
 *  Template package.
 * 
 * @package Template
 * @author Steve Montambeault
 */

Autoloader::add_core_namespace('Template');

Autoloader::add_classes(array(
	'Template\\Template'	=> __DIR__.'/classes/template.php'
));


/* End of file bootstrap.php */