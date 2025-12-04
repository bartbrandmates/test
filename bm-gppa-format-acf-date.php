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

    // Bepaal het formulier ID
    $form_id = isset( $populate->form->id ) ? $populate->form->id : 0;

    // Voor formulier ID 5: Engels formaat
    if ( $form_id === 5 ) {
        // Tijdelijk locale naar Engels zetten
        $switched = false;
        $locale_callback = null;
        
        if ( function_exists( 'switch_to_locale' ) ) {
            $switched = switch_to_locale( 'en_US' );
        } else {
            // Fallback: gebruik locale filter
            $locale_callback = function( $locale ) {
                return 'en_US';
            };
            add_filter( 'locale', $locale_callback, 999 );
        }
        
        // Engels formaat: 'Thursday 5 February 2026'
        $formatted = date_i18n( 'l j F Y', $timestamp );
        
        // Locale terugzetten
        if ( $switched && function_exists( 'restore_previous_locale' ) ) {
            restore_previous_locale();
        } elseif ( $locale_callback !== null ) {
            remove_filter( 'locale', $locale_callback, 999 );
        }
    } else {
        // WordPress-format: 'donderdag 5 februari 2026' (site-taal = NL)
        $formatted = date_i18n( 'l j F Y', $timestamp );
    }

    // Eerste letter hoofdletter
    return ucfirst( $formatted );
}
