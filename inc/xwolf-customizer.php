<?php

/* 
 * Wrapper for customizer by xwolf
 * All options in customizer are generated by an array, which is somewhat
 * easer to edit.
 * 
 * Alle options, which may to be changed are set in the array and ordered
 * by their tabs. (Only) If needed, additional JS and CSS is loaded in backend. 
 * 
 * Author: xwolf
 * Author URL: https://xwolf.de
 * Licence: GPL
 *  
 * Proudly set under GPL. Feel free to use and change. Make the world
 * a better place by sharing ideas and code.
 * 
 * Customizer-API:
 * @link http://codex.wordpress.org/Theme_Customization_API
 */

/*
$xwolf_customizer_setoptions = array(
    'my-panel-1'   => array(
        'tabtitle'   => __('My Panel 1'),
        'fields' => array(
            'my-section-1'  => array(
               'type'    => 'section',
               'title'   => __( 'Section 1 in Panel 1' ),     
            ),
            'my-option-name'=> array(
                 'type'    => 'select',
                 'title'   => __( 'Typ' ),
                 'label'   => __( 'Select something' ),
                 'liste'   => array(
                                 0 => __('Element 1'), 
                                 1 => __('Element 2'),  
                                 2 => __('Element 3') ,
                                 3 => __('Element 4') 
                     ),
                 'default' => 'some value',
                 'parent'  => 'my-section-1'

            ),  
        )   
    )
);
*/
$xwolf_customizer_setoptions = $pirate_rogue_options;
        
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    // needed for options, that may be disabled, if a defined plugin is active
    

