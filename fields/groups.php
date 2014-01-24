<?php

class BP_Xprofile_Groups_Field extends BP_Xprofile_Custom_Field {
    
    
    public function __construct() {
        parent::__construct('group', 'Group' );
    }
    /**
     * Display in fields list
     * @param type $field
     * @return type
     */
    public function admin_field( $field ) {
        
        if( $this->type != $field->type )
            return ;
        
        $this->admin_edit_field($field);

    }
    
    /**
     * When adding/Editing New Field
     * @param type $field
     */
    public function admin_edit_field( $field ) {
        
        $class = $this->type != $field->type ? 'display: none;' : '';
        $children = $field->get_children();
        $selected_groups = array();
        if( $children )
            $selected_groups = wp_list_pluck($children, 'name');
    
?>
          
          
          <div id="<?php echo esc_attr( $this->get_type() ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
		<h3><?php _e( 'Please Select a list of Groups to show:', 'buddypress' ); ?></h3>
                
              <div class="inside">
                <?php 
                    //$selected_groups = bp_xprofile_get_meta( $field->id, 'field', 'selected_groups' );
                    
                    $groups = groups_get_groups( array('per_page'=>-1, 'page'=> 0 ));
                    if( isset($groups['groups']))
                        $groups = $groups['groups']; 
                    
                ?>
                  <?php if( !empty($groups )):?>
                  <select multiple="multiple" name='groups-selected[]'>
                     <?php foreach( $groups as $group ):?>
                      <option value="<?php echo $group->id;?>" <?php selected(true, in_array($group->id, $selected_groups ));?>><?php echo $group->name;?></option>
                      <?php endforeach;?>
                  </select>
                  
                  <?php endif;?>
              </div>
                
                <p><input type="checkbox" name='show_all_groups' value='1'>Show all Groups</p>  
           
          </div>
          
	<?php 				
        
    }
    public function render( $field ) {
        ?>
        <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
	
       <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple" <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
           <?php
           if ( !method_exists( $field, 'get_children' ) ) {
			$field_obj = new BP_XProfile_Field( $field->id );

			foreach( $field as $field_prop => $field_prop_value ) {
				if ( !isset( $field_obj->{$field_prop} ) ) {
					$field_obj->{$field_prop} = $field_prop_value;
				}
			}

			$field = $field_obj;
		}
           $options = $field->get_children();
		$original_option_values = '';
                $original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $field->id ) );
                
                if ( empty( $original_option_values ) && !empty( $_POST['field_' . $field->id] ) ) {
                        $original_option_values = $_POST['field_' . $field->id];
                }

                $option_values = (array) $original_option_values;

                for ( $k = 0, $count = count( $options ); $k < $count; ++$k ) {

                        // Check for updated posted values, but errors preventing them from being saved first time
                        foreach( $option_values as $i => $option_value ) {
                                if ( isset( $_POST['field_' . $field->id] ) && $_POST['field_' . $field->id][$i] != $option_value ) {
                                        if ( !empty( $_POST['field_' . $field->id][$i] ) ) {
                                                $option_values[] = $_POST['field_' . $field->id][$i];
                                        }
                                }
                        }
                        $selected = '';

                        // Run the allowed option name through the before_save filter, so we'll be sure to get a match
                        $allowed_options = xprofile_sanitize_data_value_before_save( $options[$k]->name, false, false );

                        // First, check to see whether the user-entered value matches
                        if ( in_array( $allowed_options, (array) $option_values ) ) {
                                $selected = ' selected="selected"';
                        }

                        // Then, if the user has not provided a value, check for defaults
                        if ( !is_array( $original_option_values ) && empty( $option_values ) && !empty( $options[$k]->is_default_option ) ) {
                                $selected = ' selected="selected"';
                        }
                        $group = groups_get_group(array('group_id'=>$options[$k]->name ) );
                        
                        echo  apply_filters( 'bp_get_the_profile_field_options_multiselect', '<option' . $selected . ' value="' . esc_attr( stripslashes( $options[$k]->name ) ) . '">' . $group->name . '</option>', $options[$k], $field->id, $selected, $k );

                }
                        ?>

					</select>
     <?php    
    }
    
  /**
   * Saves when this field type is created in admin
   * @global type $bp
   * @global type $wpdb
   * @param BP_XProfile_Field $field
   */  
   public function save( BP_XProfile_Field $field ) {
       //1 save selected groups
       //this multi group selection
      global $bp, $wpdb;
      if( !$field->id )
            $field->id = $wpdb->insert_id;
       if( !empty($_POST['groups-selected'])){
           
           $groups_selected = $_POST['groups-selected'];
          
           foreach($groups_selected as $group_id )
               
               $wpdb->query( $wpdb->prepare(
                        "INSERT INTO {$bp->profile->table_name_fields} 
                            (
                            group_id,
                            parent_id,
                            type,
                            name,
                            description,
                            is_required,
                            option_order,
                            is_default_option
                            )
                            VALUES ( %d, %d, '%s', %s, '%s', %d, %d, %d)",
                                $field->group_id,
                                $field->id,
                                $this->type.'_option',
                                $group_id,
                                '',
                                0,
                                1,
                                1
                                )
                         );
                 
           
           
           
       }
   }
    
}

bp_custom_fields_manager()->register_field( new BP_Xprofile_Groups_Field() );