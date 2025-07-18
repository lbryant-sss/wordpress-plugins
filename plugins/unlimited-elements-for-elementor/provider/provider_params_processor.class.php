<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2012 Unite CMS, All Rights Reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;

class UniteCreatorParamsProcessor extends UniteCreatorParamsProcessorWork{
	
	const SHOW_DEBUG_QUERY = false;
	const SHOW_DEBUG_POSTLIST_QUERIES = false;
	
	private static $arrPostTypeTaxCache = array();
	private $arrCurrentPostIDs = array();

	private $arrIncludeDirectChildrenOfSelectedTermsIDs = array();
	private $itemsImageSize = null;
	private $advancedQueryDebug = false;
	private $arrUsersOrder;
	private $skipPostListQueryRun = false;
	private $lastValues = null;
	private $lastName = null;

	
	/**
	 * add other image thumbs based of the platform
	 */
	protected function addOtherImageData($data, $name, $imageID){
		
		if(empty($data))
			$data = array();

		if(is_numeric($imageID) === false)
			return($data);

		$post = get_post($imageID);

		if(empty($post))
			return($data);

		$title = UniteFunctionsWPUC::getAttachmentPostTitle($post);
		$caption = 	$post->post_excerpt;
		$description = 	$post->post_content;

		$alt = UniteFunctionsWPUC::getAttachmentPostAlt($imageID);

		if(empty($alt))
			$alt = $title;

		$data["{$name}_title"] = $title;
		$data["{$name}_alt"] = $alt;
		$data["{$name}_description"] = $description;
		$data["{$name}_caption"] = $caption;
		$data["{$name}_imageid"] = $imageID;

		return($data);
	}


	/**
	 * add other image thumbs based of the platform
	 */
	protected function addOtherImageThumbs($data, $name, $imageID, $filterSizes = null){

		if(empty($data))
			$data = array();

		if(is_numeric($imageID) === false)
			return($data);

		$arrSizes = UniteFunctionsWPUC::getArrThumbSizes();

		$metaData = wp_get_attachment_metadata($imageID);
		$imageWidth = UniteFunctionsUC::getVal($metaData, "width");
		$imageHeight = UniteFunctionsUC::getVal($metaData, "height");

		$urlFull = UniteFunctionsWPUC::getUrlAttachmentImage($imageID);

		$data["{$name}_width"] = $imageWidth;
		$data["{$name}_height"] = $imageHeight;

		$metaSizes = UniteFunctionsUC::getVal($metaData, "sizes");
		
		foreach($arrSizes as $size => $sizeTitle){

			if(empty($size))
				continue;

			if($size == "full")
				continue;

			if(!empty($filterSizes) && array_search($size, $filterSizes) === false)
				continue;

			//change the hypen to underscore
			$thumbName = $name."_thumb_".$size;
			if($size == "medium" && empty($filterSizes))
				$thumbName = $name."_thumb";

			$thumbName = str_replace("-", "_", $thumbName);

			if(isset($data[$thumbName]))
				continue;

			$arrSize = UniteFunctionsUC::getVal($metaSizes, $size);

			$thumbWidth = UniteFunctionsUC::getVal($arrSize, "width");
			$thumbHeight = UniteFunctionsUC::getVal($arrSize, "height");

			$thumbWidth = trim($thumbWidth);

			$urlThumb = UniteFunctionsWPUC::getUrlAttachmentImage($imageID, $size);
			if(empty($urlThumb))
				$urlThumb = $urlFull;

			if(empty($thumbWidth) && $urlThumb == $urlFull){
				$thumbWidth = $imageWidth;
				$thumbHeight = $imageHeight;
			}

			$data[$thumbName] = $urlThumb;
			$data[$thumbName."_width"] = $thumbWidth;
			$data[$thumbName."_height"] = $thumbHeight;

		}

		return($data);
	}
	

	/**
	 * get post data
	 */
	public function getPostData($postID, $arrPostAdditions = null){

		if(empty($postID))
			return(null);

		$post = get_post($postID);

		if(empty($post))
			return(null);

		try{

			$arrData = $this->getPostDataByObj($post, $arrPostAdditions);

			return($arrData);

		}catch(Exception $e){
			return(null);
		}

	}


	/**
	 * add custom fields to terms array
	 */
	private function addCustomFieldsToTermsArray($arrTermsOutput){

		if(empty($arrTermsOutput))
			return($arrTermsOutput);

		foreach($arrTermsOutput as $index => $term){

			$termID = $term["id"];

			$arrCustomFields = UniteFunctionsWPUC::getTermCustomFields($termID);

			if(empty($arrCustomFields))
				continue;

			$term = array_merge($term, $arrCustomFields);

			$arrTermsOutput[$index] = $term;
		}

		return($arrTermsOutput);
	}


	/**
	 * modify terms array for output
	 */
	public function modifyArrTermsForOutput($arrTerms, $taxonomy = "", $addCustomFields = false, $postType = null){
			
			$isWooCat = false;
			if( ($taxonomy == "product_cat" || $postType == "product") && UniteCreatorWooIntegrate::isWooActive())
				$isWooCat = true;
						
			if(empty($arrTerms))
				return(array());

			$arrOutput = array();

			$index = 0;
			foreach($arrTerms as $slug => $arrTerm){

				$item = array();

				$parentID = UniteFunctionsUC::getVal($arrTerm, "parent_id");
				
				$classAdd = "";
				
				$item["index"] = $index;
				$item["id"] = UniteFunctionsUC::getVal($arrTerm, "term_id");
				$item["slug"] = UniteFunctionsUC::getVal($arrTerm, "slug");
				$item["name"] = UniteFunctionsUC::getVal($arrTerm, "name");
				$item["description"] = UniteFunctionsUC::getVal($arrTerm, "description");
				$item["link"] = UniteFunctionsUC::getVal($arrTerm, "link");
				$item["parent_id"] = $parentID;
				$item["taxonomy"] = UniteFunctionsUC::getVal($arrTerm, "taxonomy");
				
				if(!empty($postType))
					$item["post_type"] = $postType;
				
				$classAddParent = "";
				if(empty($parentID)){
					$classAddParent = "uc-is-parent";
					
					$classAdd .= $classAddParent;
				}
				
				$item["class_add_parent"] = $classAddParent;
				
				$level = UniteFunctionsUC::getVal($arrTerm, "level");
				
				if(!empty($level)){
					$item["level"] = $level;
					
					$classAddLevel = "uc-term-level-".$level;
					
					$item["class_add_level"] = $classAddLevel;
					
					$classAdd .= " $classAddLevel";
				}
				
				$index++;

				$current = UniteFunctionsUC::getVal($arrTerm, "iscurrent");

				$item["iscurrent"] = $current;

				$item["class_selected"] = "";
				if($current == true){
					
					$classSelected = "uc-selected";
					
					$item["class_selected"] = "	$classSelected";
					
					$classAdd .= " $classSelected";
				}
				
				if(isset($arrTerm["count"])){

					if($isWooCat == true){
						$item["num_posts"] = $arrTerm["count"];
						$item["num_products"] = $arrTerm["count"];
					}
					else
						$item["num_posts"] = $arrTerm["count"];
				}
				
				$item["class_add"] = $classAdd;
				
				//get woo data, get term image
				
				if($isWooCat == true){
					
					$thumbID = UniteFunctionsWPUC::getTermImageID($item["id"], "woo_cat");
					
					$hasImage = !empty($thumbID);
					
					$item["has_image"] = $hasImage;
					
					if(!empty($thumbID))
						$item = $this->getProcessedParamsValue_image($item, $thumbID, array("name"=>"image"));
					
				}
								
				$arrOutput[] = $item;
			}

			//add custom fields
			if($addCustomFields == true)
				$arrOutput = $this->addCustomFieldsToTermsArray($arrOutput);

			
			return($arrOutput);
		}

	/**
	 * modify the meta value, process the special keywords
	 */
	private function modifyMetaValueForCompare($metaValue){

		switch($metaValue){
			case "{current_user_id}":
				$userID = get_current_user_id();
				if(empty($userID))
					$userID = "0";

				return($userID);
			break;
		}


		return($metaValue);
	}


	protected function z_______________POSTS_QUERY_CLAUSES____________(){}

	/**
	 * check and if needed start the query clauses modify
	 */
	private function checkModifyQueryClauses($args, $excludeOutofStockVariation, $showDebug){

		$postType = UniteFunctionsUC::getVal($args, "post_type");

		if($postType != "product")
			return(false);
			
		$objWoo = new UniteCreatorWooIntegrate();
		
		$objWoo->checkModifyQueryClauses($args, $excludeOutofStockVariation, $showDebug);
		
	}





	protected function z_______________POSTS____________(){}


	/**
	 * show meta debug if needed
	 */
	private function showPostsDebugMeta($arrPosts, $value, $name){

		if(empty($arrPosts))
			return(false);

		$isDebug = UniteFunctionsUC::getVal($value, $name."_includeby_meta_debug");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		if($isDebug == false)
			return(false);

		foreach ($arrPosts as $post){
			$postID = $post->ID;
			HelperUC::$operations->putPostCustomFieldsDebug($postID);
		}

	}


	/**
	 * get post ids from post meta
	 */
	private function getPostListData_getIDsFromPostMeta($value, $name, $showDebugQuery){
		
		$postIDs = UniteFunctionsUC::getVal($value, $name."_includeby_postmeta_postid");

		$metaName = UniteFunctionsUC::getVal($value, $name."_includeby_postmeta_metafield");

		$errorMessagePrefix = "Get post ids from meta error: ";

		if(empty($metaName)){

				if($showDebugQuery == true)
					dmp($errorMessagePrefix." no meta field selected");

			return(null);
		}

		if(!empty($postIDs)){
			if(is_array($postIDs))
				$postID = $postIDs[0];
			else
				$postID = $postIDs;
		}
		else{		//current post

			$post = get_post();
			if(empty($post)){

				if($showDebugQuery == true)
					dmp($errorMessagePrefix." no post found");
				return(null);
			}

			$postID = $post->ID;
		}

		if(empty($postID)){

			if($showDebugQuery == true)
				dmp($errorMessagePrefix." no post found");

			return(null);
		}

		//show the post title
		if($showDebugQuery == true){

			$post = get_post($postID);
			$title = $post->post_title;
			$postType = $post->post_type;

			dmp("Getting post id's from meta fields from post: <b>$postID - $title ($postType) </b>");
		}

		$arrPostIDs = get_post_meta($postID, $metaName, true);

		if(is_array($arrPostIDs) == false){
			$arrPostIDs = explode(",", $arrPostIDs);
		}

		$isValidIDs = UniteFunctionsUC::isValidIDsArray($arrPostIDs);

		if(empty($arrPostIDs) || $isValidIDs == false){

			if($showDebugQuery){

				$metaKeys = UniteFunctionsWPUC::getPostMetaKeys($postID, null, true);
				if(empty($metaKeys))
					$metaKeys = array();

				dmp($errorMessagePrefix." no post ids found");

				if(array_search($metaName, $metaKeys) === false){
					dmp("maybe you intent to use one of those meta keys:");
					dmp($metaKeys);
				}
			}

			return(array(0));
		}

		if($showDebugQuery == true){
			$strPosts = implode(",", $arrPostIDs);
			dmp("Found post ids : $strPosts");
		}
		
		if(empty($arrPostIDs))
			return(array(0));
		
		return($arrPostIDs);
	}


	/**
	 * get post ids from php function
	 */
	private function getPostListData_getIDsFromPHPFunction($value, $name, $showDebugQuery){
		
		$functionName = UniteFunctionsUC::getVal($value, $name."_includeby_function_name");
				
		$errorTextPrefix = "get post id's by PHP Function error: ";

		if(empty($functionName)){

			if($showDebugQuery)
				dmp($errorTextPrefix."no functon name given");

			return(null);
		}

		if(is_string($functionName) == false)
			return(false);

		if(strpos($functionName, "get") !== 0){

			if($showDebugQuery)
				dmp($errorTextPrefix."function <b>$functionName</b> should start with 'get'. like getMyPersonalPosts()");

			return(null);
		}

		if(function_exists($functionName) == false){
			
			if($showDebugQuery)
				dmp($errorTextPrefix."function <b>$functionName</b> not exists.");

			return(null);
		}

		$argument = UniteFunctionsUC::getVal($value, $name."_includeby_function_addparam");

		$arrIDs = call_user_func_array($functionName, array($argument));
		
		$isValid = UniteFunctionsUC::isValidIDsArray($arrIDs);

		if($isValid == false){

			if($showDebugQuery)
				dmp($errorTextPrefix."function <b>$functionName</b> returns invalid id's array.");

			return(null);
		}

		if($showDebugQuery == true){
			dmp("php function <b>$functionName(\"$argument\")</b> output: ");
			dmp($arrIDs);
		}

		if(empty($arrIDs))
			$arrIDs = array(0);

		return($arrIDs);
	}


	/**
	 * get post category taxonomy
	 */
	private function getPostCategoryTaxonomy($postType){

		if(isset(self::$arrPostTypeTaxCache[$postType]))
			return(self::$arrPostTypeTaxCache[$postType]);

		$taxonomy = "category";

		if($postType == "post" || $postType == "page"){

			self::$arrPostTypeTaxCache[$postType] = $taxonomy;
			return($taxonomy);
		}

		//for woo
		if($postType == "product" && UniteCreatorWooIntegrate::isWooActive()){
			$taxonomy = "product_cat";
			self::$arrPostTypeTaxCache[$postType] = $taxonomy;
			return($taxonomy);
		}

		//search in tax data
		$arrTax = UniteFunctionsWPUC::getPostTypeTaxomonies($postType);

		if(empty($arrTax)){

			self::$arrPostTypeTaxCache[$postType] = $taxonomy;
			return($taxonomy);
		}

		$taxonomy = null;
		foreach($arrTax as $key=>$name){

				$name = strtolower($name);

				if(empty($taxonomy))
					$taxonomy = $key;

				if($name == "category")
					$taxonomy = $key;
		}

		if(empty($taxonomy))
			$taxonomy = "category";

		self::$arrPostTypeTaxCache[$postType] = $taxonomy;

		return($taxonomy);
	}

	/**
	 * get post main category from the list of terms
	 */
	private function getPostMainCategory($arrTerms, $postID){

		//get term data

		if(count($arrTerms) == 1){		//single
			$arrTermData = UniteFunctionsUC::getArrFirstValue($arrTerms);
			return($arrTermData);
		}
		
		$arrMeta = UniteFunctionsWPUC::getPostMeta($postID,true);
		
		$mainCategoryID = UniteFunctionsUC::getVal($arrMeta, "_yoast_wpseo_primary_category");
		
		if(empty($mainCategoryID))
			$mainCategoryID = UniteFunctionsUC::getVal($arrMeta, "rank_math_primary_category");
				
		if(empty($mainCategoryID)){

			unset($arrTerms["uncategorized"]);
			$arrTermData = UniteFunctionsUC::getArrFirstValue($arrTerms);

			return($arrTermData);
		}
		
		
		//get by main category
		
		foreach($arrTerms as $term){

			$termID = UniteFunctionsUC::getVal($term, "term_id");
		
			if($termID == $mainCategoryID)
				return($term);
		}

		unset($arrTerms["uncategorized"]);
		$arrTermData = UniteFunctionsUC::getArrFirstValue($arrTerms);

		return($arrTermData);
	}


	/**
	 * get post category fields
	 * for single category
	 * choose category from list
	 */
	private function getPostCategoryFields($postID, $post){

		//choose right taxonomy
		$postType = $post->post_type;

		$taxonomy = $this->getPostCategoryTaxonomy($postType);

		if(empty($postID))
			return(array());

		$arrTerms = UniteFunctionsWPUC::getPostSingleTerms($postID, $taxonomy);

		//get single category
		if(empty($arrTerms))
			return(array());

		$arrCatsOutput = $this->modifyArrTermsForOutput($arrTerms, $taxonomy);

		$arrTermData = $this->getPostMainCategory($arrTerms, $postID);
		
		$catID = UniteFunctionsUC::getVal($arrTermData, "term_id");

		$urlImage = null;

		$arrCategory = array();
		$arrCategory["category_id"] = $catID;
		$arrCategory["category_name"] = UniteFunctionsUC::getVal($arrTermData, "name");
		$arrCategory["category_slug"] = UniteFunctionsUC::getVal($arrTermData, "slug");
		$arrCategory["category_link"] = UniteFunctionsUC::getVal($arrTermData, "link");

		if($taxonomy == "product_cat")
			$arrCategory["category_image"] = UniteFunctionsWPUC::getProductCatImage($catID);

		$arrCategory["categories"] = $arrCatsOutput;


		return($arrCategory);
	}

	/**
	 * get post featured images id
	 */
	private function getPostFeaturedImageID($postID, $content, $postType = null){

		if($postType == "attachment")
			return($postID);


		$featuredImageID = UniteFunctionsWPUC::getFeaturedImageID($postID);

		//try to get featured image from content
		if(empty($featuredImageID)){

				$imageID = UniteFunctionsWPUC::getFirstImageIDFromContent($content);

				if(!empty($imageID))
					$featuredImageID = $imageID;
		}


		//get first gallery image
		if(empty($featuredImageID) && $postType == "product" && UniteCreatorWooIntegrate::isWooActive()){

			$objWoo = UniteCreatorWooIntegrate::getInstance();
			$featuredImageID = $objWoo->getFirstGalleryImageID($postID);

		}

		return($featuredImageID);
	}


