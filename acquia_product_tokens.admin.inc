<?php
/**
 * @file
 * Provides an interface for the creation of tokens for Acquia Products.
 */

/**
 * Implements the admin settings for this module.
 */
function acquia_product_tokens_admin_settings($form_state) {
  $form['acquia_product_tokens'] = array(
    '#type' => 'textarea',
    '#title' => t('Definition of Acquia Products and tokens'),
    '#default_value' => variable_get('acquia_product_tokens', NULL),
    '#description' => t('Insert the Product name and its token one per line in this format "Product name|token".'),
    '#size' => 60,
    '#required' => TRUE,
  );
  return system_settings_form($form);
}

/**
 * Performs basic validation of data inserted by the administrator.
 */
function acquia_product_tokens_admin_settings_validate($form, $form_state) {
  // Implements some validation.
  $lines = explode("\n", $form_state['values']['acquia_product_tokens']);
  foreach ($lines as $line) {
    if (empty($line) || (substr_count($line, '|') != 1)) {
      form_set_error('acquia_product_tokens', t('Each line in this text area cannot be empty and should contain one single "product name|token" pair.'));
    }
  }
}
