<?php
/**
* Copyright (c) Microsoft Corporation.  All Rights Reserved.  Licensed under the MIT License.  See License in the project root for license information.
* 
* OmaSettingStringXml File
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
* OmaSettingStringXml class
*
* @category  Model
* @package   Microsoft.Graph
* @copyright (c) Microsoft Corporation. All rights reserved.
* @license   https://opensource.org/licenses/MIT MIT License
* @link      https://graph.microsoft.com
*/
class OmaSettingStringXml extends OmaSetting
{
    /**
    * Set the @odata.type since this type is immediately descended from an abstract
    * type that is referenced as the type in an entity.
    * @param array $propDict The property dictionary
    */
    public function __construct($propDict = array())
    {
        parent::__construct($propDict);
        $this->setODataType("#microsoft.graph.omaSettingStringXml");
    }

    /**
    * Gets the fileName
    * File name associated with the Value property (.xml).
    *
    * @return string|null The fileName
    */
    public function getFileName()
    {
        if (array_key_exists("fileName", $this->_propDict)) {
            return $this->_propDict["fileName"];
        } else {
            return null;
        }
    }

    /**
    * Sets the fileName
    * File name associated with the Value property (.xml).
    *
    * @param string $val The value of the fileName
    *
    * @return OmaSettingStringXml
    */
    public function setFileName($val)
    {
        $this->_propDict["fileName"] = $val;
        return $this;
    }

    /**
    * Gets the value
    * Value. (UTF8 encoded byte array)
    *
    * @return \AmeliaGuzzleHttp\Psr7\Stream|null The value
    */
    public function getValue()
    {
        if (array_key_exists("value", $this->_propDict)) {
            if (is_a($this->_propDict["value"], "\AmeliaGuzzleHttp\Psr7\Stream") || is_null($this->_propDict["value"])) {
                return $this->_propDict["value"];
            } else {
                $this->_propDict["value"] = \AmeliaGuzzleHttp\Psr7\Utils::streamFor($this->_propDict["value"]);
                return $this->_propDict["value"];
            }
        }
        return null;
    }

    /**
    * Sets the value
    * Value. (UTF8 encoded byte array)
    *
    * @param \AmeliaGuzzleHttp\Psr7\Stream $val The value to assign to the value
    *
    * @return OmaSettingStringXml The OmaSettingStringXml
    */
    public function setValue($val)
    {
        $this->_propDict["value"] = $val;
         return $this;
    }
}
