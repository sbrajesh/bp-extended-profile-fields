<?php

class BP_Xprofile_Custom_Field{
    
    protected $type = '';
    protected $label = '';
    
    public function __construct( $type = '', $label = '' ) {
        
        $this->type = $type;
        $this->label = $label;
        /*
        //show field on registration page/profile edit page
        add_filter( 'bp_custom_profile_edit_fields_pre_visibility', array( $this, 'render' ) );
        

        add_filter( 'bp_get_the_profile_field_value', array( $this, 'field_value' ), 10, 3 );
        
        add_filter( 'xprofile_get_field_data', array( $this, 'get_field_data' ), 10, 3 );
        //add_filter( 'bp_get_the_profile_field_value', array( $this, 'filter_value' ), 10, 3 );
        
        //save the field
        add_action( 'xprofile_field_after_save', array( $this, 'save' ));*/
    }
    
    
   
    
    /**
     * Render field on registration/Edit profile field
     * @var BP_Xprofile_Field
     */
    public function render( $field ){
        

    }
    
    /**
     * Render Field for admin Add/Edit field page
     */
    public function admin_render(){
        
    }
    
    /**
     * Render Field for  Add/Edit page form
     */
    public function admin_render_form( $field ){
        
    }
    
    
    function save( BP_XProfile_Field $field ){
        
    }
    
    function field_value( $val, $type, $id ){
        
        if( $this->type != $type )
            return $val;
        //otherwise 
        return $this->value();
    }
    
    //return the aactual value
    public function value(){
        
        
    }
    
    public function get_field_data($data, $field_id, $user_id){
        
    }
    
    function filter_value( $val, $type, $id){
        
    }
    /**
     * Get the field type
     * 
     * @return string
     */
    public function get_type(){
        
        return $this->type;
    }
    /**
     *  Returns label for this field type
     * @return string
     */
    public function get_label(){
      
        return $this->label;
    }
    
}