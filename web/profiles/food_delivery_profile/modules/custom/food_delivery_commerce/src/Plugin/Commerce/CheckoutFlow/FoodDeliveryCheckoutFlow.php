<?php

namespace Drupal\food_delivery_commerce\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;

/**
 * Custom checkout flow.
 *
 * @CommerceCheckoutFlow(
 *  id = "food_delivery_checkout_flow",
 *  label = @Translation("Food Delivery Checkout Flow"),
 * )
 */
class FoodDeliveryCheckoutFlow extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritDoc}
   */
  public function getSteps() {
    return [
      'login' => [
        'label' => $this->t('Login'),
        'has_sidebar' => FALSE,
        'hidden' => FALSE,
      ],
      'billing' => [
        'label' => $this->t('Billing'),
        'previous_label' => $this->t('Back to billing'),
        'has_sidebar' => FALSE,
        'hidden' => FALSE,
      ],
      'shipping' => [
        'label' => $this->t('Shipping'),
        'next_label' => $this->t('Continue'),
        'previous_label' => $this->t('Back to shipping'),
        'has_sidebar' => TRUE,
        'hidden' => FALSE,
      ],
      'payment' => [
        'label' => $this->t('Payment'),
        'next_label' => $this->t('Continue'),
        'has_sidebar' => TRUE,
        'hidden' => FALSE,
      ],
      'payment_process' => [
        'label' => $this->t('Payment Process'),
        'next_label' => $this->t('Continue'),
        'hidden' => TRUE,
      ],
      'complete' => [
        'label' => $this->t('Complete'),
        'next_label' => $this->t('Continue'),
        'has_sidebar' => FALSE,
        'hidden' => FALSE,
      ],
    ];
  }

}
