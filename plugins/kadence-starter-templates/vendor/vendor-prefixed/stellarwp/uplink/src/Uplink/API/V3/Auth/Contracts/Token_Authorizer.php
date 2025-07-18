<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */ declare( strict_types=1 );

namespace KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\API\V3\Auth\Contracts;
use WP_Error;

interface Token_Authorizer {

	/**
	 * Check if a license is authorized.
	 *
	 * @see is_authorized()
	 * @see \KadenceWP\KadenceStarterTemplates\StellarWP\Uplink\API\V3\Auth\Token_Authorizer
	 *
	 * @param  string  $license  The license key.
	 * @param  string  $slug     The plugin/service slug.
	 * @param  string  $token    The stored token.
	 * @param  string  $domain   The user's domain.
	 *
	 * @return bool|WP_Error
	 */
	public function is_authorized( string $license, string $slug, string $token, string $domain );

}
