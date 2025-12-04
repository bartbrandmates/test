<?php
/**
 * Format ACF date fields for Gravity Forms
 * - Dutch format for most forms
 * - English format for form ID 5
 */

add_filter( 'gppa_process_template_value', 'bm_gppa_format_acf_date_nl', 10, 7 );
function bm_gppa_format_acf_date_nl( $template_value, $field, $template_name, $populate, $object, $object_type, $objects ) {

    // Alleen velden met deze CSS-class formatteren
    if ( strpos( $field->cssClass, 'acf-date-nl-tekst' ) === false ) {
        return $template_value;
    }

    if ( empty( $template_value ) ) {
        return $template_value;
    }

    // ACF geeft 20251009 (Ymd)
    $date = DateTime::createFromFormat( 'Ymd', $template_value );
    if ( ! $date ) {
        return $template_value;
    }

    $timestamp = $date->getTimestamp();

    // Bepaal of dit een Engels veld is (via CSS class of formulier ID)
    $is_english = false;
    
    // Check 1: Heeft het veld een Engels CSS class?
    if ( strpos( $field->cssClass, 'acf-date-en-tekst' ) !== false ) {
        $is_english = true;
    }
    
    // Check 2: Bepaal het formulier ID - probeer meerdere manieren
    $form_id = 0;
    
    // Methode 1: Via field object (meest betrouwbaar)
    if ( isset( $field->formId ) && $field->formId > 0 ) {
        $form_id = (int) $field->formId;
    }
    // Methode 2: Via populate object
    elseif ( isset( $populate->form->id ) && $populate->form->id > 0 ) {
        $form_id = (int) $populate->form->id;
    }
    // Methode 3: Via populate form_id property
    elseif ( isset( $populate->form_id ) && $populate->form_id > 0 ) {
        $form_id = (int) $populate->form_id;
    }
    // Methode 4: Via POST data (als formulier wordt ingediend)
    elseif ( isset( $_POST['gform_submit'] ) && $_POST['gform_submit'] > 0 ) {
        $form_id = absint( $_POST['gform_submit'] );
    }
    // Methode 5: Via Gravity Forms current form (tijdens rendering)
    elseif ( class_exists( 'GFCommon' ) && method_exists( 'GFCommon', 'get_current_form' ) ) {
        $current_form = GFCommon::get_current_form();
        if ( $current_form && isset( $current_form['id'] ) ) {
            $form_id = (int) $current_form['id'];
        }
    }
    // Methode 6: Via globale variabele (als beschikbaar)
    elseif ( isset( $GLOBALS['gppa_current_form'] ) && $GLOBALS['gppa_current_form'] > 0 ) {
        $form_id = (int) $GLOBALS['gppa_current_form'];
    }
    
    // Check 3: Is het formulier ID 5?
    if ( $form_id === 5 ) {
        $is_english = true;
    }
    
    // DEBUG: Tijdelijk logging (verwijder dit later als het werkt)
    // Uncomment de volgende regel om te zien welk formulier ID wordt gedetecteerd:
    // error_log( 'GPPA Date Format - Form ID: ' . $form_id . ', Field CSS: ' . $field->cssClass . ', Is English: ' . ( $is_english ? 'yes' : 'no' ) );

    // Voor Engels formaat: direct handmatig formatteren
    if ( $is_english ) {
        // Engels maandnamen en weekdagen
        $english_months = array( 
            1 => 'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December' 
        );
        $english_days = array( 
            0 => 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' 
        );
        
        // Haal dag, maand, jaar en weekdag op
        $day = (int) date( 'j', $timestamp );
        $month = (int) date( 'n', $timestamp );
        $year = (int) date( 'Y', $timestamp );
        $weekday = (int) date( 'w', $timestamp );
        
        // Formatteer: 'Thursday 5 February 2026'
        $formatted = $english_days[ $weekday ] . ' ' . $day . ' ' . $english_months[ $month ] . ' ' . $year;
    } else {
        // WordPress-format: 'donderdag 5 februari 2026' (site-taal = NL)
        $formatted = date_i18n( 'l j F Y', $timestamp );
    }

    // Eerste letter hoofdletter
    return ucfirst( $formatted );
}
