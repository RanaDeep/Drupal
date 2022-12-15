<?php

namespace Drupal\Tests\food_delivery_profile\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Unit test for Food Delivery Profile.
 */
class Test extends BrowserTestBase {

  /**
   * An array of config object names that are excluded from schema checking.
   *
   * @var string[]
   */
  protected static $configSchemaCheckerExclusions = [
    'module_filter.settings',
    'views.view.categories',
    'views.view.catalog',
    'commerce_addtocart_ajax.settings',
    'webprofiler.config',
    'commerce_checkout.commerce_checkout_flow.paypal_checkout',
  ];

  /**
   * {@inheritdoc}
   */
  protected $profile = 'food_delivery_profile';

  /**
   * A simple user.
   *
   * @var \Drupal\user\Entity\User
   */
  private $user;

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser([
      'administer site configuration',
    ]);
  }

  /**
   * Testing adding item to basket.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testAddItemToBasket() {
    $this->drupalLogin($this->user);
    $this->drupalGet('catalog');
    $page = $this->getSession()->getPage();
    $page->pressButton('edit-submit--2');
    $this->assertSession()->pageTextContains('My cart (1)');
  }

  /**
   * Testing order.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testMakeOrder() {
    $this->drupalLogin($this->user);
    $this->drupalGet('catalog');
    $page = $this->getSession()->getPage();
    $page->pressButton('edit-submit--2');
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('cart');
    $page = $this->getSession()->getPage();
    $page->pressButton('edit-submit');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Testing remove item from cart.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   * @throws \Behat\Mink\Exception\ExpectationException
   * @throws \Behat\Mink\Exception\ResponseTextException
   */
  public function testRemoveCartButton() {
    $this->drupalLogin($this->user);
    $this->drupalGet('catalog');
    $page = $this->getSession()->getPage();
    $page->pressButton('edit-submit--2');
    $this->assertSession()->statusCodeEquals(200);
    $this->drupalGet('cart/');
    $page = $this->getSession()->getPage();
    $page->pressButton('edit-remove-button-0');
    $this->drupalGet('cart/');
    $page = $this->getSession()->getPage();
    $this->assertSession()->pageTextContains('Your shopping cart is empty.');
  }

}
