<?php
/**
 * @file
 * Contains \Drupal\acquia_product_tokens\Utility\AcquiaProductToken.
 */

namespace Drupal\acquia_product_tokens\Utility;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Utility\Token;

class AcquiaProductToken extends Token {

  /**
   * The config factory to get the token values.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The token cache.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   * @param \Drupal\Core\Cache\CacheTagsInvalidatorInterface $cache_tags_invalidator
   *   The cache tags invalidator.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache, LanguageManagerInterface $language_manager, CacheTagsInvalidatorInterface $cache_tags_invalidator, RendererInterface $renderer, ConfigFactoryInterface $config_factory) {
    parent::__construct($module_handler, $cache, $language_manager, $cache_tags_invalidator, $renderer);
    $this->configFactory = $config_factory;
  }

  /**
   * Converts text inserted by user into product/token array.
   */
  public function getValues() {
    $config = $this->configFactory->get('acquia_product_tokens.settings');
    $text = $config->get('tokens');
    $product_tokens = array();
    $lines = explode("\n", $text);
    foreach ($lines as $line) {
      if (!empty($line)) {
        list($product_name, $token) = explode('|', $line);
        $item = array();
        $item['product_name'] = trim($product_name);
        $item['token'] = '[acquia-product:' . $token . ']';
        $product_tokens[$token] = $item;
      }
    }
    return $product_tokens;
  }

  /**
   * Builds a list of all acquia-product token patterns that appear in the text.
   *
   * @param string $text
   *   The text to be scanned for possible tokens.
   *
   * @return array
   *   An associative array of discovered tokens, grouped by type.
   */
  public function scan($text) {
    // Matches tokens with the following pattern: [acquia-product:$name]
    // $type and $name may not contain [ ] characters.
    // $type may not contain : or whitespace characters, but $name may.
    preg_match_all('/
      \[acquia-product:  # [ - pattern start
      ([^\[\]]+)         # match $name not containing [ or ]
      \]                 # ] - pattern end
      /x', $text, $matches);

    $tokens = $matches[1];

    // Iterate through the matches, building an associative array containing
    // $tokens grouped by $types, pointing to the version of the token found in
    // the source text. For example, $results['node']['title'] = '[node:title]';
    $results = array();
    for ($i = 0; $i < count($tokens); $i++) {
      $results['acquia-product'][$tokens[$i]] = $matches[0][$i];
    }

    return $results;
  }
}