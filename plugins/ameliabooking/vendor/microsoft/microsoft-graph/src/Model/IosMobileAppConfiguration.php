<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* IosMobileAppConfiguration File
* PHP version 7
*
* @category  Library
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
namespace Microsoft\Graph\Model;

/**
* IosMobileAppConfiguration class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class IosMobileAppConfiguration extends ManagedDeviceMobileAppConfiguration
{
    /**
    * Gets the encodedSettingXml
    * mdm app configuration Base64 binary.
    *
    * @return \AmeliaGuzzleHttp\Psr7\Stream|null The encodedSettingXml
    */
    public function getEncodedSettingXml()
    {
        if (array_key_exists("encodedSettingXml", $this->_propDict)) {
            if (is_a($this->_propDict["encodedSettingXml"], "\AmeliaGuzzleHttp\Psr7\Stream") || is_null($this->_propDict["encodedSettingXml"])) {
                return $this->_propDict["encodedSettingXml"];
            } else {
                $this->_propDict["encodedSettingXml"] = \AmeliaGuzzleHttp\Psr7\Utils::streamFor($this->_propDict["encodedSettingXml"]);
                return $this->_propDict["encodedSettingXml"];
            }
        }
        return null;
    }

    /**
    * Sets the encodedSettingXml
    * mdm app configuration Base64 binary.
    *
    * @param \AmeliaGuzzleHttp\Psr7\Stream $val The encodedSettingXml
    *
    * @return IosMobileAppConfiguration
    */
    public function setEncodedSettingXml($val)
    {
        $this->_propDict["encodedSettingXml"] = $val;
        return $this;
    }


     /**
     * Gets the settings
    * app configuration setting items.
     *
     * @return array|null The settings
     */
    public function getSettings()
    {
        if (array_key_exists("settings", $this->_propDict)) {
           return $this->_propDict["settings"];
        } else {
            return null;
        }
    }

    /**
    * Sets the settings
    * app configuration setting items.
    *
    * @param AppConfigurationSettingItem[] $val The settings
    *
    * @return IosMobileAppConfiguration
    */
    public function setSettings($val)
    {
        $this->_propDict["settings"] = $val;
        return $this;
    }

}
