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
        register_activation_hook(__FILE__, array($this, 'install'));
    }

    public function install ()
    {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ex_newsletter_email (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);");
    }
}


// Initialiser la class
new Ex_Newsletter();

