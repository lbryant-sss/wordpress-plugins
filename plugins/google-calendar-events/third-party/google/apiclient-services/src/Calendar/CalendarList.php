<?php

/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
namespace SimpleCalendar\plugin_deps\Google\Service\Calendar;

class CalendarList extends \SimpleCalendar\plugin_deps\Google\Collection
{
    protected $collection_key = 'items';
    /**
     * @var string
     */
    public $etag;
    protected $itemsType = CalendarListEntry::class;
    protected $itemsDataType = 'array';
    /**
     * @var string
     */
    public $kind;
    /**
     * @var string
     */
    public $nextPageToken;
    /**
     * @var string
     */
    public $nextSyncToken;
    /**
     * @param string
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;
    }
    /**
     * @return string
     */
    public function getEtag()
    {
        return $this->etag;
    }
    /**
     * @param CalendarListEntry[]
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
    /**
     * @return CalendarListEntry[]
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * @param string
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
    }
    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }
    /**
     * @param string
     */
    public function setNextPageToken($nextPageToken)
    {
        $this->nextPageToken = $nextPageToken;
    }
    /**
     * @return string
     */
    public function getNextPageToken()
    {
        return $this->nextPageToken;
    }
    /**
     * @param string
     */
    public function setNextSyncToken($nextSyncToken)
    {
        $this->nextSyncToken = $nextSyncToken;
    }
    /**
     * @return string
     */
    public function getNextSyncToken()
    {
        return $this->nextSyncToken;
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarList::class, 'SimpleCalendar\plugin_deps\Google_Service_Calendar_CalendarList');
