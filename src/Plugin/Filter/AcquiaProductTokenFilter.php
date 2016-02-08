<?php

/**
 * @file
 * Contains \Drupal\acquia_product_tokens\Plugin\Filter\AcquiaProductTokenFilter.
 */

namespace Drupal\acquia_product_tokens\Plugin\Filter;

use Drupal\acquia_product_tokens\Utility\AcquiaProductToken;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter that replaces global tokens with their values.
 *
 * @Filter(
 *   id = "acquia_product_token_filter",
 *   title = @Translation("Replace Acquia Product tokens with their values"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   settings = { }
 * )
 */
class AcquiaProductTokenFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The token service.
   *
   * @var \Drupal\acquia_product_tokens\Utility\AcquiaProductToken
   */
  protected $token;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a token filter plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\acquia_product_tokens\Utility\AcquiaProductToken $token
   *   The Acquia Product token service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AcquiaProductToken $token, RendererInterface $renderer) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->token = $token;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('acquia_product_tokens.token'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    return new FilterProcessResult($this->token->replace($text), [], ['langcode' => $langcode]);
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      $values = array();
      foreach ($this->token->getValues() as $name => $product_token) {
        $values[] = $product_token['token'];
      }
      $available = array(
        '#prefix' => '<br />',
        '#plain_text' => implode(', ', $values),
        '#cache' => array(
          'tags' => array('config:acquia_product_tokens.settings'),
        ),
      );
      return $this->t('Acquia Product tokens are replaced with their values.@available', array('@available' => $this->renderer->render($available)));
    }
    else {
      return $this->t('Acquia Product tokens are replaced with their values.@available', array('@available' => ''));
    }
  }
}