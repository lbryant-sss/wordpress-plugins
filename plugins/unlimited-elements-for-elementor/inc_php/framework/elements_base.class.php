<?php
/**
 * @package Unlimited Elements
 * @author unlimited-elements.com
 * @copyright (C) 2021 Unlimited Elements, All Rights Reserved. 
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * */
if ( ! defined( 'ABSPATH' ) ) exit;


	class UniteElementsBaseUC{
		
		protected $db;
		protected $imageView;
		
		public function __construct(){
			
			$this->db = new UniteCreatorDB();
			$this->imageView = new UniteImageViewUC();
			
		}
		
	}
