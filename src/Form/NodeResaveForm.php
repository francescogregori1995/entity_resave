<?php

namespace Drupal\entity_resave\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;


class NodeResaveForm extends FormBase
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
        $content_types = \Drupal::service('entity_type.bundle.info')->getBundleInfo('node');
        $options = [];
        foreach ($content_types as $machine_name => $content_type) {
            $options[$machine_name] = $content_type['label'];
        }

        $form['content_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Content type'),
            '#options' => $options,
            '#required' => TRUE,
        ];

        $form['update_changed'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Update last changed date'),
            '#default_value' => 0,
        ];
        $form['process_data'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Resave nodes'),
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $connection = \Drupal::database();
        $query = $connection->select('node_field_data', 'n')
            ->fields('n', ['nid'])
            ->condition('n.type', $form['content_type']['#value'])
            ->execute()
            ->fetchAll();
        
        $nodes = [];
        foreach ($query as $row) {
            $nodes[] = $row->nid;
        }
        $dati = $nodes;

        $batch = [
            'title' => t('Risalvo Nodi'),
            'operations' => [],
            'init_message' => t('Inizio aggiornamento.'),
            'progress_message' => t('Salvo @current su @total. Fine del panico in: @estimate.'),
            'error_message' => t('Si è verificato un errore.'),
            'finished' => 'import_completed',
        ];

        foreach ($dati as $data) {
            $batch['operations'][] = ['update_node', [$data, $form['update_changed']['#value']]];
        }

        batch_set($batch);

        $form_state->setRedirect('entity_resave.completed');
        //$form_state->setRebuild(TRUE);
    }

}