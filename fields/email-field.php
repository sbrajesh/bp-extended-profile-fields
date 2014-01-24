<?php

class BP_Xprofile_Email_Field extends BP_Xprofile_Custom_Field {
    
    
    public function __construct() {
        parent::__construct('email', 'Email' );
    }
    
    public function admin_field( $field ) {
        
                if( $this->type != $field->type )
            return ;
                ?>

          <input type="email" name="field_<?php echo $field->id ; ?>" id="<?php echo $field->id;?>" class="input" placeholder="<?php _e( 'joe@example.com', 'bpepf' );?>" value="" />
            
      <?php           
    }
    
    public function admin_edit_field( $field ) {
        
    }
    public function render() {
        
        
    }
    
    
    
}

bp_custom_fields_manager()->register_field( new BP_Xprofile_Email_Field() );