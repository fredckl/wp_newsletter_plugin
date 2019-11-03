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
        // register_uninstall_hook(__FILE__, array($this, 'uninstall'));
        // register_deactivation_hook(__FILE__, array($this, 'deactivated'));

        add_action('wp_loaded', array($this, 'save_email'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    /**
     * Installation du plugin
     */
    public function install ()
    {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ex_newsletter_email (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);");
    }

    /**
     * Suppression du plugin
     */
    public function uninstall ()
    {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ex_newsletter_email;");
    }

    /**
     * Désactivation du plugin
     */
    public function deactivated ()
    {
        // Faire quelque chose ici ors de la désactivation
    }

    /**
     * Sauvegarder les emails
     */
    public function save_email ()
    {
        if (isset($_POST['ex_newsletter_email']) && !empty($_POST['ex_newsletter_email'])) {
            global $wpdb;
            $email = $_POST['ex_newsletter_email'];

            $row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ex_newsletter_email WHERE email = '$email'");
            if (is_null($row)) {
                $wpdb->insert("{$wpdb->prefix}ex_newsletter_email", array('email' => $email));
            }
        }
    }

    /**
     * Envoyer la newsletter
     */
    public function send_email ()
    {
        global $wpdb;
        if (isset($_POST['ex_send_newsletter'])) {

            $recipients = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}ex_newsletter_email");
            $sujet = "Lettre d'information";
            $content = "De nouveaux articles sont disponible sur le site " . get_home_url();
            $from = "contact@wp-newsletter.local";
            $header = array('From: ' . $from);

            foreach ($recipients as $recipient) {
                $result = wp_mail($recipient->email, $sujet, $content, $header);
            }
        }
    }

    /**
     * Enregistrement du nouveau menu
     */
    public function add_admin_menu ()
    {
        $hook = add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'ex_newsletter', array($this, 'menu_html'));
        add_action('load-' . $hook , array($this, 'send_email'));
    }

    /**
     * Affichage de la page Html
     */
    public function menu_html ()
    {
        global $wpdb;
        echo '<h1>'.get_admin_page_title().'</h1>';
        $recipients = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}ex_newsletter_email");
        ?>
        <table>
            <thead>
            <tr>
                <th>E-mail</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($recipients as $recipient): ?>
                    <tr>
                        <td><?php echo $recipient->email ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post" action="">
            <input type="hidden" name="ex_send_newsletter" value="1"/>
            <?php submit_button('Envoyer la newsletter') ?>
        </form>
        <?php
    }
}

// Initialiser la class
new Ex_Newsletter();

