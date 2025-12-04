<?php
/**
 * Format ACF date fields for Gravity Forms Populate Anything
 * Supports both Dutch and English formatting based on form ID
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

    // Bepaal het formulier ID
    $form_id = 0;
    if ( isset( $populate->form->id ) ) {
        $form_id = $populate->form->id;
    } elseif ( isset( $field->formId ) ) {
        $form_id = $field->formId;
    }

    // Voor formulier ID 5: gebruik Engels, anders Nederlands
    if ( $form_id == 5 ) {
        // Engels format: 'Thursday 5 February 2026'
        // Tijdelijk locale switchen naar Engels
        $original_locale = get_locale();
        switch_to_locale( 'en_US' );
        $formatted = date_i18n( 'l j F Y', $timestamp );
        switch_to_locale( $original_locale );
    } else {
        // Nederlands format: 'donderdag 5 februari 2026' (site-taal = NL)
        $formatted = date_i18n( 'l j F Y', $timestamp );
    }

    // Eerste letter hoofdletter
    return ucfirst( $formatted );
}
