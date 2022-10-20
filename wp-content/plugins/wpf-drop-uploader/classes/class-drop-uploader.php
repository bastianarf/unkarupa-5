<?php
/**
 * Drop Uploader Field
 *
 */
class WPF_Drop_Uploader extends WPForms_Field {

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function init() {

        // Define field type information.
        $this->name  = esc_html__( 'Drop Uploader', 'wpf-drop-uploader' );
        $this->type  = 'drop-uploader';
        $this->icon  = 'fa-cloud-upload';
        $this->order = 18;

        // Define additional field properties.
        add_filter( 'wpforms_field_properties_text', array( $this, 'field_properties' ), 5, 3 );
    }

    /**
     * Define additional field properties.
     *
     * @since 1.4.5
     *
     * @param array $properties Field properties.
     * @param array $field      Field settings.
     * @param array $form_data  Form data.
     *
     * @return array
     */
    public function field_properties( $properties, $field, $form_data ) {

        // Input primary: Detect custom input mask.
        if ( ! empty( $field['input_mask'] ) ) {

            // Add class that will trigger custom mask.
            $properties['inputs']['primary']['class'][] = 'wpforms-masked-input';

            if ( false !== strpos( $field['input_mask'], 'alias:' ) ) {
                $mask = str_replace( 'alias:', '', $field['input_mask'] );
                $properties['inputs']['primary']['data']['inputmask-alias'] = $mask;
            } elseif ( false !== strpos( $field['input_mask'], 'regex:' ) ) {
                $mask = str_replace( 'regex:', '', $field['input_mask'] );
                $properties['inputs']['primary']['data']['inputmask-regex'] = $mask;
            } elseif ( false !== strpos( $field['input_mask'], 'date:' ) ) {
                $mask = str_replace( 'date:', '', $field['input_mask'] );
                $properties['inputs']['primary']['data']['inputmask-alias']       = 'datetime';
                $properties['inputs']['primary']['data']['inputmask-inputformat'] = $mask;

            } else {
                $properties['inputs']['primary']['data']['inputmask-mask'] = $field['input_mask'];
            }
        }

        return $properties;
    }

    /**
     * Field options panel inside the builder.
     *
     * @since 1.0.0
     *
     * @param array $field Field settings.
     */
    public function field_options( $field ) {

        // -------------------------------------------------------------------//
        // Basic field options.
        // -------------------------------------------------------------------//

        // Options open markup.
        $this->field_option( 'basic-options', $field, array(
            'markup' => 'open',
        ) );

        // Label.
        $this->field_option( 'label', $field );

        // Required toggle.
        $this->field_option( 'required', $field );

        // Max file size
        $this->field_option( 'max_file_size', $field );

        // Files amount
        $this->field_option( 'amount', $field );

        // File types
        $this->field_option( 'types', $field );

        // Options close markup.
        $this->field_option( 'basic-options', $field, array(
            'markup' => 'close',
        ) );

        // --------------------------------------------------------------------//
        // Advanced field options.
        // --------------------------------------------------------------------//

        // Options open markup.
        $this->field_option( 'advanced-options', $field, array(
            'markup' => 'open',
        ) );

        // Hide label.
        $this->field_option( 'label_hide', $field );

        // Options close markup.
        $this->field_option( 'advanced-options', $field, array(
            'markup' => 'close',
        ) );
    }

