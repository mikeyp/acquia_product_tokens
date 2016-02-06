<?php

/**
 * @file
 * Contains \Drupal\acquia_product_tokens\Form\AcquiaProductTokensAdminSettings.
 */

namespace Drupal\acquia_product_tokens\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class AcquiaProductTokensAdminSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'acquia_product_tokens_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('acquia_product_tokens.settings');

    $config->set('tokens', $form_state->getValue('tokens'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['acquia_product_tokens.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('acquia_product_tokens.settings');
    $form['tokens'] = array(
     '#type' => 'textarea',
     '#title' => t('Definition of Acquia Products and tokens'),
     '#default_value' => $config->get('tokens'),
     '#description' => t('Insert the Product name and its token one per line in this format "Product name|token".'),
     '#size' => 60,
     '#required' => TRUE,
   );

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Implements some validation.
    $lines = explode("\n", $form_state->getValue('tokens'));
    foreach ($lines as $line) {
      $parts = explode('|', $line);
      if (count($parts) != 2 || empty($parts[0]) || empty($parts[1])) {
        $form_state->setErrorByName('tokens', $this->t('Each line in this text area cannot be empty and should contain one single "product name|token" pair.'));
      }
      elseif (!preg_match('/[a-z0-9-]/', $parts[1])) {
        $form_state->setErrorByName('tokens', $this->t('Tokens should only use alphanumeric and -'));
      }
    }
  }

}