	/**
	 * get post data
	 */
	public function getPostDataByObj($post, $arrPostAdditions = array(), $arrImageSizes = null, $options = array()){

		try{

			if(is_numeric($post))
				$post = get_post($post);

			$arrPost = (array)$post;
			$arrData = array();

			$postID = UniteFunctionsUC::getVal($arrPost, "ID");

			$postTitle = UniteFunctionsUC::getVal($arrPost, "post_title");

			$arrData["id"] = $postID;
			$arrData["title"] = $postTitle;
			$arrData["alias"] = UniteFunctionsUC::getVal($arrPost, "post_name");
			$arrData["author_id"] = UniteFunctionsUC::getVal($arrPost, "post_author");
			$arrData["post_type"] = UniteFunctionsUC::getVal($arrPost, "post_type");
			
			$password = $post->post_password;
			if(!empty($password))
				$content = "";
			else
				$content = UniteFunctionsWPUC::getPostContent($post);
			
			$arrData["content"] = $content;
			
			$link = UniteFunctionsWPUC::getPermalink($post);
			
			
			//post link addition
			
			$postLinkAdd = UniteFunctionsUC::getPostGetVariable("postlinkadd","",UniteFunctionsUC::SANITIZE_TEXT_FIELD);
			
			if(!empty($postLinkAdd))
				$link = UniteFunctionsUC::addUrlParams($link, $postLinkAdd);
			
			$arrData["link"] = $link;

			//link attributes

			$readMoreText = __("Read more about ","unlimited-elements-for-elementor").$postTitle;
			$readMoreText = esc_attr($readMoreText);

			$linkAtrributes = "aria-label=\"{$readMoreText}\" ";

			$arrData["link_attributes"] = $linkAtrributes;
			

			//dynamic popup

			$arrCustomFields = null;

			if(!empty($this->dynamicPopupParams)){

				foreach($this->dynamicPopupParams as $paramDynamic){

					$isDynamicEnabled = UniteFunctionsUC::getVal($paramDynamic, "dynamic_popup_enabled");
					$isDynamicEnabled = UniteFunctionsUC::strToBool($isDynamicEnabled);

					$dynamicSuffix = UniteFunctionsUC::getVal($paramDynamic, "dynamic_popup_suffix");

					if(!empty($dynamicSuffix))
						$dynamicSuffix = "__{$dynamicSuffix}";

					if($isDynamicEnabled == true){
						$dynamicLinkAddClass = " uc-open-popup";
						$dynamicLinkAttr = " href='javascrpit:void(0)' data-post-link='{$link}' data-postid='$postID'";
						$dynamicLinkDivAttr = " data-post-link='{$link}' data-postid='$postID'";
					}
					else{

						$dynamicPopupLink = $link;

						$linkType = UniteFunctionsUC::getVal($paramDynamic, "dynamic_popup_linktype");

						//empty link type

						if($linkType == "empty")
							$dynamicPopupLink = "javascript:void(0)";

						//meta link type
						if($linkType == "meta"){

							$dynamicPopupLink = "javascript:void(0)";

							$linkMetaField = UniteFunctionsUC::getVal($paramDynamic, "dynamic_popup_link_metafield");

							$arrCustomFields = UniteFunctionsWPUC::getPostCustomFields($postID);

							$dynamicPopupLink = UniteFunctionsUC::getVal($arrCustomFields, "cf_".$linkMetaField);

							if(is_string($dynamicPopupLink) == false)
								$dynamicPopupLink = "javascript:void(0)";
							else
								$dynamicPopupLink = filter_var($dynamicPopupLink, FILTER_SANITIZE_URL);
						}

						$dynamicLinkAddClass = "";
						$dynamicLinkAttr = "href='{$dynamicPopupLink}'";
						$dynamicLinkDivAttr = "";
						
					}

					$arrData["dynamic_popup_link_class{$dynamicSuffix}"] = $dynamicLinkAddClass;
					$arrData["dynamic_popup_link_attributes{$dynamicSuffix}"] = $dynamicLinkAttr;
					$arrData["dynamic_popup_div_attributes{$dynamicSuffix}"] = $dynamicLinkDivAttr;
					
				}

			}
			
			//get intro, intro from excerpt - tags not stripped
			$exceprt = UniteFunctionsUC::getVal($arrPost, "post_excerpt");
			
			//sometimes the tags are coming decoded
			
			$exceprt = htmlspecialchars_decode($exceprt);

			$intro = $exceprt;
			$introFull = "";
			
			if(empty($intro)){
				$intro = UniteFunctionsUC::getVal($arrData, "content");
				$intro = wp_strip_all_tags($intro);
			}

			if(!empty($intro)){
				$introFull = $intro;

				//$intro = wp_strip_all_tags($intro, true);
				//may make some errors
				
				$intro = UniteFunctionsUC::truncateString($intro, 100);
			}
			
			$arrData["excerpt"] = $exceprt;
			$arrData["intro"] = $intro;
			$arrData["intro_full"] = $introFull;
			
			//put data
			$strDate = UniteFunctionsUC::getVal($arrPost, "post_date");
			$arrData["date"] = !empty($strDate)?strtotime($strDate):"";
			
			//set modified date - if available
			$arrData["date_modified"] = $arrData["date"];
			$strDateModified = UniteFunctionsUC::getVal($arrPost, "post_modified");
			
			if(!empty($strDateModified)){
				$arrData["date_modified"] = strtotime($strDateModified);
			}
			
			//add parent id
			$arrData["parent_id"] = UniteFunctionsUC::getVal($arrPost, "post_parent");

			//check woo commmerce data
			$postType = UniteFunctionsUC::getVal($arrPost, "post_type");

			if($postType == "product" && UniteCreatorWooIntegrate::isWooActive()){

				$arrWooData = UniteCreatorWooIntegrate::getWooDataByType($postType, $postID);

				if(!empty($arrWooData))
					$arrData = $arrData + $arrWooData;
			}

			if($postType == "attachment")
				$featuredImageID = $postID;
			else
			 $featuredImageID = $this->getPostFeaturedImageID($postID, $content, $postType);



			$isAddImages = true;
			if(isset($options["skip_images"]))
				$isAddImages = false;

			if(!empty($featuredImageID) && $isAddImages == true){

				$imageArgs = array();
				$imageArgs["name"] = "image";

				if(!empty($arrImageSizes)){
					$sizeDesktop = UniteFunctionsUC::getVal($arrImageSizes, "desktop");

					if(!empty($sizeDesktop)){
						$imageArgs["add_image_sizes"] = true;
						$imageArgs["value_size"] = $sizeDesktop;
					}

				}

				$arrData = $this->getProcessedParamsValue_image($arrData, $featuredImageID, $imageArgs);
			}

			//add image id only
			if(!empty($featuredImageID) && $isAddImages == false)
				$arrData["image"] = $featuredImageID;


			if(is_array($arrPostAdditions) == false)
				$arrPostAdditions = array();


			//add custom fields
			foreach($arrPostAdditions as $addition){

				switch($addition){
					case GlobalsProviderUC::POST_ADDITION_CUSTOMFIELDS:

						if(empty($arrCustomFields))
							$arrCustomFields = UniteFunctionsWPUC::getPostCustomFields($postID);

						$arrData = array_merge($arrData, $arrCustomFields);
					break;
					case GlobalsProviderUC::POST_ADDITION_CATEGORY:

						$arrCategory = $this->getPostCategoryFields($postID, $post);

						//HelperUC::addDebug("Get Category For Post: $postID ", $arrCategory);

						$arrData = array_merge($arrData, $arrCategory);

					break;
				}

			}


		}catch(Exception $e){

			$message = $e->getMessage();
			$trace = $e->getTraceAsString();

			$errorMessage = "Get Post Exception: ($postID) ".$message;

			HelperUC::addDebug($errorMessage);

			$arrData = array(
				"error"=>$errorMessage
			);

			dmp($errorMessage);
			//dmp($trace);

			return($arrData);
		}
				
		$arrData = apply_filters("ue_modify_post_data", $arrData);
		
		return($arrData);
	}

	/**
	 * run custom query
	 */
	private function getPostListData_getCustomQueryFilters($args, $value, $name, $data, $checkPro = true){
		
		if($checkPro == true){
		if(GlobalsUC::$isProVersion == false)
			return($args);
		}

		$queryID = UniteFunctionsUC::getVal($value, "{$name}_queryid");
		$queryID = trim($queryID);


		if(empty($queryID))
			return($args);

		$showDebugQuery = UniteFunctionsUC::getVal($value, "{$name}_show_query_debug");
		$showDebugQuery = UniteFunctionsUC::strToBool($showDebugQuery);

		if($showDebugQuery == true)
			dmp("applying custom args filter: $queryID");

		//pass the widget data
		$widgetData = $data;
		unset($widgetData[$name]);

		$args = apply_filters($queryID, $args, $widgetData);

		if($showDebugQuery == true){
			dmp("args after custom query");
			dmp($args);
		}

		return($args);
	}

	/**
	 * get single page query pagination
	 */
	private function getSinglePageQueryCurrentPage(){

		if(is_archive() == true || is_front_page() == true)
			return(false);

		$page = get_query_var("page", null);

		return($page);
	}


	/**
	 * get pagination args from the query
	 */
	private function getPostListData_getPostGetFilters_pagination($args, $value, $name, $data, $param){

		$nameListing = UniteFunctionsUC::getVal($param, "name_listing");

		//check the single page pagination
		$paginationType = UniteFunctionsUC::getVal($value, $name."_pagination_type");

		//get the type in case of listing
		if(empty($paginationType) && !empty($nameListing)){
			$name = $nameListing;
			$paginationType = UniteFunctionsUC::getVal($value, $name."_pagination_type");
		}

		if(empty($paginationType))
			return($args);

		$objFilters = new UniteCreatorFiltersProcess();
		$isFrontAjax = $objFilters->isFrontAjaxRequest();

		if($isFrontAjax == false){

			if(is_archive() == true || is_home() == true)
				return($args);
		}

		$page = get_query_var("page", null);

		if(empty($page)){
			$page = get_query_var("paged", null);
		}

		if(empty($page))
			return($args);

		$postsPerPage = UniteFunctionsUC::getVal($args, "posts_per_page");
		if(empty($postsPerPage))
			return($args);

		$offset = ($page-1)*$postsPerPage;

		$args["offset"] = $offset;

		//save the last page for the pagination output
		GlobalsProviderUC::$lastPostQuery_page = $page;

		return($args);
	}



	/**
	 * add order by
	 */
	private function getPostListData_addOrderBy($filters, $value, $name, $isArgs = false){

		$keyOrderBy = "orderby";
		$keyOrderDir = "orderdir";
		$keyMeta = "meta_key";

		if($isArgs == true){
			$keyOrderDir = "order";
		}

		$orderBy = UniteFunctionsUC::getVal($value, "{$name}_orderby");
		if($orderBy == "default")
			$orderBy = null;

		if(!empty($orderBy))
			$filters[$keyOrderBy] = $orderBy;

		$orderDir = UniteFunctionsUC::getVal($value, "{$name}_orderdir1");
		if($orderDir == "default")
			$orderDir = "";

		if(!empty($orderDir))
			$filters[$keyOrderDir] = $orderDir;

		if($orderBy == UniteFunctionsWPUC::SORTBY_META_VALUE || $orderBy == UniteFunctionsWPUC::SORTBY_META_VALUE_NUM){
			$filters["meta_key"] = UniteFunctionsUC::getVal($value, "{$name}_orderby_meta_key1");
		}

		return($filters);
	}


	/**
	 * get meta values
	 */
	private function getPostListData_metaValues($arrMetaSubQuery, $metaValue, $metaKey, $metaCompare){

		//single - default

		if(strpos($metaValue, "||") === false){

			$arrMetaSubQuery[] = array(
	            'key' => $metaKey,
	            'value' => $metaValue,
				'compare'=>$metaCompare
			);

			return($arrMetaSubQuery);
		}

		$arrValues = explode("||", $metaValue);

		if(empty($arrValues))
			return($arrMetaSubQuery);

		foreach($arrValues as $metaValue){

			$arrMetaSubQuery[] = array(
	            'key' => $metaKey,
	            'value' => $metaValue,
				'compare'=>$metaCompare
			);

		}

		return($arrMetaSubQuery);
	}


	/**
	 * get date query
	 */
	private function getPostListData_dateQuery($value, $name){

		$dateString = UniteFunctionsUC::getVal($value, "{$name}_includeby_date");

		if($dateString == "all")
			return(array());

		$metaField = UniteFunctionsUC::getVal($value, "{$name}_include_date_meta");
		$metaField = trim($metaField);

		$metaFormat = UniteFunctionsUC::getVal($value, "{$name}_include_date_meta_format");

		if(empty($metaFormat))
			$metaFormat = "Ymd";

		$arrDateQuery = array();
		$arrMetaQuery = array();

		$after = "";
		$before = "";
		$year = "";
		$month = "";
		$day = "";

		$afterMeta = null;
		$beforeMeta = null;

		switch($dateString){
			case "today":
				$after = "-1 day";

			break;
			case "this_day":

				if(!empty($metaField)){
					$afterMeta = uelm_date($metaFormat);
					$beforeMeta = uelm_date($metaFormat);
				}else{

					$year = uelm_date("Y");
					$month = uelm_date("m");
					$day = uelm_date("d");

					$arrDateQuery['inclusive'] = true;
				}

			break;
			case "this_week":

				$after = "monday this week";

				$before = "sunday this week";

			break;
			case "next_week":

				$after = "monday next week";

				$before = "sunday next week";

			break;
			case "past_from_today":

				if(!empty($metaField)){
					$beforeMeta = uelm_date($metaFormat);
				}else{

					$before = "tomorrow";

					$arrDateQuery['inclusive'] = true;
				}

			break;
			case "past_from_yesterday":

				if(!empty($metaField)){
					$beforeMeta = uelm_date($metaFormat,strtotime('-1 day'));
				}else{

					$before = "today";

					$arrDateQuery['inclusive'] = false;
				}

			break;
			case "yesterday":
				$after = "-2 day";
				$before = "today";
			break;
			case "week":
				$after = '-1 week';
				$before = "today";
			break;
			case "month":
				$after = "-1 month";
				$before = "today";
			break;
			case "three_months":
				$after = "-3 months";
				$before = "today";
			break;
			case "year":
				$after = "-1 year";
				$before = "today";
			break;
			case "this_month":

				if(!empty($metaField)){

					$afterMeta = uelm_date('Ym01');
					$beforeMeta = uelm_date('Ymt');

				}else{
					$year = uelm_date("Y");
					$month = uelm_date("m");
				}

			break;
			case "next_month":

				if(!empty($metaField)){

					$afterMeta = uelm_date($metaFormat,strtotime('first day of +1 month'));
					$beforeMeta = uelm_date($metaFormat,strtotime('last day of +1 month'));
				}else{

					$time = strtotime('first day of +1 month');

					$year = uelm_date("Y",$time);
					$month = uelm_date("m",$time);
				}

			break;
			case "future":

				if(!empty($metaField)){
					$afterMeta = uelm_date($metaFormat);
				}else{

					$after = "today";

					$arrDateQuery['inclusive'] = true;
				}

			break;
			case "future_tomorrow":

				if(!empty($metaField)){

					$afterMeta = uelm_date($metaFormat,strtotime('+1 day'));
				}else{

					$after = "today";

					$arrDateQuery['inclusive'] = false;
				}

			break;
			case "custom":

				$before = UniteFunctionsUC::getVal($value, "{$name}_include_date_before");

				$after = UniteFunctionsUC::getVal($value, "{$name}_include_date_after");

				if(!empty($before) || !empty($after))
					$arrDateQuery['inclusive'] = true;

			break;
		}

		if(!empty($metaField)){

			if(!empty($after) && empty($afterMeta)){
				$afterMeta = uelm_date($metaFormat, strtotime($after));
			}

			if(!empty($afterMeta))
				$arrMetaQuery[] = array(
		            'key'     => $metaField,
		            'compare' => '>=',
		            'value'   => $afterMeta
        		);

			if(!empty($before) && empty($beforeMeta))
				$beforeMeta = uelm_date($metaFormat, strtotime($before));

			if(!empty($beforeMeta))
				$arrMetaQuery[] = array(
		            'key'     => $metaField,
		            'compare' => '<=',
		            'value'   => $beforeMeta
        		);

		}else{
			if(!empty($before))
				$arrDateQuery["before"] = $before;

			if(!empty($after))
				$arrDateQuery["after"] = $after;

			if(!empty($year))
				$arrDateQuery["year"] = $year;

			if(!empty($month))
				$arrDateQuery["month"] = $month;

			if(!empty($day))
				$arrDateQuery["day"] = $day;

		}


		$response = array();
		if(!empty($arrDateQuery))
			$response["date_query"] = $arrDateQuery;

		if(!empty($arrMetaQuery))
			$response["meta_query"] = $arrMetaQuery;

		return($response);
	}