    /**
     * Helper function to create common field options that are used frequently.
     *
     * @since 1.0.0
     *
     * @param string $option
     * @param array $field
     * @param array $args
     * @param boolean $echo
     *
     * @return mixed echo or return string
     */
    public function field_option( $option, $field, $args = array(), $echo = true ) {

        switch ( $option ) {

            // --------------------------------------------------------------//
            // Basic Fields.
            // --------------------------------------------------------------//

            // Basic Options markup. ------------------------------------------//

            case 'basic-options':
                $markup = ! empty( $args['markup'] ) ? $args['markup'] : 'open';
                $class  = ! empty( $args['class'] ) ? esc_html( $args['class'] ) : '';
                if ( 'open' === $markup ) {
                    $output   = sprintf( '<div class="wpforms-field-option-group wpforms-field-option-group-basic" id="wpforms-field-option-basic-%d">', $field['id'] );
                    $output  .= sprintf( '<a href="#" class="wpforms-field-option-group-toggle">%s <span>(ID #%d)</span> <i class="fa fa-angle-down"></i></a>', $this->name, $field['id'] );
                    $output  .= sprintf( '<div class="wpforms-field-option-group-inner %s">', $class );
                } else {
                    $output   = '</div></div>';
                }
                break;

            // Field Label. ---------------------------------------------------//

            case 'label':
                $value   = ! empty( $field['label'] ) ? esc_attr( $field['label'] ) : '';
                $tooltip = esc_html__( 'Enter text for the form field label. Field labels are recommended and can be hidden in the Advanced Settings.', 'wpforms' );
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'label', 'value' => esc_html__( 'Label', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'label', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'label', 'content' => $output ), false );
                break;

            // Field Description. ---------------------------------------------//

            case 'description':
                $value   = ! empty( $field['description'] ) ? esc_attr( $field['description'] ) : '';
                $tooltip = esc_html__( 'Enter text for the form field description.', 'wpforms' );
                $output  = $this->field_element( 'label',    $field, array( 'slug' => 'description', 'value' => esc_html__( 'Description', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'textarea', $field, array( 'slug' => 'description', 'value' => $value ), false );
                $output  = $this->field_element( 'row',      $field, array( 'slug' => 'description', 'content' => $output ), false );
                break;

            // Field Required toggle. -----------------------------------------//

            case 'required':
                $default = ! empty( $args['default'] ) ? $args['default'] : '0';
                $value   = isset( $field['required'] ) ? $field['required'] : $default;
                $tooltip = esc_html__( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.', 'wpforms' );
                $output  = $this->field_element( 'checkbox', $field, array( 'slug' => 'required', 'value' => $value, 'desc' => esc_html__( 'Required', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output  = $this->field_element( 'row',      $field, array( 'slug' => 'required', 'content' => $output ), false );
                break;

            // Field Meta (field type and ID). --------------------------------//

            case 'meta':
                $output  = sprintf( '<label>%s</label>', 'Type' );
                $output .= sprintf( '<p class="meta">%s <span class="id">(ID #%d)</span></p>', $this->name, $field['id'] );
                $output  = $this->field_element( 'row', $field, array( 'slug' => 'meta', 'content' => $output ), false );
                break;

            // Code Block. ----------------------------------------------------//

            case 'code':
                $value   = ! empty( $field['code'] ) ? esc_attr( $field['code'] ) : '';
                $tooltip = esc_html__( 'Enter code for the form field.', 'wpforms' );
                $output  = $this->field_element( 'label',    $field, array( 'slug' => 'code', 'value' => esc_html__( 'Code', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'textarea', $field, array( 'slug' => 'code', 'value' => $value ), false );
                $output  = $this->field_element( 'row',      $field, array( 'slug' => 'code', 'content' => $output ), false );
                break;

            // Choices. ------------------------------------------------------//

            case 'choices':
                $values  = ! empty( $field['choices'] ) ? $field['choices'] : $this->defaults;
                $class   = array();

                if ( ! empty( $field['show_values'] ) ) {
                    $class[] = 'show-values';
                }
                if ( ! empty( $field['dynamic_choices'] ) ) {
                    $class[] = 'wpforms-hidden';
                }
                if ( ! empty( $field['choices_images'] ) ) {
                    $class[] = 'show-images';
                }

                // Field label.
                $lbl = $this->field_element(
                    'label',
                    $field,
                    array(
                        'slug'          => 'choices',
                        'value'         => esc_html__( 'Choices', 'wpforms' ),
                        'tooltip'       => esc_html__( 'Add choices for the form field.', 'wpforms' ),
                        'after_tooltip' => '<a href="#" class="toggle-bulk-add-display"><i class="fa fa-download"></i> <span>' . esc_html__( 'Bulk Add', 'wpforms' ) . '</span></a>',
                    ),
                    false
                );

                // Field contents.
                $fld = sprintf( '<ul data-next-id="%s" class="choices-list %s" data-field-id="%d" data-field-type="%s">',
                    max( array_keys( $values ) ) + 1,
                    wpforms_sanitize_classes( $class, true ),
                    $field['id'],
                    $this->type
                );
                foreach ( $values as $key => $value ) {
                    $default   = ! empty( $value['default'] ) ? $value['default'] : '' ;
                    $base      =  sprintf( 'fields[%s][choices][%s]', $field['id'], $key );
                    $image     = ! empty( $value['image'] ) ? $value['image'] : '';
                    $image_btn = '';

                    $fld .= '<li data-key="' . absint( $key ) . '">';
                    $fld .= sprintf( '<input type="%s" name="%s[default]" class="default" value="1" %s>',
                        'checkbox' === $this->type ? 'checkbox' : 'radio',
                        $base,
                        checked( '1', $default, false )
                    );
                    $fld .= '<span class="move"><i class="fa fa-bars"></i></span>';
                    $fld .= sprintf( '<input type="text" name="%s[label]" value="%s" class="label">',
                        $base,
                        esc_attr( $value['label'] )
                    );
                    $fld .= '<a class="add" href="#"><i class="fa fa-plus-circle"></i></a><a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>';
                    $fld .= sprintf( '<input type="text" name="%s[value]" value="%s" class="value">',
                        $base,
                        esc_attr( $value['value'] )
                    );
                    $fld .= '<div class="wpforms-image-upload">';
                    $fld .= '<div class="preview">';
                    if ( ! empty( $image ) ) {
                        $fld .= sprintf( '<a href="#" title="%s" class="wpforms-image-upload-remove"><img src="%s"></a>',
                            esc_attr__( 'Remove Image', 'wpforms' ),
                            esc_url_raw( $image )
                        );
                        $image_btn = ' style="display:none;"';
                    }
                    $fld .= '</div>';
                    $fld .= sprintf( '<button class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey wpforms-btn-block wpforms-image-upload-add" data-after-upload="hide"%s>%s</button>',
                        $image_btn,
                        esc_html__( 'Upload Image', 'wpforms' )
                    );
                    $fld .= sprintf( '<input type="hidden" name="%s[image]" value="%s" class="source">',
                        $base,
                        esc_url_raw( $image )
                    );
                    $fld .= '</div>';
                    $fld .= '</li>';
                }
                $fld .= '</ul>';

                // Field note: dynamic status.
                $source  = '';
                $type    = '';
                $dynamic = ! empty( $field['dynamic_choices'] ) ? esc_html( $field['dynamic_choices'] ) : '';

                if ( 'post_type' === $dynamic && ! empty( $field[ 'dynamic_' . $dynamic ] ) ) {
                    $type   = esc_html__( 'post type', 'wpforms' );
                    $pt     = get_post_type_object( $field[ 'dynamic_' . $dynamic ] );
                    $source = $pt->labels->name;
                } elseif ( 'taxonomy' === $dynamic && ! empty( $field[ 'dynamic_' . $dynamic ] ) ) {
                    $type   = esc_html__( 'taxonomy', 'wpforms' );
                    $tax    = get_taxonomy( $field[ 'dynamic_' . $dynamic ] );
                    $source = $tax->labels->name;
                }

                $note = sprintf( '<div class="wpforms-alert-warning wpforms-alert-small wpforms-alert %s">',
                    empty( $dynamic ) && ! empty( $field[ 'dynamic_' . $dynamic ] ) ? '' : 'wpforms-hidden'
                );
                /* translators: %1$s - source name; %2$s - type name. */
                $note .= sprintf(
                    esc_html__(
                        'Choices are dynamically populated from the %1$s %2$s.',
                        'wpforms'
                    ),
                    '<span class="dynamic-name">' . $source . '</span>',
                    '<span class="dynamic-type">' . $type . '</span>'
                );
                $note .= '</div>';

                // Final field output.
                $output = $this->field_element(
                    'row',
                    $field,
                    array(
                        'slug'    => 'choices',
                        'content' => $lbl . $fld . $note,
                    ),
                    false
                );
                break;

            // Choices for payments. -----------------------------------------//

            case 'choices_payments':
                $values = ! empty( $field['choices'] ) ? $field['choices'] : $this->defaults;
                $class  = array();

                if ( ! empty( $field['choices_images'] ) ) {
                    $class[] = 'show-images';
                }

                // Field label.
                $lbl = $this->field_element(
                    'label',
                    $field,
                    array(
                        'slug'    => 'choices',
                        'value'   => esc_html__( 'Items', 'wpforms' ),
                        'tooltip' =>  esc_html__( 'Add choices for the form field.', 'wpforms' ),
                    ),
                    false
                );

                // Field contents.
                $fld = sprintf( '<ul data-next-id="%s" class="choices-list %s" data-field-id="%d" data-field-type="%s">',
                    max( array_keys( $values ) ) + 1,
                    wpforms_sanitize_classes( $class, true ),
                    $field['id'],
                    $this->type
                );
                foreach ( $values as $key => $value ) {
                    $default   = ! empty( $value['default'] ) ? $value['default'] : '' ;
                    $base      =  sprintf( 'fields[%s][choices][%s]', $field['id'], $key );
                    $image     = ! empty( $value['image'] ) ? $value['image'] : '';
                    $image_btn = '';

                    $fld .= '<li data-key="' . absint( $key ) . '">';
                    $fld .= sprintf( '<input type="radio" name="%s[default]" class="default" value="1" %s>',
                        $base,
                        checked( '1', $default, false )
                    );
                    $fld .= '<span class="move"><i class="fa fa-bars"></i></span>';
                    $fld .= sprintf( '<input type="text" name="%s[label]" value="%s" class="label">',
                        $base,
                        esc_attr( $value['label'] )
                    );
                    $fld .= sprintf( '<input type="text" name="%s[value]" value="%s" class="value value wpforms-money-input" placeholder="%s">',
                        $base,
                        esc_attr( $value['value'] ),
                        wpforms_format_amount( 0 )
                    );
                    $fld .= '<a class="add" href="#"><i class="fa fa-plus-circle"></i></a><a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>';
                    $fld .= '<div class="wpforms-image-upload">';
                    $fld .= '<div class="preview">';
                    if ( ! empty( $image ) ) {
                        $fld .= sprintf( '<a href="#" title="%s" class="wpforms-image-upload-remove"><img src="%s"></a>',
                            esc_attr__( 'Remove Image', 'wpforms' ),
                            esc_url_raw( $image )
                        );
                        $image_btn = ' style="display:none;"';
                    }
                    $fld .= '</div>';
                    $fld .= sprintf( '<button class="wpforms-btn wpforms-btn-md wpforms-btn-light-grey wpforms-btn-block wpforms-image-upload-add" data-after-upload="hide"%s>%s</button>',
                        $image_btn,
                        esc_html__( 'Upload Image', 'wpforms' )
                    );
                    $fld .= sprintf( '<input type="hidden" name="%s[image]" value="%s" class="source">',
                        $base,
                        esc_url_raw( $image )
                    );
                    $fld .= '</div>';
                    $fld .= '</li>';
                }
                $fld .= '</ul>';

                // Final field output.
                $output = $this->field_element(
                    'row',
                    $field,
                    array(
                        'slug'    => 'choices',
                        'content' => $lbl . $fld,
                    ),
                    false
                );
                break;

            // Choices Images ------------------------------------------------//

            case 'choices_images':

                // Field note: Image tips.
                $note = sprintf( '<div class="wpforms-alert-warning wpforms-alert-small wpforms-alert %s">',
                    ! empty( $field['choices_images'] ) ? '' : 'wpforms-hidden'
                );
                $note .= esc_html__( 'Images are not cropped or resized. For best results, they should be the same size and 250x250 pixels or smaller.', 'wpforms' );
                $note .= '</div>';

                // Field contents.
                $fld = $this->field_element(
                    'checkbox',
                    $field,
                    array(
                        'slug'    => 'choices_images',
                        'value'   => isset( $field['choices_images'] ) ? '1' : '0',
                        'desc'    => esc_html__( 'Use image choices', 'wpforms' ),
                        'tooltip' => esc_html__( 'Check this option to enable using images with the choices.', 'wpforms' ),
                    ),
                    false
                );

                // Final field output.
                $output = $this->field_element(
                    'row',
                    $field,
                    array(
                        'slug'    => 'choices_images',
                        'class'   => ! empty( $field['dynamic_choices'] ) ? 'wpforms-hidden' : '',
                        'content' => $note . $fld,
                    ),
                    false
                );
                break;

            // Choices Images Style ------------------------------------------//

            case 'choices_images_style':

                // Field label.
                $lbl = $this->field_element(
                    'label',
                    $field,
                    array(
                        'slug'    => 'choices_images_style',
                        'value'   => esc_html__( 'Image Choice Style', 'wpforms' ),
                        'tooltip' => esc_html__( 'Select the style for the image choices.', 'wpforms' ),
                    ),
                    false
                );

                // Field contents.
                $fld = $this->field_element(
                    'select',
                    $field,
                    array(
                        'slug'    => 'choices_images_style',
                        'value'   => ! empty( $field['choices_images_style'] ) ? esc_attr( $field['choices_images_style'] ) : 'modern',
                        'options' => array(
                            'modern'  => esc_html__( 'Modern', 'wpforms' ),
                            'classic' => esc_html__( 'Classic', 'wpforms' ),
                            'none'    => esc_html__( 'None', 'wpforms' ),
                        ),
                    ),
                    false
                );

                // Final field output.
                $output = $this->field_element(
                    'row',
                    $field,
                    array(
                        'slug'    => 'choices_images_style',
                        'content' => $lbl . $fld,
                        'class'   => ! empty( $field['choices_images'] ) ? '' : 'wpforms-hidden',
                    ),
                    false
                );
                break;

            // --------------------------------------------------------------//
            // Drop Uploader Fields.
            // --------------------------------------------------------------//

            // Max File size. ---------------------------------------------------//

            case 'max_file_size':
                $value   = ! empty( $field['max_file_size'] ) ? esc_attr( $field['max_file_size'] ) : '';
                $tooltip = esc_html__( 'Set maximum file size', 'wpf-drop-uploader' );
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'max_file_size', 'value' => esc_html__( 'Max File size', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'max_file_size', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'max_file_size', 'content' => $output ), false );
                break;

            case 'amount':
                $value   = ! empty( $field['amount'] ) ? esc_attr( $field['amount'] ) : '';
                $tooltip = esc_html__( 'Set maximum number of files, that can be uploaded', 'wpf-drop-uploader' );
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'amount', 'value' => esc_html__( 'Files Amount', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'amount', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'amount', 'content' => $output ), false );
                break;

            case 'types':
                $value   = ! empty( $field['types'] ) ? esc_attr( $field['types'] ) : '';
                $tooltip = esc_html__( 'Set what file types are allowed', 'wpf-drop-uploader' );
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'types', 'value' => esc_html__( 'File types', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'types', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'types', 'content' => $output ), false );
                break;

            // ---------------------------------------------------------------//
            // Advanced Fields.
            // ---------------------------------------------------------------//

            // Default value. -------------------------------------------------//

            case 'default_value':
                $value   = ! empty( $field['default_value'] ) ? esc_attr( $field['default_value'] ) : '';
                $tooltip = esc_html__( 'Enter text for the default form field value.', 'wpforms' );
                $toggle  = '<a href="#" class="toggle-smart-tag-display" data-type="other"><i class="fa fa-tags"></i> <span>' . esc_html__( 'Show Smart Tags', 'wpforms' ) . '</span></a>';
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'default_value', 'value' => esc_html__( 'Default Value', 'wpforms' ), 'tooltip' => $tooltip, 'after_tooltip' => $toggle ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'default_value', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'default_value', 'content' => $output ), false );
                break;

            // Size. ----------------------------------------------------------//

            case 'size':
                $value   = ! empty( $field['size'] ) ? esc_attr( $field['size'] ) : 'medium';
                $class   = ! empty( $args['class'] ) ? esc_html( $args['class'] ) : '';
                $tooltip = esc_html__( 'Select the default form field size.', 'wpforms' );
                $options = array(
                    'small'  => esc_html__( 'Small', 'wpforms' ),
                    'medium' => esc_html__( 'Medium', 'wpforms' ),
                    'large'  => esc_html__( 'Large', 'wpforms' ),
                );
                $output  = $this->field_element( 'label',  $field, array( 'slug' => 'size', 'value' => esc_html__( 'Field Size', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'select', $field, array( 'slug' => 'size', 'value' => $value, 'options' => $options ), false );
                $output  = $this->field_element( 'row',    $field, array( 'slug' => 'size', 'content' => $output, 'class' => $class ), false );
                break;

            // Advanced Options markup. ---------------------------------------//

            case 'advanced-options':
                $markup = ! empty( $args['markup'] ) ? $args['markup'] : 'open';
                if ( 'open' === $markup ) {
                    $override = apply_filters( 'wpforms_advanced_options_override', false );
                    $override = ! empty( $override ) ? 'style="display:' . $override . ';"' : '';
                    $output   = sprintf( '<div class="wpforms-field-option-group wpforms-field-option-group-advanced wpforms-hide" id="wpforms-field-option-advanced-%d" %s>', $field['id'], $override );
                    $output  .= sprintf( '<a href="#" class="wpforms-field-option-group-toggle">%s <i class="fa fa-angle-right"></i></a>', esc_html__( 'Advanced Options', 'wpforms' ) );
                    $output  .= '<div class="wpforms-field-option-group-inner">';
                } else {
                    $output   = '</div></div>';
                }
                break;

            // Placeholder. ---------------------------------------------------//

            case 'placeholder':
                $value   = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';
                $tooltip = esc_html__( 'Enter text for the form field placeholder.', 'wpforms' );
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'placeholder', 'value' => esc_html__( 'Placeholder Text', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'placeholder', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'placeholder', 'content' => $output ), false );
                break;

            // CSS classes. ---------------------------------------------------//

            case 'css':
                $toggle  = '';
                $value   = ! empty( $field['css'] ) ? esc_attr( $field['css'] ) : '';
                $tooltip = esc_html__( 'Enter CSS class names for the form field container. Class names should be separated with spaces.', 'wpforms' );
                if ( ! in_array( $field['type'], array( 'pagebreak' ), true ) ) {
                    $toggle  = '<a href="#" class="toggle-layout-selector-display"><i class="fa fa-th-large"></i> <span>' . esc_html__( 'Show Layouts', 'wpforms' ) . '</span></a>';
                }
                // Build output
                $output  = $this->field_element( 'label', $field, array( 'slug' => 'css', 'value' => esc_html__( 'CSS Classes', 'wpforms' ), 'tooltip' => $tooltip, 'after_tooltip' => $toggle ), false );
                $output .= $this->field_element( 'text',  $field, array( 'slug' => 'css', 'value' => $value ), false );
                $output  = $this->field_element( 'row',   $field, array( 'slug' => 'css', 'content' => $output ), false );
                break;

            // Hide Label. ----------------------------------------------------//

            case 'label_hide':
                $value   = isset( $field['label_hide'] ) ? $field['label_hide'] : '0';
                $tooltip = esc_html__( 'Check this option to hide the form field label.', 'wpforms' );
                // Build output
                $output  = $this->field_element( 'checkbox', $field, array( 'slug' => 'label_hide', 'value' => $value, 'desc' => esc_html__( 'Hide Label', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output  = $this->field_element( 'row',      $field, array( 'slug' => 'label_hide', 'content' => $output ), false );
                break;

            // Hide Sub-Labels. -----------------------------------------------//

            case 'sublabel_hide':
                $value   = isset( $field['sublabel_hide'] ) ? $field['sublabel_hide'] : '0';
                $tooltip = esc_html__( 'Check this option to hide the form field sub-label.', 'wpforms' );
                // Build output
                $output  = $this->field_element( 'checkbox', $field, array( 'slug' => 'sublabel_hide', 'value' => $value, 'desc' => esc_html__( 'Hide Sub-Labels', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output  = $this->field_element( 'row',      $field, array( 'slug' => 'sublabel_hide', 'content' => $output ), false );
                break;

            // Input Columns. -------------------------------------------------//

            case 'input_columns':
                $value   = ! empty( $field['input_columns'] ) ? esc_attr( $field['input_columns'] ) : '';
                $tooltip = esc_html__( 'Select the layout for displaying field choices.', 'wpforms' );
                $options = array(
                    ''       => esc_html__( 'One Column', 'wpforms' ),
                    '2'      => esc_html__( 'Two Columns', 'wpforms' ),
                    '3'      => esc_html__( 'Three Columns', 'wpforms' ),
                    'inline' => esc_html__( 'Inline', 'wpforms' ),
                );
                $output  = $this->field_element( 'label',  $field, array( 'slug' => 'input_columns', 'value' => esc_html__( 'Choice Layout', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'select', $field, array( 'slug' => 'input_columns', 'value' => $value, 'options' => $options ), false );
                $output  = $this->field_element( 'row',    $field, array( 'slug' => 'input_columns', 'content' => $output ), false );
                break;

            // Dynamic Choices. -----------------------------------------------//

            case 'dynamic_choices':
                $value   = ! empty( $field['dynamic_choices'] ) ? esc_attr( $field['dynamic_choices'] ) : '';
                $tooltip = esc_html__( 'Select auto-populate method to use.', 'wpforms' );
                $options = array(
                    ''          => esc_html__( 'Off', 'wpforms' ),
                    'post_type' => esc_html__( 'Post Type', 'wpforms' ),
                    'taxonomy'  => esc_html__( 'Taxonomy', 'wpforms' ),
                );
                $output  = $this->field_element( 'label',  $field, array( 'slug' => 'dynamic_choices', 'value' => esc_html__( 'Dynamic Choices', 'wpforms' ), 'tooltip' => $tooltip ), false );
                $output .= $this->field_element( 'select', $field, array( 'slug' => 'dynamic_choices', 'value' => $value, 'options' => $options ), false );
                $output  = $this->field_element( 'row',    $field, array( 'slug' => 'dynamic_choices', 'content' => $output ), false );
                break;

            // Dynamic Choices Source. ----------------------------------------//

            case 'dynamic_choices_source':
                $output = '';
                $type   = ! empty( $field['dynamic_choices'] ) ? esc_attr( $field['dynamic_choices'] ) : '';

                if ( ! empty( $type ) ) {

                    $type_name = '';
                    $items     = array();

                    if ( 'post_type' === $type ) {

                        $type_name = esc_html__( 'Post Type', 'wpforms' );
                        $items     = get_post_types(
                            array(
                                'public' => true,
                            ),
                            'objects'
                        );
                        unset( $items['attachment'] );

                    } elseif ( 'taxonomy' === $type ) {

                        $type_name = esc_html__( 'Taxonomy', 'wpforms' );
                        $items     = get_taxonomies(
                            array(
                                'public' => true,
                            ),
                            'objects'
                        );
                        unset( $items['post_format'] );
                    }

                    /* translators: %s - dynamic source type name. */
                    $tooltip = sprintf( esc_html__( 'Select %s to use for auto-populating field choices.', 'wpforms' ), $type_name );
                    /* translators: %s - dynamic source type name. */
                    $label   = sprintf( esc_html__( 'Dynamic %s Source', 'wpforms' ), $type_name );
                    $options = array();
                    $source  = ! empty( $field[ 'dynamic_' . $type ] ) ? esc_attr( $field[ 'dynamic_' . $type ] ) : '';

                    foreach ( $items as $key => $item ) {
                        $options[ $key ] = $item->labels->name;
                    }

                    // Field option label.
                    $option_label  = $this->field_element(
                        'label',
                        $field,
                        array(
                            'slug'    => 'dynamic_' . $type,
                            'value'   => $label,
                            'tooltip' => $tooltip,
                        ),
                        false
                    );

                    // Field option select input.
                    $option_input = $this->field_element(
                        'select',
                        $field,
                        array(
                            'slug'    => 'dynamic_' . $type,
                            'options' => $options,
                            'value'   => $source,
                        ),
                        false
                    );

                    // Field option row (markup) including label and input.
                    $output  = $this->field_element(
                        'row',
                        $field,
                        array(
                            'slug'    => 'dynamic_' . $type,
                            'content' => $option_label . $option_input,
                        ),
                        false
                    );
                } // End if().
                break;
        } // End switch().

        if ( $echo ) {

            if ( in_array( $option, array( 'basic-options', 'advanced-options' ), true ) ) {

                if ( 'open' === $markup ) {
                    do_action( "wpforms_field_options_before_{$option}", $field, $this );
                }

                if ( 'close' === $markup ) {
                    do_action( "wpforms_field_options_bottom_{$option}", $field, $this );
                }

                echo $output; // WPCS: XSS ok.

                if ( 'open' === $markup ) {
                    do_action( "wpforms_field_options_top_{$option}", $field, $this );
                }

                if ( 'close' === $markup ) {
                    do_action( "wpforms_field_options_after_{$option}", $field, $this );
                }
            } else {
                echo $output; // WPCS: XSS ok.
            }
        } else {
            return $output;
        }
    }

    /**
     * Field preview inside the builder.
     *
     * @since 1.0.0
     *
     * @param array $field Field settings.
     */
    public function field_preview( $field ) {

        // Define data.
        $placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

        // Label.
        $this->field_preview_option( 'label', $field );

        // Primary input.
        echo '<div class="drop_uploader drop_zone"><div class="text_wrapper"><i class="pe-7s-cloud-upload"></i> <span class="text">' . esc_html__("Drop files here, or", "wpf-drop-uploader") . ' <a href="#" class="button button-primary file_browse" style="position: relative; top: 26px;">' . esc_html__("Browse", "wpf-drop-uploader") . '</a></span></div></div>';

        // Description.
        $this->field_preview_option( 'description', $field );
    }

    /**
     * Field display on the form front-end.
     *
     * @since 1.0.0
     *
     * @param array $field      Field settings.
     * @param array $deprecated Deprecated.
     * @param array $form_data  Form data.
     */
    public function field_display( $field, $deprecated, $form_data ) {

        // Define data.
        $primary = $field['properties']['inputs']['primary'];

        $class = $primary['class'];

        $class[] = ' wpf-drop-uploader';

        $max_file_size = $field['max_file_size'];

        $amount = $field['amount'];

        $types = $field['types'];

        // Convert file size to bytes
        if($max_file_size != "") {
            $max_file_size = wpforms_size_to_bytes($max_file_size);

            $max_file_size = 'data-maxfilesize="' . $max_file_size . '"';
        } else {
            $max_file_size = "";
        }

        // Convert types to MIME
        if($types != "") {
            $types = $this->convert_type_to_mime($types);

            $types = 'accept="' . $types . '"';
        } else {
            $types = "";
        }

        // Check amount
        if($amount == "" || $amount > 1) {
            $multiple = "multiple";
        } else {
            $multiple = "";
        }

        if($amount != "") {
            $data_count = 'data-count="' . (int)$amount . '"';
        } else {
            $data_count = "";
        }

        // Primary field.
        printf( '<input type="file" %s %s %s %s %s %s data-method="ajax">',
            wpforms_html_attributes( $primary['id'], $class, $primary['data'], $primary['attr'] ),
            $primary['required'],
            $max_file_size,
            $data_count,
            $multiple,
            $types
        ); // WPCS: XSS ok.
    }

    /**
     * Convert file types to MIME types
     *
     * @since 1.0.0
     *
     * @param string $type   Coma separated file types
     *
     * @return string
     */
    public function convert_type_to_mime($type) {

        $return_mime = "";

        $file_ext_mime = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf,pdf',
            'zip' => 'application/zip,zip',
            'doc' => 'application/msword,doc',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document,docx',
            'ppt' => 'application/vnd.ms-powerpoint,ppt',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation,pptx',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,xlsx',
            'xls' => 'application/vnd.ms-excel,xls',
            'odt' => 'application/vnd.oasis.opendocument.text,odt',
            'avi' => 'video/x-msvideo,avi',
            'oga' => 'audio/ogg,oga',
            'ogv' => 'video/ogg,ogv',
            'aac' => 'audio/aac,aac',
            'mov' => 'video/quicktime,mov',
            'mp3' => 'audio/mpeg,mp3',
            'mp4' => 'video/mp4,mp4',
            'mpg' => 'video/mpeg,mpg',
            'wav' => 'audio/x-wav,wav',
            'wmv' => 'video/x-ms-wmv,wmv',
            // Extended
            'sldprt' => 'sldprt',
            'sldasm' => 'sldasm',
            'slddrw' => 'slddrw',
            'prt' => 'application/pro_eng,prt',
            'asm' => 'text/x-asm,asm',
            'drw' => 'application/drafting,drw',
            'psd' => 'application/octet-stream,psd',
            'ai' => 'application/postscript,ai',
            'lfp' => 'lfp',
            'sldlfp' => 'sldlfp',
            'prddot' => 'prddot',
            'asmdot' => 'asmdot',
            'drwdot' => 'drwdot',
            'dxf' => 'application/dxf,image/vnd.dwg,image/x-dwg,dxf',
            'dwg' => 'application/acad,image/vnd.dwg,image/x-dwg,dwg',
            'iges' => 'application/iges,model/iges,iges',
            'igs' => 'application/iges,model/iges,igs',
            'step' => 'application/step,step',
            'stp' => 'application/step,stp',
            'stl' => 'application/sla,application/vnd.ms-pki.stl,application/x-navistyle,stl',
            'sat' => 'sat',
            'vda' => 'application/vda,vda',
            'wrl' => 'application/x-world,model/vrml,x-world/x-vrml,wrl',
            'cgr' => 'cgr',
            'catpart' => 'catpart',
            'catproduct' => 'catproduct',
            'xpr' => 'xpr',
            'ipt' => 'ipt',
            'iam' => 'iam',
            'par' => 'par',
            'psm' => 'psm',
            '3ds' => '3ds',
            'obj' => 'obj',
            'blend' => 'blend',
            'cad' => 'cad',
            '3dm' => 'x-world/x-3dmf,3dm',
            '3dmf' => 'x-world/x-3dmf,3dmf',
            'tiff' => 'image/tiff,tiff',
            'cr2' => 'image/x-raw,image/x-canon-cr2,cr2',
            'rar' => 'application/x-rar-compressed,rar',
        );

        $type_array = explode(",", $type);

        foreach ($type_array as $type_value) {
            if( isset($file_ext_mime[$type_value]) ) {
                $return_mime .= $file_ext_mime[$type_value].",";
            }
        }

        $return_mime = rtrim($return_mime, ',');

        return $return_mime;
    }
}
new WPF_Drop_Uploader;
