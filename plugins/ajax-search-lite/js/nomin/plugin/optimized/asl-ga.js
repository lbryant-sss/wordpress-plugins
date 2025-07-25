/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// external "global"
var external_global_namespaceObject = Object(window.WPD)["global"];
;// ./src/client/plugin/core/actions/ga_events.ts


"use strict";
const ASL = window.ASL;
external_global_namespaceObject.AslPlugin.prototype.gaPageview = function(term) {
  let $this = this;
  let tracking_id = $this.gaGetTrackingID();
  if (typeof ASL.analytics == "undefined" || ASL.analytics.method != "pageview")
    return false;
  if (ASL.analytics.string != "") {
    let _ga = typeof window.__gaTracker == "function" ? window.__gaTracker : typeof window.ga == "function" ? window.ga : false;
    let _gtag = typeof window.gtag == "function" ? window.gtag : false;
    let url = $this.o.homeurl.replace(window.location.origin, "");
    if (_gtag !== false) {
      if (tracking_id !== false) {
        tracking_id.forEach(function(id) {
          _gtag("config", id, { "page_path": url + ASL.analytics.string.replace("{asl_term}", term) });
        });
      }
    } else if (_ga !== false) {
      let params = {
        "page": url + ASL.analytics.string.replace("{asl_term}", term),
        "title": "Ajax Search"
      };
      if (tracking_id !== false) {
        tracking_id.forEach(function(id) {
          _ga("create", id, "auto");
          _ga("send", "pageview", params);
        });
      } else {
        _ga("send", "pageview", params);
      }
    }
  }
};
external_global_namespaceObject.AslPlugin.prototype.gaEvent = function(which, d) {
  let $this = this;
  let tracking_id = $this.gaGetTrackingID();
  if (typeof ASL.analytics == "undefined" || ASL.analytics.method != "event")
    return false;
  let _gtag = typeof window.gtag == "function" ? window.gtag : false;
  let _ga = typeof window.__gaTracker == "function" ? window.__gaTracker : typeof window.ga == "function" ? window.ga : false;
  if (_gtag === false && _ga === false && typeof window.dataLayer == "undefined")
    return false;
  if (typeof ASL.analytics.event[which] != "undefined" && ASL.analytics.event[which].active) {
    let def_data = {
      "search_id": $this.o.id,
      "search_name": $this.o.name,
      "phrase": $this.n("text").val(),
      "option_name": "",
      "option_value": "",
      "result_title": "",
      "result_url": "",
      "results_count": ""
    };
    let event = {
      "event_category": ASL.analytics.event[which].category,
      "event_label": ASL.analytics.event[which].label,
      "value": ASL.analytics.event[which].value,
      "send_to": ""
    };
    const data = { ...def_data, ...d };
    Object.keys(data).forEach(function(k) {
      let v = data[k];
      v = String(v).replace(/[\s\n\r]+/g, " ").trim();
      Object.keys(event).forEach(function(kk) {
        let regex = new RegExp("{" + k + "}", "gmi");
        event[kk] = event[kk].replace(regex, v);
      });
    });
    if (_ga !== false) {
      if (tracking_id !== false) {
        tracking_id.forEach(function(id) {
          _ga("create", id, "auto");
          _ga(
            "send",
            "event",
            event.event_category,
            ASL.analytics.event[which].action,
            event.event_label,
            event.value
          );
        });
      } else {
        _ga(
          "send",
          "event",
          event.event_category,
          ASL.analytics.event[which].action,
          event.event_label,
          event.value
        );
      }
    } else if (_gtag !== false) {
      if (tracking_id !== false) {
        tracking_id.forEach(function(id) {
          event.send_to = id;
          _gtag("event", ASL.analytics.event[which].action, event);
        });
      } else {
        _gtag("event", ASL.analytics.event[which].action, event);
      }
    } else if (window?.dataLayer?.push !== void 0) {
      window.dataLayer.push({
        "event": "gaEvent",
        "eventCategory": event.event_category,
        "eventAction": ASL.analytics.event[which].action,
        "eventLabel": event.event_label
      });
    }
  }
};
external_global_namespaceObject.AslPlugin.prototype.gaGetTrackingID = function() {
  let ret = false;
  if (typeof ASL.analytics == "undefined") {
    return ret;
  }
  if (typeof ASL.analytics.tracking_id != "undefined" && ASL.analytics.tracking_id != "") {
    return [ASL.analytics.tracking_id];
  } else {
    let _gtag = typeof window.gtag == "function" ? window.gtag : false;
    if (_gtag === false && typeof window.ga != "undefined" && typeof window.ga.getAll != "undefined") {
      let id = [];
      window.ga.getAll().forEach(function(tracker) {
        id.push(tracker.get("trackingId"));
      });
      return id.length > 0 ? id : false;
    }
  }
  return ret;
};
/* harmony default export */ var ga_events = ((/* unused pure expression or super */ null && (AslPlugin)));

;// ./src/client/bundle/optimized/ga.ts



Object(window.WPD).AjaxSearchLite = __webpack_exports__["default"];
/******/ })()
;