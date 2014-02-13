<?php
/**
 * Allow Users to Join only one group and don't allow to change that ever again
 */
class BP_Xprofile_Unique_Group_Field extends BP_Xprofile_Custom_Field {
    
    
    public function __construct() {
        parent::__construct( 'unique-group', 'Unique Group' );
        
        add_filter( 'xprofile_data_is_valid_field', array( $this, 'is_valid' ), 10, 2 );
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
		<h3><?php _e( 'Please Select a list of Groups to Allow Users to choose from:', 'buddypress' ); ?></h3>
                
              <div class="inside">
                <?php 
                    //$selected_groups = bp_xprofile_get_meta( $field->id, 'field', 'selected_groups' );
                    
                    $groups = groups_get_groups( array('per_page' => -1, 'page'=> 0 ));
                    if( isset($groups['groups']))
                        $groups = $groups['groups']; 
                    
                ?>
                  <?php if( !empty($groups )):?>
                  <select multiple="multiple" name='unique-groups-selected[]'>
                     <?php foreach( $groups as $group ):?>
                      <option value="<?php echo $group->id;?>" <?php echo selected(true, in_array($group->id, $selected_groups ));?>><?php echo $group->name;?></option>
                      <?php endforeach;?>
                  </select>
                  
                  <?php endif;?>
              </div>
                
                <p><input type="checkbox" name='show_all_groups' value='1' <?php echo checked(1, bp_xprofile_get_meta($field->id, 'field', 'show_all_groups'));?> />Show all Groups</p>  
                <p><input type="checkbox" name='restrict_group' value='1' <?php echo checked(1, bp_xprofile_get_meta($field->id, 'field', 'restrict_group'));?> />Don't allow Changing Group</p>  
           
          </div>
          
	<?php 				
        
    }
    public function render( $field ) {
        ?>
        <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
	
       <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"  <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
           <?php
                $field = $this->extend_field( $field );//extend field to add methods etc
                $children = $field->get_children();
                $selected_groups = array();
                //get all selected group ids
                if( $children )
                    $selected_groups = wp_list_pluck( $children, 'name' );
                print_r($selected_groups);
                //copy paste from multi select box, can we make better?
                $original_option_values = '';
                $original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $field->id ) );
                
                if ( empty( $original_option_values ) && !empty( $_POST['field_' . $field->id] ) ) {
                        $original_option_values = $_POST['field_' . $field->id];
                }

                $option_values = (int) $original_option_values;

                //check for field settings
                //
                $show_all_groups = bp_xprofile_get_meta($field->id, 'field', 'show_all_groups' );
                $restrict_groups = bp_xprofile_get_meta($field->id, 'field', 'restrict_group' );
                if( $show_all_groups ){
                    
                     $groups = groups_get_groups( array('per_page' => -1, 'page'=> 0 , 'populate_extras'=>false));
                     if( isset($groups['groups'])){
                        $groups = $groups['groups'];
                      
                        $groups = wp_list_pluck($groups, 'id' );
                     }
                    
                }else{
                    $groups = $selected_groups;
                    
                }
                
                
                foreach ( $groups as $group_id ) {

                        
                        $selected = '';

                        // Run the allowed option name through the before_save filter, so we'll be sure to get a match
                        $allowed_options = xprofile_sanitize_data_value_before_save( $group_id, false, false );

                        // First, check to see whether the user-entered value matches
                        if ( $allowed_options == $option_values  ) {
                                $selected = ' selected="selected"';
                        }

                      
                        $group = groups_get_group( array( 'group_id' => $group_id ) );
                        
                        echo  apply_filters( 'bp_get_the_profile_field_options_unique_group', '<option' . $selected . ' value="' . $group_id . '">' . $group->name . '</option>', $group, $field->id, $selected);

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
       
       //delete old options
       
      global $bp, $wpdb;
      if( !$field->id )
            $field->id = $wpdb->insert_id;
      
       if( !empty( $_POST['unique-groups-selected'] ) ) {
           $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->profile->table_name_fields} WHERE group_id=%d AND parent_id = %d ", $field->group_id, $field->id ) );
                              
           
           $groups_selected = $_POST['unique-groups-selected'];
          
           
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
       
       
       //update xprofile field meta 
       bp_xprofile_update_field_meta($field->id, 'restrict_group', (int) $_POST['restrict_group'] );
       bp_xprofile_update_field_meta($field->id, 'show_all_groups', (int) $_POST['show_all_groups'] );
   }
    
   /**
    * Returns the linked group name for bp_get_the_profile_field_value
    * @param type $val
    * @param type $type
    * @param type $id
    * @return type
    */
   public function value($val, $type, $id) {
       
       $field = new BP_XProfile_Field( $id );
       
       $val = maybe_unserialize( $field->data->value );
      
       if( !is_array( $val ) )
           $val = (array) $val;
       
       $groups = array();
       
       foreach( $val as $group_id ){
           
           $group = new BP_Groups_Group( $group_id );
           $groups[] = "<a href='". bp_get_group_permalink( $group ). "'>". $group->name."</a>";
       }  
       
       return join(',', $groups );
       
       
   }
   
   public function is_valid( $ret_val, $field ){
       $xfield = xprofile_get_field($field->field_id);
      // print_r($xfield);
       if( $this->type != $xfield->type )
           return $ret_val;
       if( bp_is_current_component( 'xprofile' ) && bp_xprofile_get_meta($xfield->id, 'field', 'restrict_group' ) ){
           
           $old_val = xprofile_get_field_data($field->field_id);
           if( $old_val != $field->value)
               return false;
           //make sure to check if new/old values are same
           
          
      } 
      return true;
       
       
   }
}

bp_custom_fields_manager()->register_field( new BP_Xprofile_Unique_Group_Field() );