add_action( 'customize_register', 'xwolf_customizer_settings' );
function xwolf_customizer_settings( $wp_customize ) {
    global $xwolf_customizer_setoptions;
	// list of options, that may be changed
       
    $wp_customize->get_setting( 'blogname' )->transport		= 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport	= 'postMessage';	
    $num = 0;
       
    $definedtypes = array(
	"text", "checkbox", "radio", "select", "textarea", "dropdown-pages", "email", "url", "number", "hidden", "date",
	    // defaults
	"bool", "html", "image", "multiselect", "range", "category", "tag", "toggle", "toggle-switch", "colorlist-radio"
	    // special settings
    );
  
    
    foreach($xwolf_customizer_setoptions as $tab => $value) {        
	$tabtitel = __($value['tabtitle'], 'pirate-rogue');
	
	$desc = '';
	$capability = 'edit_theme_options';
	if (isset($value['capability'])) 
	    $capability = $value['capability'];
	if (isset($value['desc']))
	    $desc = __($value['desc'], 'pirate-rogue');
	
	$num = $num +1;
	$wp_customize->add_panel( $tab, array(
		'priority'	=> $num,
	        'capability'	=> $capability,
		'title'		=> $tabtitel,
		'description'	=> $desc,
	) );
	if (isset($xwolf_customizer_setoptions[$tab]['fields'])) {
	    
	    $nosectionentries = array();	  
	    $sectionprio = 0;
	    foreach($xwolf_customizer_setoptions[$tab]['fields'] as $field => $value) {  
		$sectionprio = $sectionprio +1; 
		if ($value['type'] == 'section') {
		    // Definition section
		    $desc = '';
		    $title = '';
		    $capability = '';
		    if (isset($value['capability'])) 
			$capability = $value['capability'];
	
		    $thisprio = $sectionprio;
		    if (isset($value['priority'])) 
			$thisprio = $value['priority'];
		    if (isset($value['title']))
			$title = __($value['title'], 'pirate-rogue');
		    if (isset($value['desc']))
			$desc = __($value['desc'], 'pirate-rogue');

		    
		    $sectionid = esc_html($field);
		    
		    $wp_customize->add_section( $sectionid , array(
			'title'		=> $title,
			'description'	=> $desc,
			'panel' 	=> $tab,
			'capability'	=> $capability,
			'priority'	=> $thisprio,
		    ) ); 
		    
		}
	    }
	    $sectionprio = $sectionprio +1; 
	    $sectionid = $tab."-elsesection";
	    $wp_customize->add_section( $sectionid , array(
			'title'		=> __('Miscellaneous'),
			'panel' 	=> $tab,
			'priority'	=> $sectionprio,
		    ) ); 
	    // Add a section for all options, that were not defined within a section
            // (Mostly forgotten)
            
	    foreach($xwolf_customizer_setoptions[$tab]['fields'] as $field => $value) {   
		if ($value['type'] != 'section') {
		    if (isset($value['parent'])) {
			$section = $value['parent'];
		    } else {
			$section =  $tab."-elsesection";
		    }
		    
		    $default = $title = $desc = $label = $type = '';
                    $notifplugin = $ifplugin = $ifclassexists = $iffunctionexists = '';
		    $optionid = esc_html($field); 
		    		    
		    if (isset($value['title']))
			$title = __($value['title'], 'pirate-rogue');
		    if (isset($value['desc']))
			$desc = __($value['desc'], 'pirate-rogue');
		    if (isset($value['label']))
			$label = __($value['label'], 'pirate-rogue');
		    if (isset($value['notifplugin']))
			$notifplugin = $value['notifplugin'];
                    if (isset($value['ifplugin']))
			$ifplugin = $value['ifplugin'];
                    if (isset($value['ifclass']))
			$ifclassexists = $value['ifclass'];
                    if (isset($value['iffunction']))
			$iffunctionexists = $value['iffunction'];
		    if (isset($value['default'])) {
			$default = $value['default'];   
		    }	
		   
		    
		    $type = $value['type'];
		    if (!in_array($type, $definedtypes)) {
			
                        $default = "UNDEFINED TYP";
                        $label = $label . " ( UNDEFINED TYP: $type ) ";
                        $type = 'text';
		    }
                    $plugin_break = false;
                    
		    if ($notifplugin) {
			if ( is_plugin_active( $notifplugin ) ) {
			    $plugin_break = true;
			}
		    } 
                    if ($ifplugin) {
                        if ( !is_plugin_active( $ifplugin ) ) {
			    $plugin_break = true;
			}                        
                    }
                    if ($ifclassexists) {
                        if (!class_exists($ifclassexists)) {
			    $plugin_break = true;
			}
                    }
                    if ($iffunctionexists) {
                        if (!function_exists($iffunctionexists)) {
			    $plugin_break = true;
			}
                    }
		    if ($plugin_break==false) {			
			if ($type == 'bool') {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
				'sanitize_callback' => 'xwolf_sanitize_customizer_bool'
			    ) );
			     $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'checkbox',
				    
			    ) );
			} elseif  (($type == 'toggle-switch') || ($type == 'toggle')) {
			    $wp_customize->add_setting( $optionid, array(
					    'default' => 0,
					    'transport' => 'refresh',
					    'sanitize_callback' => 'xwolf_sanitize_customizer_toggle_switch'
				    )
			    );
			    $wp_customize->add_control( new WP_Customize_Control_Toggle_Switch( $wp_customize, $optionid, array(
					    'label' => $title,
					    'section' => $section,
					    'description'	=> $label,
				    )
			    ) );			    
			} elseif ($type == 'range') {
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
				'sanitize_callback' => 'xwolf_sanitize_customizer_number'
			    ) );
			    
			    $min = 0;
			    $max = $step = 1;
			    $suffix = '';
			    
			    if (isset($value['min'])) {
				$min = $value['min'];
			    }
			     if (isset($value['max'])) {
				$max = $value['max'];
			    }
			    if (isset($value['step'])) {
				$step = $value['step'];
			    }
			    if (isset($value['suffix'])) {
				$suffix = $value['suffix'];
			    }
			   
			    $wp_customize->add_control( new WP_Customize_Range_Value_Control( $wp_customize, $optionid, array(
				    'type'     => 'range-value',
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'input_attrs' => array(
					    'min'    => $min,
					    'max'    => $max,
					    'step'   => $step,
					    'suffix' => $suffix, //optional suffix
				    ),
			    ) ) );
			} elseif ($type == 'category')  {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			     $wp_customize->add_control( new WP_Customize_Category_Control( $wp_customize, $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'category',
				    
			    ) ) );
                        } elseif ($type == 'tag')  {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			     $wp_customize->add_control( new WP_Customize_Tag_Control( $wp_customize, $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'tag',
				    
			    ) ) );     
			} elseif ($type == 'select')  {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			     $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'select',
				    'choices'		=> array_map(function($item) { return __($item, 'pirate-rogue'); }, $value['liste'])
				    
			    ) );
			} elseif ($type == 'multiselect')  {
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			    $wp_customize->add_control(  new WP_Customize_Control_Multiple_Select( $wp_customize, $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'multiple-select',
				    'choices'		=> array_map(function($item) { return __($item, 'pirate-rogue'); }, $value['liste'])
				    
			    ) ) );
                        } elseif ($type == 'colorlist-radio')  {
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			    $wp_customize->add_control(  new WP_Customize_Colorlist_Radio( $wp_customize, $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'colorlist-radio',
				    'choices'		=> array_map(function($item) { return __($item, 'pirate-rogue'); }, $value['liste'])

			    ) ) );    

			} elseif ($type == 'html') {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			     $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'textarea',
				    
			    ) );     
			} elseif ($type == 'image') {    
			  
			    $width = 0;
			    $flexwidth = false;
			    $height = 0;
			    $flexheight = false;
			    
			    if (isset($value['width'])) {
				$width = $value['width'];
			    }
			     if (isset($value['height'])) {
				$height = $value['height'];
			    }
			    if (isset($value['maxwidth'])) {
				$width = $value['maxwidth'];
				$flexwidth = true;
			    }
			    if (isset($value['maxheight'])) {
				$height = $value['maxheight'];
				$flexheight = true;
			    }
			    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			   
			    
			    $wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, $optionid, array(
				'section'     => $section,
				'label'       => $title,
				'description' => $label,
				'flex_width'  => $flexwidth,
				'flex_height' => $flexheight,
				'width'       => $width,
				'height'      => $height,
			    ) ) );	
			     
			} elseif ($type == 'number') {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
				'sanitize_callback' => 'xwolf_sanitize_customizer_number'	
			    ) );
			     $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'number',
				    
			    ) );          
			} elseif ($type == 'text') {    
			    $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
				'sanitize_callback' => 'sanitize_text_field'	
			    ) );
			     $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> 'text',
				    
			    ) );     
			    
			    
			} else {
			     $wp_customize->add_setting( $optionid , array(
				'default'     => $default,
				'transport'   => 'refresh',
			    ) );
			    $wp_customize->add_control( $optionid, array(
				    'label'             => $title,
				    'description'	=> $label,
				    'section'		=> $section,
				    'settings'		=> $optionid,
				    'type' 		=> $type,		    
			    ) );
			}

		    }
		}
	    }
	    
	    
	}
	
	
	
	
    }
    
    
    
}

