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
  
