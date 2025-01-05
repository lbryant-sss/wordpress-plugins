<?php

namespace SolidWP\Mail;

use SolidWP\Mail\Repository\LogsRepository;
use SolidWP\Mail\Repository\ProvidersRepository;

/**
 * Class AbstractComponent
 *
 * This abstract class provides a base for components that require access to the ProvidersRepository.
 *
 * @package Solid_SMTP
 */
abstract class AbstractComponent {

	/**
	 * Constructor
	 */
	public function __construct() {
	}
}
