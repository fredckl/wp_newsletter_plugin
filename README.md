# Création d'un plugin de newsletter pour Wordpress

### Déclaration du plugin
les plugins sont déclarés dans le répertoire wp-content/plugins. Allez dans ce répertoire,
créer un nouveau dossier appelé ex_newsletter. Maintenant pour déclarer un plugin, 
il vous suffit de créer un fichier ex_newsletter.php et de renseigner au minimum le nom de celui-ci. 

```php
<?php
/*
 * Plugin Name: Ex Newsletter
*/
```

Vous pouvez ajouter d'autres informations utiles (ex : une description, le nom de l'auteur, etc ...); Voir ci-dessous :
```php
<?php
/*
 * Plugin Name: Ex Newsletter
 * Plugin URI: https://github.com/fredckl/wp_newsletter_plugin
 * Description: Un simple plugin de gestion de newsletter
 * Author: Frédéric KOLLER
 * Author URI: https://www.frederickoller.ch
 * Version: 1.0
 * License: MIT
 */
```

Rendez vous dans votre dashboard Wordpress, section plugins. Vous devriez voir votre plugin prêt à être activé.
Faites-le !


### Création d'un widget
Les widgets permet d'inserer facile des elements dans notre site. Commençons tout de suite par en créer
un que nous pourrons placer ou bon nous semble sur notre site.
Dans votre plugin, créer un fichier ex_newsletter_widget.php et écrivez le code suivant :
```php
<?php

class Ex_Newsletter_Widget extends WP_Widget 
{
    public function __construct ()
    {
        parent::__construct(
            'ex_newsletter', // ID
            'Newsletter', // Nom
            // Options 
            array('description' => "Un formulaire d'inscription à la newsletter."));
    }
    
    public function widget ($args, $instance)
    {
        echo 'widget newsletter';
    }
}
```

Il nous faut maintenant indiquer l'existance de ce widget à wordpress. Pour ce faire, rendez-vous dans votre fichier principal et renseigner le code suivant :
```php
<?php
// .... déclaration de votre plugin

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
```

> Attention ! N'oubliez pas initialier votre classe Ex_Newsletter à la fin de votre fichier, sans quoi il ne se passera rien !

Rendez-vous maitenant dans la partie Widget du dashboard de Wordpress. Si tout c'est bien passé, vous devriez voir votre widget Newsletter.
Vous pouvez le glisser ou bon vous semble dans votre site.
---
Pour le moment notre widget est vide et ne comporte aucune fonctionnalité.
Dans un premier temps, nous souhaitons ajouter un titre pour notre formulaire.
```php
<?php
class Ex_Newsletter_Widget extends WP_Widget
{
    // ... code
    public function form($instance)
    {
        $title = isset($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo  $title; ?>" />
        </p>
        <?php
    }
}
```

Maintenant, nous avons besoin de créer le rendu pour notre visiteur. Nous aurons besoin d'un label et d'un input
Remplacer la méthode widget par celle ci-dessous :
```php
<?php

class Ex_Newsletter_Widget extends WP_Widget
{
    // .... code

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'];
        echo apply_filters('widget_title', $instance['title']);
        echo $args['after_title'];
        ?>
        <form action="" method="post">
            <p>
                <label for="zero_newsletter_email">Votre email :</label>
                <input id="zero_newsletter_email" name="zero_newsletter_email" type="email"/>
            </p>
            <input type="submit"/>
        </form>
        <?php
        echo $args['after_widget'];
    }
}
```

Ce bout de code va afficher notre formulaire sur notre site.


### Modifier la base de données
Pour enregistrer les adresses e-mails de nos visiteurs, nous devons ajouter une nouvelle table dans notre base de données.
Pour ce faire, créer une méthode install dans votre classe Ex_Newsletter comme suit :

