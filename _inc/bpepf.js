jQuery(document).ready(function() {
    
    //on the add edit page, add all options in the drop down box
    
    var jq = jQuery;
    
    jq('#fieldtype').append( jq('#bp-extended-select').html() );
    //our own custom types
    var custom_types =[];
    jq('#bp-extended-select option').each( function(){
       
        custom_types.push( jq(this).val() );
    });
   
   
   //when the type selector field is changed
    jq('#fieldtype').change(function(){
       var selected =  jq('#fieldtype').find(':selected').val();
       
       //hide all our options
       for( var i=0; i<custom_types.length; i++ ){
           jq('#postbox-container-2 div#'+custom_types[i]).hide();
       }
       //show the options if our's is selected
       if( custom_types.indexOf(selected) > 0 )
           jq('#postbox-container-2 div#'+selected).show();
       
    });
});