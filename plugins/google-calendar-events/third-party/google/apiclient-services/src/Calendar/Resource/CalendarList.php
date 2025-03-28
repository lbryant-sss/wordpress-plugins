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
namespace SimpleCalendar\plugin_deps\Google\Service\Calendar\Resource;

use SimpleCalendar\plugin_deps\Google\Service\Calendar\CalendarList as CalendarListModel;
use SimpleCalendar\plugin_deps\Google\Service\Calendar\CalendarListEntry;
use SimpleCalendar\plugin_deps\Google\Service\Calendar\Channel;
/**
 * The "calendarList" collection of methods.
 * Typical usage is:
 *  <code>
 *   $calendarService = new Google\Service\Calendar(...);
 *   $calendarList = $calendarService->calendarList;
 *  </code>
 */
class CalendarList extends \SimpleCalendar\plugin_deps\Google\Service\Resource
{
    /**
     * Removes a calendar from the user's calendar list. (calendarList.delete)
     *
     * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
     * the calendarList.list method. If you want to access the primary calendar of
     * the currently logged in user, use the "primary" keyword.
     * @param array $optParams Optional parameters.
     */
    public function delete($calendarId, $optParams = [])
    {
        $params = ['calendarId' => $calendarId];
        $params = array_merge($params, $optParams);
        return $this->call('delete', [$params]);
    }
    /**
     * Returns a calendar from the user's calendar list. (calendarList.get)
     *
     * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
     * the calendarList.list method. If you want to access the primary calendar of
     * the currently logged in user, use the "primary" keyword.
     * @param array $optParams Optional parameters.
     * @return CalendarListEntry
     */
    public function get($calendarId, $optParams = [])
    {
        $params = ['calendarId' => $calendarId];
        $params = array_merge($params, $optParams);
        return $this->call('get', [$params], CalendarListEntry::class);
    }
    /**
     * Inserts an existing calendar into the user's calendar list.
     * (calendarList.insert)
     *
     * @param CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the foregroundColor and
     * backgroundColor fields to write the calendar colors (RGB). If this feature is
     * used, the index-based colorId field will be set to the best matching option
     * automatically. Optional. The default is False.
     * @return CalendarListEntry
     */
    public function insert(CalendarListEntry $postBody, $optParams = [])
    {
        $params = ['postBody' => $postBody];
        $params = array_merge($params, $optParams);
        return $this->call('insert', [$params], CalendarListEntry::class);
    }
    /**
     * Returns the calendars on the user's calendar list.
     * (calendarList.listCalendarList)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param int maxResults Maximum number of entries returned on one result
     * page. By default the value is 100 entries. The page size can never be larger
     * than 250 entries. Optional.
     * @opt_param string minAccessRole The minimum access role for the user in the
     * returned entries. Optional. The default is no restriction.
     * @opt_param string pageToken Token specifying which result page to return.
     * Optional.
     * @opt_param bool showDeleted Whether to include deleted calendar list entries
     * in the result. Optional. The default is False.
     * @opt_param bool showHidden Whether to show hidden entries. Optional. The
     * default is False.
     * @opt_param string syncToken Token obtained from the nextSyncToken field
     * returned on the last page of results from the previous list request. It makes
     * the result of this list request contain only entries that have changed since
     * then. If only read-only fields such as calendar properties or ACLs have
     * changed, the entry won't be returned. All entries deleted and hidden since
     * the previous list request will always be in the result set and it is not
     * allowed to set showDeleted neither showHidden to False. To ensure client
     * state consistency minAccessRole query parameter cannot be specified together
     * with nextSyncToken. If the syncToken expires, the server will respond with a
     * 410 GONE response code and the client should clear its storage and perform a
     * full synchronization without any syncToken. Learn more about incremental
     * synchronization. Optional. The default is to return all entries.
     * @return CalendarListModel
     */
    public function listCalendarList($optParams = [])
    {
        $params = [];
        $params = array_merge($params, $optParams);
        return $this->call('list', [$params], CalendarListModel::class);
    }
    /**
     * Updates an existing calendar on the user's calendar list. This method
     * supports patch semantics. (calendarList.patch)
     *
     * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
     * the calendarList.list method. If you want to access the primary calendar of
     * the currently logged in user, use the "primary" keyword.
     * @param CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the foregroundColor and
     * backgroundColor fields to write the calendar colors (RGB). If this feature is
     * used, the index-based colorId field will be set to the best matching option
     * automatically. Optional. The default is False.
     * @return CalendarListEntry
     */
    public function patch($calendarId, CalendarListEntry $postBody, $optParams = [])
    {
        $params = ['calendarId' => $calendarId, 'postBody' => $postBody];
        $params = array_merge($params, $optParams);
        return $this->call('patch', [$params], CalendarListEntry::class);
    }
    /**
     * Updates an existing calendar on the user's calendar list.
     * (calendarList.update)
     *
     * @param string $calendarId Calendar identifier. To retrieve calendar IDs call
     * the calendarList.list method. If you want to access the primary calendar of
     * the currently logged in user, use the "primary" keyword.
     * @param CalendarListEntry $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param bool colorRgbFormat Whether to use the foregroundColor and
     * backgroundColor fields to write the calendar colors (RGB). If this feature is
     * used, the index-based colorId field will be set to the best matching option
     * automatically. Optional. The default is False.
     * @return CalendarListEntry
     */
    public function update($calendarId, CalendarListEntry $postBody, $optParams = [])
    {
        $params = ['calendarId' => $calendarId, 'postBody' => $postBody];
        $params = array_merge($params, $optParams);
        return $this->call('update', [$params], CalendarListEntry::class);
    }
    /**
     * Watch for changes to CalendarList resources. (calendarList.watch)
     *
     * @param Channel $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param int maxResults Maximum number of entries returned on one result
     * page. By default the value is 100 entries. The page size can never be larger
     * than 250 entries. Optional.
     * @opt_param string minAccessRole The minimum access role for the user in the
     * returned entries. Optional. The default is no restriction.
     * @opt_param string pageToken Token specifying which result page to return.
     * Optional.
     * @opt_param bool showDeleted Whether to include deleted calendar list entries
     * in the result. Optional. The default is False.
     * @opt_param bool showHidden Whether to show hidden entries. Optional. The
     * default is False.
     * @opt_param string syncToken Token obtained from the nextSyncToken field
     * returned on the last page of results from the previous list request. It makes
     * the result of this list request contain only entries that have changed since
     * then. If only read-only fields such as calendar properties or ACLs have
     * changed, the entry won't be returned. All entries deleted and hidden since
     * the previous list request will always be in the result set and it is not
     * allowed to set showDeleted neither showHidden to False. To ensure client
     * state consistency minAccessRole query parameter cannot be specified together
     * with nextSyncToken. If the syncToken expires, the server will respond with a
     * 410 GONE response code and the client should clear its storage and perform a
     * full synchronization without any syncToken. Learn more about incremental
     * synchronization. Optional. The default is to return all entries.
     * @return Channel
     */
    public function watch(Channel $postBody, $optParams = [])
    {
        $params = ['postBody' => $postBody];
        $params = array_merge($params, $optParams);
        return $this->call('watch', [$params], Channel::class);
    }
}
// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CalendarList::class, 'SimpleCalendar\plugin_deps\Google_Service_Calendar_Resource_CalendarList');
