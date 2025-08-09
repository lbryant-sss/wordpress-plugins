(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[1],{

/***/ "./node_modules/vue-frag/dist/frag.esm.js":
/*!************************************************!*\
  !*** ./node_modules/vue-frag/dist/frag.esm.js ***!
  \************************************************/
/*! exports provided: Fragment, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"Fragment\", function() { return fragment; });\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"default\", function() { return frag; });\nvar $placeholder = Symbol();\n\nvar $fakeParent = Symbol();\n\nvar $nextSiblingPatched = Symbol();\n\nvar $childNodesPatched = Symbol();\n\nvar isFrag = function isFrag(node) {\n    return \"frag\" in node;\n};\n\nvar parentNodeDescriptor = {\n    get: function get() {\n        return this[$fakeParent] || this.parentElement;\n    },\n    configurable: true\n};\n\nvar patchParentNode = function patchParentNode(node, fakeParent) {\n    if ($fakeParent in node) {\n        return;\n    }\n    node[$fakeParent] = fakeParent;\n    Object.defineProperty(node, \"parentNode\", parentNodeDescriptor);\n};\n\nvar nextSiblingDescriptor = {\n    get: function get() {\n        var childNodes = this.parentNode.childNodes;\n        var index = childNodes.indexOf(this);\n        if (index > -1) {\n            return childNodes[index + 1] || null;\n        }\n        return null;\n    }\n};\n\nvar patchNextSibling = function patchNextSibling(node) {\n    if ($nextSiblingPatched in node) {\n        return;\n    }\n    node[$nextSiblingPatched] = true;\n    Object.defineProperty(node, \"nextSibling\", nextSiblingDescriptor);\n};\n\nvar getTopFragment = function getTopFragment(node, fromParent) {\n    while (node.parentNode !== fromParent) {\n        var _node = node, parentNode = _node.parentNode;\n        if (parentNode) {\n            node = parentNode;\n        }\n    }\n    return node;\n};\n\nvar getChildNodes;\n\nvar getChildNodesWithFragments = function getChildNodesWithFragments(node) {\n    if (!getChildNodes) {\n        var _childNodesDescriptor = Object.getOwnPropertyDescriptor(Node.prototype, \"childNodes\");\n        getChildNodes = _childNodesDescriptor.get;\n    }\n    var realChildNodes = getChildNodes.apply(node);\n    var childNodes = Array.from(realChildNodes).map((function(childNode) {\n        return getTopFragment(childNode, node);\n    }));\n    return childNodes.filter((function(childNode, index) {\n        return childNode !== childNodes[index - 1];\n    }));\n};\n\nvar childNodesDescriptor = {\n    get: function get() {\n        return this.frag || getChildNodesWithFragments(this);\n    }\n};\n\nvar firstChildDescriptor = {\n    get: function get() {\n        return this.childNodes[0] || null;\n    }\n};\n\nfunction hasChildNodes() {\n    return this.childNodes.length > 0;\n}\n\nvar patchChildNodes = function patchChildNodes(node) {\n    if ($childNodesPatched in node) {\n        return;\n    }\n    node[$childNodesPatched] = true;\n    Object.defineProperties(node, {\n        childNodes: childNodesDescriptor,\n        firstChild: firstChildDescriptor\n    });\n    node.hasChildNodes = hasChildNodes;\n};\n\nfunction before() {\n    var _this$frag$;\n    (_this$frag$ = this.frag[0]).before.apply(_this$frag$, arguments);\n}\n\nfunction remove() {\n    var frag = this.frag;\n    var removed = frag.splice(0, frag.length);\n    removed.forEach((function(node) {\n        node.remove();\n    }));\n}\n\nvar getFragmentLeafNodes = function getFragmentLeafNodes(children) {\n    var _Array$prototype;\n    return (_Array$prototype = Array.prototype).concat.apply(_Array$prototype, children.map((function(childNode) {\n        return isFrag(childNode) ? getFragmentLeafNodes(childNode.frag) : childNode;\n    })));\n};\n\nvar addPlaceholder = function addPlaceholder(node, insertBeforeNode) {\n    var placeholder = node[$placeholder];\n    insertBeforeNode.before(placeholder);\n    patchParentNode(placeholder, node);\n    node.frag.unshift(placeholder);\n};\n\nfunction removeChild(node) {\n    if (isFrag(this)) {\n        var hasChildInFragment = this.frag.indexOf(node);\n        if (hasChildInFragment > -1) {\n            var _this$frag$splice = this.frag.splice(hasChildInFragment, 1), removedNode = _this$frag$splice[0];\n            if (this.frag.length === 0) {\n                addPlaceholder(this, removedNode);\n            }\n            node.remove();\n        }\n    } else {\n        var children = getChildNodesWithFragments(this);\n        var hasChild = children.indexOf(node);\n        if (hasChild > -1) {\n            node.remove();\n        }\n    }\n    return node;\n}\n\nfunction insertBefore(insertNode, insertBeforeNode) {\n    var _this = this;\n    var insertNodes = insertNode.frag || [ insertNode ];\n    if (isFrag(this)) {\n        if (insertNode[$fakeParent] === this && insertNode.parentElement) {\n            return insertNode;\n        }\n        var _frag = this.frag;\n        if (insertBeforeNode) {\n            var index = _frag.indexOf(insertBeforeNode);\n            if (index > -1) {\n                _frag.splice.apply(_frag, [ index, 0 ].concat(insertNodes));\n                insertBeforeNode.before.apply(insertBeforeNode, insertNodes);\n            }\n        } else {\n            var _lastNode = _frag[_frag.length - 1];\n            _frag.push.apply(_frag, insertNodes);\n            _lastNode.after.apply(_lastNode, insertNodes);\n        }\n        removePlaceholder(this);\n    } else if (insertBeforeNode) {\n        if (this.childNodes.includes(insertBeforeNode)) {\n            insertBeforeNode.before.apply(insertBeforeNode, insertNodes);\n        }\n    } else {\n        this.append.apply(this, insertNodes);\n    }\n    insertNodes.forEach((function(node) {\n        patchParentNode(node, _this);\n    }));\n    var lastNode = insertNodes[insertNodes.length - 1];\n    patchNextSibling(lastNode);\n    return insertNode;\n}\n\nfunction appendChild(node) {\n    if (node[$fakeParent] === this && node.parentElement) {\n        return node;\n    }\n    var frag = this.frag;\n    var lastChild = frag[frag.length - 1];\n    lastChild.after(node);\n    patchParentNode(node, this);\n    removePlaceholder(this);\n    frag.push(node);\n    return node;\n}\n\nvar removePlaceholder = function removePlaceholder(node) {\n    var placeholder = node[$placeholder];\n    if (node.frag[0] === placeholder) {\n        node.frag.shift();\n        placeholder.remove();\n    }\n};\n\nvar innerHTMLDescriptor = {\n    set: function set(htmlString) {\n        var _this2 = this;\n        if (this.frag[0] !== this[$placeholder]) {\n            this.frag.slice().forEach((function(child) {\n                return _this2.removeChild(child);\n            }));\n        }\n        if (htmlString) {\n            var domify = document.createElement(\"div\");\n            domify.innerHTML = htmlString;\n            Array.from(domify.childNodes).forEach((function(node) {\n                _this2.appendChild(node);\n            }));\n        }\n    },\n    get: function get() {\n        return \"\";\n    }\n};\n\nvar frag = {\n    inserted: function inserted(element) {\n        var parentNode = element.parentNode, nextSibling = element.nextSibling, previousSibling = element.previousSibling;\n        var childNodes = Array.from(element.childNodes);\n        var placeholder = document.createComment(\"\");\n        if (childNodes.length === 0) {\n            childNodes.push(placeholder);\n        }\n        element.frag = childNodes;\n        element[$placeholder] = placeholder;\n        var fragment = document.createDocumentFragment();\n        fragment.append.apply(fragment, getFragmentLeafNodes(childNodes));\n        element.replaceWith(fragment);\n        childNodes.forEach((function(node) {\n            patchParentNode(node, element);\n            patchNextSibling(node);\n        }));\n        patchChildNodes(element);\n        Object.assign(element, {\n            remove: remove,\n            appendChild: appendChild,\n            insertBefore: insertBefore,\n            removeChild: removeChild,\n            before: before\n        });\n        Object.defineProperty(element, \"innerHTML\", innerHTMLDescriptor);\n        if (parentNode) {\n            Object.assign(parentNode, {\n                removeChild: removeChild,\n                insertBefore: insertBefore\n            });\n            patchParentNode(element, parentNode);\n            patchChildNodes(parentNode);\n        }\n        if (nextSibling) {\n            patchNextSibling(element);\n        }\n        if (previousSibling) {\n            patchNextSibling(previousSibling);\n        }\n    },\n    unbind: function unbind(element) {\n        element.remove();\n    }\n};\n\nvar fragment = {\n    name: \"Fragment\",\n    directives: {\n        frag: frag\n    },\n    render: function render(h) {\n        return h(\"div\", {\n            directives: [ {\n                name: \"frag\"\n            } ]\n        }, this.$slots[\"default\"]);\n    }\n};\n\n\n\n\n//# sourceURL=webpack:///./node_modules/vue-frag/dist/frag.esm.js?");

/***/ }),

