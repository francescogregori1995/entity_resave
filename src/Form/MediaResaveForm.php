<?php

namespace Drupal\entity_resave\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


class MediaResaveForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'entity_resave_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $media_types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('media');
        $options = [];
        foreach ($media_types as $machine_name => $media_types) {
            $options[$machine_name] = $media_types['label'];
        }

        $form['media_types'] = [
            '#type' => 'select',
            '#title' => $this->t('Media type'),
            '#options' => $options,
            '#required' => TRUE,
        ];

        $form['process_data'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Resave media'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $connection = \Drupal::database();
        $query = $connection->select('media_field_data', 'm')
            ->fields('m', ['mid'])
            ->condition('m.bundle', $form['media_types']['#value'])
            ->execute()
            ->fetchAll();
        
        $media = [];
        foreach ($query as $row) {
            $media[] = $row->mid;
        }
        $dati = $media;
       
        $batch = [
            'title' => t('Risalvo Media'),
            'operations' => [],
            'init_message' => t('Inizio aggiornamento.'),
            'progress_message' => t('Salvo @current su @total. Fine del panico in: @estimate.'),
            'error_message' => t('Si è verificato un errore.'),
            'finished' => 'import_completed',
        ];

        foreach ($dati as $data) {
            $batch['operations'][] = ['update_media', [$data]];
        }

        batch_set($batch);

        $form_state->setRedirect('entity_resave.completed');
        //$form_state->setRebuild(TRUE);
    }

}