	/**
	 * get post list data custom from filters
	 */
	private function getPostListData_custom($value, $name, $processType, $param, $data, $nameListing = null){
		
		if(empty($value))
			return(array());

		if(is_array($value) == false)
			return(array());
		
		//validate for empty array
		$purePostType = UniteFunctionsUC::getVal($value, "{$name}_posttype");
		if($purePostType){
			$isEmpty = UniteFunctionsUC::isAllArrayItemsEmpty($value);
			if($isEmpty == true)
				return(array());
		}
		
		$filters = array();

		$showDebugQuery = UniteFunctionsUC::getVal($value, "{$name}_show_query_debug");
		$showDebugQuery = UniteFunctionsUC::strToBool($showDebugQuery);
		
		if(self::SHOW_DEBUG_QUERY == true)
			$showDebugQuery = true;
		
		//show debug by url only for admins
		
		$debugType = null;
		if($showDebugQuery == true)
			$debugType = UniteFunctionsUC::getVal($value, "{$name}_query_debug_type");

		if(self::SHOW_DEBUG_QUERY == true)
			$debugType = "show_query";
		
		if(GlobalsUC::$showQueryDebugByUrl == true){
			$showDebugQuery = true;
			$this->advancedQueryDebug = true;
			$debugType = "show_query";
		}
		
		if($showDebugQuery == true)
			GlobalsProviderUC::$showPostsQueryDebug = true;
		
		$source = UniteFunctionsUC::getVal($value, "{$name}_source");
		
		$isForWoo = UniteFunctionsUC::getVal($param, "for_woocommerce_products");
		$isForWoo = UniteFunctionsUC::strToBool($isForWoo);
		
		//add the include by
		$arrIncludeBy = UniteFunctionsUC::getVal($value, "{$name}_includeby");
		if(empty($arrIncludeBy))
			$arrIncludeBy = array();

		
		//enable filters
		
		$nameForFilter = $name;
		if(!empty($nameListing))
			$nameForFilter = $nameListing;

		$isFilterable = $this->getIsFilterable($value, $nameForFilter);
		
		$isRelatedPosts = $source == "related";
		$relatePostsType = "";
		
		$addParentType = null;
		$addParentIDs = null;

		if(is_singular() == false)
			$isRelatedPosts = false;

		if($isForWoo == true && function_exists("is_checkout") && is_checkout() && $source == "related"){
			$isRelatedPosts = true;
			$relatePostsType = "checkout";
		}


		$arrMetaQuery = array();

		$getRelatedProducts = false;
		
		//get post type
		$postType = UniteFunctionsUC::getVal($value, "{$name}_posttype", "post");
		if($isForWoo)
			$postType = "product";

		$filters["posttype"] = $postType;
		
		$post = null;
		$category = null;

		$allowPostsBySinglePostAuthor = null;

		if($isRelatedPosts == true){

			$post = get_post();
			
			$postType = $post->post_type;

			$allowPostsBySinglePostAuthor = UniteFunctionsUC::getVal($value, $name."_by_single_post_author");
			$allowPostsBySinglePostAuthor = UniteFunctionsUC::strToBool($allowPostsBySinglePostAuthor);

			$allowAnyTypes = UniteFunctionsUC::getVal($value, $name."_allow_custom_post_types_in_related_posts");
			$allowAnyTypes = UniteFunctionsUC::strToBool($allowAnyTypes);
			
			if($allowAnyTypes == true)
				$postType = get_post_types(array('public' => true), 'names');
			
			$filters["posttype"] = $postType;		//rewrite the post type argument
			
			if($postType == "product" || $relatePostsType == "checkout"){

				$getRelatedProducts = true;
				$productID = $post->ID;

				if($relatePostsType == "checkout")
					$filters["posttype"] = "product";		//rewrite the post type argument
		
			}else{

				if($showDebugQuery == true){
					dmp("Related Posts Query");
				}

				
				//prepare terms string
				$arrTerms = UniteFunctionsWPUC::getPostTerms($post);
				
				$strTerms = "";

				$arrRelatedTaxonomies = UniteFunctionsUC::getVal($value, $name."_related_taxonomies");
										
				
				foreach($arrTerms as $tax => $terms){

					if($tax == "product_type")
						continue;

					//filter by only related taxonomies from the settings.

					if(!empty($arrRelatedTaxonomies) && in_array($tax, $arrRelatedTaxonomies) == false)
						continue;

					foreach($terms as $term){
						$termID = UniteFunctionsUC::getVal($term, "term_id");
						$strTerm = "{$tax}--{$termID}";

						if(!empty($strTerms))
							$strTerms .= ",";

						$strTerms .= $strTerm;
					}
				}
				
				//add terms
				if(!empty($strTerms)){
					$filters["category"] = $strTerms;
					
					$relatedMode = UniteFunctionsUC::getVal($value, $name."_related_mode");
					
					$relation = "OR";
					if($relatedMode == "and")
						$relation = "AND";
			
					if($relatedMode == "grouping")
						$relation = "GROUP";
					
					$filters["category_relation"] = $relation;
				}
				
				
				$filters["exclude_current_post"] = true;
			}


		}else{		//if not related posts

			$category = UniteFunctionsUC::getVal($value, "{$name}_category");

			if(!empty($category))
				$filters["category"] = $category;
			

			$termsIncludeChildren = UniteFunctionsUC::getVal($value, "{$name}_terms_include_children");
			$termsIncludeChildren = UniteFunctionsUC::strToBool($termsIncludeChildren);

			if($termsIncludeChildren === true)
				$filters["category_include_children"] = true;
		}

		$limit = UniteFunctionsUC::getVal($value, "{$name}_maxitems");

		$limit = (int)$limit;
		if($limit <= 0)
			$limit = 100;

		if($limit > 1000)
			$limit = 1000;
		

		//------ Exclude ---------

		$arrExcludeBy = UniteFunctionsUC::getVal($value, "{$name}_excludeby", array());
		if(is_string($arrExcludeBy))
			$arrExcludeBy = array($arrExcludeBy);

		if(is_array($arrExcludeBy) == false)
			$arrExcludeBy = array();

		$excludeProductsOnSale = false;
		$excludeSpecificPosts = false;
		$excludeByAuthors = false;
		$arrExcludeTerms = array();
		$offset = null;
		$isAvoidDuplicates = false;
		$arrExcludeIDsDynamic = null;
		$excludeOutofStockVariation = false;
		
		foreach($arrExcludeBy as $excludeBy){

			switch($excludeBy){
				case "out_of_stock_variation":

					$excludeOutofStockVariation = true;

				break;
				case "current_post":
					$filters["exclude_current_post"] = true;
				break;
				case "out_of_stock":
					$arrMetaQuery[] = array(
			            'key' => '_stock_status',
			            'value' => 'instock'
					);
					$arrMetaQuery[] = array(
				            'key' => '_backorders',
				            'value' => 'no'
				    );
				break;
				case "terms":

					$arrTerms = UniteFunctionsUC::getVal($value, "{$name}_exclude_terms");

					$arrExcludeTerms = UniteFunctionsUC::mergeArraysUnique($arrExcludeTerms, $arrTerms);

					$termsExcludeChildren = UniteFunctionsUC::getVal($value, "{$name}_terms_exclude_children");
					$termsExcludeChildren = UniteFunctionsUC::strToBool($termsExcludeChildren);

					$filters["category_exclude_children"] = $termsExcludeChildren;

				break;
				case "products_on_sale":

					$excludeProductsOnSale = true;
				break;
				case "specific_posts":

					$excludeSpecificPosts = true;
				break;
				case "author":

					$excludeByAuthors = true;
				break;
				case "no_image":

					$arrMetaQuery[] = array(
						"key"=>"_thumbnail_id",
						"compare"=>"EXISTS"
					);

				break;
				case "current_category":

					if(empty($post))
						$post = get_post();

					$arrCatIDs = UniteFunctionsWPUC::getPostCategoriesIDs($post);
	
					$arrExcludeTerms = UniteFunctionsUC::mergeArraysUnique($arrExcludeTerms, $arrCatIDs);
				break;
				case "current_tag":

					if(empty($post))
						$post = get_post();

					$arrTagsIDs = UniteFunctionsWPUC::getPostTagsIDs($post);

					$arrExcludeTerms = UniteFunctionsUC::mergeArraysUnique($arrExcludeTerms, $arrTagsIDs);
				break;
				case "offset":

					$offset = UniteFunctionsUC::getVal($value, $name."_offset");
					$offset = (int)$offset;

				break;
				case "avoid_duplicates":

					$isAvoidDuplicates = true;

				break;
				case "ids_from_dynamic":
					
					$arrExcludeIDsDynamic = UniteFunctionsUC::getVal($value, $name."_exclude_dynamic_field");
					$arrExcludeIDsDynamic = UniteFunctionsUC::getIDsArray($arrExcludeIDsDynamic);

				break;
			}

		}

		if(!empty($arrExcludeTerms))
			$filters["exclude_category"] = $arrExcludeTerms;

		//includeby before filters
		foreach($arrIncludeBy as $includeby){
			
			switch($includeby){
				case "terms_from_dynamic":
				case "terms_from_current_meta":
				case "terms_from_user_meta":
										
					$arrTermIDs = array();

					//get term id's

					switch($includeby){
						default:
						case "terms_from_dynamic":
							$strTermIDs = UniteFunctionsUC::getVal($value, $name."_includeby_terms_dynamic_field");
							$arrTermIDs = UniteFunctionsUC::getIDsArray($strTermIDs);
						break;
						case "terms_from_current_meta":
							
							$metaFieldName = UniteFunctionsUC::getVal($value, "{$name}_includeby_terms_from_meta");
							$postID = get_post();
	
							if(!empty($metaFieldName) && !empty($postID)){
	
								$strTermIDs = UniteFunctionsWPUC::getPostCustomField($postID, $metaFieldName);
								$arrTermIDs = UniteFunctionsUC::getIDsArray($strTermIDs);
							}
						break;
						case "terms_from_user_meta":
							
							$metaFieldName = UniteFunctionsUC::getVal($value, "{$name}_includeby_terms_from_user_meta");
							
							//show current user data
							
							if($metaFieldName == "show"){
								
								echo "<hr>";
								
								dmp("Terms From User Meta Debug");
								
								HelperProviderUC::showCurrentUserMetaDataDebug();
								
								echo "<hr>";
								
								$metaFieldName = null;
							}
							
							$userID = get_current_user_id();
							
							if(!empty($userID)){
								$strTermIDs = UniteFunctionsWPUC::getUserCustomFields($userID);
								$arrTermIDs = UniteFunctionsUC::getIDsArray($strTermIDs);
							}
							
						break;
					}
					

					if(!empty($arrTermIDs)){

						$firstID = $arrTermIDs[0];

						//add the taxonomy

						$term = get_term($firstID);

						$taxonomy = null;

						if(!empty($term))
							$taxonomy = $term->taxonomy;

						if($taxonomy != "category"){
							foreach($arrTermIDs as $key => $termID)
								$arrTermIDs[$key] = "{$taxonomy}--{$termID}";
						}

						if(empty($category))
							$category = array();

						$category = array_merge($arrTermIDs, $category);
						$category = array_unique($category);

						$filters["category"] = $category;

					}

				break;
				case "terms_free_selection":
					
					$arrTermIDs = UniteFunctionsUC::getVal($value, $name."_include_terms_freeselect");
					
					if(!empty($arrTermIDs)){
						
						if(empty($category))
							$category = array();

						$category = array_merge($arrTermIDs, $category);
						$category = array_unique($category);

						$filters["category"] = $category;
					}
					
				break;
			}

		}
		
		//set category relation
		
		$relation = UniteFunctionsUC::getVal($value, "{$name}_category_relation");
		 
		if(!empty($relation) && isset($filters["category"]))
			$filters["category_relation"] = $relation;
		
		//set category relation
		
		$filters["limit"] = $limit;

		$filters = $this->getPostListData_addOrderBy($filters, $value, $name);
		
		$orderBy = UniteFunctionsUC::getVal($filters, "orderby");
		
		
		//add debug for further use
		HelperUC::addDebug("Post Filters", $filters);

		//run custom query if available
		$args = UniteFunctionsWPUC::getPostsArgs($filters);

		// include related posts by single post author
		if($allowPostsBySinglePostAuthor == true)
			$args["author__in"] = $post->post_author;
		
		//exclude by authors

		if($excludeByAuthors == true){

			$arrExcludeByAuthors = UniteFunctionsUC::getVal($value, "{$name}_excludeby_authors");

			foreach($arrExcludeByAuthors as $key => $userID){

				if($userID == "uc_loggedin_user"){

					$userID = get_current_user_id();

					if(empty($userID))
						unset($arrExcludeByAuthors[$key]);
					else
						$arrExcludeByAuthors[$key] = $userID;
				}

			}

			if(!empty($arrExcludeByAuthors))
				$args["author__not_in"] = $arrExcludeByAuthors;
		}

		//exclude by specific posts

		$arrPostsNotIn = array();

		if($excludeProductsOnSale == true){

			$arrPostsNotIn = wc_get_product_ids_on_sale();
		}

		if($excludeSpecificPosts == true){

			$specificPostsToExclude = UniteFunctionsUC::getVal($value, "{$name}_exclude_specific_posts");

			if(!empty($specificPostsToExclude)){

				if(empty($arrPostsNotIn))
					$arrPostsNotIn = $specificPostsToExclude;
				else
					$arrPostsNotIn = array_merge($arrPostsNotIn, $specificPostsToExclude);
			}

		}

		//exclude from dynamic field

		if(!empty($arrExcludeIDsDynamic)){

			if(empty($arrExcludeIDsDynamic))
				$arrPostsNotIn = $arrExcludeIDsDynamic;
			else
				$arrPostsNotIn = array_merge($arrPostsNotIn, $arrExcludeIDsDynamic);
		}


		// exclude duplicates
		if($isAvoidDuplicates == true && !empty(GlobalsProviderUC::$arrFetchedPostIDs)){

			$arrFetchedIDs = array_keys(GlobalsProviderUC::$arrFetchedPostIDs);

			if(empty($arrPostsNotIn))
				$arrPostsNotIn = $arrFetchedIDs;
			else
				$arrPostsNotIn = array_merge($arrPostsNotIn, $arrFetchedIDs);

		}

		$args["ignore_sticky_posts"] = true;

		$getOnlySticky = false;
		$checkStickyPostsByPlugin = false;

		$product = null;

		$arrProductsUpSells = array();
		$arrProductsCrossSells = array();
		$arrIDsOnSale = array();
		$arrRecentProducts = array();
		$arrIDsPopular = array();
		$arrIDsPHPFunction = array();
		$arrIDsPostMeta = array();
		$arrIDsDynamicField = array();
		$arrIDsFromContent = array();
		$arrTermIDs = array();

		$currentTaxQuery = null;
		$termsFromCurrentQuery = null;

		$makePostINOrder = false;
		$arrQueryBase = null;

		foreach($arrIncludeBy as $includeby){

			switch($includeby){
				case "sticky_posts":
					$args["ignore_sticky_posts"] = false;

					if($postType != "post")
						$checkStickyPostsByPlugin = true;

				break;
				case "sticky_posts_only":
					$getOnlySticky = true;
				break;
				case "products_on_sale":

					$arrIDsOnSale = wc_get_product_ids_on_sale();

					if(empty($arrIDsOnSale))
						$arrIDsOnSale = array("0");

				break;
				case "up_sells":		//product up sells

					if(empty($product))
						$product = wc_get_product();

					if(!empty($product)){
						$arrProductsUpSells = $product->get_upsell_ids();
						if(empty($arrProductsUpSells))
							$arrProductsUpSells = array("0");
					}

				break;
				case "cross_sells":

					if(empty($product))
						$product = wc_get_product();

					if(!empty($product)){
						$arrProductsCrossSells = $product->get_cross_sell_ids();
						if(empty($arrProductsCrossSells))
							$arrProductsCrossSells = array("0");
					}

				break;
				case "out_of_stock":

					$arrMetaQuery[] = array(
			            'key' => '_stock_status',
			            'value' => 'instock',
						'compare'=>'!='
					);

				break;
				case "products_from_post":		//get products from post content

					$objWoo = new UniteCreatorWooIntegrate();
					$arrIDsFromContent = $objWoo->getProductIDsFromCurrentPostContent();

				break;
				case "author":

					$arrIncludeByAuthors = UniteFunctionsUC::getVal($value, "{$name}_includeby_authors");

					$strAuthorsDynamic = UniteFunctionsUC::getVal($value, "{$name}_includeby_authors_dynamic");

					$arrAuthorsDynamic = UniteFunctionsUC::getIDsArray($strAuthorsDynamic);

					if(empty($arrIncludeByAuthors))
						$arrIncludeByAuthors = array();

					if(!empty($arrAuthorsDynamic))
						$arrIncludeByAuthors = array_merge($arrIncludeByAuthors ,$arrAuthorsDynamic);


					//if set to current user, and no user logged in, then get no posts at all
					$authorMakeZero = false;
					foreach($arrIncludeByAuthors as $key => $userID){

						if($userID == "uc_loggedin_user"){

							$userID = get_current_user_id();
							$arrIncludeByAuthors[$key] = $userID;

							if(empty($userID))
								$authorMakeZero = true;
						}

					}

					if($authorMakeZero == true)
						$arrIncludeByAuthors = array("0");

					if(!empty($arrIncludeByAuthors))
						$args["author__in"] = $arrIncludeByAuthors;

				break;
				case "date":

					$response = $this->getPostListData_dateQuery($value, $name);
					$arrDateQuery = UniteFunctionsUC::getVal($response, "date_query");

					if(!empty($arrDateQuery))
						$args["date_query"] = $arrDateQuery;

					$arrDateMetaQuery = UniteFunctionsUC::getVal($response, "meta_query");
					if(!empty($arrDateMetaQuery))

					$arrMetaQuery = array_merge($arrMetaQuery, $arrDateMetaQuery);

				break;
				case "parent":

					$parent =  UniteFunctionsUC::getVal($value, "{$name}_includeby_parent");
					if(!empty($parent)){

						if(is_array($parent) && count($parent) == 1)
							$parent = $parent[0];

						$addParentType = UniteFunctionsUC::getVal($value, "{$name}_includeby_parent_addparent");

						if($addParentType == "start" || $addParentType == "end")
							$addParentIDs = $parent;

						if(is_array($parent))
							$args["post_parent__in"] = $parent;
						else
							$args["post_parent"] = $parent;
					}
				break;
				case "recent":

					if(isset($_COOKIE["woocommerce_recently_viewed"])){

						$strRecentProducts = $_COOKIE["woocommerce_recently_viewed"];
						$strRecentProducts = trim($strRecentProducts);
						$arrRecentProducts = explode("|", $strRecentProducts);
						
						if(!empty($arrRecentProducts))
							$arrRecentProducts = array_unique($arrRecentProducts);
					}

				break;
				case "meta":

					$metaKey = UniteFunctionsUC::getVal($value, "{$name}_includeby_metakey");
					$metaCompare = UniteFunctionsUC::getVal($value, "{$name}_includeby_metacompare");

					$metaValue = UniteFunctionsUC::getVal($value, "{$name}_includeby_metavalue");
					$metaValue = $this->modifyMetaValueForCompare($metaValue);

					$metaValue2 = UniteFunctionsUC::getVal($value, "{$name}_includeby_metavalue2");
					$metaValue2 = $this->modifyMetaValueForCompare($metaValue2);

					$metaValue3 = UniteFunctionsUC::getVal($value, "{$name}_includeby_metavalue3");
					$metaValue3 = $this->modifyMetaValueForCompare($metaValue3);

					//second key

					$metaAddSecond = UniteFunctionsUC::getVal($value, "{$name}_includeby_meta_addsecond");
					$metaAddSecond = UniteFunctionsUC::strToBool($metaAddSecond);

					$metaKeySecond = UniteFunctionsUC::getVal($value, "{$name}_includeby_second_metakey");
					$metaCompareSecond = UniteFunctionsUC::getVal($value, "{$name}_includeby_second_metacompare");

					$metaValueSecond = UniteFunctionsUC::getVal($value, "{$name}_includeby_second_metavalue");
					$metaValueSecond = $this->modifyMetaValueForCompare($metaValueSecond);

					$metaRelation = UniteFunctionsUC::getVal($value, "{$name}_includeby_meta_relation");
				
					$arrMetaSubQuery = array();
					$arrMetaSubQuery2 = array();

					if(!empty($metaKey)){

						$arrMetaSubQuery = $this->getPostListData_metaValues($arrMetaSubQuery, $metaValue, $metaKey, $metaCompare);

						if(!empty($metaValue2))
							$arrMetaSubQuery = $this->getPostListData_metaValues($arrMetaSubQuery, $metaValue2, $metaKey, $metaCompare);

						if(!empty($metaValue3))
							$arrMetaSubQuery = $this->getPostListData_metaValues($arrMetaSubQuery, $metaValue3, $metaKey, $metaCompare);

						if(count($arrMetaSubQuery) > 1)
							$arrMetaSubQuery["relation"] = "OR";

					}


					if($metaAddSecond == true && !empty($metaKeySecond)){

						$arrMetaSubQuery2[] = array(
				            'key' => $metaKeySecond,
				            'value' => $metaValueSecond,
							'compare'=>$metaCompareSecond
						);

					}


					if(!empty($arrMetaSubQuery) && !empty($arrMetaSubQuery2)){

							if(count($arrMetaSubQuery) == 1){	//both single

								$arrMetaSubQuery[] = $arrMetaSubQuery2[0];
								$arrMetaSubQuery["relation"] = $metaRelation;

								$arrMetaQuery[] = $arrMetaSubQuery;

							}else{							//both - first multiple
								$arrMetaQuery[] = array(
								$arrMetaSubQuery,
								$arrMetaSubQuery2,
								"relation"=>$metaRelation);

							}

					}else{

						if(!empty($arrMetaSubQuery))
							$arrMetaQuery[] = $arrMetaSubQuery;

						if(!empty($arrMetaSubQuery2))
							$arrMetaQuery[] = $arrMetaSubQuery2;
					}


				break;
				case "most_viewed":

					$isWPPPluginExists = UniteCreatorPluginIntegrations::isWPPopularPostsExists();

					if($showDebugQuery == true && $isWPPPluginExists == false){
						dmp("Select Most Viewed posts posible only if you install 'WordPress Popular Posts' plugin. Please install it");
					}

					if($isWPPPluginExists){

						$objIntegrations = new UniteCreatorPluginIntegrations();

						$wppRange = UniteFunctionsUC::getVal($value, "{$name}_includeby_mostviewed_range");

						$wpp_args = array(
							"post_type"=>$postType,
							"limit"=>$limit,
							"range"=>$wppRange
						);

						if(!empty($category))
							$wpp_args["cat"] = $category;

						$response = $objIntegrations->WPP_getPopularPosts($wpp_args, $showDebugQuery);

						$arrIDsPopular = UniteFunctionsUC::getVal($response, "post_ids");

						$debugWPP = UniteFunctionsUC::getVal($response, "debug");

						if($showDebugQuery == true && !empty($debugWPP)){
							dmp("Pupular Posts Data: ");
							dmp($debugWPP);
						}

					}

				break;
				case "php_function":
					
					$arrIDsPHPFunction = $this->getPostListData_getIDsFromPHPFunction($value, $name, $showDebugQuery);
					
					if(empty($arrIDsPHPFunction))
						$arrIDsPHPFunction = array(0);
					
				break;
				case "ids_from_meta":
					
					$arrIDsPostMeta = $this->getPostListData_getIDsFromPostMeta($value, $name, $showDebugQuery);
										
				break;
				case "ids_from_dynamic":

					$arrIDsDynamicField = UniteFunctionsUC::getVal($value, $name."_includeby_dynamic_field");
					
					$arrIDsDynamicField = UniteFunctionsUC::getIDsArray($arrIDsDynamicField);
					
					if(empty($arrIDsDynamicField))
						$arrIDsDynamicField = array(0);
										
				break;
				case "current_terms":

					$currentTaxQuery = UniteFunctionsWPUC::getCurrentPageTaxQuery();

				break;
				case "current_query_base":	//get current query as a query base

					$arrQueryBase = UniteFunctionsWPUC::getCurrentQueryVars();

				break;
				default:	//apply some filter for custom post id's
					
					$customPostINIDs = apply_filters("ue_get_custom_includeby_postids", null, $includeby, $limit);
										
				break;
			}

		}
		
		//include id's
		$arrPostInIDs = UniteFunctionsUC::mergeArraysUnique($arrProductsCrossSells, $arrProductsUpSells, $arrRecentProducts);
		
		if(!empty($arrIDsOnSale)){

			if(!empty($arrPostInIDs))		//intersect with previous id's
				$arrPostInIDs = array_intersect($arrPostInIDs, $arrIDsOnSale);
			else
				$arrPostInIDs = $arrIDsOnSale;
		}

		if(!empty($arrIDsPopular)){
			$makePostINOrder = true;
			$arrPostInIDs = $arrIDsPopular;
		}

		if(!empty($arrIDsPHPFunction)){
			$arrPostInIDs = $arrIDsPHPFunction;
			$makePostINOrder = true;
		}

		if(!empty($arrIDsPostMeta)){
			$arrPostInIDs = $arrIDsPostMeta;
			$makePostINOrder = true;
		}

		if(!empty($arrIDsDynamicField)){
			$arrPostInIDs = $arrIDsDynamicField;
			$makePostINOrder = true;
		}

		if(!empty($arrIDsFromContent)){
			$arrPostInIDs = $arrIDsFromContent;
			$makePostINOrder = true;
		}
		
		if(!empty($customPostINIDs)){
			$arrPostInIDs = $customPostINIDs;
			$makePostINOrder = true;
		}
		
		
		//make order as "post__id"
		
		if($makePostINOrder == true && empty($orderBy)){
						
			//set order
			$args["orderby"] = "post__in";
			
			$orderDir = UniteFunctionsUC::getVal($args, "order");
			if($orderDir == "ASC")
				$arrIDsPopular = array_reverse($arrIDsPopular);

			unset($args["order"]);
		}


		//exclude posts not in from posts in
		$arrPostsNotInTest = UniteFunctionsUC::getVal($args, "post__not_in");

		if(!empty($arrPostInIDs) && !empty($arrPostsNotInTest) && is_array($arrPostsNotInTest))
			$arrPostInIDs = array_diff($arrPostInIDs, $arrPostsNotInTest);
		
		
		if(!empty($arrPostInIDs)){
			$args["post__in"] = $arrPostInIDs;
		}
		

		//------ get woo  related products ------

		if($getRelatedProducts == true){

			if($showDebugQuery == true){

				$debugText = "Debug: Getting up to $limit related products";

				if(!empty($arrPostsNotIn)){
					$strPostsNotIn = implode(",", $arrPostsNotIn);
					$debugText = " excluding $strPostsNotIn";
				}

				dmp($debugText);
			}

			if(function_exists("is_checkout") && is_checkout() == true){
				
				$objWoo = new UniteCreatorWooIntegrate();
				$arrRelatedProductIDs = $objWoo->getRelatedProductsFromCart($limit, $arrPostsNotIn);

			}else{

				if(empty($arrPostsNotIn))
					$arrPostsNotIn = array();

				if(!empty($productID))
					$arrRelatedProductIDs = wc_get_related_products($productID, $limit, $arrPostsNotIn);

			}

			if(empty($arrRelatedProductIDs))
				$arrRelatedProductIDs = array("0");


			$args["post__in"] = $arrRelatedProductIDs;
		}

		if(!empty($arrMetaQuery))
			$args["meta_query"] = $arrMetaQuery;

		//add exclude specific posts if available
		if(!empty($arrPostsNotIn)){
			$arrPostsNotIn = array_unique($arrPostsNotIn);
			$args["post__not_in"] = $arrPostsNotIn;
		}

		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		if($isWpmlExists)
			$args["suppress_filters"] = false;

		//add post status
		$arrStatuses = UniteFunctionsUC::getVal($value, "{$name}_status");

		//add inherit for attachment
		if(is_array($postType) && in_array("attachment", $postType)){

			if(is_string($arrStatuses))
				$arrStatuses = array($arrStatuses);

			$arrStatuses[] = "inherit";
		}

		if(empty($arrStatuses))
			$arrStatuses = "publish";
		
		
		if(!empty($offset))
			$args["offset"] = $offset;

		if(is_array($arrStatuses) && count($arrStatuses) == 1)
			$arrStatuses = $arrStatuses[0];

		$args["post_status"] = $arrStatuses;


		//add sticky posts only
		$arrStickyPosts = array();

		if($getOnlySticky == true){

			/*
			$stickyPostDefaultLangOption = UniteFunctionsUC::getVal( $value, "{$name}_sticky_post_default_lang" );
			$stickyPostDefaultLangOption = UniteFunctionsUC::strToBool( $stickyPostDefaultLangOption );
			
			$objWPML = new UniteCreatorWpmlIntegrate();
			$arrStickyPosts = $objWPML->getStickyPostsBasedOnDefaultLanguage($stickyPostDefaultLangOption);
			*/
			
			//for wpml integration
			
			do_action("ue_before_get_only_sticky_posts",$value,$name);
			
			$arrStickyPosts = get_option('sticky_posts', array());
			
			$args["ignore_sticky_posts"] = true;

			if(!empty($arrStickyPosts) && is_array($arrStickyPosts)){
				$args["post__in"] = $arrStickyPosts;
			}else{
				$args["post__in"] = array("0");		//no posts at all
			}

		}



		//merge current tax query
		if(!empty($currentTaxQuery))
			$args = UniteFunctionsWPUC::mergeArgsTaxQuery($args, $currentTaxQuery);

		//merge the whole query
		if(!empty($arrQueryBase))
			$args = UniteFunctionsWPUC::mergeQueryVars($arrQueryBase, $args);

		if($showDebugQuery == true){
			
			$style = HelperHtmlUC::getQueryDebugWrapperStyles();
			
			echo("<div class='uc-debug-query-wrapper' style='{$style}'>");	//start debug wrapper
		}
			
		$args = $this->getPostListData_getPostGetFilters_pagination($args, $value, $name, $data, $param);

		//---- disable other hooks:
		
		$disableOtherHooks = UniteFunctionsUC::getVal($value, "{$name}_disable_other_hooks");
		
		//disable by url
		if(GlobalsUC::$showQueryDebugByUrl == true && HelperUC::hasPermissionsFromQuery("uctestquery_clear"))
			$disableOtherHooks = "yes";
		
		if($disableOtherHooks === "yes" && GlobalsProviderUC::$isUnderAjax == true){
			
			global $wp_filter;
			$wp_filter = array();
			
			if($showDebugQuery == true){
				dmp("<b>Disable third party hooks...</b>");
			}
			
		}
		
		//for woocommerce - add visiblity hidden
		if($isForWoo && $postType == "product"){
			$args = UniteCreatorWooIntegrate::addExcludeCatalogVisibilityArguments($args);
		}
		
		
		//update by post and get filters
		
		$objFiltersProcess = new UniteCreatorFiltersProcess();
		
		$args = $objFiltersProcess->processRequestFilters($args, $isFilterable);
		
		$args = $this->getPostListData_getCustomQueryFilters($args, $value, $name, $data);
		
		HelperUC::addDebug("Posts Query", $args);

		//-------- show debug query --------------

		if($showDebugQuery == true){
			
			$argsForDebug = $args;
			if(!empty($arrQueryBase))
				$argsForDebug = UniteFunctionsWPUC::cleanQueryArgsForDebug($argsForDebug);
			
			dmp("<strong>Custom Post. The Query Is:</strong>");
			dmp($argsForDebug);
			
		}
		
		
		//clear some hook by url - for debug
				
		if(GlobalsUC::$showQueryDebugByUrl == true){
					
			$filterToDisable = HelperUC::getQueryVarWithPermission("uctestquery_clearhook");
						
			if(!empty($filterToDisable)){
				
				dmp("<b>Debug: clear filter:  $filterToDisable </b>");
				
				UniteFunctionsWPUC::clearFiltersFunctions($filterToDisable);
				
			}
			
		}
		
		
		//remember last args
		GlobalsProviderUC::$lastQueryArgs = $args;
		
		//check for modify orderby query clauses (for woo)
		$this->checkModifyQueryClauses($args, $excludeOutofStockVariation, $showDebugQuery);
		
		//for debug
		//UniteFunctionsWPUC::clearFiltersFunctions("posts_where");

		$query = new WP_Query();

		do_action("ue_before_custom_posts_query", $query);
		
		$args["cache_results"] = true;
		$args["update_post_meta_cache"] = true;
		
		$args = apply_filters("ue_modify_posts_query_args", $args, $value, $name);

		//set debug errors
		if($showDebugQuery == true && $debugType == "show_query"){
			add_action("wp_error_added",array($this,"showWPErrorLog"),10,4);
		}
		
		//skip from the globals variabe
		if(GlobalsProviderUC::$skipRunPostQueryOnce == true)
			$this->skipPostListQueryRun = true;
		
		//debug - clear some arguments by query
		
		if(GlobalsUC::$showQueryDebugByUrl == true){
		
			$argsToClear = HelperUC::getQueryVarWithPermission("uctestquery_cleararg");
						
			if(!empty($argsToClear))
				add_action("pre_get_posts", array($this,"preGetPostsModifyQueryDebug"), 9999, 1);
		}
					
		$wasSkipRun = false;
		
		
		if($this->skipPostListQueryRun == false){

			$query->query($args);
			GlobalsProviderUC::$lastQueryRequest = $query->request;

		}
		else{
			
			//if skipped - set to false for next run time.
			$this->skipPostListQueryRun = false;
			
			if($showDebugQuery == true)
				dmp("Skip main query run");
				
			$wasSkipRun = true;
		}

		$objFiltersProcess->afterQueryRun();
		
		do_action("ue_after_custom_posts_query", $query);
		
		//custom posts debug

		if($showDebugQuery == true && $debugType == "show_query"){
						
			remove_action("wp_error_added",array($this,"showWPErrorLog"));
			
			$originalQueryVars = $query->query_vars;
			$originalQueryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($originalQueryVars);
			
			dmp("<strong>The Query Request Is:</strong>");
			dmp($query->request);
		
			dmp("<strong>The Final Query Vars:</strong>");
			dmp($originalQueryVars);

			$this->showPostsDebugCallbacks($isForWoo);
			
		}

		/*
	 	dmp("request debug output");

		dmp($query->request);
		dmp("the query");
		dmp($query->query);
		dmp($query->post_count);
		dmp($query->found_posts);
		exit();
		*/
		
		$arrPosts = $query->posts;

		$numPosts = $query->found_posts;

		if(!empty($arrPosts) && $numPosts == 0)
			$arrPosts = array();

		if(!$arrPosts)
			$arrPosts = array();

		//sticky posts integration
		if($checkStickyPostsByPlugin == true)
			$arrPosts = UniteCreatorPluginIntegrations::checkAddStickyPosts($arrPosts, $args);


		//add parent posts

		if(!empty($addParentType) && !empty($addParentIDs)){

			if(is_array($addParentIDs) == false)
				$addParentIDs = array($addParentIDs);

			$argsParents = array();
			$argsParents["post_type"] = $postType;
			$argsParents["post__in"] = $addParentIDs;

			$arrParents = get_posts($argsParents);

			if(!empty($arrParents)){

				if($addParentType == "end")
					$arrPosts = array_merge($arrPosts, $arrParents);
				else
					$arrPosts = array_merge($arrParents, $arrPosts);

			}

			if($showDebugQuery == "true")
				dmp("adding parent post to ".$addParentType);

		}


		//sort sticky posts
		if($getOnlySticky == true && !empty($arrStickyPosts)){

			$orderby = UniteFunctionsUC::getVal($args, "orderby");
			if(empty($orderby))
				$arrPosts = UniteFunctionsWPUC::orderPostsByIDs($arrPosts, $arrStickyPosts);
		}

		//save last query and page
		$this->saveLastQueryAndPage($query,GlobalsProviderUC::QUERY_TYPE_CUSTOM, $offset);

		$this->arrCurrentPostIDs = array();

		$postIDs = array();

		//remember duplicate posts

		if($isAvoidDuplicates == true){

			foreach($arrPosts as $post){

				GlobalsProviderUC::$arrFetchedPostIDs[$post->ID] = true;
				$this->arrCurrentPostIDs[] = $post->ID;
			}

		}

		HelperUC::addDebug("posts found: ".count($arrPosts));

		if($showDebugQuery == true){
			
			if($wasSkipRun == false)
				dmp("Found Posts: ".count($arrPosts));
			
		}
        
		//filter results
		
		$filters = array();
		$value["uc_posts_name"] = $name;

		$arrPostsFromFilter = UniteProviderFunctionsUC::applyFilters("uc_filter_posts_list", $arrPosts, $value, $filters);
		
		if(!empty($arrPostsFromFilter))
			$arrPosts = $arrPostsFromFilter;
		
		if($showDebugQuery == true){
			
			echo "</div> <!-- end debug query -->";
		}
		
		//show debug meta if needed
		$this->showPostsDebugMeta($arrPosts, $value, $name);
        
		return($arrPosts);
	}


