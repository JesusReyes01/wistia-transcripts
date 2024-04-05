<?php

namespace Drupal\ins_wistia_integration\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use GuzzleHttp\Exception\ClientException;

/**
 *
 */
class WistiaService {

  // const WISTIA_API_ENDPOINT = 'https://api.wistia.com/v1/medias.json';

  /**
   * Lever configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Language manager for retrieving the default langcode when none is specified.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new WistiaService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(
    ConfigFactoryInterface $configFactory,
    LanguageManagerInterface $language_manager
  ) {
    $this->config = $configFactory->get('ins_wistia_integration.settings');
    $this->languageManager = $language_manager;
  }

  /**
   * Get array of Wistia items.
   *
   * @return array|mixed
   */
  public function fetchWistiaItemCaption($VideoId) {
    $item_data = [];
    // Get an account api key.
    $auth_token = $this->config->get('account_api_key');

    if (!empty($auth_token)) {
      $client = \Drupal::httpClient();
      $url = "https://api.wistia.com/v1/medias/" . $VideoId . "/captions.json";
      
      try {
        $request = $client->get($url, [
          'headers' => [
            'Authorization' => 'Bearer ' . $auth_token,
          ],
        ]);
        
        $raw_response = $request->getBody()->getContents();
        if ($request->getStatusCode() < 400) {
          $item_data = json_decode($raw_response, TRUE);
        }
      }
      catch (ClientException $e) {
        // The guzzle get call failed.
        \Drupal::logger('ins_wistia_integration')->error('Unable to fetch item data from Wistia. Guzzle call failed with message: @message', ['@message' => $e->getMessage()]);
      }
    }
    return $item_data;
  }
}
