<?php

namespace Drupal\ins_wistia_integration\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class WistiaAPIConfigForm.
 */
class WistiaAPIConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ins_wistia_integration.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ins_wistia_integration_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ins_wistia_integration.settings');

    $form['account_api_key'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Account API key'),
      '#default_value' => $config->get('account_api_key') ? $config->get('account_api_key') : '',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['flush_cache'] = [
      '#type' => 'submit',
      '#value' => $this->t('Invalidate cache tag'),
      '#submit' => ['::invalidateCacheTag'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('ins_wistia_integration.settings');
    $values = $form_state->getUserInput();

    foreach ($values as $key => $value) {
      if ($key !== 'op' && strstr($key, 'form_') === FALSE) {
        // Ignore the 'op' and form_build, id, etc values.
        $config->set($key, $value);
      }

    }
    $config->save();
  }

  /**
   * {@inheritdoc}
   */
  public function invalidateCacheTag(array &$form, FormStateInterface $form_state) {
    Cache::invalidateTags(['ins_wistia_integration.wistia_items']);
  }

}