	/**
	 * show wordpress error if available
	 */
	public function showWPErrorLog($code, $message, $data, $obj){
		
		dmp("<div style='color:red;'>wp error found!</div>");
		dmp("<div style='color:red;'>$message</div>");
	}
	
	/**
	 * show modify callbacks for debug
	 */
	private function showPostsDebugCallbacks($isForWoo = false){
		
		$arrNames = array(
				"posts_request",
				"posts_pre_query",
				"posts_where",
				"posts_clauses",
				"posts_join",
				"pre_get_posts",
				"posts_orderby",
				"parse_tax_query",
				"posts_selection",
				"parse_term_query"
		);
		
		foreach($arrNames as $name){
			
			$arrActions = UniteFunctionsWPUC::getFilterCallbacks($name);
	
			dmp("Query modify callbacks ( {$name} ):");
			HelperProviderUC::printFilterCallbacks($arrActions);
		}
		
		
		if($isForWoo == true){

			$arrActions = UniteFunctionsWPUC::getFilterCallbacks("loop_shop_per_page");

			dmp("Query modify callbacks ( loop_shop_per_page ):");
			HelperProviderUC::printFilterCallbacks($arrActions);
			
			$arrActions = UniteFunctionsWPUC::getFilterCallbacks("loop_shop_columns");

			dmp("Query modify callbacks ( loop_shop_columns ):");
			HelperProviderUC::printFilterCallbacks($arrActions);
			
			//products change
		}

	}

	/**
	 * save last query and page - for pagination widget
	 */
	private function saveLastQueryAndPage($query, $type, $initialOffset = null){
		
		$isDebug = HelperUC::hasPermissionsFromQuery("ucpaginationdebug");
				
		//don't save under dynamic template loop - not allow paging or filter there.
		if(GlobalsProviderUC::$isUnderDynamicTemplateLoop == true){
			
			if($isDebug == true)
				dmp("Save query - exit, under dynamic loop");
				
			return(false);
		}
		
		//some protection manual query in last widget, take the working one
		//under ajax - no need for those checks
				
		if(GlobalsProviderUC::$isUnderAjax == false && 
		   !empty(GlobalsProviderUC::$lastPostQuery_type) &&
		   $type == GlobalsProviderUC::QUERY_TYPE_MANUAL && 
		   GlobalsProviderUC::$lastPostQuery_type != GlobalsProviderUC::QUERY_TYPE_MANUAL){
			
			if($isDebug == true)
				dmp("Save query - exit, manual type");
			
			return(false);
		}
		
		//skip if no pagination set in widget
		
		$paginationType = UniteFunctionsUC::getVal($this->lastValues, $this->lastName."_pagination_type");
		$isAjax = UniteFunctionsUC::getVal($this->lastValues, $this->lastName."_isajax");
		$isAjax = UniteFunctionsUC::strToBool($isAjax);
		
		if(GlobalsProviderUC::$isUnderAjax == false && !empty($this->lastName) && $isAjax == false && empty($paginationType)){
			
			if($isDebug == true)
				dmp("Save query - exit, no ajax or type selected");
			
			return(false);
		}

		GlobalsProviderUC::$lastPostQuery = $query;
		GlobalsProviderUC::$lastPostQuery_page = 1;
		GlobalsProviderUC::$lastPostQuery_type = $type;

		//set type for pagination, stay on current if exists
		if(GlobalsProviderUC::$lastPostQuery_paginationType != GlobalsProviderUC::QUERY_TYPE_CURRENT)
			GlobalsProviderUC::$lastPostQuery_paginationType = $type;

		$queryVars = $query->query;
		
		if($isDebug == true){
			dmp("Save query - query saved");
			dmp($queryVars);
		}
		
		$perPage = UniteFunctionsUC::getVal($queryVars, "posts_per_page");

		if(empty($perPage))
			return(false);

		$offset = UniteFunctionsUC::getVal($queryVars, "offset");

		if(!empty($initialOffset))
			$offset = $offset - $initialOffset;

		if(empty($offset))
			return(false);

		$page = ceil($offset / $perPage)+1;
		
		if(!empty($page))
			GlobalsProviderUC::$lastPostQuery_page = $page;

		GlobalsProviderUC::$lastPostQuery_offset = $offset;

	}

	/**
	 * get if the request filterable
	 */
	private function getIsFilterable($value, $name){
		
		//all under dynamic template is not filterable
		
		if(GlobalsProviderUC::$isUnderDynamicTemplateLoop == true)
			return(false);
		
		$isAjax = UniteFunctionsUC::getVal($value, "{$name}_isajax");
		$isAjax = UniteFunctionsUC::strToBool($isAjax);
		
		//all ajax related under ajax is positive
		if(UniteCreatorFiltersProcess::$isUnderAjax == true && $isAjax == true)
			return(true);

		
		//if it's not under ajax - then allow request only if ajax url is set to true
			
		$isAjaxSetUrl = UniteFunctionsUC::getVal($value, "{$name}_ajax_seturl");
		
		$isFilterable = $isAjax && ($isAjaxSetUrl != "ajax");
		
		if($isFilterable == true)
			return(true);
		
		//check ajax search

		$isAjaxSearch = $this->addon->isAjaxSearch();
		if($isAjaxSearch == true)
			return(true);

		return(false);
	}


