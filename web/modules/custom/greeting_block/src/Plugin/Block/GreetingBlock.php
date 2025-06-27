<?php

namespace Drupal\greeting_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a 'GreetingBlock' block.
 *
 * @Block(
 *  id = "greeting_block",
 *  admin_label = @Translation("Saudação Dinâmica"),
 *  category = @Translation("Custom")
 * )
 */
class GreetingBlock extends BlockBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new GreetingBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, DateFormatterInterface $date_formatter) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get current hour using Drupal's date service to respect timezone settings.
    // The 'H' format gives the hour in 24-hour format (00 to 23).
    $current_hour = (int) $this->dateFormatter->format(REQUEST_TIME, 'custom', 'H');
    $greeting = '';

    if ($current_hour >= 5 && $current_hour < 12) { // Morning: 5 AM to 11:59 AM
      $greeting = $this->t('Bom dia!');
    } elseif ($current_hour >= 12 && $current_hour < 20) { // Afternoon: 12 PM to 7:59 PM
      $greeting = $this->t('Boa tarde!');
    } else { // Evening/Night: 8 PM to 4:59 AM
      // The original plan only had Bom dia/Boa tarde. Added Boa noite for completeness.
      // If strictly only two states are needed, this else can be merged or adjusted.
      $greeting = $this->t('Boa noite!');
    }

    return [
      '#markup' => '<div class="greeting-block-content">' . $greeting . '</div>',
      '#cache' => [
        // Cache contexts ensure that the block varies by the user's timezone if relevant,
        // though for simple time of day, this might not be strictly necessary if server time is okay.
        // 'contexts' => ['timezone'],
        // Cache for 1 hour. Drupal will invalidate earlier if needed.
        // Max-age is in seconds. 3600 seconds = 1 hour.
        // This block should change roughly every hour, so a shorter max-age might be better
        // to ensure freshness, or rely on cache tags if content changes.
        // For time-based greeting, it should update based on time, so max-age is key.
        // Let's set it to 60 seconds to ensure it updates reasonably quickly after the hour changes.
        'max-age' => 60,
      ],
    ];
  }

}
