<?php

class BP_Xprofile_Groups_Field extends BP_Xprofile_Custom_Field {
    
    
    public function __construct() {
        parent::__construct('group', 'Group' );
    }
    
    public function admin_render( $field ) {
        
                if( $this->type != $field->type )
            return ;
                
              $show_all = bp_xprofile_get_meta( $field->id, 'field', 'show_all_groups' );
                ?>

          <input type="email" name="field_<?php echo $field->id ; ?>" id="<?php echo $field->id;?>" class="input" placeholder="<?php _e( 'joe@example.com', 'bpepf' );?>" value="" />
            
      <?php           
    }
    
    public function admin_render_form( $field ) {
        
        $class = $this->type != $field->type ? 'display: none;' : '';
    
    
?>
          
          
          <div id="<?php echo esc_attr( $this->get_type() ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
		<h3><?php _e( 'Please Select a list of Groups to show:', 'buddypress' ); ?></h3>
                
              <div class="inside">
                <?php 
                    $selected_groups = bp_xprofile_get_meta( $field->id, 'field', 'selected_groups' );
                    
                    $groups = groups_get_groups( array('per_page'=>-1, 'page'=> 0 ));
                    if( isset($groups['groups']))
                        $groups = $groups['groups']; 
                    
                ?>
                  <?php if( !empty($groups )):?>
                  <select multiple="true" name='group-selected'>
                     <?php foreach( $groups as $group ):?>
                      <option value="<?php echo $group->id;?>"><?php echo $group->name;?></option>
                      <?php endforeach;?>
                  </select>
                  
                  <?php endif;?>
              </div>
                
                <p><input type="checkbox" name='show_all_groups' value='1'>Show all Groups</p>  
           
          </div>
          
	<?php 				
        
    }
    public function render() {
        
        
    }
    
    
    
}

bp_custom_fields_manager()->register_field( new BP_Xprofile_Groups_Field() );