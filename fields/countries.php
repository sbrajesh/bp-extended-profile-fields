<?php
/**
 * Allows admin to specify a Country Drop Down 
 */
class BP_Xprofile_Country_Field extends BP_Xprofile_Custom_Field {
    //we store list of countries as array
    private static $countries = array();
    
    public function __construct() {
        parent::__construct( 'country', __( 'Country') );
        self::setup_countries();//initialize the array of countries
    }
    /**
     * Display in fields list
     * @param type $field
     * @return type
     */
    public function admin_field( $field ) {
        
        if( $this->type != $field->type )
            return ;
        
        $this->admin_edit_field( $field );

    }
    
    /**
     * When adding/Editing New Field
     * @param type $field
     */
    public function admin_edit_field( $field ) {
        
        $class = $this->type != $field->type ? 'display: none;' : '';
           
        ?>
          
          
          <div id="<?php echo esc_attr( $this->get_type() ); ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
              <h3><?php _e( 'Countries');?></h3>
              <div class="inside">
                <?php 
                   $selected_country =  $this->get_default_country_code($field->id);
                    
                    $countries = self::get_countries();
                    
                ?>
                  <?php if( !empty( $countries ) ):?>
                  <select  name='country'>
                      <?php foreach( $countries as $country_code => $country_name ):?>
                      <option value ="<?php echo $country_code;?>" <?php echo selected( $country_code, $selected_country );?>><?php echo $country_name;?></option>
                      <?php endforeach;?>
                  </select>
                  
                  <?php endif;?>
              </div>
                
              
           
          </div>
          
	<?php 				
        
    }
    
