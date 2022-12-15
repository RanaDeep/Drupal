<?php

namespace Drupal\food_delivery_commerce\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a custom Account Information pane.
 *
 * @CommerceCheckoutPane(
 *   id = "custom_account_information_pane",
 *   label = @Translation("Custom account information"),
 * )
 */
class CustomAccountInformationPane extends CheckoutPaneBase {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $pane_form['account_information'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Account information'),
    ];
    $pane_form['account_information']['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number'),
    ];
    $pane_form['account_information']['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full name'),
      '#required' => TRUE,
    ];
    $pane_form['account_information']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your email'),
      '#required' => TRUE,
    ];

    return $pane_form;
  }

}
