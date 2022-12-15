<?php

namespace Drupal\custom_csv_import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Routing;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\Response;
use \Drupal\node\Entity\Node;
/**
 * Provides the form for adding countries.
 */
class ImportcsvForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csv_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
  
    $form = array(
      '#attributes' => array('enctype' => 'multipart/form-data'),
    );
	
    $validators = array(
      'file_validate_extensions' => array('csv'),
    );
    $form['csv_file'] = array(
      '#type' => 'managed_file',
      '#name' => 'csv_file',
      '#title' => t('File *'),
      '#size' => 20,
      '#description' => t('csv format only'),
      '#upload_validators' => $validators,
      '#upload_location' => 'public://csv_files/',
    );
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    );

    return $form;

  }
  
  
  public function validateForm(array &$form, FormStateInterface $form_state) {    
    if ($form_state->getValue('csv_file') == NULL) {
      $form_state->setErrorByName('csv_file', $this->t('upload proper File.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file = \Drupal::entityTypeManager()->getStorage('file')->load($form_state->getValue('csv_file')[0]);
 	  $full_path = $file->get('uri')->value;
	  $file_name = basename($full_path);
	 
		try{
			$input_file = \Drupal::service('file_system')->realpath('public://csv_files/'.$file_name);
			$spreadsheet = IOFactory::load($input_file);
			$sheetData = $spreadsheet->getActiveSheet();
			$rows = array();
			foreach ($sheetData->getRowIterator() as $row) {
				//echo "<pre>";print_r($row);exit;
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(FALSE); 
				$cells = [];
				foreach ($cellIterator as $cell) {
					$cells[] = $cell->getValue();
				}
	      $rows[] = $cells;	   
			}

			// Remove first item since it is the header row
			array_shift($rows);
			foreach($rows as $row){
				\Drupal::database()->insert('csv_data')
            ->fields(array(
                'full_name' => $row[0] . ' ' .$row[1],
            ))
            ->execute();
			}
			
			\Drupal::messenger()->addMessage('Imported successfully');
		}
		catch (Exception $e) {
			\Drupal::logger('type')->error($e->getMessage());
    }
  }
}
