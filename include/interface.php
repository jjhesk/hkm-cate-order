<?php


    function TOPluginInterface()
        {
            global $wpdb, $wp_locale;
            
            $options = get_option('tto_options');
              
            $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : '';
            $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
                    
            $post_type_taxonomies = get_object_taxonomies($post_type);
        
            //use the first taxonomy if emtpy taxonomy
            if ($taxonomy == '' || !taxonomy_exists($taxonomy))
                {
                    reset($post_type_taxonomies);   
                    $taxonomy = current($post_type_taxonomies);
                }
                                
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';
            
            //set as default for auto
            $order_type = (isset($options['taxonomy_settings'][$taxonomy]['order_type'])) ? $options['taxonomy_settings'][$taxonomy]['order_type'] : 'manual'; 
            
            $taxonomy_info = get_taxonomy($taxonomy);
            
            //check for order type update
            if (isset($_GET['order_type']))
                {
                    $new_order_type = $_GET['order_type'];
                    if ($new_order_type != 'auto' && $new_order_type != 'manual')
                        $new_order_type = '';
                        
                    if ($new_order_type != '')
                        {
                            
                            echo '<div class="message updated fade"><p>'. __('Order type for','atto') . ' ' .$taxonomy_info->label.' '. __('Switched to', 'atto'). ' ' . ucfirst($new_order_type)  .'</p></div>';
                            $order_type = $new_order_type;
                            
                            //save the new order
                            $options['taxonomy_settings'][$taxonomy]['order_type'] = $order_type;

                            //update the orde_by
                            if (isset($_GET['auto_order_by']))
                                {
                                    $new_order_by = $_GET['auto_order_by'];
                                    if ($new_order_by != '')
                                        $options['taxonomy_settings'][$taxonomy]['auto']['order_by'] = $new_order_by;
                                } 
                            
                            //update the orde_by
                            if (isset($_GET['auto_order']))
                                {
                                    $new_order = $_GET['auto_order'];
                                    if ($new_order_by != '')
                                        $options['taxonomy_settings'][$taxonomy]['auto']['order'] = $new_order;
                                }    
                                
                            update_option('tto_options', $options);                        
                        }
                }
            
            if(isset($taxonomy_info->hierarchical) && $taxonomy_info->hierarchical === TRUE)    
                $is_hierarchical = TRUE;
                else
                $is_hierarchical = FALSE;

            ?>
            <div class="wrap">
                <div class="icon32" id="icon-edit"><br></div>
                <h2>Taxonomy Order</h2>

                <div id="ajax-response"></div>
                
                <noscript>
                    <div class="error message">
                        <p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'atto') ?></p>
                    </div>
                </noscript>

                <div class="clear"></div>
                
                <?php do_action('ato_interface_before_form'); ?>
                
                <form action="edit.php" method="get" id="to_form">
                    <input type="hidden" name="page" value="to-interface-<?php echo $post_type ?>" />
                    <?php
                
                     if ($post_type != 'post')
                        echo '<input type="hidden" name="post_type" value="'. $post_type .'" />';

                    
                                            
                    if (count($post_type_taxonomies) > 1)
                        {
                
                            ?>
                            
                            <h2 class="subtitle"><?php echo ucfirst($post_type_data->labels->name) ?> Taxonomies</h2>
                            <table cellspacing="0" class="wp-list-taxonomy widefat fixed">
                                <thead>
                                <tr>
                                    <th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e('Taxonomy Title', 'atto') ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e('Total Terms', 'atto') ?></th>    </tr>
                                </thead>

                                <tfoot>
                                <tr>
                                    <th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e('Taxonomy Title', 'atto') ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e('Total Terms', 'atto') ?></th>    </tr>
                                </tfoot>

                                <tbody id="the-list">
                                <?php
                                    
                                    $alternate = FALSE;
                                    foreach ($post_type_taxonomies as $key => $post_type_taxonomy)
                                        {
                                            $taxonomy_info = get_taxonomy($post_type_taxonomy);

                                            $alternate = $alternate === TRUE ? FALSE :TRUE;
                                            
                                            
                                            $args = array(
                                                            'hide_empty'    =>  0
                                                            );
                                            $taxonomy_terms = get_terms($post_type_taxonomy, $args);
                                                             
                                            ?>
                                                <tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo $taxonomy  ?>">
                                                        <th class="check-column" scope="row"><input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy ?>" <?php if ($post_type_taxonomy == $taxonomy) {echo 'checked="checked"';} ?> name="taxonomy">&nbsp;</th>
                                                        <td class="categories column-categories"><b><?php echo $taxonomy_info->label ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)</td>
                                                        <td class="categories column-categories"><?php echo count($taxonomy_terms) ?></td>
                                                </tr>
                                            
                                            <?php
                                        }
                                ?>
                                </tbody>
                            </table>
                            <br /><br /> 
                            <?php
                        }
                            ?>
                </form>
              
                <script type="text/javascript">    

                    var taxonomy    = '<?php echo $taxonomy ?>';

                </script>
               
                <form action="edit.php" method="get" id="to_form">
                    <input type="hidden" name="page" value="to-interface-<?php echo $post_type ?>" />
                    <input type="hidden" name="taxonomy" value="<?php echo $taxonomy ?>" />
                    <?php
                
                     if ($post_type != 'post')
                        echo '<input type="hidden" name="post_type" value="'. $post_type .'" />';
                     ?>
               <h2 class="subtitle"><input type="radio" <?php if ($order_type == 'auto') {echo 'checked="checked"';} ?> name="order_type" value="auto" onclick="jQuery(this).closest('form').submit();"> Automatic Order</h2>
               <?php if ($order_type == 'auto')
                        {
                           ?>
                            <div id="order-terms">
                                
                                <div id="nav-menu-header">
                                    <div class="major-publishing-actions">

                                            
                                            <div class="alignright actions">
                                                <p class="actions">
                                                    <input type="submit" value="Update" class="button-primary" name="update">
                                                </p>
                                            </div>
                                            
                                            <div class="clear"></div>

                                    </div><!-- END .major-publishing-actions -->
                                </div><!-- END #nav-menu-header -->

                                
                                <div id="post-body">                    
                                    
                                    <table class="form-table">
                                        <tbody>
                                            <tr valign="top">
                                                <th scope="row"><b>Order By</b></th>
                                                <td>
                                                    <?php
                                                    
                                                        $auto_order_by = isset($options['taxonomy_settings'][$taxonomy]['auto']['order_by']) ? $options['taxonomy_settings'][$taxonomy]['auto']['order_by'] : 'name';

                                                    ?>
                                                    <input type="radio" <?php if ($auto_order_by == 'default') {echo 'checked="checked"'; } ?> value="default" name="auto_order_by" />
                                                    <label for="blog-public">Default</label><br>
                                                    
                                                    <input type="radio" <?php if ($auto_order_by == 'id') {echo 'checked="checked"'; } ?> value="id" name="auto_order_by" />
                                                    <label for="blog-public">Creation Time / ID</label><br>
                                                    
                                                    <input type="radio" <?php if ($auto_order_by == 'name') {echo 'checked="checked"'; } ?> value="name" name="auto_order_by" />
                                                    <label for="blog-norobots">Name</label><br>
                                                    
                                                    <input type="radio" <?php if ($auto_order_by == 'count') {echo 'checked="checked"'; } ?> value="count" name="auto_order_by" />
                                                    <label for="blog-norobots">Count</label><br>
                                                    
                                                    <input type="radio" <?php if ($auto_order_by == 'slug') {echo 'checked="checked"'; } ?> value="slug" name="auto_order_by" />
                                                    <label for="blog-norobots">Slug</label><br>
                                                    
                                                    <input type="radio" <?php if ($auto_order_by == 'random') {echo 'checked="checked"'; } ?> value="random" name="auto_order_by" />
                                                    <label for="blog-norobots">Random</label><br>
                                                     
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <table class="form-table">
                                        <tbody>
                                            <tr valign="top">
                                                <th scope="row"><b>Order</b></th>
                                                <td>
                                                    <?php
                                                    
                                                        $auto_order = isset($options['taxonomy_settings'][$taxonomy]['auto']['order']) ? $options['taxonomy_settings'][$taxonomy]['auto']['order'] : 'desc';

                                                    ?>
                                                    
                                                    <input type="radio" <?php if ($auto_order == 'desc') {echo 'checked="checked"'; } ?> value="desc" name="auto_order" />
                                                    <label for="blog-public">Descending</label><br>

                                                    <input type="radio" <?php if ($auto_order == 'asc') {echo 'checked="checked"'; } ?> value="asc" name="auto_order" />
                                                    <label for="blog-public">Ascending</label><br>
                                                    
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                        
                                    <br />
                                    <div class="clear"></div>
                                </div>
                                
                                <div id="nav-menu-footer">
                                    <div class="major-publishing-actions">
                                            <div class="alignright actions">
                                                <p class="submit">
                                                    <input type="submit" value="Update" class="button-primary" name="update">
                                                </p>
                                            </div>
                                            
                                            <div class="clear"></div>

                                    </div><!-- END .major-publishing-actions -->
                                </div><!-- END #nav-menu-header -->
                                
                            </div>
                            
                            <?php
                        }
                ?>
               
               
               
               <h2 class="subtitle"><input type="radio" <?php if ($order_type == 'manual') {echo 'checked="checked"';} ?> name="order_type" value="manual" onclick="jQuery(this).closest('form').submit();"> Manual Order</h2>
               <?php if ($order_type == 'manual')
                        {
                           ?>
                            <div id="order-terms">
                                
                                <div id="nav-menu-header">
                                    <div class="major-publishing-actions">

                                            
                                            <div class="alignright actions">
                                                <p class="actions">
                  
                                                    <span class="img_spacer">&nbsp;
                                                        <img alt="" src="<?php echo TOURL ?>/images/wpspin_light.gif" class="waiting pto_ajax_loading" style="display: none;">
                                                    </span>
                                                    <a href="javascript:;" class="save-order button-primary">Update</a>
                                                </p>
                                            </div>
                                            
                                            <div class="clear"></div>

                                    </div><!-- END .major-publishing-actions -->
                                </div><!-- END #nav-menu-header -->

                                
                                <div id="post-body">                    
                                    
                                        <ul id="sortable">
                                            <?php 
                                                
                                                listTerms($taxonomy); 
                                            ?>
                                        </ul>
                                        
                                        <div class="clear"></div>
                                </div>
                                
                                <div id="nav-menu-footer">
                                    <div class="major-publishing-actions">
                                            <div class="alignright actions">
                                                <p class="submit">
                                                    <img alt="" src="<?php echo TOURL ?>/images/wpspin_light.gif" class="waiting pto_ajax_loading" style="display: none;">
                                                    <a href="javascript:;" class="save-order button-primary">Update</a>
                                                </p>
                                            </div>
                                            
                                            <div class="clear"></div>

                                    </div><!-- END .major-publishing-actions -->
                                </div><!-- END #nav-menu-header -->
                                
                            </div>
                            
                            <?php
                        }
                ?> 

                </form>

                
                <script type="text/javascript">
    
                    jQuery(document).ready(function() {
                        
                        jQuery('ul#sortable').nestedSortable({
                                handle:             'div',
                                tabSize:            20,
                                listType:           'ul',
                                items:              'li',
                                toleranceElement:   '> div',
                                placeholder:        'ui-sortable-placeholder',
                                disableNesting:     'no-nesting'
                                <?php
                    
                                    if ($is_hierarchical === TRUE)
                                        {
                                        }
                                        else
                                        {
                                            ?>,disableNesting      :true<?php
                                        }
                                ?>});
                          
                        jQuery(".save-order").bind( "click", function() {
                            jQuery(this).parent().find('img').show();
                            
                            var serialized = jQuery('ul#sortable').nestedSortable('serialize');
                            
                            jQuery.post( ajaxurl, { 
                                                        action:         'update-taxonomy-order', 
                                                        order:          jQuery("#sortable").nestedSortable("serialize"),
                                                        taxonomy:       taxonomy
                            }, function() {
                                    jQuery("#ajax-response").html('<div class="message updated fade"><p><?php _e( "Items Order Updated", 'atto' ) ?></p></div>');
                                    jQuery("#ajax-response div").delay(3000).hide("slow");
                                    jQuery('img.pto_ajax_loading').hide();
                                });
                        });
                    });
                </script>
                
            </div>
            <?php 
            
            
        }
    
    
    function listTerms($taxonomy) 
            {

                // Query pages.
                $args = array(
                            'orderby'       =>  'term_order',
                            'depth'         =>  0,
                            'child_of'      => 0,
                            'hide_empty'    =>  0
                );
                $taxonomy_terms = get_terms($taxonomy, $args);

                $output = '';
                if (count($taxonomy_terms) > 0)
                    {
                        $output = TOwalkTree($taxonomy_terms, $args['depth'], $args);    
                    }

                echo $output; 
                
            }
        
        function TOwalkTree($taxonomy_terms, $depth, $r) 
            {
                $walker = new TO_Terms_Walker; 
                $args = array($taxonomy_terms, $depth, $r);
                return call_user_func_array(array(&$walker, 'walk'), $args);
            }

?>