<?php


function to_plugin_options()
    {
        $options = get_option('tto_options');
        
        if (isset($_POST['form_submit']))
            {
                    
                $options['capability'] = $_POST['capability']; 
                
                $options['autosort']    = isset($_POST['autosort'])     ? $_POST['autosort']    : '';
                $options['adminsort']   = isset($_POST['adminsort'])    ? $_POST['adminsort']   : '';
                    
                echo '<div class="updated fade"><p>' . __('Settings Saved', 'atto') . '</p></div>';

                update_option('tto_options', $options);   
            }
            
            
        //build an array containing the user role and capability
        $user_roles = array();
        $user_roles['Subscriber']       = apply_filters('atto_user_role_capability', 'read', 'Subscriber');
        $user_roles['Contributor']      = apply_filters('atto_user_role_capability', 'edit_posts', 'Contributor');
        $user_roles['Author']           = apply_filters('atto_user_role_capability', 'publish_posts', 'Author');
        $user_roles['Editor']           = apply_filters('atto_user_role_capability', 'publish_pages', 'Editor');
        $user_roles['Administrator']    = apply_filters('atto_user_role_capability', 'install_plugins', 'Administrator');
        
        //allow to add custom roles
        $user_roles = apply_filters('atto_user_roles_and_capabilities', $user_roles);
                        
                    ?>
                      <div class="wrap"> 
                        <div id="icon-settings" class="icon32"></div>
                            <h2>General Settings</h2>
                           
                            <form id="form_data" name="form" method="post">   
                                <br />
                                <h2 class="subtitle">General</h2>                              
                                <table class="form-table">
                                    <tbody>
                            
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label><?php _e( "Minimum Level to use this plugin", 'atto' ) ?></label></th>
                                            <td>
                                                <select id="role" name="capability">
                                                    <?php
    
                                                        foreach ($user_roles as $user_role => $user_capability)
                                                            {
                                                                ?><option value="<?php echo $user_capability ?>" <?php if (isset($options['capability']) && $options['capability'] == $user_capability) echo 'selected="selected"'?>><?php _e($user_role, 'atto') ?></option><?php   
                                                            }
    
    

                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Auto Sort</label></th>
                                            <td>
                                                <select id="role" name="autosort">
                                                    <option value="0" <?php if ($options['autosort'] == "0") echo 'selected="selected"'?>><?php _e('OFF', 'atto') ?></option>
                                                    <option value="1" <?php if ($options['autosort'] == "1") echo 'selected="selected"'?>><?php _e('ON', 'atto') ?></option>
                                                </select> *(global setting, you can turn on/off from each Taxonomy Order menu)
                                            </td>
                                        </tr>
                                        
                                        <tr valign="top">
                                            <th scope="row" style="text-align: right;"><label>Admin Sort</label></th>
                                            <td>
                                                <label for="users_can_register">
                                                <input type="checkbox" <?php if ($options['adminsort'] == "1") {echo ' checked="checked"';} ?> value="1" name="adminsort">
                                                <?php _e("This will change the order of terms within the admin interface", 'atto') ?>.</label>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                                

                                
                                <br /><br /><br />
                                <p><b><u>OFF</u></b></p>                                                
                                <p class="example"><?php _e('The query will not be touched (unless you manually change that from a specific Taxonomy Order menu), all terms will be retrieved in the same order as before. To retrieve the terms in the required order you must use the term_order on the orderby parameter:', 'atto') ?>:</p>
                                <pre class="example">
$argv = array(
                'orderby'       =>  'term_order',
                'hide_empty'    => false
                );
get_terms('category', $argv);
</pre>
                                <p><?php _e('See more info on the get_terms usage', 'atto') ?> <a href="http://codex.wordpress.org/Function_Reference/get_terms" target="_blank">here</a></p>

                                <p><b><u>ON</u></b></p> 
                                <p class="example"><?php _e('The query will be updated (unless you manually change that from a specific Taxonomy Order menu), any orderby parameter will be changed to term_order. Practically all queries will be forced to use the new order. This is recommended if you don\'t want to change any theme code to apply the terms order', 'atto') ?></p>
                                                   
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'atto') ?>">
                               </p>
                            
                                <input type="hidden" name="form_submit" value="true" />
                                
                            </form>
                                                        
                    <?php  
            echo '</div>';   
        
        
    }

?>