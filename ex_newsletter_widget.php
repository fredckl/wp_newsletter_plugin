<?php

/**
 * Widget
 */

class Ex_Newsletter_Widget extends WP_Widget
{
    /**
     * Ex_Newsletter_Widget constructor.
     */
    public function __construct ()
    {
        parent::__construct(
            'ex_newsletter',
            'Newsletter',
            array('description' => "Un formulaire d'inscription à la newsletter."));
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'];
        echo apply_filters('widget_title', $instance['title']);
        echo $args['after_title'];
        ?>
        <form action="" method="post">
            <p>
                <label for="ex_newsletter_email">Votre email :</label>
                <input id="ex_newsletter_email" name="ex_newsletter_email" type="email"/>
            </p>
            <input type="submit"/>
        </form>
        <?php
        echo $args['after_widget'];
    }

    /**
     * @param array $instance
     * @return string|void
     */
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
