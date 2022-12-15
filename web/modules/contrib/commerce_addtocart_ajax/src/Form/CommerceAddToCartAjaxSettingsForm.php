<?php

namespace Drupal\commerce_addtocart_ajax\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Mail Login settings.
 */
class CommerceAddToCartAjaxSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_addtocart_ajax_form_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['commerce_addtocart_ajax.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->getEditable('commerce_addtocart_ajax.settings');

    $form['general'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('General Configurations'),
      '#open' => TRUE,
    ];

    $form['general']['status_messages_selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The status messages selector'),
      '#default_value' => $config->get('status_messages_selector'),
      '#description' => $this->t('CSS selector to replace status messages after ajax call.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('commerce_addtocart_ajax.settings');
    $config
      ->set('status_messages_selector', $form_state->getValue('status_messages_selector'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