	/**
	 * get current posts
	 */
	private function getPostListData_currentPosts($value, $name, $data, $nameListing = null){
        
		//add debug for further use
		HelperUC::addDebug("Getting Current Posts");

		$orderBy = UniteFunctionsUC::getVal($value, $name."_orderby");
		$orderDir = UniteFunctionsUC::getVal($value, $name."_orderdir1");
		$orderByMetaKey = UniteFunctionsUC::getVal($value, $name."_orderby_meta_key1");

		$maxItems = UniteFunctionsUC::getVal($value, $name."_maxitems_current");

		$postType = UniteFunctionsUC::getVal($value, $name."_posttype_current");

		//enable filters
		$nameForFilter = $name;
		if(!empty($nameListing))
			$nameForFilter = $nameListing;
		
		$isFilterable = $this->getIsFilterable($value, $nameForFilter);

		if($orderBy == "default")
			$orderBy = null;

		if($orderDir == "default")
			$orderDir = null;

		global $wp_query;
		$currentQueryVars = $wp_query->query_vars;

		// ----- current query settings --------

		//--- set order ---
		if(!empty($orderBy)){

			$currentQueryVars = UniteFunctionsWPUC::updatePostArgsOrderBy($currentQueryVars, $orderBy);
		}

		if($orderBy == "meta_value" || $orderBy == "meta_value_num")
			$currentQueryVars["meta_key"] = $orderByMetaKey;

		if(!empty($orderDir))
			$currentQueryVars["order"] = $orderDir;



		//--- set posts per page ---

		if(!empty($maxItems) && is_numeric($maxItems))
			$currentQueryVars["posts_per_page"] = $maxItems;

		if(!empty($postType))
			$currentQueryVars["post_type"] = $postType;


		$currentQueryVars = apply_filters( 'elementor/theme/posts_archive/query_posts/query_vars', $currentQueryVars);

		//update by post and get filters
		$objFiltersProcess = new UniteCreatorFiltersProcess();
		$currentQueryVars = $objFiltersProcess->processRequestFilters($currentQueryVars, $isFilterable);

		//custom filters
		$currentQueryVars = $this->getPostListData_getCustomQueryFilters($currentQueryVars, $value, $name, $data);

		$showDebugQuery = UniteFunctionsUC::getVal($value, "{$name}_show_query_debug");
		$showDebugQuery = UniteFunctionsUC::strToBool($showDebugQuery);

		$debugType = null;

		if(self::SHOW_DEBUG_QUERY == true)
			$showDebugQuery = true;
		
		if(GlobalsUC::$showQueryDebugByUrl == true){
			$showDebugQuery = true;
			$this->advancedQueryDebug = true;
		}
		
		$args = apply_filters("ue_modify_posts_query_args", $currentQueryVars, $value, $name);
		
		
		$isForWoo = false;
		if($showDebugQuery == true){

			$postType = UniteFunctionsUC::getVal($currentQueryVars, "post_type");
			if($postType == "product")
				$isForWoo = true;
			
			$style = HelperHtmlUC::getQueryDebugWrapperStyles();
			
			echo "<div class='uc-debug-query-wrapper' style='{$style}'>";	//start debug wrapper

			dmp("Current Posts. The Query Is:");

			$argsForDebug = UniteFunctionsWPUC::cleanQueryArgsForDebug($currentQueryVars);
			dmp($argsForDebug);

			$debugType = UniteFunctionsUC::getVal($value, "{$name}_query_debug_type");

		}

		if(self::SHOW_DEBUG_QUERY == true)
			$debugType = "show_query";


		$query = $wp_query;

		$objFilters = new UniteCreatorFiltersProcess();
		$isFrontAjax = $objFilters->isFrontAjaxRequest();
		
		//remember last args
		GlobalsProviderUC::$lastQueryArgs = $wp_query->query_vars;

		//remake the query - not inside ajax
				
		if($currentQueryVars !== $wp_query->query_vars){

			//dmp($currentQueryVars);exit();

			HelperUC::addDebug("New Query", $currentQueryVars);

			if($showDebugQuery == true){
				dmp("Run New Query");
			}

			//skip run
			GlobalsProviderUC::$lastQueryArgs = $wp_query->query_vars;

			$query = new WP_Query( $currentQueryVars );
			
		}
			
		if(!empty($query))
			GlobalsProviderUC::$lastQueryRequest = $query->request;
		

		HelperUC::addDebug("Query Vars", $currentQueryVars);
	
		//save last query
		$this->saveLastQueryAndPage($query, GlobalsProviderUC::QUERY_TYPE_CURRENT);
		
		$arrPosts = $query->posts;

		if(empty($arrPosts))
			$arrPosts = array();

		$numPosts = $query->found_posts;

		if(!empty($arrPosts) && $numPosts == 0)
			$arrPosts = array();

		if($showDebugQuery == true && $debugType == "show_query"){

			$originalQueryVars = $query->query_vars;
			$originalQueryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($originalQueryVars);
			
			dmp("The Query Request Is:");
			dmp($query->request);
			
			dmp("The finals query vars:");
			dmp($originalQueryVars);
		
			$this->showPostsDebugCallbacks($isForWoo);

		}

		if($showDebugQuery == true){
			dmp("Found Posts: ".count($arrPosts));
			
			dmp("Total Posts: ".$numPosts);
			
			echo "</div>";	//close query wrapper div
		}

		HelperUC::addDebug("Posts Found: ". count($arrPosts));

		return($arrPosts);
	}


	/**
	 * get manual selection
	 */
	private function getPostListData_manualSelection($value, $name, $data, $nameListing){
		
		$args = array();

		$postIDs = UniteFunctionsUC::getVal($value, $name."_manual_select_post_ids");
		
		$isAvoidDuplicates = UniteFunctionsUC::getVal($value, $name."_manual_avoid_duplicates");
		$isAvoidDuplicates = UniteFunctionsUC::strToBool($isAvoidDuplicates);


		if(empty($postIDs))
			$postIDs = array();

		//post id's by dynamic text field

		$dynamicIDs = UniteFunctionsUC::getVal($value, $name."_manual_post_ids_dynamic");

		$arrDynamicIDs = UniteFunctionsUC::getIDsArray($dynamicIDs);

		if(!empty($arrDynamicIDs))
			$postIDs = array_merge($postIDs, $arrDynamicIDs);
		
		//set posts per page
		
		$postsPerPage = count($postIDs);

		if($postsPerPage < 1000)
			$postsPerPage = 1000;

		$limit = UniteFunctionsUC::getVal($value, "{$name}_maxitems_manual");
			
		if(!empty($limit) && is_numeric($limit) == true){
			
			$limit = (int)$limit;
			if($limit <= 0)
				$limit = 100;
	
			if($limit > 1000)
				$limit = 1000;
			
			$postsPerPage = $limit;
		}
			
		$showDebugQuery = UniteFunctionsUC::getVal($value, "{$name}_show_query_debug");
		$showDebugQuery = UniteFunctionsUC::strToBool($showDebugQuery);
		
		$debugType = UniteFunctionsUC::getVal($value, "{$name}_query_debug_type");

		if(self::SHOW_DEBUG_QUERY == true)
			$debugType = "show_query";
		
		if(GlobalsUC::$showQueryDebugByUrl == true){
			
			$showDebugQuery = true;
			$this->advancedQueryDebug = true;
			$debugType = "show_query";
			
		}
			
		if(empty($postIDs)){

			if($showDebugQuery == true){

				dmp("Query Debug, Manual Selection: No Posts Selected");
				HelperUC::addDebug("No Posts Selected");
			}

			return(array());
		}

		$args["post__in"] = $postIDs;
		$args["ignore_sticky_posts"] = true;

		$postTypes = get_post_types(array("exclude_from_search"=>false));
		
		//add elementor_template to any types
		
		if(isset($postTypes["e-landing-page"]))
			$postTypes["elementor_library"] = "elementor_library";

		$args["post_type"] = $postTypes;

		$args["posts_per_page"] = $postsPerPage;
		$args["suppress_filters"] = true;

		$args["post_status"] = "publish, private";

		$args = $this->getPostListData_addOrderBy($args, $value, $name, true);

		//enable filters
		
		$nameForFilter = $name;
		if(!empty($nameListing))
			$nameForFilter = $nameListing;
		
		$isFilterable = $this->getIsFilterable($value, $nameForFilter);
		
		//update by post and get filters
		$objFiltersProcess = new UniteCreatorFiltersProcess();
		$args = $objFiltersProcess->processRequestFilters($args, $isFilterable);
		
		
		if($showDebugQuery == true){
			dmp("Manual Selection. The Query Is:");
			dmp($args);
		}

		GlobalsProviderUC::$lastQueryArgs = $args;

		$wasQuerySkipRun = false;

		add_action("pre_get_posts", array($this,"clearTaxQueryForGetPostListData_manualSelection"), 1, 1);
		
		
		if($this->skipPostListQueryRun == false){
			$query = new WP_Query($args);
			
			GlobalsProviderUC::$lastQueryRequest = $query->request;
		}
		else{
			
			$query = new WP_Query();
			
			//skip false for next time
			$this->skipPostListQueryRun = false;
			
			$wasQuerySkipRun = true;
			
			if($showDebugQuery == true){
				dmp("Query skip run!");
			}
			
		}

		remove_action('pre_get_posts', array($this,"clearTaxQueryForGetPostListData_manualSelection"), 1);

		
		if($showDebugQuery == true && $debugType == "show_query"){
			
			$originalQueryVars = $query->query_vars;
			$originalQueryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($originalQueryVars);

			dmp("The Query Request Is:");
			dmp($query->request);

			dmp("The finals query vars:");
			dmp($originalQueryVars);

			$this->showPostsDebugCallbacks(false);

		}

		$arrPosts = $query->posts;

		if(empty($arrPosts))
			$arrPosts = array();
		
		//keep original order if no orderby
		$orderby = UniteFunctionsUC::getVal($args, "orderby");
		if(empty($orderby))
			$arrPosts = UniteFunctionsWPUC::orderPostsByIDs($arrPosts, $postIDs);

		//save last query
		$this->saveLastQueryAndPage($query,GlobalsProviderUC::QUERY_TYPE_MANUAL);
		
		HelperUC::addDebug("posts found: ".count($arrPosts));

		if($showDebugQuery == true){
			dmp("Found Posts: ".count($arrPosts));
		}

		//handle avoid duplicates - save post ids

		$this->arrCurrentPostIDs = array();

		//remember duplicate posts
		if($isAvoidDuplicates == true){

			foreach($arrPosts as $post){
				GlobalsProviderUC::$arrFetchedPostIDs[$post->ID] = true;
				$this->arrCurrentPostIDs[] = $post->ID;
			}

		}



		return($arrPosts);

	}



	/**
	 * get the ue templates data
	 */
	private function getPostListData_ueTemplates($value, $name, $data){

		$strTemplatesIDs = UniteFunctionsUC::getVal($value, $name."_uetemplates_ids");

		$showDebugQuery = UniteFunctionsUC::getVal($value, "{$name}_show_query_debug");
		$showDebugQuery = UniteFunctionsUC::strToBool($showDebugQuery);
		
		if(GlobalsUC::$showQueryDebugByUrl == true)
			$showDebugQuery = true;

		
		if(empty($strTemplatesIDs)){

			if($showDebugQuery == true){
				
				$style = HelperHtmlUC::getQueryDebugWrapperStyles();
				
				echo "<div class='uc-debug-query-wrapper' style='{$style}'>";	//start debug wrapper
				
				dmp("UE Templates. No template id's found, no query");

				dmp($value);

				echo "</div>";
			}

			return(array());
		}

		$arrPostInIDs = explode(",", $strTemplatesIDs);

		$args = array();
		$args["post_type"] = "ue_templates";
		$args["post__in"] = $arrPostInIDs;
		$args["orderby"] = "post__in";
		$args["posts_per_page"] = 100;

		$arrPosts = get_posts($args);

		if($showDebugQuery == true){

			$style = HelperHtmlUC::getQueryDebugWrapperStyles();
			
			echo "<div class='uc-debug-query-wrapper' style='{$style}'>";	//start debug wrapper

			dmp("UE Templates. The Query Is:");

			dmp($args);

			dmp("Found Posts: ".count($arrPosts));
			echo "</div>";
		}

		return($arrPosts);

	}

	/**
	 * get post list data
	 */
	public function getPostListData($value, $name, $processType, $param, $data){
		
		if($processType != self::PROCESS_TYPE_OUTPUT && $processType != self::PROCESS_TYPE_OUTPUT_BACK)
			return($data);
		
		$this->lastValues = $value;
		$this->lastName = $name;
		
		//skip get data for ajax search as it puts
		
		$isAjaxSearch = $this->addon->isAjaxSearch();
		
		if($isAjaxSearch == true && UniteCreatorFiltersProcess::$isUnderAjax == false && GlobalsProviderUC::$isInsideEditor == false){
			
			$this->skipPostListQueryRun = true;			
		}

		// ---
		
		HelperUC::addDebug("getPostList values", $value);
		HelperUC::addDebug("getPostList param", $param);

		$source = UniteFunctionsUC::getVal($value, "{$name}_source");

		$useForListing = UniteFunctionsUC::getVal($param, "use_for_listing");
		$useForListing = UniteFunctionsUC::strToBool($useForListing);

		$nameListing = UniteFunctionsUC::getVal($param, "name_listing");

		if($useForListing == true)
			$this->lastName = $nameListing;
		
		
		if(self::SHOW_DEBUG_POSTLIST_QUERIES == true)
			HelperProviderUC::startDebugQueries();

		$arrPosts = array();
		
		switch($source){
			case "ue_templates":

				$arrPosts = $this->getPostListData_ueTemplates($value, $name, $data);
				
			break;
			case "manual":

				$arrPosts = $this->getPostListData_manualSelection($value, $name, $data, $nameListing);

			break;
			case "current":

				$arrPosts = $this->getPostListData_currentPosts($value, $name, $data, $nameListing);

			break;
			default:		//custom
				
				$arrPosts = $this->getPostListData_custom($value, $name, $processType, $param, $data, $nameListing);
				
			break;
		}
		
		if(self::SHOW_DEBUG_QUERY == true){

			dmp("don't forget to turn off the query debug");
			exit();
		}


		if(empty($arrPosts))
			$arrPosts = array();

		//save last posts
		GlobalsProviderUC::$arrFetchedPostsObjectsCache = UniteFunctionsUC::arrPostsToAssoc($arrPosts);


		//cache post attachment and data queries

		UniteFunctionsWPUC::cachePostsAttachmentsQueries($arrPosts);


		$useCustomFields = UniteFunctionsUC::getVal($param, "use_custom_fields");
		$useCustomFields = UniteFunctionsUC::strToBool($useCustomFields);

		$useCategory = UniteFunctionsUC::getVal($param, "use_category");
		$useCategory = UniteFunctionsUC::strToBool($useCategory);

		if($useCategory == true && $useForListing == false)
			UniteFunctionsWPUC::cachePostsTermsQueries($arrPosts);

		$arrPostAdditions = HelperProviderUC::getPostDataAdditions($useCustomFields, $useCategory);

		HelperUC::addDebug("post additions", $arrPostAdditions);
		

		//image sizes
		$showImageSizes = UniteFunctionsUC::getVal($param, "show_image_sizes");
		$showImageSizes = UniteFunctionsUC::strToBool($showImageSizes);

		$arrImageSizes = null;

		if($showImageSizes == true){

			$imageSize = UniteFunctionsUC::getVal($value, "{$name}_imagesize","medium_large");

			$arrImageSizes["desktop"] = $imageSize;
		}


		//prepare listing output. no items prepare for the listing

		$objFilters = new UniteCreatorFiltersProcess();


		if($useForListing == true){

			//add filterable variables - dynamic
			$data = $objFilters->addWidgetFilterableVarsFromData($data, $value, $nameListing, $this->arrCurrentPostIDs);

			//add the settings

			$data[$nameListing."_settings"] = $value;

			$data[$nameListing."_items"] = $arrPosts;

			return($data);
		}else{

			//filters additions - regular

			$data = $objFilters->addWidgetFilterableVariables($data, $this->addon, $this->arrCurrentPostIDs);
		}
		
		
		$arrData = array();
		$arrPostIDs = array();

		foreach($arrPosts as $post){

			//protection in case that post is id
			if(is_numeric($post))
				$post = get_post($post);
			
			$postData = $this->getPostDataByObj($post, $arrPostAdditions, $arrImageSizes);

			$postID = UniteFunctionsUC::getVal($postData, "id");

			$arrPostIDs[] = $postID;

			$arrData[] = $postData;
		}

		$strPostIDs = implode(",", $arrPostIDs);

		$data[$name] = $arrData;

		
		//add post output id's variable

		$keyIDs = $name."_output_ids";

		if(!isset($data[$keyIDs]))
			$data[$keyIDs] = $strPostIDs;

		if(self::SHOW_DEBUG_POSTLIST_QUERIES == true){

			dmp("debug qieries inside post list");

			HelperProviderUC::printDebugQueries(true);
		}
		
		$showPostListDebug = HelperUC::hasPermissionsFromQuery("ucpostlistdebug");
		
		if($showPostListDebug == true)
			HelperProviderUC::showPostsDebug($arrPosts,true);
		
		//turn off the query debug flag
		GlobalsProviderUC::$showPostsQueryDebug = false;

		return($data);
	}
	
	protected function z_______________BY_PRE_GET_POSTS____________(){}
	
	/**
	 * pre get posts - modify the query for debug
	 */
	public function preGetPostsModifyQueryDebug($query){
		
		$argsToClear = HelperUC::getQueryVarWithPermission("uctestquery_cleararg");

		if (!empty($argsToClear)) {
			
	        // Convert comma-separated string to an array
	        $argsArray = array_map('trim', explode(',', $argsToClear));
	
	        foreach ($argsArray as $arg) {
	        	
	            dmp("<b>Unsetting argument: $arg</b>");
	            unset($query->query_vars[$arg]);
	        }
	    }		
		
		remove_action("pre_get_posts", array($this,"preGetPostsModifyQueryDebug"), 9999, 1);
		
	}
	
	/**
	 * remove tax query from manual query
	 */
	public function clearTaxQueryForGetPostListData_manualSelection($query) {
		if (isset($query->query_vars['tax_query']))
			unset($query->query_vars['tax_query']);
	}
	
	
	protected function z_______________DYNAMIC_LOOP_GALLERY____________(){}

	/**
	 * get gallery item title
	 */
	private function getGalleryItem_title($source, $data, $name, $post, $item){
		
		switch($source){
			case "post_title":
				$title = $post->post_title;
			break;
			case "post_excerpt":
				$title = $post->post_excerpt;
			break;
			case "post_content":
				$title = $post->post_content;
			break;
			case "image_title":
				$title = UniteFunctionsUC::getVal($data, $name."_title");
			break;
			case "image_alt":
				$title = UniteFunctionsUC::getVal($data, $name."_alt");
			break;
			case "image_caption":
				$title = UniteFunctionsUC::getVal($data, $name."_caption");
			break;
			case "image_description":
				$title = UniteFunctionsUC::getVal($data, $name."_description");
			break;
			case "item_title":
				$title = UniteFunctionsUC::getVal($item, "title");
			break;
			case "item_description":
				$title = UniteFunctionsUC::getVal($item, "description");
			break;
			default:
			case "image_auto":
				
				$title = UniteFunctionsUC::getVal($data, $name."_title");

				if(empty($title))
					$title = UniteFunctionsUC::getVal($data, $name."_caption");

				if(empty($title))
					$title = UniteFunctionsUC::getVal($data, $name."_alt");

			break;
		}
		
		
		
		return($title);
	}	
		