```php
<?php
class Ex_Newsletter
{
    // ...
    
    public function install ()
    {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ex_newsletter_email (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(255) NOT NULL);");
    }

}
``` 
#### Tracer l'activiation de notre plugin dans wordpress
Pour savoir quand wordpress active notre plugin, il existe une fonction qui permet de savoir quand le celui-ci est activé
Ajouter le code ci-dessous dans l'initialisation de votre classe Ex_Newsletter
```php
<?php
class Ex_Newsletter
{
    public function __construct ()
    {
        // ...
        register_activation_hook(__FILE__, array($this, 'install'));
    }
}
```
Désactiver votre plugin et ré-activer le pour qu'il créé la nouvelle table.
Vous devrier votre dans phpmyadmin qu'une nouvelle table appellée wp_ex_newsletter_email vient d'être créée.

#### Insérer les données des utilisateurs
Il faut maintenant permettre l'enregistrement des e-mails que nos visiteurs nous envoient lors de la validation du formulaire.
Créer une nouvelle méthode dans la classe Ex_Newsletter qui va s'appeller save_email ():
```php
<?php

class Ex_Newsletter
{
    // ...
    
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
}
```

> Ici, nous ne vérifions pas si l'adresses email est correcte, ni même si elle est dèjà présente dans la base de données mais vous pouvez toujour le faire ultérieurement.
 
Il nous reste à indiquer à wordpress la présence de cette nouvelle méthode qui sera utilisée lors de la sousmission de notre formulaire.
Ajouter le code ci-dessous dans le constructeur de la classe Ex_Newsletter :
```php
<?php
class Ex_Newsletter
{
    public function __construct ()
    {
       // ...
        add_action('wp_loaded', array($this, 'save_email'));
    }
}
```

Tester votre nouvelle fonctionnalité. Aller sur la partie publique de votre site et renseigner votre adresse e-mail.
Vous pouvez voir dans phpmyadmin qu'une nouvelle ligne contenant votre e-mail vient d'être ajoutée.

### L'Administration
Nous avons bien avancé. Nous devons maintenant réaliser l'interface de gestion des e-mails dans l'administration de Wordpress.
Commençons par ajouter le menu. Dans le constructeur de votre classe Ex_Newsletter, insérer le code ci-dessous :
```php
<?php

class Ex_Newsletter
{
    public function __construct ()
    {
     
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
}
```
Puis la méthode add_admin_menu
```php
<?php

class Ex_Newsletter
{
    public function add_admin_menu ()
    {
        add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'ex_newsletter', array($this, 'menu_html'));
    }
}
```
Petite explication sur les arguments passés dans la fonction add_menu_page
1. Le titre de la page
2. Le titre dans le menu
3. Les droits
4. Le slug
5. la fonction qui sera appelée pour afficher la page

Bien, maitenant il faut créer cette méthode menu_html :

```php
<?php

class Ex_Newsletter
{
    public function menu_html ()
    {
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Bienvenue sur la page d\'accueil de la newsletter</p>';
    }
}
```

#### Afficher la liste des e-mails :
Modifiez la méthode menu_html comme suit :

```php
<?php

class Ex_Newsletter
{
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
        <?php
    }
}
```
Nous pouvons voir un tableau répertoriant toutes les adresses e-mails enregistrées dans notre table wp_ex_newsletter_email !

Il nous reste plus qu'a afficher un bouton pour envoyer la newsletter à nos visiteurs enregistrés.
Insérer ce code en bas de votre méthode menu_html.
```php
<?php
class Ex_Newsletter
{
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
            <input type="hidden" name="send_newsletter" value="1"/>
            <?php submit_button('Envoyer la newsletter') ?>
        </form>
        <?php
    }
}
```
 
Procédons enfin de la même façon que nous l'avons fait pour sauvegarder les emails. 
Modifions la méthode add_admin_menu dans la classe Ex_Newsletter pour qu'elle appelle la méthode send_email et ajoutons la méthode send_email

```php
<?php

class Ex_Newsletter
{
    public function add_admin_menu ()
    {
        $hook = add_menu_page('Newsletter', 'Newsletter', 'manage_options', 'ex_newsletter', array($this, 'menu_html'));
        add_action('load-' . $hook , array($this, 'send_email'));
    }

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

}
```
> Attention ! Lorsque vous développer votre application en local, les e-mails envoyés sont filtrer par les fournisseurs et peuvent ne pas arriver chez le destinataire

Félication, vous venez de créer votre premier plugin avec wordpress et maintenant que vous avez les bases, vous pouvez aller encore plus loin.

Bonne route et bon code.
