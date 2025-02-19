<?php
namespace Ollyo\PaymentHub\Contracts\Support;

interface UriContract
{
	/**
	 * Include the scheme (http, https, etc.)
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const SCHEME = 1;

	/**
	 * Include the host
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const HOST = 8;

	/**
	 * Include the port
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const PORT = 16;

	/**
	 * Include the path
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const PATH = 32;

	/**
	 * Include the query string
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const QUERY = 64;

	/**
	 * Include the fragment
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const FRAGMENT = 128;

	/**
	 * Include all available url parts (scheme, user, pass, host, port, path, query, fragment)
	 *
	 * @var    integer
	 * @since  1.2.0
	 */
	public const ALL = 255;

	/**
	 * Magic method to get the string representation of the URI object.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function __toString();

	/**
	 * Returns full URI string.
	 *
	 * @param   array  $parts  An array of strings specifying the parts to render.
	 *
	 * @return  string  The rendered URI string.
	 *
	 * @since   1.0
	 */
	public function toString($parts = ['scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment']);

	/**
	 * Checks if variable exists.
	 *
	 * @param   string  $name  Name of the query variable to check.
	 *
	 * @return  boolean  True if the variable exists.
	 *
	 * @since   1.0
	 */
	public function hasVar($name);

	/**
	 * Returns a query variable by name.
	 *
	 * @param   string  $name     Name of the query variable to get.
	 * @param   string  $default  Default value to return if the variable is not set.
	 *
	 * @return  mixed  Requested query variable if present otherwise the default value.
	 *
	 * @since   1.0
	 */
	public function getVar($name, $default = null);


	/**
	 * Set a query param variable 
	 *
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public function setVar($name, $value);

	/**
	 * Returns flat query string.
	 *
	 * @param   boolean  $toArray  True to return the query as a key => value pair array.
	 *
	 * @return  array|string   Query string, optionally as an array.
	 *
	 * @since   1.0
	 */
	public function getQuery($toArray = false);

	/**
	 * Get the URI scheme (protocol)
	 *
	 * @return  string  The URI scheme.
	 *
	 * @since   1.0
	 */
	public function getScheme();

	/**
	 * Get the URI host
	 *
	 * @return  string  The hostname/IP or null if no hostname/IP was specified.
	 *
	 * @since   1.0
	 */
	public function getHost();

	/**
	 * Get the URI port
	 *
	 * @return  integer  The port number, or null if no port was specified.
	 *
	 * @since   1.0
	 */
	public function getPort();

	/**
	 * Gets the URI path string
	 *
	 * @return  string  The URI path string.
	 *
	 * @since   1.0
	 */
	public function getPath();

	/**
	 * Get the URI archor string
	 *
	 * @return  string  The URI anchor string.
	 *
	 * @since   1.0
	 */
	public function getFragment();

	/**
	 * Checks whether the current URI is using HTTPS.
	 *
	 * @return  boolean  True if using SSL via HTTPS.
	 *
	 * @since   1.0
	 */
	public function isSsl();
}