	/**
	 * get gallery item data
	 */
	private function getGalleryItem_sourceItemData($item, $sourceItem){

		$itemType = UniteFunctionsUC::getVal($sourceItem, "item_type", "image");

		switch($itemType){
			case "image":
			break;
			case "youtube":

				$urlYoutube = UniteFunctionsUC::getVal($sourceItem, "url_youtube");

				$videoID = UniteFunctionsUC::getYoutubeVideoID($urlYoutube);

				$item["type"] = "youtube";
				$item["videoid"] = $videoID;

			break;
			case "html5":

				$urlMp4 = UniteFunctionsUC::getVal($sourceItem, "url_html5");
								
				$item["type"] = "html5video";
				$item["url_mp4"] = $urlMp4;

			break;
			case "iframe":

				$urlIframe = UniteFunctionsUC::getVal($sourceItem, "url_iframe");

				$item["type"] = "iframe";
				$item["url_video"] = $urlIframe;

			break;
			case "vimeo":

				$videoID = UniteFunctionsUC::getVal($sourceItem, "vimeo_id");

				$videoID = UniteFunctionsUC::getVimeoIDFromUrl($videoID);

				$item["type"] = "vimeo";
				$item["videoid"] = $videoID;
			break;
			case "wistia":

				$videoID = UniteFunctionsUC::getVal($sourceItem, "wistia_id");

				$item["type"] = "wistia";
				$item["videoid"] = $videoID;

			break;
			default:

				dmp("wrong gallery item type: $itemType");
				dmp($sourceItem);

			break;
		}

		//get the link url
		$link = UniteFunctionsUC::getVal($sourceItem, "link");
		if(is_array($link))
			$link = UniteFunctionsUC::getVal($link, "url");

		if(empty($link))
			$link = "";

		$item["link"] = $link;


		return($item);
	}


	/**
	 * get gallery item from instagram
	 */
	private function getGalleryItem_instagram($instaItem, $isEnableVideo){

		$isVideo = UniteFunctionsUC::getVal($instaItem, "isvideo");
		$isVideo = UniteFunctionsUC::strToBool($isVideo);

		$item["type"] = "image";
		$item["image"] = UniteFunctionsUC::getVal($instaItem, "image");
		$item["thumb"] = UniteFunctionsUC::getVal($instaItem, "thumb");

		if($isVideo == true && $isEnableVideo == true){

			$urlVideo = UniteFunctionsUC::getVal($instaItem, "url_video");

			$item["type"] = "html5video";
			$item["url_mp4"] = $urlVideo;
		}

		$imageSize = 1080;

		$item["image_width"] = $imageSize;
		$item["image_height"] = $imageSize;
		$item["thumb_width"] = $imageSize;
		$item["thumb_height"] = $imageSize;

		$item["title"] = UniteFunctionsUC::getVal($instaItem, "caption");
		$item["description"] = "";
		$item["link"] = UniteFunctionsUC::getVal($instaItem, "link");
		$item["imageid"] = 0;

		return($item);
	}

	/**
	 * modify the item for video item
	 */
	private function getGalleryItem_checkHtml5VideoAttachment($item, $value){

		if(empty($value))
			return($item);

		if(is_numeric($value) == false)
			return($item);

		$post = get_post($value);

		$arrData = UniteFunctionsWPUC::getAttachmentData($value);

		dmp($arrData);
		exit();
	}


	/**
	 * check add post gallery video
	 */
	private function checkAddPostVideo($item, $arrParams, $post){

		$maybeVideo = UniteFunctionsUC::getVal($item, "maybe_video");
		$maybeVideo = UniteFunctionsUC::strToBool($maybeVideo);

		if($maybeVideo == true){

			//look for video

			$attachmentID = UniteFunctionsUC::getVal($item, "imageid");

			$post = null;

			if(!empty($attachmentID))
				$post = get_post($attachmentID);

			$post = (array)$post;

			$mimeType = UniteFunctionsUC::getVal($post, "post_mime_type");

			//set video

			if($mimeType == "video/mp4"){

				$urlVideo = UniteFunctionsUC::getVal($post, "guid");

				$item["type"] = "html5video";
				$item["url_mp4"] = $urlVideo;

				$urlImage = UniteFunctionsUC::getVal($item, "image");
				$urlThumb = UniteFunctionsUC::getVal($item, "thumb");

				//check alternative image url
				//from attachent meta - happends some time in jet for example

				$arrMeta = UniteFunctionsWPUC::getPostMeta($attachmentID);
				$thumbnailID = UniteFunctionsUC::getVal($arrMeta, "_thumbnail_id");

				if(!empty($thumbnailID)){

					$arrData = UniteFunctionsWPUC::getAttachmentData($thumbnailID);
					if(!empty($arrData)){

						$urlImage = UniteFunctionsUC::getVal($arrData, "image");
						$urlThumb = UniteFunctionsUC::getVal($arrData, "thumb_large");
					}

				}


				if($urlImage == GlobalsUC::$url_no_image_placeholder)
					$urlImage = GlobalsUC::$url_video_thumbnail;

				if($urlThumb == GlobalsUC::$url_no_image_placeholder)
					$urlThumb = GlobalsUC::$url_video_thumbnail;

				$item["image"] = $urlImage;
				$item["thumb"] = $urlThumb;

			}


			return($item);
		}

		$enableVideo = UniteFunctionsUC::getVal($arrParams, "enable_video");
		$enableVideo = UniteFunctionsUC::strToBool($enableVideo);

		if($enableVideo == false)
			return($item);

		$metaItemType = UniteFunctionsUC::getVal($arrParams, "meta_itemtype");
		$metaVideoID = UniteFunctionsUC::getVal($arrParams, "meta_videoid");

		if(empty($metaItemType))
			return($item);

		if(empty($metaVideoID))
			return($item);

		$postID = $post->ID;

		$arrMeta = UniteFunctionsWPUC::getPostMeta($postID);

		$itemType = UniteFunctionsUC::getVal($arrMeta, $metaItemType);
		$videoID = UniteFunctionsUC::getVal($arrMeta, $metaVideoID);

		if(empty($videoID))
			return($item);

		if(empty($itemType))
			return($item);

		switch($itemType){
			case "youtube":
			case "vimeo":

				$item["type"] = $itemType;
				$item["videoid"] = $videoID;

			break;
			default:
				return($item);
			break;
		}

		return($item);
	}

	/**
	 * get gallery item
	 */
	private function getGalleryItem($id, $url = null, $arrParams = null){

		$data = array();

		$arrFilters = UniteFunctionsUC::getVal($arrParams, "size_filters");

		$thumbSize = UniteFunctionsUC::getVal($arrParams, "thumb_size");
		$imageSize = UniteFunctionsUC::getVal($arrParams, "image_size");

		$titleSource = UniteFunctionsUC::getVal($arrParams, "title_source");
		$descriptionSource = UniteFunctionsUC::getVal($arrParams, "description_source");
		$post = UniteFunctionsUC::getVal($arrParams, "post");
		$sourceItem = UniteFunctionsUC::getVal($arrParams, "item");
		
		$isAddItemsData = UniteFunctionsUC::getVal($arrParams, "add_item_data");
		$isAddItemsData = UniteFunctionsUC::strToBool($isAddItemsData);

		$index = UniteFunctionsUC::getVal($arrParams, "index");

		$name = "image";

		$param = array();
		$param["name"] = $name;
		$param["size_filters"] = $arrFilters;
		$param["no_attributes"] = true;

		//no extra data needed
		if( strpos($titleSource,"post_") !== false && strpos($descriptionSource, "post_") !== false)
			$param["no_image_data"] = true;
		else
		if($titleSource == "item_title" && $descriptionSource == "item_description")
			$param["no_image_data"] = true;

		$value = $id;
		$isByUrl = false;
		if(empty($value)){
			$value = $url;
			$isByUrl = true;
		}

		$item = array();
		$item["type"] = "image";
		
		
		if(empty($value)){

			$item["image"] = GlobalsUC::$url_no_image_placeholder;
			$item["thumb"] = GlobalsUC::$url_no_image_placeholder;

			$item["image_width"] = 600;
			$item["image_height"] = 600;
			$item["thumb_width"] = 600;
			$item["thumb_height"] = 600;
			
			$title = $this->getGalleryItem_title($titleSource, $data, $name, $post, $sourceItem);
			$description = $this->getGalleryItem_title($descriptionSource, $data, $name, $post, $sourceItem);

			if(empty($title) && !empty($post))
				$title = $post->post_title;

			$item["title"] = $title;
			$item["description"] = $description;

			$item["link"] = "";
			
			if(!empty($post))
				$item["link"] = $post->guid;

			$item["imageid"] = 0;

			return($item);
		}
				
		$data = $this->getProcessedParamsValue_image($data, $value, $param);

		$arrItem = array();
		$keyThumb = "{$name}_thumb_$thumbSize";
		$keyImage = "{$name}_thumb_$imageSize";

		if(!isset($data[$keyThumb]))
			$keyThumb = $name;

		if(!isset($data[$keyImage]))
			$keyImage = $name;

		//add extra data
		if($isAddItemsData == true)
			$item = $this->getGalleryItem_sourceItemData($item, $sourceItem);
		
		$urlImage = UniteFunctionsUC::getVal($data, $keyImage);
		$urlThumb = UniteFunctionsUC::getVal($data, $keyThumb);


		if(empty($urlImage)){
			$urlImage = GlobalsUC::$url_no_image_placeholder;
			$item["maybe_video"] = true;
		}

		if(empty($urlThumb)){
			$urlThumb = $urlImage;
			if(empty($urlThumb))
				$urlThumb = GlobalsUC::$url_no_image_placeholder;
		}

		$item["image"] = $urlImage;
		$item["thumb"] = $urlThumb;

		$item["image_width"] = UniteFunctionsUC::getVal($data, $keyImage."_width");
		$item["image_height"] = UniteFunctionsUC::getVal($data, $keyImage."_height");

		$item["thumb_width"] = UniteFunctionsUC::getVal($data, $keyThumb."_width");
		$item["thumb_height"] = UniteFunctionsUC::getVal($data, $keyThumb."_height");
		
		$title = $this->getGalleryItem_title($titleSource, $data, $name, $post, $sourceItem);
		$description = $this->getGalleryItem_title($descriptionSource, $data, $name, $post, $sourceItem);
		
		
		//demo item text
		if($isByUrl == true && count($data) <= 2){
			
			if(empty($title))
				$title = "Demo Item {$index} Title";

			if(empty($description))
				$description = "Demo Item {$index} Description";
				
			
		}

		$item["title"] = $title;
		$item["description"] = $description;
		
		$item["title_source"] = $titleSource;
		$item["description_source"] = $descriptionSource;
		
		
		if(!isset($item["link"])){
			$item["link"] = "";
			if(!empty($post))
				$item["link"] = get_permalink($post);
		}
		
		$item["imageid"] = $id;

		$item = $this->checkAddPostVideo($item, $arrParams, $post);
		
		
		return($item);
	}


	/**
	 * convert grouped data for gallery
	 * return the images data at the end
	 */
	private function getGroupedData_convertForGallery($arrItems, $source, $value, $param){

		
		$name = UniteFunctionsUC::getVal($param, "name");
		
		$thumbSize = UniteFunctionsUC::getVal($value, $name."_thumb_size","medium_large");
		$imageSize = UniteFunctionsUC::getVal($value, $name."_image_size","large");
		
		//for instagram
		$isEnableVideo = UniteFunctionsUC::getVal($param, "gallery_enable_video");
		$isEnableVideo = UniteFunctionsUC::strToBool($isEnableVideo);
		
		
		//for posts

		$arrFilters = array();
		if(!empty($thumbSize))
			$arrFilters[] = $thumbSize;

		if(!empty($imageSize))
			$arrFilters[] = $imageSize;

		$params = array();
		$params["thumb_size"] = $thumbSize;
		$params["image_size"] = $imageSize;
		$params["size_filters"] = $arrFilters;

		
		//set title and description source

		$titleSource = null;
		$descriptionSource = null;
		
		
		switch($source){
			case "products":
			case "posts":
				
				$titleSource = UniteFunctionsUC::getVal($value, $name."_title_source_post","post_title");
				$descriptionSource = UniteFunctionsUC::getVal($value, $name."_description_source_post","post_excerpt");
				
				$enableVideos = UniteFunctionsUC::getVal($value, $name."_posts_enable_videos");
				$enableVideos = UniteFunctionsUC::strToBool($enableVideos);
				
				if($enableVideos == true){
					
					$metaItemType = UniteFunctionsUC::getVal($value, $name."_meta_itemtype");
					$metaVideoID = UniteFunctionsUC::getVal($value, $name."_meta_videoid");

					$params["enable_video"] = true;
					$params["meta_itemtype"] = $metaItemType;
					$params["meta_videoid"] = $metaVideoID;
				}
				
			break;
			case "gallery":
			case "current_post_meta":
				
				$titleSource = UniteFunctionsUC::getVal($value, $name."_title_source_gallery");
				$descriptionSource = UniteFunctionsUC::getVal($value, $name."_description_source_gallery");
				
			break;
			case "image_video_repeater":

				$titleSource = "item_title";
				$descriptionSource = "item_description";

			break;
		}

		$params["title_source"] = $titleSource;
		$params["description_source"] = $descriptionSource;
		
		if(empty($arrItems))
			$arrItems = array();
		
		if(!is_array($arrItems)) {
			$arrItems = json_decode($arrItems, true);
		}

		$output = array();
		foreach($arrItems as $index => $item){

			$params["index"] = ($index+1);

			switch($source){
				case "products":
				case "posts":

					$postID = $item->ID;
					$content = $item->post_content;

					$featuredImageID = $this->getPostFeaturedImageID($postID, $content, $item->post_type);

					$params["post"] = $item;

					$galleryItem = $this->getGalleryItem($featuredImageID,null,$params);

					$galleryItem["postid"] = $postID;

				break;
				case "gallery":
					
					$id = UniteFunctionsUC::getVal($item, "id");
					$url = UniteFunctionsUC::getVal($item, "url");
					
					//for default items
					if(empty($id) && empty($url)){
						
						$url = UniteFunctionsUC::getVal($item, "image");
						
						if(!empty($url)){
							$params["item"] = $item;
							$params["title_source"] = "item_title";
						}
					}
					
					if($id === 0)
						$params["item"] = $item;
										
					$galleryItem = $this->getGalleryItem($id, $url, $params);

				break;
				case "current_post_meta":
				case "current_product_variations":
				case "current_product_gallery":
					
					//item is ID
					$galleryItem = $this->getGalleryItem($item, null, $params);
					
				break;
				
				case "image_video_repeater":

					$image = UniteFunctionsUC::getVal($item, "image");

					$url = UniteFunctionsUC::getVal($image, "url");
					$id = UniteFunctionsUC::getVal($image, "id");

					$params["add_item_data"] = true;
					$params["item"] = $item;

					$galleryItem = $this->getGalleryItem($id, $url, $params);

				break;
				case "instagram":

					$galleryItem = $this->getGalleryItem_instagram($item, $isEnableVideo);

				break;
				default:
					UniteFunctionsUC::throwError("group gallery error: unknown type: $source");
				break;
			}

			if(!empty($galleryItem))
				$output[] = $galleryItem;

		}

		return($output);
	}

	/**
	 * get image ids from meta key
	 */
	private function getGroupedData_getArrImageIDsFromMeta($value, $name){

		if(is_singular() == false)
			return(array());

		$post = get_post();
		if(empty($post))
			return(array());

		$postID = $post->ID;

		$isShowMeta = UniteFunctionsUC::getVal($value, $name."_show_metafields");
		$isShowMeta = UniteFunctionsUC::strToBool($isShowMeta);

		$arrMeta = array();

		//--- output debug
		if($isShowMeta == true){

			$arrMeta = UniteFunctionsWPUC::getPostMeta($postID);

			$arrMetaDebug = UniteFunctionsUC::modifyDataArrayForShow($arrMeta);

			dmp("<b>Debug Post Meta</b>, please turn it off on release");
			dmp($arrMetaDebug);
		}

		//get meta key:

		$metaKey = UniteFunctionsUC::getVal($value, $name."_current_metakey");


		if(empty($metaKey)){

			if($isShowMeta == true)
				dmp("empty meta key, please set it");

			return(array());
		}

		$metaValues = get_post_meta($postID, $metaKey, true);

		if(empty($metaValues)){

			if($isShowMeta)
				dmp("no value for this meta key: $metaKey");

			return(array());
		}

		if(is_array($metaValues))
			return($metaValues);

		//if string - convert to array

		$arrValues = explode(",", $metaValues);

		$arrIDs = array();
		foreach($arrValues as $value){
			$value = trim($value);
			if(is_numeric($value) == false)
				continue;

			$arrIDs[] = $value;
		}

		return($arrIDs);
	}





	/**
	 * get listing data
	 */
	private function getListingData($value, $name, $processType, $param, $data){

		if($processType != self::PROCESS_TYPE_OUTPUT && $processType != self::PROCESS_TYPE_OUTPUT_BACK)
			return($data);

		$useFor = UniteFunctionsUC::getVal($param, "use_for");

		switch($useFor){
			case "remote":

				$data = $this->getRemoteSettingsData($value, $name, $processType, $param, $data);

				return($data);
			break;
			case "items":

				$data = $this->getMultisourceSettingsData($value, $name, $processType, $param, $data);

				return($data);
			break;
		}

		$isForGallery = ($useFor == "gallery");

		$source = UniteFunctionsUC::getVal($value, $name."_source", "posts");

		if(empty($source) && $isForGallery == true)
			$source = "gallery";
		
		
		$templateID = UniteFunctionsUC::getVal($value, $name."_template_templateid");
		$alternate_templateID = UniteFunctionsUC::getVal($value, $name."_template2_templateid");
				
		$data[$name."_source"] = $source;
		$data["listing_setting_name"] = $name;
		$data[$name."_templateid"] = $templateID;
		$data[$name."_alt_templateid"] = $alternate_templateID;
		
		unset($data[$name]);

		switch($source){
			case "posts":

				$paramPosts = $param;
				
				$paramPosts["name"] = $paramPosts["name"]."_posts";
				$paramPosts["name_listing"] = $name;
				$paramPosts["use_for_listing"] = true;
				
				$data = $this->getPostListData($value, $paramPosts["name"], $processType, $paramPosts, $data);
				
			break;
			case "products":

				$paramProducts = $param;

				$paramProducts["name"] = $paramProducts["name"]."_products";
				$paramProducts["name_listing"] = $name;
				$paramProducts["use_for_listing"] = true;
				$paramProducts["for_woocommerce_products"] = true;

				$data = $this->getPostListData($value, $paramProducts["name"], $processType, $paramProducts, $data);

			break;
			case "terms":

				dmp("get terms");
				$data[$name."_items"] = array();

			break;
			case "gallery":

				$arrGalleryItems = UniteFunctionsUC::getVal($value, $name."_gallery");
				
				$data[$name."_items"] = $arrGalleryItems;

			break;
			case "current_post_meta":		//meta field with image id's

				$data[$name."_items"] = $this->getGroupedData_getArrImageIDsFromMeta($value, $name);

			break;
			case "image_video_repeater":

				$data[$name."_items"] = UniteFunctionsUC::getVal($value, $name."_items");

				//do nothing, convert later

			break;
			case "current_product_variations":
				
				$data[$name."_items"] = UniteCreatorWooIntegrate::getCurrentProductVariationImageItems();
				
			break;
			case "current_product_gallery":
				
				$data[$name."_items"] = UniteCreatorWooIntegrate::getCurrentProductGalleryIDs();
				
			break;
			case "instagram":

				$paramInstagram = $param;
				$paramInstagram["name"] = $paramInstagram["name"]."_instagram";

				$arrInstagramData = $this->getInstagramData($value, $name."_instagram", $paramInstagram);

				$error = UniteFunctionsUC::getVal($arrInstagramData, "error");
				if(!empty($error))
					UniteFunctionsUC::throwError($error);

				$arrInstagramItems = UniteFunctionsUC::getVal($arrInstagramData, "items");


				if(empty($arrInstagramItems))
					$arrInstagramItems = array();

				$data[$name."_items"] = $arrInstagramItems;

			break;
			default:
				UniteFunctionsUC::throwError("Wrong dynamic content source: $source");
			break;
		}

		if($isForGallery == true){

			$arrItems = $data[$name."_items"];

			$data[$name."_items"] = $this->getGroupedData_convertForGallery($arrItems, $source, $value, $param);


			return($data);
		}

		//modify items output
		$arrItems = UniteFunctionsUC::getVal($data, $name."_items");


		if(empty($arrItems))
			$arrItems = array();

		//convert listing items

		foreach($arrItems as $index => $item){

			$numItem = $index+1;

			switch($source){
				case "posts":
				case "products":
					$title = $item->post_title;

					$newItem = array(
						"index"=>$numItem,
						"title"=>$title,
						"object"=>$item
					);

				$postData = $this->getPostDataByObj($item);
				
				$arrFields = array("id","alias","link","intro","intro_full","excerpt","date","date_modified","image","image_thumb","image_thumb_large");
				
				foreach($arrFields as $fieldKey){

					if(array_key_exists($fieldKey, $postData) == false)
						continue;

					$value = UniteFunctionsUC::getVal($postData, $fieldKey);

					$newItem[$fieldKey] = $value;
				}

				break;
				case "terms":
				break;
				case "gallery":
					continue(2);
				break;
				default:
					$key = $index++;
					$title = "item_{$index}";
				break;
			}

			$arrItems[$index] = $newItem;
		}


		$data[$name."_items"] = $arrItems;



		return($data);
	}

