<?php

/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorForm{

	const ANTISPAM_BLOCKS_OPTIONS_KEY = "unlimited_elements_form_antispam_blocks";
	const ANTISPAM_SUBMISSIONS_OPTIONS_KEY = "unlimited_elements_form_antispam_submissions";

	const LOGS_OPTIONS_KEY = "unlimited_elements_form_logs";
	const LOGS_MAX_COUNT = 10;

	const FOLDER_NAME = "unlimited_elements_form";
	const HOOK_NAMESPACE = "ue_form";

	const ERROR_CODE_VALIDATION = -1;
	const ERROR_CODE_SPAM = -2;

	const ACTION_SAVE = "save";
	const ACTION_EMAIL = "email";
	const ACTION_EMAIL2 = "email2";
	const ACTION_WEBHOOK = "webhook";
	const ACTION_WEBHOOK2 = "webhook2";
	const ACTION_REDIRECT = "redirect";
	const ACTION_GOOGLE_SHEETS = "google_sheets";
	const ACTION_HOOK = "hook";
	const ACTION_MAILPOET = "mailpoet";
	const PLACEHOLDER_ADMIN_EMAIL = "admin_email";
	const PLACEHOLDER_EMAIL_FIELD = "email_field";
	const PLACEHOLDER_FORM_FIELDS = "form_fields";
	const PLACEHOLDER_SITE_NAME = "site_name";
	const PLACEHOLDER_PAGE_URL = "page_url";
	const PLACEHOLDER_PAGE_TITLE = "page_title";
	
	const TYPE_FILES = "files";

	const FIELD_NAME_HONEYPOT = "ue_extra_field";
	
	private static $isFormIncluded = false;    //indicator that the form included once

	private $formSettings;
	private $formFields;
	private $formMeta;
	private $lastSpamError;
	private $recaptchaDebug = null;
	
	/**
	 * add conditions elementor control
	 */
	public static function getConditionsRepeaterSettings(){

		$settings = new UniteCreatorSettings();

		//--- operator

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrOptions = array("And" => "and", "Or" => "or");

		$settings->addSelect("operator", $arrOptions, __("Operator", "unlimited-elements-for-elementor"), "and", $params);

		//--- field name

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;

		$settings->addTextBox("field_name", "", __("Field Name", "unlimited-elements-for-elementor"), $params);

		//--- condition

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_DROPDOWN;

		$arrOptions = array(
			"=" => "= (equal)",
			">" => "> (more)",
			">=" => ">= (more or equal)",
			"<" => "< (less)",
			"<=" => "<= (less or equal)",
			"!=" => "!= (not equal)");

		$arrOptions = array_flip($arrOptions);

		$settings->addSelect("condition", $arrOptions, __("Condition", "unlimited-elements-for-elementor"), "=", $params);

		//--- value

		$params = array();
		$params["origtype"] = UniteCreatorDialogParam::PARAM_TEXTFIELD;
		$params["label_block"] = true;

		$settings->addTextBox("field_value", "", __("Field Value", "unlimited-elements-for-elementor"), $params);

		return ($settings);
	}

	/**
	 * add form includes
	 */
	public function addFormIncludes(){

		//don't include inside editor

		if(self::$isFormIncluded == true)
			return;

		//include common scripts only once
		if(self::$isFormIncluded == false){
			$urlFormJS = GlobalsUC::$url_assets_libraries . "form/uc_form.js";

			UniteProviderFunctionsUC::addAdminJQueryInclude();
			HelperUC::addScriptAbsoluteUrl_widget($urlFormJS, "uc_form");
		}

		self::$isFormIncluded = true;
	}

	/**
	 * get conditions data
	 * modify the data, add class and attributes
	 */
	public function getVisibilityConditionsParamsData($data, $visibilityParam){

		$name = UniteFunctionsUC::getVal($visibilityParam, "name");

		$arrValue = UniteFunctionsUC::getVal($visibilityParam, "value");

		if(empty($arrValue))
			return ($data);

		$arrValue = UniteFunctionsUC::getVal($arrValue, "{$name}_conditions");

		if(empty($arrValue))
			return ($data);

		$data["ucform_class"] = " ucform-has-conditions";

		return ($data);
	}

	/**
	 * get the list of form logs
	 */
	public static function getFormLogs(){

		$logs = get_option(self::LOGS_OPTIONS_KEY, array());

		return $logs;
	}

	/**
	 * get the form values
	 */
	private function getFieldsData($arrContent, $arrFields, $arrFiles){

		$data = array();

		foreach($arrFields as $arrField){
			// get field input
			$fieldId = UniteFunctionsUC::getVal($arrField, "id");
			$fieldType = UniteFunctionsUC::getVal($arrField, "type");
			$fieldValue = UniteFunctionsUC::getVal($arrField, "value");
			$fieldParams = array();

			// get saved settings from layout
			$fieldSettings = HelperProviderCoreUC_EL::getAddonValuesWithDataFromContent($arrContent, $fieldId);

			// @TODO: get array of the uc_items and extract text of the selected value
			$fieldText = "";

			if($fieldType === self::TYPE_FILES){
				$fieldValue = UniteFunctionsUC::getVal($arrFiles, $fieldId, array());
				$fieldParams["allowed_types"] = $this->prepareFilesFieldAllowedTypes($fieldSettings);
			}

			// get values that we'll use in the form
			// note: not all the fields will have a name/title
			$name = UniteFunctionsUC::getVal($fieldSettings, "field_name");
			$title = UniteFunctionsUC::getVal($fieldSettings, "label");
			$required = UniteFunctionsUC::getVal($fieldSettings, "required");
			$required = UniteFunctionsUC::strToBool($required);

			$data[] = array(
				"title" => $title,
				"name" => $name,
				"type" => $fieldType,
				"text" => $fieldText,
				"value" => $fieldValue,
				"required" => $required,
				"params" => $fieldParams,
			);
		}

		return $data;
	}



	/**
	 * mailpoet service
	 */

	private function mailPoetService()
	{
		// Check if MailPoet API is available
		if (!class_exists('\MailPoet\API\API')) {
			return(array());
		}

		$mailPoetAPI    = \MailPoet\API\API::MP( 'v1' );
		$formFields     = $this->formFields;
		$mailPoetLists  = explode( ',', UniteFunctionsUC::getVal( $this->formSettings, "mailpoet_list_field" ) );
		$emailField     = UniteFunctionsUC::getVal( $this->formSettings, "name_email_field" );
		$firstNameField = UniteFunctionsUC::getVal( $this->formSettings, "name_first_name_field" );
		$lastNameField  = UniteFunctionsUC::getVal( $this->formSettings, "name_last_name_field" );
		$mailPoetMessage  = array();

		// Build subscriber data
		$subscriber = array();
		foreach ($formFields as $field) {
			switch ($field['name']) {
				case $emailField:
					$subscriber['email'] = $field['value'];
					break;
				case $firstNameField:
					$subscriber['first_name'] = $field['value'];
					break;
				case $lastNameField:
					$subscriber['last_name'] = $field['value'];
					break;
			}
		}

		// Process subscription if email exists and API is available
		$subscriber_email = UniteFunctionsUC::getVal($subscriber, 'email');
		if (!empty($subscriber_email) && $mailPoetAPI) {
			try {
				$mailPoetAPI->addSubscriber($subscriber, (array) $mailPoetLists);
				$mailPoetMessage[] = 'Subscriber added successfully!';
			} catch (Exception $exception) {
				$mailPoetMessage[] = $exception->getMessage();
			}
		}

		return $mailPoetMessage;
	}



	/**
	 * check if the email is valid (including placeholders)
	 */
	private function isEmailValid($fieldValue){

		if($fieldValue === "{" . self::PLACEHOLDER_ADMIN_EMAIL . "}")
			return true;

		if($fieldValue === "{" . self::PLACEHOLDER_EMAIL_FIELD . "}")
			return true;

		return UniteFunctionsUC::isEmailValid($fieldValue);
	}

	/**
	 * validate form settings
	 */
	public function validateFormSettings($formSettings){

		$errors = array();

		$formActions = UniteFunctionsUC::getVal($formSettings, "form_actions");
		$formValidations = $this->getFormSettingsValidations();

		foreach($formValidations as $validation){
			foreach($validation["actions"] as $actionKey){
				if(in_array($actionKey, $formActions) === false)
					continue;

				$actionTitle = $this->getActionTitle($actionKey);

				foreach($validation["rules"] as $fieldName => $rules){
					$fieldKey = $this->getFieldKey($fieldName, $actionKey);
					$fieldTitle = UniteFunctionsUC::getVal($validation["titles"], $fieldName, $fieldKey);
					$fieldValue = UniteFunctionsUC::getVal($formSettings, $fieldKey);
					$errorTitle = $actionTitle . ":";

					if(empty($fieldTitle) === false)
						$errorTitle .= " " . $fieldTitle;

					foreach($rules as $ruleName => $ruleParams){
						switch($ruleName){
							case "required":
								if($fieldValue === "")
									// translators: %s is field name
									$errors[] = sprintf(esc_html__("%s field is empty.", "unlimited-elements-for-elementor"), $errorTitle);
							break;

							case "required_if":
								foreach($ruleParams as $depFieldName => $depFieldRequiredValue){
									$depFieldKey = $this->getFieldKey($depFieldName, $actionKey);
									$depFieldValue = UniteFunctionsUC::getVal($formSettings, $depFieldKey);

									if($depFieldValue === $depFieldRequiredValue && $fieldValue === ""){
										// translators: %s is field name
										$errors[] = sprintf(esc_html__("%s field is empty.", "unlimited-elements-for-elementor"), $errorTitle);
										break;
									}
								}
							break;

							case "email":
								$validEmail = $this->isEmailValid($fieldValue);

								if($fieldValue !== "" && $validEmail === false)
									// translators: %s is field name
									$errors[] = sprintf(esc_html__("%s field has an invalid email address:", "unlimited-elements-for-elementor"), $errorTitle) . ' ' . $fieldValue . '.';
							break;

							case "email_recipients":
								$emails = $this->prepareEmailRecipients($fieldValue);

								foreach($emails as $email){
									$validEmail = $this->isEmailValid($email);

									if($validEmail === false)
										// translators: %1$s is field name, %2$s is field value
										$errors[] = sprintf(esc_html__("%1\$s field has an invalid email address: %2\$s.", "unlimited-elements-for-elementor"), $errorTitle, $email);
								}
							break;

							case "google_connect":
								$services = new UniteServicesUC();
								$services->includeGoogleAPI();

								try{
									UEGoogleAPIHelper::getFreshAccessToken();
								}catch(Exception $exception){
									// translators: %s is a string
									$errors[] = sprintf(__("%s Google access token is missing or expired. Please connect to Google in the \"General Settings > Integrations\".", "unlimited-elements-for-elementor"), $errorTitle);
								}
							break;

							case "url":
								$validUrl = UniteFunctionsUC::isUrlValid($fieldValue);

								if($fieldValue !== "" && $validUrl === false)
									// translators: %s is page url
									$errors[] = sprintf(esc_html__("%s field has an invalid URL.", "unlimited-elements-for-elementor"), $errorTitle);
							break;

							default:
								UniteFunctionsUC::throwError("Validation rule \"$ruleName\" is not implemented.");
						}
					}
				}
			}
		}

		return $errors;
	}

	/**
	 * get form settings validations
	 */
	private function getFormSettingsValidations(){

		$validations = array(

			array(
				"actions" => array(self::ACTION_EMAIL, self::ACTION_EMAIL2),
				"rules" => array(
					"to" => array(
						"required" => true,
					),
					"custom_to" => array(
						"required_if" => array("to" => "custom"),
						"email_recipients" => true,
					),
					"subject" => array(
						"required" => true,
					),
					"message" => array(
						"required" => true,
					),
					"from" => array(
						"email" => true,
					),
					"reply_to" => array(
						"email" => true,
					),
					"cc" => array(
						"email_recipients" => true,
					),
					"bcc" => array(
						"email_recipients" => true,
					),
				),
				"titles" => array(
					"to" => __("To", "unlimited-elements-for-elementor"),
					"custom_to" => __("Custom To", "unlimited-elements-for-elementor"),
					"subject" => __("Subject", "unlimited-elements-for-elementor"),
					"message" => __("Message", "unlimited-elements-for-elementor"),
					"from" => __("From Address", "unlimited-elements-for-elementor"),
					"reply_to" => __("Reply To", "unlimited-elements-for-elementor"),
					"cc" => __("Cc", "unlimited-elements-for-elementor"),
					"bcc" => __("Bcc", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(self::ACTION_WEBHOOK, self::ACTION_WEBHOOK2),
				"rules" => array(
					"url" => array(
						"required" => true,
						"url" => true,
					),
				),
				"titles" => array(
					"url" => __("URL", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(self::ACTION_REDIRECT),
				"rules" => array(
					"url" => array(
						"required" => true,
						"url" => true,
					),
				),
				"titles" => array(
					"url" => __("URL", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(self::ACTION_GOOGLE_SHEETS),
				"rules" => array(
					"credentials" => array(
						"google_connect" => true,
					),
					"id" => array(
						"required" => true,
					),
				),
				"titles" => array(
					"credentials" => "",
					"id" => __("Spreadsheet ID", "unlimited-elements-for-elementor"),
				),
			),

			array(
				"actions" => array(self::ACTION_HOOK),
				"rules" => array(
					"name" => array(
						"required" => true,
					),
				),
				"titles" => array(
					"name" => __("Name", "unlimited-elements-for-elementor"),
				),
			),

		);

		return $validations;
	}

	/**
	 * validate form fields
	 */
	private function validateFormFields($formFields){

		$errors = array();

		foreach($formFields as $field){
			$value = $field["value"];

			if($field["required"] === true)
				if($value === "" || (is_array($value) === true && empty($value) === true))
					$errors[] = $this->formatFieldError($field, $this->getFieldEmptyErrorMessage());

			if($field["type"] === self::TYPE_FILES){
				foreach($value as $file){
					if($file["error"] !== UPLOAD_ERR_OK){
						$errors[] = $this->formatFieldError($field, $this->getFileUploadErrorMessage());

						break;
					}

					$result = wp_check_filetype_and_ext($file["tmp_name"], $file["name"], $field["params"]["allowed_types"]);
					$allowedExtensions = array_keys($field["params"]["allowed_types"]);

					if($result["ext"] === false || $result["type"] === false){
						$errors[] = $this->formatFieldError($field, $this->getFileTypeErrorMessage($allowedExtensions));

						break;
					}
				}
			}
		}

		return $errors;
	}

	/**
	 * create form entry
	 */
	private function createFormEntry(){

		$isFormEntriesEnabled = HelperProviderUC::isFormEntriesEnabled();

		if($isFormEntriesEnabled === false)
			return;

		try{
			UniteFunctionsWPUC::processDBTransaction(function(){

				global $wpdb;

				$entriesTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRIES_NAME);
				
				$entriesData = array_merge($this->getFormMeta(), array(
					"form_name" => $this->getFormName(),
				));

				$isEntryCreated = $wpdb->insert($entriesTable, $entriesData);

				if($isEntryCreated === false)
					UniteFunctionsUC::throwError($wpdb->last_error);

				$entryId = $wpdb->insert_id;

				$entryFieldsTable = UniteFunctionsWPUC::prefixDBTable(GlobalsUC::TABLE_FORM_ENTRY_FIELDS_NAME);

				foreach($this->formFields as $field){
					
					$entryFieldsData = array(
						"entry_id" => $entryId,
						"title" => $this->getFieldTitle($field),
						"name" => $field["name"],
						"type" => $field["type"],
						"text" => $field["text"],
						"value" => $field["value"],
					);
					
					$isFieldCreated = $wpdb->insert($entryFieldsTable, $entryFieldsData);

					if($isFieldCreated === false)
						UniteFunctionsUC::throwError($wpdb->last_error);
				}
			});
		}catch(Exception $e){
			UniteFunctionsUC::throwError("Unable to create form entry: {$e->getMessage()}");
		}
	}
	
	

	/**
	 * create form log
	 */
	private function createFormLog($messages){

		$isFormLogsSavingEnabled = HelperProviderUC::isFormLogsSavingEnabled();

		if($isFormLogsSavingEnabled === false)
			return;

		$logs = self::getFormLogs();

		$logs[] = array(
			"form" => $this->getFormName(),
			"message" => implode(" ", $messages),
			"date" => current_time("mysql"),
		);

		$logs = array_slice($logs, -self::LOGS_MAX_COUNT);

		update_option(self::LOGS_OPTIONS_KEY, $logs);
	}

	/**
	 * upload form files
	 */
	private function uploadFormFiles(){

		// Create upload folder
		$folderName = self::FOLDER_NAME . "/"
			. uelm_date("Y") . "/"
			. uelm_date("m") . "/"
			. uelm_date("d") . "/";

		$folderPath = GlobalsUC::$path_images . $folderName;

		$created = wp_mkdir_p($folderPath);

		if($created === false)
			UniteFunctionsUC::throwError("Unable to create upload folder: $folderPath");
		
		
		// Process files upload
		$errors = array();
		
		foreach($this->formFields as &$field){
			if($field["type"] !== self::TYPE_FILES)
				continue;

			$urls = array();
			
			$fieldValue = UniteFunctionsUC::getVal($field, "value");
			
			foreach($fieldValue as $file){
				
				$fileName = wp_unique_filename($folderPath, $file["name"]);
				$filePath = $folderPath . "/" . $fileName;

				$filePath = rtrim($folderPath, '/') . "/" . $fileName;
				
				// Ensure the target directory exists and is writable
				$targetDir = dirname($filePath);
				if ( !is_dir($targetDir) ) {
					wp_mkdir_p($targetDir);
				}
				
				if ( !is_writable($targetDir) ) {
					$errors[] = "Target directory is not writable: " . $targetDir;
					continue;
				}
				
				$moved = false;
				$uploaded_file = wp_handle_upload( $file );
				
				//error handling
				$error = UniteFunctionsUC::getVal($uploaded_file, "error");
				
				if(!empty($error)){
					
					//tru to move the file different way
					if ( isset($file['tmp_name']) && is_uploaded_file($file['tmp_name']) ) {
						$moved = move_uploaded_file($file['tmp_name'], $filePath);
					}
					
					if($moved == false)
						$errors[] = "Direct upload failed: Unable to move " . $file['tmp_name'] . " to " . $filePath;
						
					continue;
				}
				
				$uploadedFile = UniteFunctionsUC::getVal($uploaded_file, "file");
				
				if ( !empty($uploadedFile)) {
					UniteFunctionsUC::move( $uploadedFile, $filePath, true );
					$moved = true;
				}
				
				if($moved === false){
					$errors[] = "Unable to move uploaded file: $filePath";

					continue;
				}

				$chmoded = UniteFunctionsUC::chmod($filePath, 0644);

				if($chmoded === false){
					$errors[] = "Unable to change file permissions: $filePath";

					continue;
				}

				$urls[] = GlobalsUC::$url_images . $folderName . $fileName;
			}

			$field["value"] = $this->encodeFilesFieldValue($urls);
		}
		
		
		
		return $errors;
	}

	/**
	 * send email
	 */
	private function sendEmail($emailFields){
				
		try {
			
			$isSent = @wp_mail(
				$emailFields["to"],
				$emailFields["subject"],
				$emailFields["message"],
				$emailFields["headers"],
				$emailFields["attachments"]
			);

			if($isSent === false){
				$emails = implode(", ", $emailFields["to"]);
	
				UniteFunctionsUC::throwError("Unable to send email to $emails.");
			}
		
		} catch (Exception $e) {
    		UniteFunctionsUC::throwError($e->getMessage());
		}		
	}


	/**
	 * prepare email recipients
	 */
	private function prepareEmailRecipients($emailAddresses){

		$emailAddresses = strtolower($emailAddresses);
		$emailAddresses = explode(",", $emailAddresses);
		$emailAddresses = array_map("trim", $emailAddresses);
		$emailAddresses = array_filter($emailAddresses);
		$emailAddresses = array_unique($emailAddresses);

		return $emailAddresses;
	}

	/**
	 * get form fields replaces
	 */
	private function getFieldsReplaces($includeAllFields = false){
		
		$formFieldsReplace = array();
		
		$formFieldReplaces = array();

		foreach($this->formFields as $field){
			$title = $this->getFieldTitle($field);
			$name = $field["name"];
			$value = $field["text"] ?: $field["value"];

			if($field["type"] === self::TYPE_FILES)
				$value = $this->getFilesFieldLinksHtml($value);

			$formFieldsReplace[] = "$title: $value";

			if(empty($name) === false){
				$placeholder = self::PLACEHOLDER_FORM_FIELDS . "." . $name;

				$formFieldPlaceholders[] = $placeholder;
				$formFieldReplaces[$placeholder] = $value;
			}
		}

		$formFieldsReplace = implode("<br />", $formFieldsReplace);

		$emailReplaces = array_merge(array(
			self::PLACEHOLDER_FORM_FIELDS => $formFieldsReplace,
		), $formFieldReplaces);
		
		
		return(array($formFieldPlaceholders, $emailReplaces));
	}
	
	/**
	 * prepare email message field
	 */
	private function prepareEmailMessageField($emailMessage,$includeFormFields = true){

		$formFieldPlaceholders = array();
		
		$arrResponse = $this->getFieldsReplaces($includeFormFields);
		
		$formFieldPlaceholders = $arrResponse[0];
		$emailReplaces = $arrResponse[1];
		
		if(empty($formFieldPlaceholders))
			$formFieldPlaceholders = array();
		
		$emailPlaceholders = array_merge(array(
			self::PLACEHOLDER_ADMIN_EMAIL,
			self::PLACEHOLDER_EMAIL_FIELD,
			self::PLACEHOLDER_SITE_NAME,
			self::PLACEHOLDER_PAGE_URL,
			self::PLACEHOLDER_PAGE_TITLE,
		), $formFieldPlaceholders);
		
		if($includeFormFields == true)
			$emailPlaceholders[] = self::PLACEHOLDER_FORM_FIELDS;
		
		$emailMessage = $this->replacePlaceholders($emailMessage, $emailPlaceholders, $emailReplaces);
		$emailMessage = preg_replace("/(\r\n|\r|\n)/", "<br />", $emailMessage); // nl2br
		
		//clear placeholders that left
		$emailMessage = $this->clearPlaceholders($emailMessage);
		
		
		return $emailMessage;
	}
	
	/**
	 * replace title placeholders
	 */
	private function replaceTitlePlaceholders($fromName){
		
		$fromName = $this->prepareEmailMessageField($fromName, false);
		
		return($fromName);
	}
	
	
	/**
	 * prepare email headers
	 */
	private function prepareEmailHeaders($emailFields){

		$headers = array("Content-Type: text/html; charset=utf-8");

		if(empty($emailFields["from"]) === false){
			$from = $emailFields["from"];

			if($emailFields["from_name"])
				$from = "{$emailFields["from_name"]} <{$emailFields["from"]}>";

			$headers[] = "From: $from";
		}

		if(empty($emailFields["reply_to"]) === false)
			$headers[] = "Reply-To: {$emailFields["reply_to"]}";

		if(empty($emailFields["cc"]) === false){
			foreach($emailFields["cc"] as $email){
				$headers[] = "Cc: $email";
			}
		}

		if(empty($emailFields["bcc"]) === false){
			foreach($emailFields["bcc"] as $email){
				$headers[] = "Bcc: $email";
			}
		}

		return $headers;
	}

	/**
	 * send webhook
	 */
	private function sendWebhook($webhookFields){

		$response = UEHttp::make()->post($webhookFields["url"], $webhookFields["body"]);

		if($response->status() !== 200)
			UniteFunctionsUC::throwError("Unable to send webhook to {$webhookFields["url"]}.");
	}

	/**
	 * get webhook fields
	 */
	private function getWebhookFields($action){

		$url = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("url", $action));
		$mode = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("mode", $action));
		$body = array();

		if($mode === "advanced"){
			$body["form"] = array(
				"name" => $this->getFormName(),
			);

			$body["fields"] = $this->formFields;
			$body["meta"] = $this->getFormMeta();
		}else{
			foreach($this->formFields as $index => $field){
				if(empty($field["name"]) === false)
					$name = $field["name"];
				elseif(empty($field["title"]) === false)
					$name = $field["title"];
				else
					$name = $this->getFieldTitle($field) . " " . $index;

				$body[$name] = $field["value"];
			}

			$body["form_name"] = $this->getFormName();
		}

		$webhookFields = array(
			"url" => $url,
			"mode" => $mode,
			"body" => $body,
		);

		$webhookFields = $this->applyActionFieldsFilter($action, $webhookFields);

		return $webhookFields;
	}

	/**
	 * get redirect fields
	 */
	private function getRedirectFields(){
		
		$url = UniteFunctionsUC::getVal($this->formSettings, "redirect_url");
		$url = $this->addFormFieldsValueToRedirectUrl($url);
		$url = esc_url_raw($url);

		$redirectFields = array(
			"url" => $url,
		);

		return $redirectFields;
	}

	/**
	 * add form fields to redirect url
	 */
	private function addFormFieldsValueToRedirectUrl($url) {
		
		$hasPlaceholders = preg_match('/\{(.*?)\}/', $url);
		if (!$hasPlaceholders)
			return $url;

		$formFields = $this->formFields;

		$fieldValues = array();
		foreach ($formFields as $field) {
			$fieldValues[$field['name']] = $field['value'];
		}

		preg_match_all('/\{(.*?)\}/', $url, $matches);
		$fieldNamesInUrl = $matches[1];


		$searchStrings = array();
		$replaceValues = array();

		foreach ($fieldNamesInUrl as $fieldName) {
			$searchStrings[] = '{' . $fieldName . '}';

			if (isset($fieldValues[$fieldName]) && !empty($fieldValues[$fieldName])) {
				$replaceValues[] = $fieldValues[$fieldName];
			} else {
				$replaceValues[] = '';
			}
		}

		$updatedUrl = str_replace($searchStrings, $replaceValues, $url);

		return $updatedUrl;
	}

	/**
	 * send to google sheets
	 */
	private function sendToGoogleSheets($spreadsheetFields){

		$services = new UniteServicesUC();
		$services->includeGoogleAPI();

		$sheetsService = new UEGoogleAPISheetsService();
		$sheetsService->setAccessToken(UEGoogleAPIHelper::getFreshAccessToken());

		$headersRow = array();
		$emptyRow = array();
		$valuesRow = array();

		foreach($spreadsheetFields["headers"] as $value){
			$cell = $sheetsService->prepareCellData($value);
			$cell = $sheetsService->applyBoldFormatting($cell);

			$headersRow[] = $cell;
			$emptyRow[] = $sheetsService->prepareCellData("");
		}

		foreach($spreadsheetFields["values"] as $value){
			$valuesRow[] = $sheetsService->prepareCellData($value);
		}

		$headersRow = $sheetsService->prepareRowData($headersRow);
		$emptyRow = $sheetsService->prepareRowData($emptyRow);
		$valuesRow = $sheetsService->prepareRowData($valuesRow);

		
		$headersRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 0, 1, array($headersRow));
		$emptyRowRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 1, 2, array($emptyRow));
		$insertRowRequest = $sheetsService->getInsertDimensionRequest($spreadsheetFields["sheet_id"], 2, 3);
		$valuesRequest = $sheetsService->getUpdateCellsRequest($spreadsheetFields["sheet_id"], 2, 3, array($valuesRow));

		// Flow:
		// - override the 1st row with headers
		// - override the 2nd row with empty values for separation
		// - insert the 3d row
		// - update the 3rd row with values
		$sheetsService->batchUpdateSpreadsheet($spreadsheetFields["id"], array(
			$headersRequest,
			$emptyRowRequest,
			$insertRowRequest,
			$valuesRequest,
		));
	}

	/**
	 * get google sheets fields
	 */
	private function getGoogleSheetsFields(){

		$spreadsheetId = UniteFunctionsUC::getVal($this->formSettings, "google_sheets_id");
		$sheetId = UniteFunctionsUC::getVal($this->formSettings, "google_sheets_sheet_id", 0);
		$sheetId = intval($sheetId);

		$headers = array();
		$values = array();

		// Add form fields
		foreach($this->formFields as $index => $field){
			if(empty($field["title"]) === false)
				$title = $field["title"];
			elseif(empty($field["name"]) === false)
				$title = $field["name"];
			else
				$title = $this->getFieldTitle($field) . " " . $index;

			$headers[] = $title;
			$values[] = $field["value"];
		}

		// Add empty column between fields and meta
		$headers[] = "";
		$values[] = "";

		// Add form meta
		$formMeta = $this->getFormMeta();

		unset($formMeta["post_id"]);
		unset($formMeta["user_id"]);

		foreach($formMeta as $key => $value){
			$headers[] = $this->getMetaTitle($key);
			$values[] = $value;
		}

		$spreadsheetFields = array(
			"id" => $spreadsheetId,
			"sheet_id" => $sheetId,
			"headers" => $headers,
			"values" => $values,
		);


		return $spreadsheetFields;
	}

	/**
	 * get hook fields
	 */
	private function getHookFields(){

		$name = UniteFunctionsUC::getVal($this->formSettings, "hook_name");
		
		$hookFields = array(
			"name" => $name,
		);

		return $hookFields;
	}

	/**
	 * execute custom action
	 */
	private function executeCustomAction($name){
		
		$hookName = self::HOOK_NAMESPACE . "/$name";
		
		do_action($hookName, $this->formFields, $this->formSettings);
	}
	
	/**
	 * execute form action
	 */
	private function executeFormAction($name){
		
		do_action(self::HOOK_NAMESPACE . "/$name", $this->formFields, $this->formSettings);
	}

	/**
	 * apply action fields filter
	 */
	private function applyActionFieldsFilter($action, $fields){

		$fields = apply_filters(self::HOOK_NAMESPACE . "/{$action}_fields", $fields, $this->formFields, $this->formSettings);

		return $fields;
	}

	
	private function ________SUBMIT_________(){}
	
	
	/**
	 * submit form
	 */
	public function submitFormFront(){
		
		$formData = UniteFunctionsUC::getPostGetVariable("formData", null, UniteFunctionsUC::SANITIZE_NOTHING);
		$formFiles = UniteFunctionsUC::getFilesVariable("formFiles");
		$formId = UniteFunctionsUC::getPostGetVariable("formId", null, UniteFunctionsUC::SANITIZE_KEY);
		$postId = UniteFunctionsUC::getPostGetVariable("postId", null, UniteFunctionsUC::SANITIZE_ID);
		$templateId = UniteFunctionsUC::getPostGetVariable("templateId", null, UniteFunctionsUC::SANITIZE_ID);
		
		$recaptchaToken = UniteFunctionsUC::getPostGetVariable("recaptcha_token", "", UniteFunctionsUC::SANITIZE_NOTHING);
		$honeyPotVal = UniteFunctionsUC::getPostGetVariable("honeypot_val", "", UniteFunctionsUC::SANITIZE_NOTHING);
				
		UniteFunctionsUC::validateNotEmpty($formId, "form id");
		UniteFunctionsUC::validateNumeric($postId, "post id");

		if(empty($formData) === true)
			UniteFunctionsUC::throwError("No form data found.");

		$postContent = HelperProviderCoreUC_EL::getElementorContentByPostID($postId);

		if(empty($postContent))
			UniteFunctionsUC::throwError("Form elementor content not found.");

		$templateContent = null;

		if(empty($templateId) === false){
			$templateContent = HelperProviderCoreUC_EL::getElementorContentByPostID($templateId);

			if(empty($templateContent) === true)
				UniteFunctionsUC::throwError("Template elementor content not found.");
		}
		
		$addonForm = HelperProviderCoreUC_EL::getAddonWithDataFromContent($postContent, $formId);

		$formSettings = $addonForm->getProcessedMainParamsValues();
		$formFields = $this->getFieldsData($templateContent ?: $postContent, $formData, $formFiles);
						
		if(!empty($recaptchaToken))
			$formSettings["recaptcha_token"] = $recaptchaToken;

		if(!empty($honeyPotVal))
			$formSettings["honeypot_value"] = $honeyPotVal;
		
			
		$this->doSubmitActions($formSettings, $formFields);
	}
	
	
	/**
	 * do submit actions
	 */
	private function doSubmitActions($formSettings, $formFields){

		$this->formSettings = $formSettings;
		$this->formFields = $formFields;
		
		$data = array();
		$errors = array();
		$debugData = array();
		$debugMessages = array();
		
		
		try{
			$debugMessages[] = "Form has been received.";
	
			// Validate form settings
			$formErrors = $this->validateFormSettings($this->formSettings);

			if(empty($formErrors) === false){
				$errors = array_merge($errors, $formErrors);

				$formErrors = implode(" ", $formErrors);

				UniteFunctionsUC::throwError("Form settings validation failed ($formErrors).");
			}

			// Check for spam
			$isSpam = $this->detectFormSpam();
			
			if($isSpam === true){
				$spamError = $this->getSpamErrorMessage();
				
				$saveError = $this->lastSpamError;
				if(empty($saveError))
					$saveError = $spamError;
					
				$errors[] = $saveError;
				
				UniteFunctionsUC::throwError($spamError, self::ERROR_CODE_SPAM);
			}
			
						
			// Validate form fields
			$fieldsErrors = $this->validateFormFields($this->formFields);
			
			if(empty($fieldsErrors) === false){
				
				$errors = array_merge($errors, $fieldsErrors);

				$validationError = $this->getValidationErrorMessage($fieldsErrors);

				UniteFunctionsUC::throwError($validationError, self::ERROR_CODE_VALIDATION);
			}

			// Upload form files
			$filesErrors = $this->uploadFormFiles();

			if(empty($filesErrors) === false){
				$errors = array_merge($errors, $filesErrors);

				UniteFunctionsUC::throwError("Form upload failed.");
			}

			// Process form actions
			$formActions = UniteFunctionsUC::getVal($this->formSettings, "form_actions");
			$actionsErrors = array();

			foreach($formActions as $action){
				try{
					$this->executeFormAction("before_{$action}_action");

					switch($action){
						case self::ACTION_SAVE:
							$this->createFormEntry();

							$debugMessages[] = "Form entry has been successfully created.";
						break;

						case self::ACTION_EMAIL:
						case self::ACTION_EMAIL2:
							
							$emailFields = $this->getEmailFields($action);
							
							$debugData[$action] = $emailFields;

							$this->sendEmail($emailFields);

							$emails = implode(", ", $emailFields["to"]);

							$debugMessages[] = "Email has been successfully sent to $emails.";
						break;

						case self::ACTION_WEBHOOK:
						case self::ACTION_WEBHOOK2:
							$webhookFields = $this->getWebhookFields($action);

							$debugData[$action] = $webhookFields;

							$this->sendWebhook($webhookFields);

							$debugMessages[] = "Webhook has been successfully sent to {$webhookFields["url"]}.";
						break;

						case self::ACTION_REDIRECT:
							$redirectFields = $this->getRedirectFields();

							$data["redirect"] = $redirectFields["url"];
							$debugData[$action] = $redirectFields["url"];

							$debugMessages[] = "Redirecting to {$redirectFields["url"]}.";
						break;

						case self::ACTION_GOOGLE_SHEETS:
							$spreadsheetFields = $this->getGoogleSheetsFields();

							$debugData[$action] = $spreadsheetFields;

							$this->sendToGoogleSheets($spreadsheetFields);

							$debugMessages[] = "Data has been successfully sent to Google Sheets.";
						break;

						case self::ACTION_HOOK:
							
							$hookFields = $this->getHookFields();

							$debugData[$action] = $hookFields;
							
							$customActionName = $hookFields["name"];
							
							$this->executeCustomAction($customActionName);

							$debugMessages[] = "Hook: $customActionName has been successfully executed.";
						break;

						case self::ACTION_MAILPOET:

							$mailPoetMessage = $this->mailPoetService();

							$debugMessages[] = $mailPoetMessage;

							break;

						default:
							UniteFunctionsUC::throwError("Form action \"$action\" is not implemented.");
					}

					$this->executeFormAction("after_{$action}_action");
				}catch(Exception $exception){
					$errorMessage = "{$this->getActionTitle($action)}: {$exception->getMessage()}";
					$debugType = UniteFunctionsUC::getVal($this->formSettings, "debug_type");

					if($debugType === "full")
						$errorMessage .= "<pre>{$exception->getTraceAsString()}</pre>";

					$actionsErrors[] = $errorMessage;
				}
			}

			if(empty($actionsErrors) === false){
				
				$errors = array_merge($errors, $actionsErrors);
				
				$actionsErrors = implode(" ", $actionsErrors);

				UniteFunctionsUC::throwError("Form actions failed ($actionsErrors).");
			}

			$success = true;
			$message = $this->getFormSuccessMessage();
		}catch(Exception $exception){
			
			$success = false;
			$message = $this->getFormErrorMessage();

			$preserveMessageErrorCodes = array(
				self::ERROR_CODE_VALIDATION,
				self::ERROR_CODE_SPAM,
			);

			if(in_array($exception->getCode(), $preserveMessageErrorCodes) === true)
				$message = $exception->getMessage();
			
			$errors[] = $exception->getMessage();
				
			$debugMessages[] = $exception->getMessage();
		}

		$this->createFormLog($debugMessages);

		$isDebug = UniteFunctionsUC::getVal($this->formSettings, "debug_mode");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		if($isDebug === true){
			$debugMessage = implode(" ", $debugMessages);
			$debugType = UniteFunctionsUC::getVal($this->formSettings, "debug_type");

			$data["debug"] = "<p><b>DEBUG:</b> $debugMessage</p>";

			if($debugType === "full"){
				$debugData["errors"] = $errors;				
				$debugData["fields"] = $this->formFields;
				if(!empty($this->recaptchaDebug))
					$debugData["recaptcha"] = $this->recaptchaDebug;
				$debugData["settings"] = $this->formSettings;
				
				$debugData = json_encode($debugData, JSON_PRETTY_PRINT);
				$debugData = esc_html($debugData);

				$data["debug"] .= "<pre>$debugData</pre>";
			}
		}

		HelperUC::ajaxResponse($success, $message, $data);
	}
	
	
	private function ________ANTISPAM_________(){}
	
	
	/**
	 * gedect from antispam
	 */
	private function detectSpamFromAntispam(){
		
		$antispamSettings = $this->getAntispamSettings();

		// check if anti-spam is enabled
		if($antispamSettings["enabled"] === false)
			return false;

		$userIp = UniteFunctionsUC::getUserIp();
		$currentTime = current_time("timestamp");

		// get blocks
		$antispamBlocks = $this->getAntispamBlocks();
		$userBlockTime = UniteFunctionsUC::getVal($antispamBlocks, $userIp, 0);

		// check if the user is blocked
		if($userBlockTime + $antispamSettings["block_period"] > $currentTime)
			return true;

		// get submissions
		$antispamSubmissions = $this->getAntispamSubmissions();
		$userSubmissions = UniteFunctionsUC::getVal($antispamSubmissions, $userIp, array());

		// check if the user has reached the submissions limit
		if(count($userSubmissions) >= $antispamSettings["submissions_limit"]){
			$lastSubmissionTime = end($userSubmissions);

			// check if the user's last submission is within the period
			if($lastSubmissionTime + $antispamSettings["submissions_period"] > $currentTime){
				// block the user
				$antispamBlocks[$userIp] = $currentTime;

				$this->saveAntispamBlocks($antispamBlocks, $antispamSettings["block_period"]);

				return true;
			}

			// reset the user submissions
			$userSubmissions = array();
		}

		// save the user submission
		$userSubmissions[] = $currentTime;
		$antispamSubmissions[$userIp] = $userSubmissions;

		$this->saveAntispamSubmissions($antispamSubmissions, $antispamSettings["submissions_period"]);

		return false;
	}
	
	
	/**
	 * detect the honeypot
	 */
	private function detectSpamFromHoneypot(){
		
		$enableHoneypot = UniteFunctionsUC::getVal($this->formSettings, "enable_honeypot");
		$enableHoneypot = UniteFunctionsUC::strToBool($enableHoneypot);
		
		if($enableHoneypot == false)
			return(false);
		
		$honeyPotValue = UniteFunctionsUC::getVal($this->formSettings, "honeypot_value");
		
		if(empty($honeyPotValue))
			return(false);
		
		$this->lastSpamError = __("Honey Pot Value Detected", "unlimited-elements-for-elementor");
		
		return(true);
	}
	
	
	/**
	 * detect the recaptcha
	 * todo: Finish this function
	 */
	private function detectSpamRecaptcha(){
		
		$recaptchaExists = UniteFunctionsUC::getVal($this->formSettings, "add_recaptcha_protection");
		$recaptchaExists = UniteFunctionsUC::strToBool($recaptchaExists);
		
		if($recaptchaExists == false)
			return(false);
		
	    $siteKey = HelperProviderCoreUC_EL::getGeneralSetting("recaptcha_site_key");
	    $secretKey = HelperProviderCoreUC_EL::getGeneralSetting("recaptcha_secret_key");
	
	    if (empty($siteKey) || empty($secretKey))
	        return false;
		
	    $token = UniteFunctionsUC::getVal($this->formSettings, "recaptcha_token");
	    
	    //get the trashold
	    $threshold = UniteFunctionsUC::getVal($this->formSettings, "recaptcha_threshold",0.5);
	    
	    if(is_numeric($threshold) == false || $threshold < 0 && $threshold > 1)
	    	$threshold = 0.5;
	    
	    if (empty($token)) {
	    	
	    	$this->lastSpamError = __("No recaptcha token recieved","unlimited-elements-for-elementor");
	    	
	        return true; // No token -> suspicious!
	    }
	    
	    // Verify token with Google
	    $args = array(
	        "body" => array(
	            "secret" => $secretKey,
	            "response" => $token,
	            "remoteip" => UniteFunctionsUC::getUserIp(),
	        ),
	        "timeout" => 20,
	    );
	    
	    $url = "https://www.google.com/recaptcha/api/siteverify";
	    
	    $response = wp_remote_post($url, $args);
	    
	    if (is_wp_error($response)) {
	    	
	    	$this->lastSpamError = $response->get_error_message();
	    	
	        return true; // Request failed, treat as spam
	    }
	
	    $body = wp_remote_retrieve_body($response);
	    $result = json_decode($body, true);
		
	    $errorPrefix = __("reCAPTCHA error(s):","unlimited-elements-for-elementor");
	    
	    $this->recaptchaDebug = array();
	    $this->recaptchaDebug["threshold"] = $threshold;
	    $this->recaptchaDebug["result"] = $result;
	    
	    
	    if (empty($result["success"]) || $result["success"] !== true) {

		    if (!empty($result["error-codes"])) {
	        	$this->lastSpamError =  $errorPrefix . implode(", ", $result["error-codes"]);
	    	} else {
	        	$this->lastSpamError =  __("reCAPTCHA verification failed.","unlimited-elements-for-elementor");
	    	}
    	    
	        return true; // Verification failed
	    }
	
	    $score = floatval(UniteFunctionsUC::getVal($result, "score", 0));
		
	    if ($score < $threshold) {
	       	
	    	$this->lastSpamError = $errorPrefix.__("score too low: $score, the threshold is: $threshold","unlimited-elements-for-elementor");
	    	
	        return true; // Score too low, treat as spam
	    }
		
	    return false; // Passed		
	}
	
	
	/**
	 * detect form spam
	 */
	private function detectFormSpam(){
		
		$isHoneypotSpam = $this->detectSpamFromHoneypot();
		
		if($isHoneypotSpam == true)
			return(true);
		
		$isRecaptchaSpam = $this->detectSpamRecaptcha();
		
		if($isRecaptchaSpam == true)
			return(true);
			
		$isSpam = $this->detectSpamFromAntispam();
		
		if($isSpam)
			return(true);
		
		return(false);
	}
	
	private function ________GETTERS_________(){}
	
	/**
	 * get form name
	 */
	private function getFormName(){

		return $this->formSettings["form_name"] ?: __("Unnamed", "unlimited-elements-for-elementor");
	}

	/**
	 * get form meta
	 */
	private function getFormMeta(){

		if($this->formMeta === null)
			$this->formMeta = array(
				"post_id" => get_the_ID(),
				"post_title" => get_the_title(),
				"post_url" => get_permalink(),
				"user_id" => get_current_user_id(),
				"user_ip" => UniteFunctionsUC::getUserIp(),
				"user_agent" => UniteFunctionsUC::getUserAgent(),
				"created_at" => current_time("mysql"),
			);

		return $this->formMeta;
	}

	/**
	 * get form message
	 */
	private function getFormMessage($key, $fallback){

		$message = UniteFunctionsUC::getVal($this->formSettings, $key);

		if(empty($message) === true)
			$message = $fallback;

		return $message;
	}

	/**
	 * get form success message
	 */
	private function getFormSuccessMessage(){

		$fallback = __("Your submission has been received!", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("success_message", $fallback);
		$message = esc_html($message);

		return $message;
	}

	/**
	 * get form error message
	 */
	private function getFormErrorMessage(){
		
		$fallback = __("Oops! Something went wrong, please try again later.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("error_message", $fallback);
		$message = esc_html($message);

		return $message;
	}

	/**
	 * get spam error message
	 */
	private function getSpamErrorMessage(){
		
		$fallback = __("Something went wrong, please try again later.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("spam_error_message", $fallback);
		$message = esc_html($message);
		
		return $message;
	}

	/**
	 * get validation error message
	 */
	private function getValidationErrorMessage($errors){

		$fallback = __("Please correct the following errors:", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("validation_error_message", $fallback);
		$message = esc_html($message);
		$message .= "<br />- " . implode("<br />- ", $errors);

		return $message;
	}

	/**
	 * get field empty error message
	 */
	private function getFieldEmptyErrorMessage(){

		$fallback = __("The field is empty.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("field_empty_error_message", $fallback);

		return $message;
	}

	/**
	 * get file upload error message
	 */
	private function getFileUploadErrorMessage(){

		$fallback = __("The file upload failed.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("file_upload_error_message", $fallback);

		return $message;
	}

	/**
	 * get file type error message
	 */
	private function getFileTypeErrorMessage($extensions){
		// translators: %s is file type
		$fallback = __("The file must be of type: %s.", "unlimited-elements-for-elementor");
		$message = $this->getFormMessage("file_type_error_message", $fallback);
		$message = sprintf($message, implode(", ", $extensions));

		return $message;
	}

	/**
	 * get field key
	 */
	private function getFieldKey($fieldName, $fieldPrefix){

		$fieldKey = $fieldPrefix . "_" . $fieldName;

		return $fieldKey;
	}

	/**
	 * get field title
	 */
	private function getFieldTitle($field){

		return $field["title"] ?: __("Untitled", "unlimited-elements-for-elementor");
	}
	
	/**
	 * get field error
	 */
	private function formatFieldError($field, $error){

		return sprintf(esc_html("%s: %s"), $this->getFieldTitle($field), $error);
	}

	/**
	 * get action title
	 */
	private function getActionTitle($key){

		$titles = array(
			self::ACTION_EMAIL => __("Email", "unlimited-elements-for-elementor"),
			self::ACTION_EMAIL2 => __("Email 2", "unlimited-elements-for-elementor"),
			self::ACTION_WEBHOOK => __("Webhook", "unlimited-elements-for-elementor"),
			self::ACTION_WEBHOOK2 => __("Webhook 2", "unlimited-elements-for-elementor"),
			self::ACTION_REDIRECT => __("Redirect", "unlimited-elements-for-elementor"),
			self::ACTION_GOOGLE_SHEETS => __("Google Sheets", "unlimited-elements-for-elementor"),
			self::ACTION_HOOK => __("WordPress Hook", "unlimited-elements-for-elementor"),
		);

		return UniteFunctionsUC::getVal($titles, $key, $key);
	}

	/**
	 * get meta title
	 */
	private function getMetaTitle($key){

		$titles = array(
			"post_id" => "Page ID",
			"post_title" => "Page Title",
			"post_url" => "Page Link",
			"user_id" => "User ID",
			"user_ip" => "User IP",
			"user_agent" => "User Agent",
			"created_at" => "Creation Date",
		);

		return UniteFunctionsUC::getVal($titles, $key, $key);
	}

	/**
	 * get placeholder replacement
	 */
	private function getPlaceholderReplace($placeholder){

		switch($placeholder){
			case self::PLACEHOLDER_ADMIN_EMAIL:
				return get_bloginfo("admin_email");

			case self::PLACEHOLDER_EMAIL_FIELD:
				foreach($this->formFields as $field){
					$validEmail = UniteFunctionsUC::isEmailValid($field["value"]);

					if($validEmail === true)
						return $field["value"];
				}

				return "";
			break;
			case self::PLACEHOLDER_SITE_NAME:
				return get_bloginfo("name");
			break;
			case self::PLACEHOLDER_PAGE_URL:
				
				$urlPage = UniteFunctionsWPUC::getUrlCurrentPage();
				
			return($urlPage);
			break;
			case self::PLACEHOLDER_PAGE_TITLE:
			    return get_the_title();
    		break;
			default:
				return "";
		}
		
		
	}
	
	/**
	 * clear placeholders - content within curly braces
	 */
	private function clearPlaceholders($str) {
		
	    return preg_replace('/\{[^}]*\}/', '', $str);
	}
	
	/**
	 * get email fields
	 */
	private function getEmailFields($action){
	
		$from = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("from", $action));
		$from = $this->replacePlaceholders($from, array(self::PLACEHOLDER_ADMIN_EMAIL));
		
		$fromName = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("from_name", $action));
		$fromName = $this->replaceTitlePlaceholders($fromName);
		
		$replyTo = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("reply_to", $action));
		$replyTo = $this->replacePlaceholders($replyTo, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		
		$to = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("to", $action));

		if($to === "custom")
			$to = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("custom_to", $action));

		$to = $this->replacePlaceholders($to, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		$to = $this->prepareEmailRecipients($to);

		$cc = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("cc", $action));
		$cc = $this->replacePlaceholders($cc, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		$cc = $this->prepareEmailRecipients($cc);

		$bcc = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("bcc", $action));
		$bcc = $this->replacePlaceholders($bcc, array(self::PLACEHOLDER_ADMIN_EMAIL, self::PLACEHOLDER_EMAIL_FIELD));
		
		$bcc = $this->prepareEmailRecipients($bcc);
		
		$subject = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("subject", $action));
		$subject = $this->replaceTitlePlaceholders($subject);
		
		$subject = html_entity_decode($subject, ENT_QUOTES | ENT_HTML5);
		
		$message = UniteFunctionsUC::getVal($this->formSettings, $this->getFieldKey("message", $action));
		$message = $this->prepareEmailMessageField($message);
		
		$emailFields = array(
			"from" => $from,
			"from_name" => $fromName,
			"reply_to" => $replyTo,
			"to" => $to,
			"cc" => $cc,
			"bcc" => $bcc,
			"subject" => $subject,
			"message" => $message,
			"headers" => array(),
			"attachments" => array(),
		);
		
		$emailFields = $this->applyActionFieldsFilter($action, $emailFields);

		$emailFields["headers"] = array_merge($this->prepareEmailHeaders($emailFields), $emailFields["headers"]);
		
		return $emailFields;
	}
	
	
	/**
	 * replace placeholders
	 */
	private function replacePlaceholders($value, $placeholders, $additionalReplaces = array()){

		foreach($placeholders as $placeholder){
			if(isset($additionalReplaces[$placeholder]) === true)
				$replace = $additionalReplaces[$placeholder];
			else
				$replace = $this->getPlaceholderReplace($placeholder);

			$value = $this->replacePlaceholder($value, $placeholder, $replace);
		}

		return $value;
	}

	/**
	 * replace placeholder
	 */
	private function replacePlaceholder($value, $placeholder, $replace){

		$value = str_replace("{{$placeholder}}", $replace, $value);

		return $value;
	}

	/**
	 * prepare files field allowed types
	 */
	private function prepareFilesFieldAllowedTypes($fieldSettings){

		$allowedTypes = UniteFunctionsUC::getVal($fieldSettings, "allowed_types", array());
		$customAllowedTypes = UniteFunctionsUC::getVal($fieldSettings, "custom_allowed_types");
		$customAllowedTypes = strtolower($customAllowedTypes);
		$customAllowedTypes = explode(",", $customAllowedTypes);
		$customAllowedTypes = array_map("trim", $customAllowedTypes);
		$customAllowedTypes = array_filter($customAllowedTypes);
		$customAllowedTypes = array_unique($customAllowedTypes);

		$typesMap = array(
			"archives" => array("tar", "zip", "gz", "gzip", "rar", "7z"),
			"audios" => array("mp3", "aac", "wav", "ogg", "flac", "wma"),
			"documents" => array("txt", "csv", "tsv", "pdf", "doc", "docx", "pot", "potx", "pps", "ppsx", "ppt", "pptx", "xls", "xlsx", "odt", "odp", "ods", "key", "pages"),
			"images" => array("jpeg", "jpg", "png", "tif", "tiff", "svg", "webp", "gif", "bmp", "ico", "heic"),
			"videos" => array("wmv", "avi", "flv", "mov", "mpeg", "mp4", "ogv", "webm", "3gp", "3gpp"),
			"custom" => $customAllowedTypes,
		);

		// merge wp mime types with the plugin mimes (in case of missing one)
		// format: extension => mime
		$mimes = array_merge(wp_get_mime_types(), array(
			"svg" => "image/svg+xml",
		));

		$types = array();

		foreach($allowedTypes as $type){
			if(isset($typesMap[$type]) === false)
				UniteFunctionsUC::throwError("File type \"$type\" is not implemented.");

			foreach($typesMap[$type] as $extension){
				$result = wp_check_filetype("temp.$extension", $mimes);

				if($result["ext"] !== false && $result["type"] !== false)
					$types[$result["ext"]] = $result["type"];
			}
		}

		return $types;
	}

	/**
	 * encode files field value
	 */
	private function encodeFilesFieldValue($urls){

		$value = implode(", ", $urls);

		return $value;
	}

	/**
	 * decode files field value
	 */
	public function decodeFilesFieldValue($value){

		$urls = explode(", ", $value);
		$urls = array_filter($urls);

		return $urls;
	}

	/**
	 * get files field links html
	 */
	public function getFilesFieldLinksHtml($value, $separator = ", ", $withDownload = false){

		$urls = $this->decodeFilesFieldValue($value);

		if(empty($urls) === true)
			return "";

		$links = array();

		foreach($urls as $url){
			$href = esc_attr($url);
			$label = esc_html(basename($url));
			$link = "<a href=\"$href\" target=\"_blank\">$label</a>";

			if($withDownload === true)
				$link .= "<a href=\"$href\" target=\"_blank\" download><i class=\"dashicons dashicons-download\"></i></a>";

			$links[] = $link;
		}

		$links = implode($separator, $links);

		return $links;
	}

	/**
	 * get anti-spam settings
	 */
	private function getAntispamSettings(){

		//for local installations always not check spam
		if(GlobalsUC::$isLocal == true)
			return(false);
		
		$enabled = HelperProviderCoreUC_EL::getGeneralSetting("form_antispam_enabled");
		$enabled = UniteFunctionsUC::strToBool($enabled);

		$submissionsLimit = HelperProviderCoreUC_EL::getGeneralSetting("form_antispam_submissions_limit");
		$submissionsLimit = empty($submissionsLimit) === false ? intval($submissionsLimit) : 3; // default is 3
		$submissionsLimit = max($submissionsLimit, 1); // minimum is 1

		$submissionsPeriod = HelperProviderCoreUC_EL::getGeneralSetting("form_antispam_submissions_period");
		$submissionsPeriod = empty($submissionsPeriod) === false ? intval($submissionsPeriod) : 60; // default is 60 seconds
		$submissionsPeriod = max($submissionsPeriod, 1); // minimum is 1 second

		$blockPeriod = HelperProviderCoreUC_EL::getGeneralSetting("form_antispam_block_period");
		$blockPeriod = empty($blockPeriod) === false ? intval($blockPeriod) : 180; // default is 180 minutes
		$blockPeriod = max($blockPeriod * 60, 60); // minimum is 60 seconds

		$settings = array(
			"enabled" => $enabled,
			"submissions_limit" => $submissionsLimit,
			"submissions_period" => $submissionsPeriod,
			"block_period" => $blockPeriod,
		);

		return $settings;
	}

	/**
	 * get anti-spam blocks
	 */
	private function getAntispamBlocks(){

		$blocks = get_option(self::ANTISPAM_BLOCKS_OPTIONS_KEY, array());

		return $blocks;
	}

	/**
	 * save anti-spam blocks
	 */
	private function saveAntispamBlocks($blocks, $preservationTime){

		$currentTime = current_time("timestamp");

		// purge blocks
		foreach($blocks as $ip => $time){
			if($time + $preservationTime <= $currentTime)
				unset($blocks[$ip]);
		}

		update_option(self::ANTISPAM_BLOCKS_OPTIONS_KEY, $blocks);
	}

	/**
	 * get anti-spam submissions
	 */
	private function getAntispamSubmissions(){

		$submissions = get_option(self::ANTISPAM_SUBMISSIONS_OPTIONS_KEY, array());

		return $submissions;
	}

	/**
	 * save anti-spam submissions
	 */
	private function saveAntispamSubmissions($submissions, $preservationTime){

		$currentTime = current_time("timestamp");

		// purge submissions
		foreach($submissions as $ip => $times){
			$newValues = array();

			foreach($times as $time){
				if($time + $preservationTime > $currentTime)
					$newValues[] = $time;
			}

			if(empty($newValues) === true)
				unset($submissions[$ip]);
			else
				$submissions[$ip] = $newValues;
		}

		update_option(self::ANTISPAM_SUBMISSIONS_OPTIONS_KEY, $submissions);
	}

}
