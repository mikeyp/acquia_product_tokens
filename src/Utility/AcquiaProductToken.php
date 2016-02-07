<?php
/**
 * @file
 * Contains \Drupal\acquia_product_tokens\Utility\AcquiaProductToken.
 */

namespace Drupal\acquia_product_tokens\Utility;

use Drupal\Core\Utility\Token;

class AcquiaProductToken extends Token {

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