    /**
     * Renders Field on Registration/Edit Profile page
     * @param BP_XProfile_Field $field 
     */
    public function render( $field ) {
        ?>
        <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
	
        <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"  <?php if ( bp_get_the_profile_field_is_required() ) : ?>aria-required="true"<?php endif; ?>>
           <?php
                //countries list as array
                $options = self::get_countries();
               //which country is selected by default?
                
                $default_option = $this->get_default_country_code( $field->id );
                //copy paste from multi select box, can we make better?
                $original_option_values = '';
                $original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $field->id ) );
                
                if ( empty( $original_option_values ) && !empty( $_POST['field_' . $field->id] ) ) {
                        $original_option_values = $_POST['field_' . $field->id];
                }

                $option_values = (array) $original_option_values;

                foreach (  $options  as $code => $country ) {

                       
                        $selected = '';

                        // Run the allowed option name through the before_save filter, so we'll be sure to get a match
                        $allowed_options = xprofile_sanitize_data_value_before_save( $code, false, false );

                        // First, check to see whether the user-entered value matches
                        if ( in_array( $allowed_options, (array) $option_values ) ) {
                                $selected = ' selected="selected"';
                        }

                        
					// Then, if the user has not provided a value, check for defaults
					if ( !is_array( $original_option_values ) && empty( $option_values ) && $code == $default_option ) {
						$selected = ' selected="selected"';
					}
                       // $country= self::get_country( $options[$k]->name );
                        
                        echo  apply_filters( 'bp_get_the_profile_field_options_country', '<option' . $selected . ' value="' . esc_attr( stripslashes( $code ) ) . '">' . $country . '</option>', $code, $field->id, $selected );

                }
                        ?>

    </select>
     <?php    
    }
    
  /**
   * Saves when this field type is created in admin
   * WE ARE JUST SAVING THE DEFAULT COUNTRY
   * @param BP_XProfile_Field $field
   */  
   public function save( BP_XProfile_Field $field ) {
       
       
      global $wpdb;
      if( !$field->id )
            $field->id = $wpdb->insert_id;
      
       if( !empty( $_POST['country'] ) ) {
           
           $country_selected = $_POST['country'];
          
          bp_xprofile_update_meta( $field->id, 'field', 'default_country', $country_selected );
           
           
           
       }
   }
   
   /**
    * Get the code for default selected country
    * @param type $field_id
    * @return type 
    */
   public function get_default_country_code( $field_id ){
       return bp_xprofile_get_meta( $field_id, 'field', 'default_country' );
   }
   /**
    * Returns Name of Country
    * @param type $val
    * @param type $type
    * @param type $id
    * @return type
    */
   public function value($val, $type, $id) {
       
       $field = new BP_XProfile_Field( $id );
       
       $val = maybe_unserialize( $field->data->value );
      
       if( is_array( $val ) )
           $val = array_pop( $val );
       
       return self::get_country( $val );
       
       
   }
   
   public static function get_country( $code ){
       
       return self::$countries[$code];
   }
   /**
    * Credit: Country list is taken from http://www.robertmullaney.com/2013/04/03/updated-iso-3316-country-list-php-array/
    */
   private static function setup_countries(){
       
       if( empty (self:: $countries )){
           
           self::$countries = array(
                'AF' => 'Afghanistan',
                'AX' => 'Åland Islands',
                'AL' => 'Albania',
                'DZ' => 'Algeria',
                'AS' => 'American Samoa',
                'AD' => 'Andorra',
                'AO' => 'Angola',
                'AI' => 'Anguilla',
                'AQ' => 'Antarctica',
                'AG' => 'Antigua and Barbuda',
                'AR' => 'Argentina',
                'AM' => 'Armenia',
                'AW' => 'Aruba',
                'AU' => 'Australia',
                'AT' => 'Austria',
                'AZ' => 'Azerbaijan',
                'BS' => 'Bahamas',
                'BH' => 'Bahrain',
                'BD' => 'Bangladesh',
                'BB' => 'Barbados',
                'BY' => 'Belarus',
                'BE' => 'Belgium',
                'BZ' => 'Belize',
                'BJ' => 'Benin',
                'BM' => 'Bermuda',
                'BT' => 'Bhutan',
                'BO' => 'Bolivia, Plurinational State of',
                'BQ' => 'Bonaire, Sint Eustatius and Saba',
                'BA' => 'Bosnia and Herzegovina',
                'BW' => 'Botswana',
                'BV' => 'Bouvet Island',
                'BR' => 'Brazil',
                'IO' => 'British Indian Ocean Territory',
                'BN' => 'Brunei Darussalam',
                'BG' => 'Bulgaria',
                'BF' => 'Burkina Faso',
                'BI' => 'Burundi',
                'KH' => 'Cambodia',
                'CM' => 'Cameroon',
                'CA' => 'Canada',
                'CV' => 'Cape Verde',
                'KY' => 'Cayman Islands',
                'CF' => 'Central African Republic',
                'TD' => 'Chad',
                'CL' => 'Chile',
                'CN' => 'China',
                'CX' => 'Christmas Island',
                'CC' => 'Cocos (Keeling) Islands',
                'CO' => 'Colombia',
                'KM' => 'Comoros',
                'CG' => 'Congo',
                'CD' => 'Congo, the Democratic Republic of the',
                'CK' => 'Cook Islands',
                'CR' => 'Costa Rica',
                'CI' => 'Côte d\'Ivoire',
                'HR' => 'Croatia',
                'CU' => 'Cuba',
                'CW' => 'Curaçao',
                'CY' => 'Cyprus',
                'CZ' => 'Czech Republic',
                'DK' => 'Denmark',
                'DJ' => 'Djibouti',
                'DM' => 'Dominica',
                'DO' => 'Dominican Republic',
                'EC' => 'Ecuador',
                'EG' => 'Egypt',
                'SV' => 'El Salvador',
                'GQ' => 'Equatorial Guinea',
                'ER' => 'Eritrea',
                'EE' => 'Estonia',
                'ET' => 'Ethiopia',
                'FK' => 'Falkland Islands (Malvinas)',
                'FO' => 'Faroe Islands',
                'FJ' => 'Fiji',
                'FI' => 'Finland',
                'FR' => 'France',
                'GF' => 'French Guiana',
                'PF' => 'French Polynesia',
                'TF' => 'French Southern Territories',
                'GA' => 'Gabon',
                'GM' => 'Gambia',
                'GE' => 'Georgia',
                'DE' => 'Germany',
                'GH' => 'Ghana',
                'GI' => 'Gibraltar',
                'GR' => 'Greece',
                'GL' => 'Greenland',
                'GD' => 'Grenada',
                'GP' => 'Guadeloupe',
                'GU' => 'Guam',
                'GT' => 'Guatemala',
                'GG' => 'Guernsey',
                'GN' => 'Guinea',
                'GW' => 'Guinea-Bissau',
                'GY' => 'Guyana',
                'HT' => 'Haiti',
                'HM' => 'Heard Island and McDonald Islands',
                'VA' => 'Holy See (Vatican City State)',
                'HN' => 'Honduras',
                'HK' => 'Hong Kong',
                'HU' => 'Hungary',
                'IS' => 'Iceland',
                'IN' => 'India',
                'ID' => 'Indonesia',
                'IR' => 'Iran, Islamic Republic of',
                'IQ' => 'Iraq',
                'IE' => 'Ireland',
                'IM' => 'Isle of Man',
                'IL' => 'Israel',
                'IT' => 'Italy',
                'JM' => 'Jamaica',
                'JP' => 'Japan',
                'JE' => 'Jersey',
                'JO' => 'Jordan',
                'KZ' => 'Kazakhstan',
                'KE' => 'Kenya',
                'KI' => 'Kiribati',
                'KP' => 'Korea, Democratic People\'s Republic of',
                'KR' => 'Korea, Republic of',
                'KW' => 'Kuwait',
                'KG' => 'Kyrgyzstan',
                'LA' => 'Lao People\'s Democratic Republic',
                'LV' => 'Latvia',
                'LB' => 'Lebanon',
                'LS' => 'Lesotho',
                'LR' => 'Liberia',
                'LY' => 'Libya',
                'LI' => 'Liechtenstein',
                'LT' => 'Lithuania',
                'LU' => 'Luxembourg',
                'MO' => 'Macao',
                'MK' => 'Macedonia, The Former Yugoslav Republic of',
                'MG' => 'Madagascar',
                'MW' => 'Malawi',
                'MY' => 'Malaysia',
                'MV' => 'Maldives',
                'ML' => 'Mali',
                'MT' => 'Malta',
                'MH' => 'Marshall Islands',
                'MQ' => 'Martinique',
                'MR' => 'Mauritania',
                'MU' => 'Mauritius',
                'YT' => 'Mayotte',
                'MX' => 'Mexico',
                'FM' => 'Micronesia, Federated States of',
                'MD' => 'Moldova, Republic of',
                'MC' => 'Monaco',
                'MN' => 'Mongolia',
                'ME' => 'Montenegro',
                'MS' => 'Montserrat',
                'MA' => 'Morocco',
                'MZ' => 'Mozambique',
                'MM' => 'Myanmar',
                'NA' => 'Namibia',
                'NR' => 'Nauru',
                'NP' => 'Nepal',
                'NL' => 'Netherlands',
                'NC' => 'New Caledonia',
                'NZ' => 'New Zealand',
                'NI' => 'Nicaragua',
                'NE' => 'Niger',
                'NG' => 'Nigeria',
                'NU' => 'Niue',
                'NF' => 'Norfolk Island',
                'MP' => 'Northern Mariana Islands',
                'NO' => 'Norway',
                'OM' => 'Oman',
                'PK' => 'Pakistan',
                'PW' => 'Palau',
                'PS' => 'Palestine, State of',
                'PA' => 'Panama',
                'PG' => 'Papua New Guinea',
                'PY' => 'Paraguay',
                'PE' => 'Peru',
                'PH' => 'Philippines',
                'PN' => 'Pitcairn',
                'PL' => 'Poland',
                'PT' => 'Portugal',
                'PR' => 'Puerto Rico',
                'QA' => 'Qatar',
                'RE' => 'Réunion',
                'RO' => 'Romania',
                'RU' => 'Russian Federation',
                'RW' => 'Rwanda',
                'BL' => 'Saint Barthélemy',
                'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
                'KN' => 'Saint Kitts and Nevis',
                'LC' => 'Saint Lucia',
                'MF' => 'Saint Martin (French part)',
                'PM' => 'Saint Pierre and Miquelon',
                'VC' => 'Saint Vincent and the Grenadines',
                'WS' => 'Samoa',
                'SM' => 'San Marino',
                'ST' => 'Sao Tome and Principe',
                'SA' => 'Saudi Arabia',
                'SN' => 'Senegal',
                'RS' => 'Serbia',
                'SC' => 'Seychelles',
                'SL' => 'Sierra Leone',
                'SG' => 'Singapore',
                'SX' => 'Sint Maarten (Dutch part)',
                'SK' => 'Slovakia',
                'SI' => 'Slovenia',
                'SB' => 'Solomon Islands',
                'SO' => 'Somalia',
                'ZA' => 'South Africa',
                'GS' => 'South Georgia and the South Sandwich Islands',
                'SS' => 'South Sudan',
                'ES' => 'Spain',
                'LK' => 'Sri Lanka',
                'SD' => 'Sudan',
                'SR' => 'Suriname',
                'SJ' => 'Svalbard and Jan Mayen',
                'SZ' => 'Swaziland',
                'SE' => 'Sweden',
                'CH' => 'Switzerland',
                'SY' => 'Syrian Arab Republic',
                'TW' => 'Taiwan, Province of China',
                'TJ' => 'Tajikistan',
                'TZ' => 'Tanzania, United Republic of',
                'TH' => 'Thailand',
                'TL' => 'Timor-Leste',
                'TG' => 'Togo',
                'TK' => 'Tokelau',
                'TO' => 'Tonga',
                'TT' => 'Trinidad and Tobago',
                'TN' => 'Tunisia',
                'TR' => 'Turkey',
                'TM' => 'Turkmenistan',
                'TC' => 'Turks and Caicos Islands',
                'TV' => 'Tuvalu',
                'UG' => 'Uganda',
                'UA' => 'Ukraine',
                'AE' => 'United Arab Emirates',
                'GB' => 'United Kingdom',
                'US' => 'United States',
                'UM' => 'United States Minor Outlying Islands',
                'UY' => 'Uruguay',
                'UZ' => 'Uzbekistan',
                'VU' => 'Vanuatu',
                'VE' => 'Venezuela, Bolivarian Republic of',
                'VN' => 'Viet Nam',
                'VG' => 'Virgin Islands, British',
                'VI' => 'Virgin Islands, U.S.',
                'WF' => 'Wallis and Futuna',
                'EH' => 'Western Sahara',
                'YE' => 'Yemen',
                'ZM' => 'Zambia',
                'ZW' => 'Zimbabwe'
            );
       }
       
      
       
   }
   
   public static function get_countries( ){
       
       return self::$countries;
   }
}

bp_custom_fields_manager()->register_field( new BP_Xprofile_Country_Field() );