/***/ "./src/assets/scan-now.svg":
/*!*********************************!*\
  !*** ./src/assets/scan-now.svg ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__.p + \"img/scan-now.svg\";\n\n//# sourceURL=webpack:///./src/assets/scan-now.svg?");

/***/ }),

/***/ "./src/assets/schedule-scan.svg":
/*!**************************************!*\
  !*** ./src/assets/schedule-scan.svg ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__.p + \"img/schedule-scan.svg\";\n\n//# sourceURL=webpack:///./src/assets/schedule-scan.svg?");

/***/ }),

/***/ "./src/assets/search.svg":
/*!*******************************!*\
  !*** ./src/assets/search.svg ***!
  \*******************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__.p + \"img/search.svg\";\n\n//# sourceURL=webpack:///./src/assets/search.svg?");

/***/ }),

/***/ "./src/assets/time-schedule.svg":
/*!**************************************!*\
  !*** ./src/assets/time-schedule.svg ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("module.exports = __webpack_require__.p + \"img/time-schedule.svg\";\n\n//# sourceURL=webpack:///./src/assets/time-schedule.svg?");

/***/ }),

/***/ "./src/mixins/visibility.js":
/*!**********************************!*\
  !*** ./src/mixins/visibility.js ***!
  \**********************************/
/*! exports provided: visibility */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"visibility\", function() { return visibility; });\n/* harmony import */ var _helpers_common__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/helpers/common */ \"./src/helpers/common.js\");\n\nconst visibility = {\n  data() {\n    return {\n      checkFocusTimer: 0,\n      hidden: 'hidden',\n      visibilityChange: 'visibilitychange',\n      hasFocus: false\n    };\n  },\n  components: {},\n  computed: {\n    account() {\n      return Object(_helpers_common__WEBPACK_IMPORTED_MODULE_0__[\"getOption\"])('account');\n    }\n  },\n  mounted() {\n    this.initialize();\n  },\n  beforeDestroy() {\n    document.removeEventListener(this.visibilityChange, this.handleVisibilityChange);\n  },\n  methods: {\n    initialize() {\n      if (this.account.connected === true) return;\n      if (typeof document.hidden !== 'undefined') {\n        this.hidden = 'hidden';\n        this.visibilityChange = 'visibilitychange';\n      } else if (typeof document.msHidden !== 'undefined') {\n        // IE10.\n        this.hidden = 'msHidden';\n        this.visibilityChange = 'msvisibilitychange';\n      } else if (typeof document.webkitHidden !== 'undefined') {\n        // Android.\n        this.hidden = 'webkitHidden';\n        this.visibilityChange = 'webkitvisibilitychange';\n      }\n      document.addEventListener(this.visibilityChange, this.handleVisibilityChange, false);\n    },\n    handleVisibilityChange() {\n      if (!document[this.hidden]) {\n        this.checkForScreenChange();\n      }\n    },\n    async checkForScreenChange() {\n      await Object(_helpers_common__WEBPACK_IMPORTED_MODULE_0__[\"reloadSettings\"])();\n      if (this.account.connected) {\n        document.removeEventListener(this.visibilityChange, this.handleVisibilityChange);\n        this.$router.redirectToDashboard(this.$route.name);\n      }\n    }\n  }\n};\n\n//# sourceURL=webpack:///./src/mixins/visibility.js?");

/***/ })

}]);