	protected function z_______________REMOTE____________(){}

	/**
	 * update for the template switcher, to keep the sync inside the template
	 */
	private function modifySyncGroupName($syncParentName){
		
		if(empty(GlobalsProviderUC::$renderTemplateID)) 
			return($syncParentName);
			
		$syncParentName .= "_".GlobalsProviderUC::$renderTemplateID;
		
		return($syncParentName);
	}
	
	/**
	 * get remote parent type data
	 */
	private function getRemoteParentData($value, $name, $processType, $param, $data){

		$arrOutput = array();

		$isInsideEditor = GlobalsProviderUC::$isInsideEditor;

		$isEnable = UniteFunctionsUC::getVal($value, $name."_enable");
		$isEnable = UniteFunctionsUC::strToBool($isEnable);

		$isDebug = UniteFunctionsUC::getVal($value, $name."_debug");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		$isSync = UniteFunctionsUC::getVal($value, $name."_sync");
		$isSync = UniteFunctionsUC::strToBool($isSync);

		$widgetName = $this->addon->getTitle();

		if($isEnable == false && $isSync == false){

			$arrOutput["attributes"] = "";
			$arrOutput["class"] = "";
			$arrOutput["click_event"] = "click";

			$data[$name] = $arrOutput;

			return($data);
		}

		HelperUC::addRemoteControlsScript();

		$attributes = "";

		//get the name
		if($isEnable == true){

			$parentName = UniteFunctionsUC::getVal($value, $name."_name");

			if($parentName == "custom")
				$parentName = UniteFunctionsUC::getVal($value, $name."_custom_name");

			if(empty($parentName))
				$parentName = "auto";

			$parentName = UniteFunctionsUC::sanitizeAttr($parentName);

			//create attributes and classes

			$attributes .= " data-remoteid='$parentName'";
		}

		if($isDebug == true)
			$attributes .= " data-debug='true'";

		$widgetName = UniteFunctionsUC::sanitizeAttr($widgetName);

		if(!empty($widgetName))
			$attributes .= " data-widgetname='$widgetName'";

		if($isSync == true){
			
			$syncParentName = UniteFunctionsUC::getVal($value, $name."_sync_name");
			
			$syncParentName = $this->modifySyncGroupName($syncParentName);
						
			$attributes .= " data-sync='true' data-syncid='$syncParentName'";
		}

		$class = " uc-remote-parent";

		//output

		$arrOutput["attributes"] = $attributes;
		$arrOutput["class"] = $class;
		$arrOutput["click_event"] = $isInsideEditor?"ucclick":"click";

		$data[$name] = $arrOutput;

		return($data);
	}

	/**
	 * get background data
	 */
	private function getRemoteBackgroundData($value, $name, $processType, $param, $data){

		$isSync = UniteFunctionsUC::getVal($value, $name."_sync");
		$isSync = UniteFunctionsUC::strToBool($isSync);

		if($isSync == false){

			$arrOutput["attributes"] = "";
			$arrOutput["class"] = "";

			$data[$name] = $arrOutput;

			return($data);
		}

		$syncParentName = UniteFunctionsUC::getVal($value, $name."_sync_name");
		$remoteParentName = UniteFunctionsUC::getVal($value, $name."_remote_name");

		$isDebug = UniteFunctionsUC::getVal($value, $name."_debug");
		$isDebug = UniteFunctionsUC::strToBool($isDebug);

		HelperUC::addRemoteControlsScript();

		$syncParentName = $this->modifySyncGroupName($syncParentName);
		
		
		$attributes = "";
		$attributes .= " data-sync='true' data-syncid='$syncParentName' data-remoteid='$remoteParentName'";
		
		if($isDebug == true)
			$attributes .= " data-debug='true'";

		$widgetName = $this->addon->getTitle();
		$widgetName = UniteFunctionsUC::sanitizeAttr($widgetName);

		if(!empty($widgetName))
			$attributes .= " data-widgetname='$widgetName'";


		$class = " uc-remote-parent ";

		$arrOutput["attributes"] = $attributes;
		$arrOutput["class"] = $class;

		$data[$name] = $arrOutput;



		return($data);
	}


	/**
	 * add remote controller data
	 */
	private function getRemoteControllerData($value, $name, $processType, $param, $data){

		HelperUC::addRemoteControlsScript();

		$parentName = UniteFunctionsUC::getVal($value, $name."_name");

		if($parentName == "custom")
			$parentName = UniteFunctionsUC::getVal($value, $name."_custom_name");

		if(empty($name))
			$parentName = "auto";

		$parentName = UniteFunctionsUC::sanitizeAttr($parentName);

		$attributes = " data-parentid='$parentName'";

		//more parent

		$isAddMoreParent = UniteFunctionsUC::getVal($value, $name."_more_parent");

		$isAddMoreParent = UniteFunctionsUC::strToBool($isAddMoreParent);

		if($isAddMoreParent == true){

			$parentName2 = UniteFunctionsUC::getVal($value, $name."_name2");

			$parentName2 = UniteFunctionsUC::sanitizeAttr($parentName2);

			if(!empty($parentName2))
				$attributes .= " data-parentid2='$parentName2'";
		}

		//show debug
		$showDebug = UniteFunctionsUC::getVal($value, $name."_show_debug");
		$showDebug = UniteFunctionsUC::strToBool($showDebug);

		if($showDebug == true)
			$attributes .= " data-debug='true'";


		$arrOutput = array();
		$arrOutput["attributes"] = $attributes;

		$data[$name] = $arrOutput;

		return($data);
	}

	/**
	 * get remote settings data
	 */
	private function getRemoteSettingsData($value, $name, $processType, $param, $data){

		$type = UniteFunctionsUC::getVal($param, "remote_type");

		switch($type){
			case "controller":

				$data = $this->getRemoteControllerData($value, $name, $processType, $param, $data);

			break;
			default:
			case "parent":
				$data = $this->getRemoteParentData($value, $name, $processType, $param, $data);
			break;
			case "background":

				$data = $this->getRemoteBackgroundData($value, $name, $processType, $param, $data);

			break;
		}


		return($data);
	}



	protected function z_______________MULTISOURCE____________(){}

	/**
	 * get multisource data
	 */
	private function getMultisourceSettingsData($value, $name, $processType, $param, $data){


		$objMultisourceProcessor = new UniteCreatetorParamsProcessorMultisource();

		$objMultisourceProcessor->init($this);

		$data = $objMultisourceProcessor->getMultisourceSettingsData($value, $name, $processType, $param, $data);

		return($data);
	}



	protected function z_______________TERMS____________(){}


	/**
	 * get woo categories data
	 */
	protected function getWooCatsData($value, $name, $processType, $param){

		$selectionType = UniteFunctionsUC::getVal($value, $name."_type");

		//add params
		$params = array();
		$taxonomy = "product_cat";

		$showDebug = UniteFunctionsUC::getVal($value, $name."_show_query_debug");
		$showDebug = UniteFunctionsUC::strToBool($showDebug);


		if($selectionType == "manual"){

			$includeSlugs = UniteFunctionsUC::getVal($value, $name."_include");

			$arrTerms = UniteFunctionsWPUC::getSpecificTerms($includeSlugs, $taxonomy);

		}else{

				$orderBy =  UniteFunctionsUC::getVal($value, $name."_orderby");
				$orderDir =  UniteFunctionsUC::getVal($value, $name."_orderdir");

				$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty");

				$strExclude = UniteFunctionsUC::getVal($value, $name."_exclude");
				$strExclude = trim($strExclude);

				$excludeUncategorized = UniteFunctionsUC::getVal($value, $name."_excludeuncat");

				$parent = UniteFunctionsUC::getVal($value, $name."_parent");
				$parent = trim($parent);

				$includeChildren = UniteFunctionsUC::getVal($value, $name."_children");

				$parentID = 0;
				if(!empty($parent)){

					$term = UniteFunctionsWPUC::getTermBySlug("product_cat", $parent);

					if(!empty($term))
						$parentID = $term->term_id;
				}

				$isHide = false;
				if($hideEmpty == "hide")
					$isHide = true;

				//add exclude
				$arrExcludeSlugs = null;

				if(!empty($strExclude))
					$arrExcludeSlugs = explode(",", $strExclude);

				//exclude uncategorized
				if($excludeUncategorized == "exclude"){
					if(empty($arrExcludeSlugs))
						$arrExcludeSlugs = array();

					$arrExcludeSlugs[] = "uncategorized";
				}

				if($includeChildren == "not_include"){
					$params["parent"] = $parentID;

				}else{
					$params["child_of"] = $parentID;
				}


				$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
				if($isWpmlExists)
					$params["suppress_filters"] = false;

			if(!empty($orderBy)){

				$metaKey = "";
				if($orderBy == "meta_value" || $orderBy == "meta_value_num"){

					$metaKey = UniteFunctionsUC::getVal($value, $name."_orderby_meta_key");
					$metaKey = trim($metaKey);

					if(empty($metaKey))
						$orderBy = null;
					else
						$params["meta_key"] = $metaKey;
				}
			}

			$arrTerms = UniteFunctionsWPUC::getTerms($taxonomy, $orderBy, $orderDir, $isHide, $arrExcludeSlugs, $params);

			if($showDebug == true){
				echo "<div class='uc-div-ajax-debug'>";
					dmp("The terms query is:");
					dmp(UniteFunctionsWPUC::$arrLastTermsArgs);
					dmp("num terms found: ".count($arrTerms));
				echo "</div>";
			}


		}//not manual

		$arrTerms = $this->modifyArrTermsForOutput($arrTerms, $taxonomy);

		return($arrTerms);
	}

	/**
	 * add meta query
	 */
	private function addMetaQueryItem($arrMetaQuery, $metaKey, $metaValue, $metaCompare = "="){

		if(empty($metaKey))
			return($arrMetaQuery);

		if(empty($metaCompare))
			$metaCompare = "=";

		$isValueArray = false;
		switch($metaCompare){
			case "IN":
			case "NOT IN":
			case "BETWEEN":
			case "NOT BETWEEN":
				$isValueArray = true;
			break;
		}

		if($isValueArray == true){
			$arrValues = explode(",", $metaValue);
			foreach($arrValues as $key=>$value)
				$arrValues[$key] = trim($value);

			$value = $arrValues;
		}

		$arr = array();
		
		$arrItem = array(
		        'key'     => $metaKey,
		        'value'   => $metaValue,
		        'compare' => $metaCompare
		);

		$arrMetaQuery[] = $arrItem;

		return($arrMetaQuery);
	}


	/**
	 * get terms data
	 */
	public function getWPTermsData($value, $name, $processType, $param, $data){

				
		$postType = UniteFunctionsUC::getVal($value, $name."_posttype","post");
		$taxonomy =  UniteFunctionsUC::getVal($value, $name."_taxonomy","category");

		$orderBy =  UniteFunctionsUC::getVal($value, $name."_orderby","name");

		$orderDir =  UniteFunctionsUC::getVal($value, $name."_orderdir","ASC");

		$hideEmpty = UniteFunctionsUC::getVal($value, $name."_hideempty");
		
		$strExclude = UniteFunctionsUC::getVal($value, $name."_exclude");
		$excludeWithTree = UniteFunctionsUC::getVal($value, $name."_exclude_tree");
		$excludeWithTree = UniteFunctionsUC::strToBool($excludeWithTree);

		$showDebug = UniteFunctionsUC::getVal($value, $name."_show_query_debug");
		$showDebug = UniteFunctionsUC::strToBool($showDebug);
		

		$queryDebugType = "";
		if($showDebug == true)
			$queryDebugType = UniteFunctionsUC::getVal($value, $name."_query_debug_type");

		if(GlobalsUC::$showQueryDebugByUrl == true){
			$showDebug = true;
			$queryDebugType = "show_query";
		}
			
			
		$maxTerms = UniteFunctionsUC::getVal($value, $name."_maxterms");
		$maxTerms = (int)$maxTerms;
		if(empty($maxTerms))
			$maxTerms = 100;

		$arrIncludeBy = UniteFunctionsUC::getVal($value, $name."_includeby");
		if(empty($arrIncludeBy))
			$arrIncludeBy = array();
		
		//add special auto mode
		$arrWidgetValues = $this->addon->getOriginalValues();
		$filterRole = UniteFunctionsUC::getVal($arrWidgetValues, "filter_role");
		
		if($filterRole == UniteCreatorFiltersProcess::ROLE_CHILD_AUTO_TERMS)
			$arrIncludeBy[] = "auto_terms_main_filter_children";
		
		$arrExcludeBy = UniteFunctionsUC::getVal($value, $name."_excludeby");
		if(empty($arrExcludeBy))
			$arrExcludeBy = array();

		$arrExcludeIDs = array();

		if(is_string($strExclude))
			$strExclude = trim($strExclude);
		else{
			$arrExcludeIDs = $strExclude;
			$strExclude = null;
		}

		$useCustomFields = UniteFunctionsUC::getVal($param, "use_custom_fields");
		$useCustomFields = UniteFunctionsUC::strToBool($useCustomFields);


		$isHide = false;

		if($hideEmpty == "hide")
			$isHide = true;

		if(empty($postType)){
			$postType = "post";
			$taxonomy = "category";
		}

		if(empty($taxonomy))
			$taxonomy = "category";

		if(is_array($taxonomy) && count($taxonomy) == 1)
			$taxonomy = $taxonomy[0];
		
		//add exclude
		$arrExcludeSlugs = null;

		if(!empty($strExclude))
			$arrExcludeSlugs = explode(",", $strExclude);

		//includeby
		$arrIncludeTermIDs = array();
		$arrIncludeDirectChildrenOfSelectedTermsIDs = array();
		$includeParentID = null;
		$includeParentTermIdForAutoChild = null;
		$isDirectParent = true;
		
		$args = array();

		$arrMetaQuery = array();
		
		
		foreach($arrIncludeBy as $includeby){

			switch($includeby){
				case "spacific_terms":

					$arrIncludeTermIDs = UniteFunctionsUC::getVal($value, $name."_include_specific");

				break;
				case "direct_children_of_selected_terms":

					$arrIncludeDirectChildrenOfSelectedTermsIDs = UniteFunctionsUC::getVal($value, $name."_include_direct_children_of_selected_terms");

				break;
				case "parents":

					$includeParentID = UniteFunctionsUC::getVal($value, $name."_include_parent");
					if(is_array($includeParentID))
						$includeParentID = $includeParentID[0];

					$isDirectParent = UniteFunctionsUC::getVal($value, $name."_include_parent_isdirect");
					$isDirectParent = UniteFunctionsUC::strToBool($isDirectParent);

				break;
				case "search":

					$search = UniteFunctionsUC::getVal($value, $name."_include_search");
					$search = trim($search);

					if(!empty($search))
						$args["search"] = $search;

				break;
				case "childless":


					$args["childless"] = true;

				break;
				case "no_parent":

					$args["parent"] = "0";

				break;
				case "meta":

					$metaKey = UniteFunctionsUC::getVal($value, $name."_include_metakey");
					$metaValue = UniteFunctionsUC::getVal($value, $name."_include_metavalue");
					$metaCompare = UniteFunctionsUC::getVal($value, $name."_include_metacompare");


					$arrMetaQuery = $this->addMetaQueryItem($arrMetaQuery, $metaKey, $metaValue, $metaCompare);

				break;
				case "children_of_current":

					$parentTermID = UniteFunctionsWPUC::getCurrentTermID();

					$args["parent"] = $parentTermID;

				break;
				case "only_direct_children":	//not hierarchial

					$args["hierarchical"] = false;

				break;
				case "current_post_terms":

					$arrTermIDs = UniteFunctionsWPUC::getPostTermIDs();

					if(!empty($arrTermIDs))
						$arrIncludeTermIDs = array_merge($arrIncludeTermIDs, $arrTermIDs);
				
					if(empty($arrIncludeTermIDs))
						$arrIncludeTermIDs = array("999999999");

				break;
				case "auto_terms_main_filter_children":
					
					$arrIncludeTermIDs = array("0");
										
				break;
				default:
					dmp("wrong include by: $includeby");
				break;
			}

		}

		$hideFirstLevelTerms = false;

		foreach($arrExcludeBy as $excludeBy){

			switch($excludeBy){
				case "current_term":

					$currentTermID = UniteFunctionsWPUC::getCurrentTermID();
					if(!empty($currentTermID))
						$arrExcludeIDs[] = $currentTermID;

				break;
				case "hide_empty":
					$isHide = true;
				break;
				case "spacific_terms":
				break;
				case "current_post_terms":
					$arrTermIDs = UniteFunctionsWPUC::getPostTermIDs();

					if(!empty($arrTermIDs))
						$arrExcludeIDs  = array_merge($arrExcludeIDs, $arrTermIDs);

				break;
				case "hide_first_level_terms":
					$hideFirstLevelTerms = true;
				break;
				default:
					dmp("wrong exclude by: ".$excludeBy);
				break;
			}

		}

		if(!empty($arrMetaQuery))
			$args["meta_query"] = $arrMetaQuery;


		//---------- get the args

		$args["hide_empty"] = $isHide;
		$args["taxonomy"] = $taxonomy;
		$args["count"] = true;
		$args["number"] = $maxTerms;
		
		$isSortbyParentChildren = false;
		
		if(!empty($orderBy)){

			$metaKey = "";
			if($orderBy == "meta_value" || $orderBy == "meta_value_num"){
				
				$metaKey = UniteFunctionsUC::getVal($value, $name."_orderby_meta_key");
				$metaKey = trim($metaKey);

				if(empty($metaKey))
					$orderBy = null;
			}

			//set the default
			if($orderBy == "default"){

				$orderBy = "name";

				if(!empty($arrIncludeTermIDs))
					$orderBy = "include";
			}


			if(!empty($orderBy)){
				
				if($orderBy == "parent_children"){
					$orderBy = "parent_children";
					$isSortbyParentChildren = true;
				}
				
				$args["orderby"] = $orderBy;

				if(!empty($metaKey))
					$args["meta_key"] = $metaKey;

				if(empty($orderDir))
					$orderDir = UniteFunctionsWPUC::ORDER_DIRECTION_ASC;

				$args["order"] = $orderDir;
			}

			
			if($orderBy == "rand"){
				add_filter( 'terms_clauses', array($this, "randomOrderTaxonomyTerms"), 1, 1);
			}
			
		}


		//exclude
		if(!empty($arrExcludeIDs)){

			$key = "exclude";
			if($excludeWithTree == true)
				$key = "exclude_tree";

			$args[$key] = $arrExcludeIDs;
		}


		//include specific
		if(!empty($arrIncludeTermIDs)){

			if(!empty($arrExcludeIDs))
				$arrIncludeTermIDs = array_diff($arrIncludeTermIDs, $arrExcludeIDs);

			$args["include"] = $arrIncludeTermIDs;
		}


		//include children of selected parents terms
		if(!empty($arrIncludeDirectChildrenOfSelectedTermsIDs)){


			if(!empty($arrExcludeIDs))
				$arrIncludeDirectChildrenOfSelectedTermsIDs = array_diff($arrIncludeDirectChildrenOfSelectedTermsIDs, $arrExcludeIDs);
			
			if(!empty($arrIncludeDirectChildrenOfSelectedTermsIDs)){
				$this->arrIncludeDirectChildrenOfSelectedTermsIDs = $arrIncludeDirectChildrenOfSelectedTermsIDs;
				add_filter( 'terms_clauses', array($this, "getDirectChildrenOfSelectedTerms"), 1, 1);
			}
		}
		
			
		if(!empty($includeParentID)){
			$parentKey = "parent";
			if($isDirectParent == false)
				$parentKey = "child_of";

			$args[$parentKey] = $includeParentID;
		}


		if(!empty($includeParentTermIdForAutoChild)){
			
			unset($args['child_of']);
			
			$parentKey = "parent";
			$args[$parentKey] = $includeParentTermIdForAutoChild;
		}


		$isWpmlExists = UniteCreatorWpmlIntegrate::isWpmlExists();
		if($isWpmlExists)
			$args["suppress_filters"] = false;

		//------- get the terms and filter by slugs if available

		if($hideFirstLevelTerms == true)
			add_filter( 'terms_clauses', array($this, "hideFirstLevelTaxonomyTerms"), 1, 1);

		HelperUC::addDebug("Terms Query", $args);

		if($showDebug == true){
			echo "<div class='uc-div-ajax-debug'>";
			dmp("The terms query is:");
			dmp($args);
		}

		$args = $this->getPostListData_getCustomQueryFilters($args, $value, $name, $data, false);

		if($isHide){
			$args['selected_post_types'] = $postType;
			add_filter( 'terms_clauses', array($this, "filterTermsByPostTypes"), 10, 3);
		}

		$term_query = new WP_Term_Query();
		$arrTermsObjects = $term_query->query( $args );




		if($showDebug == true){
			dmp("terms found: ".count($arrTermsObjects));
		}

		//term query debug

		if($showDebug == true && $queryDebugType == "show_query"){

			$originalQueryVars = $term_query->query_vars;
			$originalQueryVars = UniteFunctionsWPUC::cleanQueryArgsForDebug($originalQueryVars);

			dmp("The Query Request Is:");
			dmp($term_query->request);

			dmp("The finals query vars:");
			dmp($originalQueryVars);


			$arrActions = UniteFunctionsWPUC::getFilterCallbacks("get_terms_args");

			dmp("Query modify callbacks ( get_terms_args ):");
			dmp($arrActions);

			$arrActions = UniteFunctionsWPUC::getFilterCallbacks("get_terms_orderby");

			dmp("Query modify callbacks ( get_terms_orderby ):");
			dmp($arrActions);

		}

		if(!empty($arrExcludeSlugs)){
			HelperUC::addDebug("Terms Before Filter:", $arrTermsObjects);
			HelperUC::addDebug("Exclude by:", $arrExcludeSlugs);
		}
		
		if(!empty($arrExcludeSlugs) && is_array($arrExcludeSlugs))
			$arrTermsObjects = UniteFunctionsWPUC::getTerms_filterBySlugs($arrTermsObjects, $arrExcludeSlugs);

		if($showDebug == true)
			echo "</div>";

		$useForListing = UniteFunctionsUC::getVal($param, "use_for_listing");
		$useForListing = UniteFunctionsUC::strToBool($useForListing);
		
		if($isSortbyParentChildren == true)
			$arrTermsObjects = UniteFunctionsWPUC::sortTermsByParents($arrTermsObjects);
		
		$arrTerms = UniteFunctionsWPUC::getTermsObjectsData($arrTermsObjects, $taxonomy);
		
		$arrTerms = $this->modifyArrTermsForOutput($arrTerms, $taxonomy, $useCustomFields, $postType);



		return($arrTerms);
	}

