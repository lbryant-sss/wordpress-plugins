<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license https://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik;

use Exception;
use Piwik\Container\StaticContainer;
use Piwik\Period\Day;
use Piwik\Period\Month;
use Piwik\Period\Range;
use Piwik\Period\Week;
use Piwik\Period\Year;
use Piwik\Plugins\UsersManager\API as APIUsersManager;
use Piwik\Plugins\UsersManager\Model;
use Piwik\Translation\Translator;
/**
 * Main piwik helper class.
 *
 * Contains helper methods for a variety of common tasks. Plugin developers are
 * encouraged to reuse these methods as much as possible.
 */
class Piwik
{
    /**
     * Piwik periods
     * @var array
     */
    public static $idPeriods = array('day' => Day::PERIOD_ID, 'week' => Week::PERIOD_ID, 'month' => Month::PERIOD_ID, 'year' => Year::PERIOD_ID, 'range' => Range::PERIOD_ID);
    /**
     * The idGoal query parameter value for the special 'abandoned carts' goal.
     *
     * @api
     */
    public const LABEL_ID_GOAL_IS_ECOMMERCE_CART = 'ecommerceAbandonedCart';
    /**
     * The idGoal query parameter value for the special 'ecommerce' goal.
     *
     * @api
     */
    public const LABEL_ID_GOAL_IS_ECOMMERCE_ORDER = 'ecommerceOrder';
    /**
     * Trigger E_USER_ERROR with optional message
     *
     * @param string $message
     */
    public static function error($message = '')
    {
        trigger_error($message, \E_USER_ERROR);
    }
    /**
     * Display the message in a nice red font with a nice icon
     * ... and dies
     *
     * @param string $message
     */
    public static function exitWithErrorMessage($message)
    {
        \Piwik\Common::sendHeader('Content-Type: text/html; charset=utf-8');
        $message = str_replace("\n", "<br/>", $message);
        $output = "<html><body>" . "<style>a{color:red;}</style>\n" . "<div style='color:red;font-size:120%; width:100%;margin: 30px;'>" . " <div style='width: 50px; float: left;'><img src='plugins/Morpheus/images/error_medium.png' /></div>" . "  <div style='margin-left: 70px; min-width: 950px;'>" . $message . "  </div>" . " </div>" . "</div>" . "</body></html>";
        print $output;
        exit;
    }
    /**
     * Computes the division of i1 by i2. If either i1 or i2 are not number, or if i2 has a value of zero
     * we return 0 to avoid the division by zero.
     *
     * @param number $i1
     * @param number $i2
     * @return number The result of the division or zero
     */
    public static function secureDiv($i1, $i2)
    {
        if (is_numeric($i1) && is_numeric($i2) && floatval($i2) != 0) {
            return $i1 / $i2;
        }
        return 0;
    }
    /**
     * Safely compute a percentage.  Return 0 to avoid division by zero.
     *
     * @param number $dividend
     * @param number $divisor
     * @param int $precision
     * @return number
     */
    public static function getPercentageSafe($dividend, $divisor, $precision = 0)
    {
        return self::getQuotientSafe(100 * $dividend, $divisor, $precision);
    }
    /**
     * Safely compute a ratio. Returns 0 if divisor is 0 (to avoid division by 0 error).
     *
     * @param number $dividend
     * @param number $divisor
     * @param int $precision
     * @return number
     */
    public static function getQuotientSafe($dividend, $divisor, $precision = 0)
    {
        if ($divisor == 0) {
            return 0;
        }
        if ($dividend == 0 || $dividend === '-') {
            $dividend = 0;
        }
        if (!is_numeric($dividend) || !is_numeric($divisor)) {
            throw new \Exception(sprintf('Trying to round unsupported operands for dividend %s (%s) and divisor %s (%s)', $dividend, gettype($dividend), $divisor, gettype($divisor)));
        }
        return round($dividend / $divisor, $precision);
    }
    /**
     * Generate a title for image tags
     *
     * @return string
     */
    public static function getRandomTitle()
    {
        static $titles = array('Web analytics', 'Open analytics platform', 'Real Time Web Analytics', 'Analytics', 'Real Time Analytics', 'Analytics in Real time', 'Analytics Platform', 'Data Platform');
        $id = abs(intval(md5(\Piwik\Url::getCurrentHost())));
        $title = $titles[$id % count($titles)];
        return $title;
    }
    /*
     * Access
     */
    /**
     * Returns the current user's email address.
     *
     * @return string
     * @api
     */
    public static function getCurrentUserEmail()
    {
        $user = APIUsersManager::getInstance()->getUser(\Piwik\Piwik::getCurrentUserLogin());
        return $user['email'] ?? '';
    }
    public static function getCurrentUserCreationDate()
    {
        $user = APIUsersManager::getInstance()->getUser(\Piwik\Piwik::getCurrentUserLogin());
        return $user['date_registered'] ?? '';
    }
    /**
     * Returns the current user's Last Seen.
     *
     * @return string
     * @api
     */
    public static function getCurrentUserLastSeen()
    {
        $user = APIUsersManager::getInstance()->getUser(\Piwik\Piwik::getCurrentUserLogin());
        return $user['last_seen'] ?? '';
    }
    /**
     * Returns the email addresses configured as contact. If none is configured the mail addresses of all super users will be returned instead.
     *
     * @return array
     */
    public static function getContactEmailAddresses() : array
    {
        $contactAddresses = trim(\Piwik\Config::getInstance()->General['contact_email_address']);
        if (empty($contactAddresses)) {
            return self::getAllSuperUserAccessEmailAddresses();
        }
        $contactAddresses = explode(',', $contactAddresses);
        return array_map('trim', $contactAddresses);
    }
    /**
     * Get a list of all email addresses having Super User access.
     *
     * @return array
     */
    public static function getAllSuperUserAccessEmailAddresses()
    {
        $emails = array();
        try {
            $superUsers = APIUsersManager::getInstance()->getUsersHavingSuperUserAccess();
        } catch (\Exception $e) {
            return $emails;
        }
        foreach ($superUsers as $superUser) {
            $emails[$superUser['login']] = $superUser['email'];
        }
        return $emails;
    }
    /**
     * Returns the current user's username.
     *
     * @return string
     * @api
     */
    public static function getCurrentUserLogin()
    {
        $login = \Piwik\Access::getInstance()->getLogin();
        if (empty($login)) {
            return 'anonymous';
        }
        return $login;
    }
    /**
     * Returns the current user's token auth.
     *
     * @return string
     * @api
     */
    public static function getCurrentUserTokenAuth()
    {
        return \Piwik\Access::getInstance()->getTokenAuth();
    }
    /**
     * Returns `true` if the current user is either the Super User or the user specified by
     * `$theUser`.
     *
     * @param string $theUser A username.
     * @return bool
     * @api
     */
    public static function hasUserSuperUserAccessOrIsTheUser($theUser)
    {
        try {
            self::checkUserHasSuperUserAccessOrIsTheUser($theUser);
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Returns if the given user needs to confirm his password in UI and for certain API methods
     *
     * @param string $login
     * @return bool
     */
    public static function doesUserRequirePasswordConfirmation(string $login)
    {
        $requiresPasswordConfirmation = \true;
        /**
         * Triggered to check if a password confirmation for a user is required.
         *
         * This event can be used in custom login plugins to skip the password confirmation checks for certain users,
         * where e.g. no password would be available.
         *
         * Attention: Use this event wisely. Disabling password confirmation decreases the security.
         *
         * @param bool $requiresPasswordConfirmation Indicates if the password should be checked or not
         * @param string $login Login of a user the password should be confirmed for
         */
        \Piwik\Piwik::postEvent('Login.userRequiresPasswordConfirmation', [&$requiresPasswordConfirmation, $login]);
        return $requiresPasswordConfirmation;
    }
    /**
     * Check that the current user is either the specified user or the superuser.
     *
     * @param string $theUser A username.
     * @throws NoAccessException If the user is neither the Super User nor the user `$theUser`.
     * @api
     */
    public static function checkUserHasSuperUserAccessOrIsTheUser($theUser)
    {
        try {
            if (\Piwik\Piwik::getCurrentUserLogin() !== $theUser) {
                // or to the Super User
                \Piwik\Piwik::checkUserHasSuperUserAccess();
            }
        } catch (\Piwik\NoAccessException $e) {
            throw new \Piwik\NoAccessException(\Piwik\Piwik::translate('General_ExceptionCheckUserHasSuperUserAccessOrIsTheUser', array($theUser)));
        }
    }
    /**
     * Request a token auth to authenticate in a request.
     *
     * Note: During one request the token is only being requested once and used throughout the request. So you want to make
     * sure the token is valid for enough time for the whole request to finish.
     *
     * @param string $reason some short string/text explaining the reason for the token generation, eg "CliMultiAsyncHttpArchiving"
     * @param int $validForHours For how many hours the token should be valid. Should not be valid for more than 14 days.
     * @return mixed
     */
    public static function requestTemporarySystemAuthToken($reason, $validForHours)
    {
        static $token = array();
        if (isset($token[$reason])) {
            // note: For now we do not increase the expire time when it is already requested
            return $token[$reason];
        }
        $twoWeeksInHours = 14 * 24;
        if ($validForHours > $twoWeeksInHours) {
            throw new Exception('The token cannot be valid for so many hours: ' . $validForHours);
        }
        $model = new Model();
        $users = $model->getUsersHavingSuperUserAccess();
        if (!empty($users)) {
            $user = reset($users);
            $expireDate = \Piwik\Date::now()->addHour($validForHours)->getDatetime();
            $token[$reason] = $model->generateRandomTokenAuth();
            $model->addTokenAuth($user['login'], $token[$reason], 'System generated ' . $reason, \Piwik\Date::now()->getDatetime(), $expireDate, \true);
            return $token[$reason];
        }
    }
    /**
     * Check whether the given user has superuser access.
     *
     * @param string $theUser A username.
     * @return bool
     * @api
     */
    public static function hasTheUserSuperUserAccess($theUser)
    {
        if (empty($theUser)) {
            return \false;
        }
        if (\Piwik\Piwik::getCurrentUserLogin() === $theUser && \Piwik\Piwik::hasUserSuperUserAccess()) {
            return \true;
        }
        try {
            $superUsers = APIUsersManager::getInstance()->getUsersHavingSuperUserAccess();
        } catch (\Exception $e) {
            return \false;
        }
        foreach ($superUsers as $superUser) {
            if ($theUser === $superUser['login']) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Returns true if the current user has Super User access.
     *
     * @return bool
     * @api
     */
    public static function hasUserSuperUserAccess()
    {
        try {
            $hasAccess = \Piwik\Access::getInstance()->hasSuperUserAccess();
            return $hasAccess;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Returns true if the current user is the special **anonymous** user or not.
     *
     * @return bool
     * @api
     */
    public static function isUserIsAnonymous()
    {
        $currentUserLogin = \Piwik\Piwik::getCurrentUserLogin();
        $isSuperUser = self::hasUserSuperUserAccess();
        return !$isSuperUser && $currentUserLogin && strtolower($currentUserLogin) == 'anonymous';
    }
    /**
     * Checks that the user is not the anonymous user.
     *
     * @throws NoAccessException if the current user is the anonymous user.
     * @api
     */
    public static function checkUserIsNotAnonymous()
    {
        \Piwik\Access::getInstance()->checkUserIsNotAnonymous();
    }
    /**
     * Check that the current user has superuser access.
     *
     * @throws Exception if the current user is not the superuser.
     * @api
     */
    public static function checkUserHasSuperUserAccess()
    {
        \Piwik\Access::getInstance()->checkUserHasSuperUserAccess();
    }
    /**
     * Returns `true` if the user has admin access to the requested sites, `false` if otherwise.
     *
     * @param int|array $idSites The list of site IDs to check access for.
     * @return bool
     * @api
     */
    public static function isUserHasAdminAccess($idSites)
    {
        try {
            self::checkUserHasAdminAccess($idSites);
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Checks that the current user has admin access to the requested list of sites.
     *
     * @param int|array $idSites One or more site IDs to check access for.
     * @throws Exception If user doesn't have admin access.
     * @api
     */
    public static function checkUserHasAdminAccess($idSites)
    {
        \Piwik\Access::getInstance()->checkUserHasAdminAccess($idSites);
    }
    /**
     * Returns `true` if the current user has admin access to at least one site.
     *
     * @return bool
     * @api
     */
    public static function isUserHasSomeAdminAccess()
    {
        return \Piwik\Access::getInstance()->isUserHasSomeAdminAccess();
    }
    /**
     * Checks that the current user has write access to at least one site.
     *
     * @throws Exception if user doesn't have write access to any site.
     * @api
     */
    public static function checkUserHasSomeWriteAccess()
    {
        \Piwik\Access::getInstance()->checkUserHasSomeWriteAccess();
    }
    /**
     * Returns `true` if the current user has write access to at least one site.
     *
     * @return bool
     * @api
     */
    public static function isUserHasSomeWriteAccess()
    {
        return \Piwik\Access::getInstance()->isUserHasSomeWriteAccess();
    }
    /**
     * Checks whether the user has the given capability or not.
     * @param array $idSites
     * @param string $capability
     * @throws NoAccessException Thrown if the user does not have the given capability
     */
    public static function checkUserHasCapability($idSites, $capability)
    {
        \Piwik\Access::getInstance()->checkUserHasCapability($idSites, $capability);
    }
    /**
     * Returns `true` if the current user has the given capability for the given sites.
     *
     * @return bool
     * @api
     */
    public static function isUserHasCapability($idSites, $capability)
    {
        try {
            self::checkUserHasCapability($idSites, $capability);
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Checks that the current user has admin access to at least one site.
     *
     * @throws Exception if user doesn't have admin access to any site.
     * @api
     */
    public static function checkUserHasSomeAdminAccess()
    {
        \Piwik\Access::getInstance()->checkUserHasSomeAdminAccess();
    }
    /**
     * Returns `true` if the user has view access to the requested list of sites.
     *
     * @param int|array $idSites One or more site IDs to check access for.
     * @return bool
     * @api
     */
    public static function isUserHasViewAccess($idSites)
    {
        try {
            self::checkUserHasViewAccess($idSites);
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Returns `true` if the user has write access to the requested list of sites.
     *
     * @param int|array $idSites One or more site IDs to check access for.
     * @return bool
     * @api
     */
    public static function isUserHasWriteAccess($idSites)
    {
        try {
            self::checkUserHasWriteAccess($idSites);
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Checks that the current user has view access to the requested list of sites
     *
     * @param int|array $idSites The list of site IDs to check access for.
     * @throws Exception if the current user does not have view access to every site in the list.
     * @api
     */
    public static function checkUserHasViewAccess($idSites)
    {
        \Piwik\Access::getInstance()->checkUserHasViewAccess($idSites);
    }
    /**
     * Checks that the current user has write access to the requested list of sites
     *
     * @param int|array $idSites The list of site IDs to check access for.
     * @throws Exception if the current user does not have write access to every site in the list.
     * @api
     */
    public static function checkUserHasWriteAccess($idSites)
    {
        \Piwik\Access::getInstance()->checkUserHasWriteAccess($idSites);
    }
    /**
     * Returns `true` if the current user has view access to at least one site.
     *
     * @return bool
     * @api
     */
    public static function isUserHasSomeViewAccess()
    {
        try {
            self::checkUserHasSomeViewAccess();
            return \true;
        } catch (Exception $e) {
            return \false;
        }
    }
    /**
     * Checks that the current user has view access to at least one site.
     *
     * @throws Exception if user doesn't have view access to any site.
     * @api
     */
    public static function checkUserHasSomeViewAccess()
    {
        \Piwik\Access::getInstance()->checkUserHasSomeViewAccess();
    }
    /*
     * Current module, action, plugin
     */
    /**
     * Returns the name of the Login plugin currently being used.
     * Must be used since it is not allowed to hardcode 'Login' in URLs
     * in case another Login plugin is being used.
     *
     * @return string
     * @api
     */
    public static function getLoginPluginName()
    {
        return StaticContainer::get('Piwik\\Auth')->getName();
    }
    /**
     * Returns the plugin currently being used to display the page
     *
     * @return Plugin
     */
    public static function getCurrentPlugin()
    {
        return \Piwik\Plugin\Manager::getInstance()->getLoadedPlugin(\Piwik\Piwik::getModule());
    }
    /**
     * Returns the current module read from the URL (eg. 'API', 'DevicesDetection', etc.)
     *
     * @return string
     */
    public static function getModule()
    {
        return \Piwik\Common::getRequestVar('module', '', 'string');
    }
    /**
     * Returns the current action read from the URL
     *
     * @return string
     */
    public static function getAction()
    {
        return \Piwik\Common::getRequestVar('action', '', 'string');
    }
    /**
     * Helper method used in API function to introduce array elements in API parameters.
     * Array elements can be passed by comma separated values, or using the notation
     * array[]=value1&array[]=value2 in the URL.
     * This function will handle both cases and return the array.
     *
     * @param array|string $columns
     * @return array
     */
    public static function getArrayFromApiParameter($columns, $unique = \true)
    {
        if (empty($columns)) {
            return array();
        }
        if (is_array($columns)) {
            return $columns;
        }
        $array = explode(',', $columns);
        if ($unique) {
            $array = array_unique($array);
        }
        return $array;
    }
    /**
     * Redirects the current request to a new module and action.
     *
     * @param string $newModule The target module, eg, `'UserCountry'`.
     * @param string $newAction The target controller action, eg, `'index'`.
     * @param array $parameters The query parameter values to modify before redirecting.
     * @api
     */
    public static function redirectToModule($newModule, $newAction = '', $parameters = array())
    {
        $newUrl = 'index.php' . \Piwik\Url::getCurrentQueryStringWithParametersModified(array('module' => $newModule, 'action' => $newAction) + $parameters);
        \Piwik\Url::redirectToUrl($newUrl);
    }
    /*
     * User input validation
     */
    /**
     * Returns `true` if supplied the email address is a valid.
     *
     * @param string $emailAddress
     * @return bool
     * @api
     */
    public static function isValidEmailString($emailAddress)
    {
        return filter_var($emailAddress, \FILTER_VALIDATE_EMAIL) !== \false;
    }
    /**
     * Returns `true` if the login is valid.
     *
     * _Warning: does not check if the login already exists! You must use UsersManager_API->userExists as well._
     *
     * @param string $userLogin
     * @throws Exception
     */
    public static function checkValidLoginString($userLogin) : void
    {
        if (!\Piwik\SettingsPiwik::isUserCredentialsSanityCheckEnabled() && !empty($userLogin)) {
            return;
        }
        $loginMinimumLength = 2;
        $loginMaximumLength = 100;
        $l = strlen($userLogin);
        if (!($l >= $loginMinimumLength && $l <= $loginMaximumLength && preg_match('/^[A-Za-zÄäÖöÜüß0-9_.@+-]*$/D', $userLogin) > 0)) {
            throw new Exception(\Piwik\Piwik::translate('UsersManager_ExceptionInvalidLoginFormat', array($loginMinimumLength, $loginMaximumLength)));
        }
    }
    /**
     * Utility function that checks if an object type is in a set of types.
     *
     * @param mixed $o
     * @param array $types List of class names that $o is expected to be one of.
     * @throws Exception if $o is not an instance of the types contained in $types.
     */
    public static function checkObjectTypeIs($o, $types)
    {
        foreach ($types as $type) {
            if ($o instanceof $type) {
                return;
            }
        }
        $oType = is_object($o) ? get_class($o) : gettype($o);
        throw new Exception("Invalid variable type '{$oType}', expected one of following: " . implode(', ', $types));
    }
    /**
     * Returns true if an array is an associative array, false if otherwise.
     *
     * This method determines if an array is associative by checking that the
     * first element's key is 0, and that each successive element's key is
     * one greater than the last.
     *
     * @param array $array
     * @return bool
     */
    public static function isAssociativeArray($array)
    {
        reset($array);
        if (!is_numeric(key($array)) || key($array) != 0) {
            // first key must be 0
            return \true;
        }
        // check that each key is == next key - 1 w/o actually indexing the array
        while (\true) {
            $current = key($array);
            next($array);
            $next = key($array);
            if ($next === null) {
                break;
            } elseif ($current + 1 != $next) {
                return \true;
            }
        }
        return \false;
    }
    public static function isMultiDimensionalArray($array)
    {
        $first = reset($array);
        foreach ($array as $first) {
            if (is_array($first)) {
                // Yes, this is a multi dim array
                return \true;
            }
        }
        return \false;
    }
    /**
     * Returns the class name of an object without its namespace.
     *
     * @param mixed|string $object
     * @return string
     */
    public static function getUnnamespacedClassName($object)
    {
        $className = is_string($object) ? $object : get_class($object);
        $parts = explode('\\', $className);
        return end($parts);
    }
    /**
     * Post an event to Piwik's event dispatcher which will execute the event's observers.
     *
     * @param string $eventName The event name.
     * @param array $params The parameter array to forward to observer callbacks.
     * @param bool $pending If true, plugins that are loaded after this event is fired will
     *                      have their observers for this event executed.
     * @param array|null $plugins The list of plugins to execute observers for. If null, all
     *                            plugin observers will be executed.
     * @api
     */
    public static function postEvent($eventName, $params = array(), $pending = \false, $plugins = null)
    {
        \Piwik\EventDispatcher::getInstance()->postEvent($eventName, $params, $pending, $plugins);
    }
    /**
     * Register an observer to an event.
     *
     * **_Note: Observers should normally be defined in plugin objects. It is unlikely that you will
     * need to use this function._**
     *
     * @param string $eventName The event name.
     * @param callable|array $function The observer.
     * @api
     */
    public static function addAction($eventName, $function)
    {
        \Piwik\EventDispatcher::getInstance()->addObserver($eventName, $function);
    }
    /**
     * Posts an event if we are currently running tests. Whether we are running tests is
     * determined by looking for the PIWIK_TEST_MODE constant.
     */
    public static function postTestEvent($eventName, $params = array(), $pending = \false, $plugins = null)
    {
        if (defined('PIWIK_TEST_MODE')) {
            \Piwik\Piwik::postEvent($eventName, $params, $pending, $plugins);
        }
    }
    /**
     * Returns an internationalized string using a translation token. If a translation
     * cannot be found for the token, the token is returned.
     *
     * @param string $translationId Translation ID, eg, `'General_Date'`.
     * @param array|string|int $args `sprintf` arguments to be applied to the internationalized
     *                               string.
     * @param string|null $language Optionally force the language.
     * @return string The translated string or `$translationId`.
     * @api
     */
    public static function translate($translationId, $args = array(), $language = null)
    {
        /** @var Translator $translator */
        $translator = StaticContainer::get('Piwik\\Translation\\Translator');
        return $translator->translate($translationId, $args, $language);
    }
    /**
     * Returns the period provided in the current request.
     * If no $default is provided, this method will throw an Exception if `period` can't be found in the request
     *
     * @param string|null $default  default value to use
     * @throws Exception
     * @return string
     * @api
     */
    public static function getPeriod($default = null)
    {
        return \Piwik\Common::getRequestVar('period', $default, 'string');
    }
    /**
     * Returns the date provided in the current request.
     * If no $default is provided, this method will throw an Exception if `date` can't be found in the request
     *
     * @param string|null $default  default value to use
     * @throws Exception
     * @return string
     * @api
     */
    public static function getDate($default = null)
    {
        return \Piwik\Common::getRequestVar('date', $default, 'string');
    }
    /**
     * Returns the earliest date to rearchive provided in the config.
     * @return Date|null
     */
    public static function getEarliestDateToRearchive()
    {
        $lastNMonthsToInvalidate = \Piwik\Config::getInstance()->General['rearchive_reports_in_past_last_n_months'];
        if (empty($lastNMonthsToInvalidate)) {
            return null;
        }
        if (!is_numeric($lastNMonthsToInvalidate)) {
            $lastNMonthsToInvalidate = (int) str_replace('last', '', $lastNMonthsToInvalidate);
            if (empty($lastNMonthsToInvalidate)) {
                return null;
            }
        }
        if ($lastNMonthsToInvalidate <= 0) {
            return null;
        }
        return \Piwik\Date::yesterday()->subMonth($lastNMonthsToInvalidate)->setDay(1);
    }
    /**
     * Given the fully qualified name of a class located within a Matomo plugin,
     * returns the name of the plugin.
     *
     * Uses the fact that Matomo plugins have namespaces like Piwik\Plugins\MyPlugin.
     *
     * @param string $className the name of a class located within a Matomo plugin
     * @return string the plugin name
     */
    public static function getPluginNameOfMatomoClass(string $className) : string
    {
        $parts = explode('\\', $className);
        $parts = array_filter($parts);
        $plugin = $parts[2] ?? '';
        return $plugin;
    }
}
