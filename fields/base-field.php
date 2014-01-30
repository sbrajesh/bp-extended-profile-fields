<?php

class BP_Xprofile_Custom_Field{
    
    protected $type = '';
    protected $label = '';
    
    public function __construct( $type = '', $label = '' ) {
        
        $this->type = $type;
        $this->label = $label;
        /*
        //show field on registration page/profile edit page
       
        

        
        
        add_filter( 'xprofile_get_field_data', array( $this, 'get_field_data' ), 10, 3 );
        //add_filter( 'bp_get_the_profile_field_value', array( $this, 'filter_value' ), 10, 3 );
        */
       
        
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
    public function admin_field(){
        
    }
    
    /**
     * Render Field for  Add/Edit profile field page form
     */
    public function admin_edit_field( $field ){
        
    }
    
    
    function save( BP_XProfile_Field $field ){
        
        //now anyone can use this to save extra data
        
    }
    
    
    
    
    function field_value( $val, $type, $id ){
        
        if( $this->type != $type )
            return $val;
        //otherwise 
        return $this->value();
    }
    
    //return the aactual value
    public function value( $val, $type, $id ) {
        
        
        
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