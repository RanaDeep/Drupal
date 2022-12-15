<?php

namespace Drupal\adci_contact_info\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an contact info block.
 *
 * @Block(
 *   id = "adci_contact_info_block",
 *   admin_label = @Translation("ADCI Solutions contact information block"),
 *   category = @Translation("adci_contact_info")
 * )
 */
class ContactInfoBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * Creates a ContactInfoBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ThemeManagerInterface $theme_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->themeManager = $theme_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('theme.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $return_as_object = FALSE) {
    $permissions = [
      'administer site configuration',
    ];
    return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    $defaults = $this->defaultConfiguration();

    $form['info'] = [
      '#type' => 'details',
      '#title' => $this->t('Information'),
      '#open' => $defaults['info'] !== $config['info'],
    ];
    $form['info']['info'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Information'),
      '#default_value' => $config['info'],
      '#description' => $this->t('You can use @version placeholder to print the version of Drupal core.'),
    ];
    $form['site'] = [
      '#type' => 'details',
      '#title' => $this->t('Site link'),
      '#open' => $defaults['site'] !== $config['site'],
    ];
    $form['site']['link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link'),
      '#default_value' => $config['site']['link'],
      '#attributes' => [
        'id' => 'adci-info-block-site-link',
      ],
    ];
    $form['site']['text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link text'),
      '#default_value' => $config['site']['text'],
      '#states' => [
        'disabled' => [
          ':input[id="adci-info-block-site-link"]' => [
            'empty' => TRUE,
          ],
        ],
      ],
    ];
    $form['site']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['site']['title'],
      '#states' => [
        'disabled' => [
          ':input[id="adci-info-block-site-link"]' => [
            'empty' => TRUE,
          ],
        ],
      ],
    ];
    $form['email'] = [
      '#type' => 'details',
      '#title' => $this->t('Email'),
      '#open' => $defaults['email'] !== $config['email'],
    ];
    $form['email']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#default_value' => $config['email']['email'],
      '#attributes' => [
        'id' => 'adci-info-block-email-email',
      ],
    ];
    $form['email']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['email']['title'],
      '#states' => [
        'disabled' => [
          ':input[id="adci-info-block-email-email"]' => [
            'empty' => TRUE,
          ],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->configuration['info'] = $values['info']['info'];
    $this->configuration['site'] = $values['site'];
    $this->configuration['email'] = $values['email'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $version = \Drupal::VERSION;
    $system_manager = \Drupal::service('system.manager');
    $requirements = $system_manager->listRequirements();
    $severities = [];

    foreach ($requirements as $requirement) {
      if (isset($requirement['severity'])) {
        if (isset($severities[$requirement['severity']])) {
          $severities[(int) $requirement['severity']]++;
          continue;
        }
        $severities += [(int) $requirement['severity'] => 1];
      }
    }

    $listItems = [];

    if (isset($severities[$system_manager::REQUIREMENT_ERROR])) {
      $count = $severities[$system_manager::REQUIREMENT_ERROR];
      $listItems[] = [
        '#title' => $this->formatPlural($count, '1 error', '@count errors'),
        '#type' => 'link',
        '#url' => Url::fromRoute('system.status'),
      ];
    }

    if (isset($severities[$system_manager::REQUIREMENT_WARNING])) {
      $count = $severities[$system_manager::REQUIREMENT_WARNING];
      $listItems[] = [
        '#title' => $this->formatPlural($count, '1 warning', '@count warnings'),
        '#type' => 'link',
        '#url' => Url::fromRoute('system.status'),
      ];
    }

    $build['info'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => str_replace('@version', $version, $config['info']),
    ];

    if (!empty($config['site']['link'])) {
      $build['site'] = [
        '#type' => 'container',
      ];
      $build['site']['title'] = [
        '#type' => 'label',
        '#title' => $config['site']['title'],
        "#title_display" => 'before',
      ];
      $build['site']['link']  = [
        '#title' => $config['site']['text'],
        '#type' => 'link',
        '#url' => Url::fromUri($config['site']['link']),
        '#attributes' => [
          'target' => '_blank',
          'rel' => 'noopener noreferrer',
        ],
      ];
    }

    if (!empty($config['email']['email'])) {
      $build['email'] = [
        '#type' => 'container',
      ];
      $build['email']['title'] = [
        '#type' => 'label',
        '#title' => $config['email']['title'],
        "#title_display" => 'before',
      ];
      $build['email']['email'] = [
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#value' => $config['email']['email'],
        '#attributes' => [
          'href' => 'mailto:' . $config['email']['email'],
        ],
      ];
    }

    if (count($listItems) > 0) {
      $build['errors'] = [
        '#theme' => 'item_list',
        '#list_type' => 'ul',
        '#title' => $this->t('Your website requires your attention:'),
        '#items' => $listItems,
      ];
    }

    $build['#attributes'] = [
      'class' => [
        'adci-contact-info'
      ],
    ];

    if ($this->themeManager->getActiveTheme()->getName() === 'adminimal_theme') {
      $build['#attributes']['class'][] = 'adci-contact-info-adminimal';
    }

    $build['#attached'] = [
      'library' => [
        'adci_contact_info/main',
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'info' => 'The website is using Drupal @version',
      'site' => [
        'title' => 'Supported by',
        'text' => 'ADCI Solutions',
        'link' => 'https://www.adcisolutions.com/',
      ],
      'email' => [
        'title' => 'Email',
        'email' => 'hello@adcillc.com',
      ],
    ];
  }

}
