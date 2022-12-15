<?php

namespace Drupal\commerce_addtocart_ajax\Helper;

use Drupal\block\BlockRepositoryInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\block\Entity\Block;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\PrependCommand;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class AjaxCartHelper.
 *
 * @package Drupal\modules\commerce_addtocart_ajax
 */
class AjaxCartHelper {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Protected cartBlock variable.
   *
   * @var cartBlock
   */
  protected $cartBlock;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The Addtocart ajax config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The Drupal messenger.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The block repository.
   *
   * @var \Drupal\block\BlockRepositoryInterface
   */
  protected $blockRepository;

  /**
   * AjaxCartHelper constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The drupal messenger.
   * @param \Drupal\block\BlockRepositoryInterface $block_repository
   *   The block repository.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, MessengerInterface $messenger, BlockRepositoryInterface $block_repository, RendererInterface $renderer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
    $this->blockRepository = $block_repository;
    $this->renderer = $renderer;
    $this->cartBlock = $this->getCartBlock();
    $this->config = $this->configFactory->get('commerce_addtocart_ajax.settings');
  }

  /**
   * Gets cart block.
   *
   * @return array
   *   Return render array.
   */
  private function getCartBlock() {
    $block_id = $this->getCartBlockId();
    if ($block_id !== FALSE) {
      $block = Block::load($block_id);
      $render = $this->entityTypeManager
        ->getViewBuilder('block')
        ->view($block);
    }
    return isset($render) ? $render : NULL;
  }

  /**
   * Gets the machine name of a commerce cart block visible on the current page.
   *
   * Returns only the first cart found.
   *
   * @return string|false
   *   Return id of the first commerce cart block found on current page.
   *   Returns FALSE if no commerce cart block is visible.
   */
  private function getCartBlockId() {
    // Returns an array of regions each with an array of blocks.
    $regions = $this->blockRepository->getVisibleBlocksPerRegion();

    // Iterate all visible blocks and regions.
    foreach ($regions as $region) {
      foreach ($region as $block) {
        $plugin_id = $block->get('plugin');
        // Check if this is a commerce cart block.
        if ($plugin_id === 'commerce_cart') {
          $cart_block_id = $block->id();
          return $cart_block_id;
        }
      }
    }

    return FALSE;
  }

  /**
   * Ajax add to cart Form.
   *
   * @param array $form
   *   Form array.
   * @param string $form_id
   *   Form id.
   *
   * @return array
   *   Return Form array.
   */
  public function ajaxAddToCartAjaxForm(array &$form, $form_id) {
    $messages = [
      $form_id => $this->t('Adding to cart ...'),
    ];

    $form['form_id'] = [
      '#type' => 'hidden',
      '#value' => $form_id,
    ];

    // Add ajax callback to the form.
    $form['actions']['submit']['#attributes']['class'][] = 'use-ajax';
    $form['actions']['submit']['#ajax'] = [
      'callback' => 'commerce_addtocart_ajax_ajax_validate',
      'disable-refocus' => TRUE,
      'event' => 'click',
      'progress' => [
        'type' => 'throbber',
        'message' => $messages[$form_id],
      ],
    ];
    return $form;
  }

  /**
   * Ajax add to cart response.
   *
   * @param array $form
   *   Form.
   * @param string $form_id
   *   Form id.
   * @param \Drupal\Core\Ajax\AjaxResponse $response
   *   Response object to store information.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Return response object.
   */
  public function ajaxAddToCartAjaxResponse(array &$form, $form_id, AjaxResponse $response) {
    $response->addCommand(
      new ReplaceCommand('.block-commerce-cart', $this->cartBlock)
    );

    $selector = $this->config->get('status_messages_selector') ?: '.status-messages';
    $status_messages = ['#type' => 'status_messages'];
    $messages = $this->renderer->renderRoot($status_messages);
    if (!empty($messages) && $selector) {
      $response->addCommand(new PrependCommand($selector, $messages));
    }
    return $response;

  }

}
