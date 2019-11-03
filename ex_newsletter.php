<?php

/**
 * Plugin Name: Ex Newsletter
 * Plugin URI: https://github.com/fredckl/wp_newsletter_plugin
 * Description: Un simple plugin de gestion de newsletter
 * Author: Frédéric KOLLER
 * Author URI: https://www.frederickoller.ch
 * Version: 1.0
 * License: MIT
 */

// Include notre widget dans notre fichier principal
include_once plugin_dir_path( __FILE__ ) . '/ex_newsletter_widget.php';

class Ex_Newsletter
{
    /**
     * Ex_Newsletter constructor.
     */
    public function __construct ()
    {
        // Enregistrer notre widget
        add_action('widgets_init', function() { register_widget('Ex_Newsletter_Widget'); } );
    }
}

// Initialiser la class
new Ex_Newsletter();

