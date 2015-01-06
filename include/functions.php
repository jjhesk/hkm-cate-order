<?php

    /**
    * @desc 
    * 
    * Return UserLevel
    * 
    */
    function atto_userdata_get_user_level($return_as_numeric = FALSE)
        {
            global $userdata;
            
            $user_level = '';
            for ($i=10; $i >= 0;$i--)
                {
                    if (current_user_can('level_' . $i) === TRUE)
                        {
                            $user_level = $i;
                            if ($return_as_numeric === FALSE)
                                $user_level = 'level_'.$i; 
                            break;
                        }    
                }        
            return ($user_level);
        }    
        
        
    /**
    * @desc 
    * 
    * Check the latest plugin version
    * 
    */
    function atto_check_plugin_version($plugin)
        {
            if( strpos( TOPATH . '/taxonomy-order.php', $plugin ) !== FALSE )
                {
                    //check last update check attempt
                    $last_check = get_option('atto_last_version_check');
                    if (is_numeric($last_check) && (time() - 60*60*12) > $last_check)
                        {
                            $last_version_data = wp_remote_fopen(TO_VERSION_CHECK_URL);
                            update_option('atto_last_version_check_data', $last_version_data);    
                        }
                        else
                            {
                                $last_version_data = get_option('atto_last_version_check_data'); 
                            }
                    
                    if($last_version_data !== FALSE && $last_version_data != '') 
                        {
                            $info_raw = explode( '/',$last_version_data );
                            $info = array();
                            foreach ($info_raw as $line)
                                {
                                    list($name, $value)= explode("=", $line);
                                    $info[$name] = $value;
                                }
                                
                            if( ( version_compare( strval( $info['version'] ), TOVERSION , '>' ) == 1 ) ) 
                                {
                                    ?>
                                        <tr class="plugin-update-tr">
                                            <td colspan="3" class="plugin-update colspanchaccpo_post_order_relnge">
                                                <div class="update-message"><?php _e('There is a new version of Advanced Taxonomy Terms Order. Use your personal link to update or contact us for recover.', 'atto' ) ?></div>
                                            </td>
                                        </tr>
                                    <?php
                                } 
                        }
                        
                    //update last version check attempt
                    update_option('atto_last_version_check', time());
                }   
            
        }
        
    function atto_get_the_terms($terms = '', $id = '', $taxonomy = '')
        {
            if($terms == '' || $id == '' || $taxonomy == '' || (is_array($terms) && count($terms) < 1))
                return '';
            
            $options = get_option('tto_options'); 

            //if admin make sure use the admin setting
            if (is_admin())
                return $terms;
            
            if (!is_array($terms) || $terms === FALSE)
                return $terms;
                
            if ($options['autosort'] != "1")
                return $terms;
                
            if (is_array($taxonomy) && count($taxonomy) > 1)
                return $terms;
                else if(is_array($taxonomy))
                    {
                        $taxonomy = $taxonomy[0];
                    }
            
            //check the current setting for current taxonomy
            $order_type = (isset($options['taxonomy_settings'][$taxonomy]['order_type'])) ? $options['taxonomy_settings'][$taxonomy]['order_type'] : 'manual'; 
            
            //if manual
            if ($order_type == 'manual')
                {

                    $terms = atto_reindex_terms_array($terms, 'term_order');
                    
                    return $terms;
                }
                
            //if auto
            $auto_order_by = isset($options['taxonomy_settings'][$taxonomy]['auto']['order_by']) ? $options['taxonomy_settings'][$taxonomy]['auto']['order_by'] : 'name';
            $auto_order = isset($options['taxonomy_settings'][$taxonomy]['auto']['order']) ? $options['taxonomy_settings'][$taxonomy]['auto']['order'] : 'desc';
            
            
            $order_by = "";
            switch ($auto_order_by)
                {
                    case 'id':
                                $terms = atto_reindex_terms_array($terms, 'term_id');
                                break;
                    case 'name':
                                $terms = atto_reindex_terms_array($terms, 'name');
                                break;
                    case 'slug':
                                $terms = atto_reindex_terms_array($terms, 'slug');
                                break;
                    case 'count':
                                $terms = atto_reindex_terms_array($terms, 'count');
                                break;
                                
                    case 'random':
                                shuffle($terms);
                                break;
                    default :
                                break;
                }
            
            if ($auto_order == 'desc')
                $terms = array_reverse($terms);
            
            return $terms;
        }
        
    function atto_reindex_terms_array($terms, $required_field)
        {
            //re-arange the term list
            $_reordered_key_relation = array();
            foreach ($terms as $key => $term)
                {
                    $_reordered_key_relation[$key]  = strtolower($term->{$required_field});
                }
            
            asort($_reordered_key_relation);
            $_terms = array();
            
            foreach ($_reordered_key_relation as $key => $term)
                {
                    $_terms[] = $terms[$key];
                }    
                
                
            $terms = array_values($_terms);
            
            return $terms;   
            
        }
        
    function atto_get_term_hierarchy($taxonomy)
        {
            if ( !is_taxonomy_hierarchical($taxonomy) )
                return array();
            $children = get_option("{$taxonomy}_children");

            if ( is_array($children) )
                return $children;
            $children = array();
    
            return $children;   
            
        }
    
?>