Template package for Fuel
=====================

Introduction
------------
+ This package is a port of Phil Sturgeon Codeigniter Template library. http://philsturgeon.co.uk/code/codeigniter-template

# Install


# Configuration

Copy config/template.php to app/config/template.php and change whatever setting in need of changing.


# Usage

Template Usage
-----------------
#### Loading the template class
$this->template = Template::forge();

Using layout
-----------------
#### All Layout files should be in the layout folder and by default will be app/views/layouts/default.php, but this can be changed.
$this->template->set_layout('default');

Using theme
-----------------
$this->template->set_theme('mytheme');

more to come...
-----------------
