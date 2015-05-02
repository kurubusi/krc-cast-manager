<?php
    class Krc_Post_Types_Order_Walker extends Walker 
        {

            var $db_fields = array ('parent' => 'post_parent', 'id' => 'ID');

            function start_lvl(&$output, $depth = 0, $args = array()) {
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<ul class='children'>\n";
            }


            function end_lvl(&$output, $depth = 0, $args = array()) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</ul>\n";
            }


            function start_el(&$output, $page, $depth = 0, $args = array(), $id = 0) {
                if ( $depth )
                    $indent = str_repeat("\t", $depth);
                else
                    $indent = '';

                extract($args, EXTR_SKIP);
								$screens = json_decode(get_post_meta($page->ID, "_krc_cast_screens", true));
								$name = get_post_meta( $page->ID, "_krc_name", true );
								
                $output .= $indent . '<li id="item_'.$page->ID.'"><dl><dt>'. esc_html($name) .'</dt><dd><img src="' . esc_url($screens[0]) . '" class="list_cast_photo cast_photo" /></dd></dl>';
            }


            function end_el(&$output, $page, $depth = 0, $args = array()) {
                $output .= "</li>\n";
            }

        }



?>