	/**
	 * filter remove empty terms for selected post types
	 */
	public function filterTermsByPostTypes( $clauses, $taxonomies, $args ) {
		if ( ! empty( $args['selected_post_types'] ) && is_array( $args['selected_post_types'] ) ) {
			global $wpdb;


			$post_types = $args['selected_post_types'];
			$post_types_in = "'" . implode( "','", $post_types ) . "'";

			$clauses['where'] .= "
                AND EXISTS (
                    SELECT 1
                    FROM {$wpdb->term_relationships} tr
                    INNER JOIN {$wpdb->posts} p ON p.ID = tr.object_id
                    WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
                    AND p.post_type IN ({$post_types_in})
                    AND p.post_status = 'publish'
                )
            ";
		}

		remove_filter( 'terms_clauses', array($this, "filterTermsByPostTypes"), 10, 3);

		return $clauses;
	}

	/**
	 * filter order random taxonomy terms
	 */
	public function randomOrderTaxonomyTerms($clauses) {
		
		$clauses["orderby"] = "ORDER BY RAND()";
		
		remove_filter( 'terms_clauses', array($this, "randomOrderTaxonomyTerms"), 1, 1);
		
		return $clauses;
	}


	/**
	 * filter hide first level taxonomy terms
	 */
	public function hideFirstLevelTaxonomyTerms($clauses) {
		
		// display only categories where parent != 0, it means display only child categories
		$clauses['where'] .= " AND tt.parent != 0";
		
		remove_filter( 'terms_clauses', array($this, "hideFirstLevelTaxonomyTerms"), 1, 1);
		
		return $clauses;
	}


	/**
	 * filter get direct children of selected parent terms
	 */
	public function getDirectChildrenOfSelectedTerms($clauses) {

		$termsIDs = implode(', ', $this->arrIncludeDirectChildrenOfSelectedTermsIDs);

		$clauses['where'] .= " AND tt.parent IN (" . $termsIDs . ")";

		remove_filter('terms_clauses', array($this, "getDirectChildrenOfSelectedTerms"), 1, 1);

		return $clauses;
	}


	protected function z_______________USERS____________(){}

	
	/**
	 * compare users order
	 */
	public function sortUsersByValues_compare($user1, $user2){
	    $a_index = array_search($user1->ID, $this->arrUsersOrder);
	    $b_index = array_search($user2->ID, $this->arrUsersOrder);
	    return $a_index - $b_index;
	}
	
	/**
	 * sort users by values
	 */
	private function sortUsersByValues($arrUsers,$manualOrder ){
		
		if(empty($arrUsers))
			return($arrUsers);
			
		if(count($arrUsers) == 1)
			return($arrUsers);
				
		$isIDsList = UniteFunctionsUC::isValidIDsList($manualOrder);
				
		if($isIDsList == false)
			return($arrUsers);
		
		$this->arrUsersOrder = UniteFunctionsUC::csvToArray($manualOrder);
		
		if(empty($this->arrUsersOrder))
			return($arrUsers);
		
		usort($arrUsers, array($this,'sortUsersByValues_compare'));		
		
		return($arrUsers);
	}
	
	/**
	 * modify users array for output
	 */
	public function modifyArrUsersForOutput($arrUsers, $getMeta, $getAvatar, $arrMetaKeys = null){

		if(empty($arrUsers))
			return(array());

		$arrUsersData = array();

		foreach($arrUsers as $objUser){

			$arrUser = UniteFunctionsWPUC::getUserData($objUser, $getMeta, $getAvatar, $arrMetaKeys);

			$arrUsersData[] = $arrUser;
		}
		
		return($arrUsersData);
	}


	/**
	 * get users data
	 */
	public function getWPUsersData($value, $name, $processType, $param){

		$showDebug = UniteFunctionsUC::getVal($value, $name."_show_query_debug");
		$showDebug = UniteFunctionsUC::strToBool($showDebug);

		$selectType = UniteFunctionsUC::getVal($value, $name."_type");

		$args = array();

		if($selectType == "manual"){		//manual select

			$arrIncludeUsers = UniteFunctionsUC::getVal($value, $name."_include_authors");
			if(empty($arrIncludeUsers))
				$arrIncludeUsers = array("0");

			$args["include"] = $arrIncludeUsers;

		}else{

			//create the args
			$strRoles = UniteFunctionsUC::getVal($value, $name."_role");

			if(is_array($strRoles))
				$arrRoles = $strRoles;
			else
				$arrRoles = explode(",", $strRoles);

			$arrRoles = UniteFunctionsUC::arrayToAssoc($arrRoles);
			unset($arrRoles["__all__"]);

			if(!empty($arrRoles)){
				$arrRoles = array_values($arrRoles);

				$args["role__in"] = $arrRoles;
			}

			//add exclude roles:
			$arrRolesExclude = UniteFunctionsUC::getVal($value, $name."_role_exclude");

			if(!empty($strRolesExclude) && is_string($strRolesExclude))
				$arrRolesExclude = explode(",", $arrRolesExclude);

			if(!empty($arrRolesExclude))
				$args["role__not_in"] = $arrRolesExclude;

			//--- number of users

			$numUsers = UniteFunctionsUC::getVal($value, $name."_maxusers");
			$numUsers = (int)$numUsers;

			if(!empty($numUsers))
				$args["number"] = $numUsers;

			//--- exclude by users

			$arrExcludeAuthors = UniteFunctionsUC::getVal($value, $name."_exclude_authors");

			if(!empty($arrExcludeAuthors))
				$args["exclude"] = $arrExcludeAuthors;


		}

		//--- orderby ---

		$orderby = UniteFunctionsUC::getVal($value, $name."_orderby");

		$isManualOrder = false;
		
		if($orderby == "manual")
			$isManualOrder = true;
		
		if($orderby == "default" || $orderby == "manual")
			$orderby = null;
		
		if(!empty($orderby))
			$args["orderby"] = $orderby;
		
		//--- order dir ----
	
		$orderdir = UniteFunctionsUC::getVal($value, $name."_orderdir");
		if($orderdir == "default")
			$orderdir = null;
		
		if(!empty($orderdir))
			$args["order"] = $orderdir;
		
		
		//---- debug

		if($showDebug == true){
			dmp("The users query is:");
			dmp($args);
		}

		HelperUC::addDebug("Get Users Args", $args);

		$arrUsers = get_users($args);
		
		HelperUC::addDebug("Num Users fetched: ".count($arrUsers));

		if($showDebug == true){
			dmp("Num Users fetched: ".count($arrUsers));
		}

		
		if($isManualOrder == true){
			
			$manualOrder = UniteFunctionsUC::getVal($value, $name."_order_manual");
			
			if(!empty($manualOrder)){
								
				if($showDebug == true){
					
					dmp("sorting users by: $manualOrder");
					
					$arrOrderShow = array();
					foreach($arrUsers as $index => $user){
						
						$name = $user->display_name;
						$id = $user->ID;
						
						$arrOrderShow[] = "$name [$id]";
					}
					
					$strShow = implode(",", $arrOrderShow);
					
					dmp("Selected Users Order: ".$strShow);
					
					dmp("sorting users by: $manualOrder");
				}
				
				$arrUsers = $this->sortUsersByValues($arrUsers,$manualOrder );
			}
		}
		
		
		$getMeta = UniteFunctionsUC::getVal($param, "get_meta");
		$getMeta = UniteFunctionsUC::strToBool($getMeta);

		$getAvatar = UniteFunctionsUC::getVal($param, "get_avatar");
		$getAvatar = UniteFunctionsUC::strToBool($getAvatar);

		//add meta fields

		$strAddMetaKeys = UniteFunctionsUC::getVal($value, $name."_add_meta_keys");

		$arrMetaKeys = null;
		if(!empty($strAddMetaKeys))
			$arrMetaKeys = explode(",", $strAddMetaKeys);
		
		$arrUsers = $this->modifyArrUsersForOutput($arrUsers, $getMeta, $getAvatar, $arrMetaKeys);

		return($arrUsers);
	}

	protected function z_______________MENU____________(){}


	/**
	 * get menu output
	 */
	public function getWPMenuData($data, $value, $name, $param, $processType){

		$menuID = UniteFunctionsUC::getVal($value, $name."_id");

		//get first menu
		if(empty($menuID)){

			$htmlMenu = __("menu not selected","unlimited-elements-for-elementor");
			$data[$name] = $htmlMenu;

			return($data);
		}

		$depth = UniteFunctionsUC::getVal($value, $name."_depth");

		$depth = (int)$depth;

		//make the arguments
		$args = array();
		$args["echo"] = false;
		$args["container"] = "";

		if(!empty($depth) && is_numeric($depth))
			$args["depth"] = $depth;
		
		$args["menu"] = $menuID;

		$arrKeysToAdd = array(
			"menu_class",
			"before",
			"after"
		);

		foreach($arrKeysToAdd as $key){

			$value = UniteFunctionsUC::getVal($param, $key);
			if(!empty($value))
				$args[$key] = $value;
		}

		HelperUC::addDebug("menu arguments", $args);

		$htmlMenu = wp_nav_menu($args);

		$data[$name."_id"] = $menuID;
		$data[$name] = $htmlMenu;

		return($data);
	}


	protected function z_______________TEMPLATE____________(){}

	/**
	 * get template data
	 */
	private function getElementorTemplateData($value, $name, $processType, $param, $data){
		
		$templateID = UniteFunctionsUC::getVal($value, $name."_templateid");

		if(empty($templateID))
			return($data);

		if($templateID == "__none__")
			$templateID = "";

		if(empty($templateID))
			$shortcode = "";
		else
			$shortcode = "[elementor-template id=\"$templateID\"]";

		$data[$name] = $shortcode;
		$data[$name."_templateid"] = $templateID;

		return($data);
	}

	protected function z_______________POST_FILTERS____________(){}



	/**
	 * get post filter options
	 */
	private function modifyData_postFilterOptions($data, $filterType){

		$objFilters = new UniteCreatorFiltersProcess();

		$data = $objFilters->addEditorFilterArguments($data, $filterType);

		return($data);
	}


	/**
	 * modify data by special behaviour
	 */
	protected function modifyDataBySpecialAddonBehaviour($data){

		$special = $this->addon->getOption("special");
		$specialData = $this->addon->getOption("special_data");

		if(empty($special))
			return $data;

		if($this->processType === self::PROCESS_TYPE_CONFIG)
			return $data;

		//skip backend editor
	
		switch($special){
			case "post_filter":
				$data = $this->modifyData_postFilterOptions($data, $specialData);
			break;
			case "ue_form":
				$objFrom = new UniteCreatorForm();
				$objFrom->addFormIncludes();
			break;
		}

		return $data;
	}

	protected function z_______________GET_PARAMS____________(){}


	/**
	 * get processed param data, function with override
	 */
	public function getProcessedParamData($data, $value, $param, $processType){

		$type = UniteFunctionsUC::getVal($param, "type");
		$name = UniteFunctionsUC::getVal($param, "name");

		//special params
		switch($type){
			case UniteCreatorDialogParam::PARAM_POSTS_LIST:
				$data = $this->getPostListData($value, $name, $processType, $param, $data);
			break;
			case UniteCreatorDialogParam::PARAM_LISTING:
				$data = $this->getListingData($value, $name, $processType, $param, $data);
			break;
			case UniteCreatorDialogParam::PARAM_POST_TERMS:
				$data[$name] = $this->getWPTermsData($value, $name, $processType, $param, $data);
			break;
			case UniteCreatorDialogParam::PARAM_WOO_CATS:
				$data[$name] = $this->getWooCatsData($value, $name, $processType, $param);
			break;
			case UniteCreatorDialogParam::PARAM_USERS:
				
				$data[$name."_settings"] = $data[$name];
				
				$data[$name] = $this->getWPUsersData($value, $name, $processType, $param);
				
			break;
			case UniteCreatorDialogParam::PARAM_TEMPLATE:
				$data = $this->getElementorTemplateData($value, $name, $processType, $param, $data);
			break;
			default:
				$data = parent::getProcessedParamData($data, $value, $param, $processType);
			break;
		}

		return $data;
	}

	/**
	 * set extra params value, add it to the param values fields
	 * like value_extra = something
	 */
	public function setExtraParamsValues($paramType, $param, $name, $arrValues){

	    switch($paramType){
	    	//add size param for image
	    	case UniteCreatorDialogParam::PARAM_IMAGE:

	    		$isAddSizes = UniteFunctionsUC::getVal($param, "add_image_sizes");
	    		$isAddSizes = UniteFunctionsUC::strToBool($isAddSizes);

	    		if($isAddSizes == true){
	    			$existingSize = UniteFunctionsUC::getVal($param, "value_size");

	    			$newSize = UniteFunctionsUC::getVal($arrValues, $name."_size");

	    			if(empty($newSize) && !empty($existingSize))
	    				$newSize = $existingSize;

	    			$param["value_size"] = $newSize;
	    		}

	    	break;
	    }

	    return($param);
	}


	/**
	 * get param value, function for override, by type
	 * to get multiple values from one, as array
	 */
	public function getSpecialParamValue($paramType, $paramName, $value, $arrValues){

	    switch($paramType){
	        case UniteCreatorDialogParam::PARAM_POSTS_LIST:
	        case UniteCreatorDialogParam::PARAM_LISTING:
	        case UniteCreatorDialogParam::PARAM_POST_TERMS:
	        case UniteCreatorDialogParam::PARAM_WOO_CATS:
	        case UniteCreatorDialogParam::PARAM_USERS:
	        case UniteCreatorDialogParam::PARAM_CONTENT:
	        case UniteCreatorDialogParam::PARAM_BACKGROUND:
	        case UniteCreatorDialogParam::PARAM_MENU:
	        case UniteCreatorDialogParam::PARAM_SPECIAL:
	        case UniteCreatorDialogParam::PARAM_INSTAGRAM:
	        case UniteCreatorDialogParam::PARAM_TEMPLATE:

	            $paramArrValues = array();
	            $paramArrValues[$paramName] = $value;

	            foreach($arrValues as $key=>$value){
	                if(strpos($key, $paramName."_") === 0)
	                    $paramArrValues[$key] = $value;
	            }

	            $value = $paramArrValues;

	        break;
	    }

	    return($value);
	}



}