/*--------------------------------------------------------------------*/
/* Multiple select customize control class.
/*--------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Control_Multiple_Select extends WP_Customize_Control {
	// The type of customize control being rendered.
	public $type = 'multiple-select';

	//Displays the multiple select on the customize screen.
	public function render_content() {
	    if ( empty( $this->choices ) )
		return;
	    ?>
		<label>
		    <?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		    <?php endif;
		    if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		    <?php endif; ?>
		    <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;">
			<?php
			    foreach ( $this->choices as $value => $label ) {
				$selected = ( in_array( $value, $this->value() ) ) ? selected( 1, 1, false ) : '';
				echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
			    }
			?>
		    </select>
		</label>
	<?php }
    }
}
/*--------------------------------------------------------------------*/
/* Toogle switch
 * adapted from https://github.com/maddisondesigns/customizer-custom-controls
/*--------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Control_Toggle_Switch extends WP_Customize_Control {
	// The type of control being rendered
	public $type = 'toogle-switch';


	public function render_content(){
	?>
		<div class="toggle-switch-control">
			<div class="toggle-switch">
				<input type="checkbox" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" class="toggle-switch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
				<label class="toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
					<span class="toggle-switch-inner"></span>
					<span class="toggle-switch-switch"></span>
				</label>
			</div>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if( !empty( $this->description ) ) { ?>
				<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php } ?>
		</div>
	<?php
	}
    }
}
/*-----------------------------------------------------------------------------------*/
/* Add Custom Customizer Controls - Category Dropdown
/*-----------------------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Colorlist_Radio extends WP_Customize_Control {
        // The type of customize control being rendered.
        public $type = 'colorlist-radio';

        // Displays the multiple select on the customize screen.
        public function render_content() {
            if ( empty( $this->choices ) )
                return;
           ?>
                <?php if ( ! empty( $this->label ) ) : ?>
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php endif;
                if ( ! empty( $this->description ) ) : ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php endif; ?>
                    <div class="colorlist-radio-group">
                        
                        
                        
                    <?php foreach ( $this->choices as $name => $value ) : ?>                        
                        <label for="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>_<?php echo esc_attr( $name ); ?>" <?php if ($value=="#000") { echo 'style="color: white;"'; } ?>>
                            <input name="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>" id="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>_<?php echo esc_attr( $name ); ?>" type="radio" value="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $name ); ?> >
                                <span class="colorbox" style="background-color: <?php echo esc_attr( $value ); ?>">&nbsp;</span>
                            </input>
                            <span class="screen-reader-text"><?php echo ucfirst(esc_attr( $name) ); ?></span>
                        </label>
                    <?php endforeach; ?>
                        
                        <label for="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>_reset">
                            <input name="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>" id="_customize-colorlist-radio_<?php echo esc_attr( $this->id ); ?>_reset" type="radio" value="" <?php $this->link(); checked( $this->value(), "" ); ?> >
                                <span class="reset"><?php echo __("Reset",'pirate-rogue'); ?></span> 
                            </input>
                        </label>
                    </div>
	<?php }
        
    }
}

/*-----------------------------------------------------------------------------------*/
/* Add Custom Customizer Controls - Category Dropdown
/*-----------------------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Category_Control extends WP_Customize_Control {

        public function render_content() {
	    ?>

		    <label>
		    <?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		    <?php endif;
		    if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		    <?php endif;


			    $dropdown = wp_dropdown_categories(
				array(
				    'name'              => '_customize-dropdown-categories-' . $this->id,
				    'echo'              => 0,
				    'show_option_none'  => __( '&mdash; Select &mdash;' ),
				    'option_none_value' => '',
				    'selected'          => $this->value(),
				)
			    );

			    // Hackily add in the data link parameter.
			    $dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );
			    echo $dropdown;

		    ?>	
		    </label>

	    <?php

        }
    }
}
/*-----------------------------------------------------------------------------------*/
/* Add Custom Customizer Controls - Range Value Control
 * adapted from https://github.com/soderlind/class-customizer-range-value-control 
/*-----------------------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Range_Value_Control extends WP_Customize_Control {
	public $type = 'range-value';

	public function enqueue() {
		wp_enqueue_script( 'xwolf-customizer', get_template_directory_uri() . '/js/xwolf-customizer.js', array( 'jquery' ), rand(), true );
	}
	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<div class="range-slider"  style="width:100%; display:flex;flex-direction: row;justify-content: flex-start;">
				<span  style="width:100%; flex: 1 0 0; vertical-align: middle;"><input class="range-slider__range" type="range" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->input_attrs(); $this->link(); ?>>
				<span class="range-slider__value">0</span></span>
			</div>
			<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		</label>
		<?php
	}
    }
}
/*-----------------------------------------------------------------------------------*/
/* Add Custom Customizer Controls - Tag Dropdown
/*-----------------------------------------------------------------------------------*/
if (class_exists('WP_Customize_Control')) {
    class WP_Customize_Tag_Control extends WP_Customize_Control {
        public function render_content() {
            $dropdown = wp_dropdown_categories(
                array(
                    'name'              => '_customize-dropdown-tags-' . $this->id,
                    'echo'              => 0,
                    'orderby'           => 'name',
                    'show_option_none'  => esc_html__( '&mdash; Select &mdash;'),
                    'option_none_value' => '',
                    'taxonomy'           => 'post_tag',
                    'selected'          => $this->value(),
                )
            );

            $dropdown = str_replace( '<select', '<select ' . $this->get_link(), $dropdown );

            printf(
                '<label class="customize-control-select"><span class="customize-control-title">%s</span> %s</label>',
                $this->label,
                $dropdown
            );
        }
    }
}
/*-----------------------------------------------------------------------------------*/
/* Sanitize Checkboxes.
/*-----------------------------------------------------------------------------------*/
function xwolf_sanitize_customizer_bool( $input ) {
	if ( 1 == $input ) {
		return true;
	} else {
		return false;
	}
}
function xwolf_sanitize_customizer_toggle_switch( $input ) {
	if ( true == $input ) {
		return true;
	} else {
		return false;
	}
}

function xwolf_sanitize_customizer_number( $number, $setting ) {
  $number = absint( $number );

  return ( $number ? $number : $setting->default );
}
/*--------------------------------------------------------------------*/
/* EOCustomizer
/*--------------------------------------------------------------------*/





