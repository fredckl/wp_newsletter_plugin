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
 


 
