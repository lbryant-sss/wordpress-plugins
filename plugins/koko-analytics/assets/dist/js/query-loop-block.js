/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/src/js/query-loop-block.js":
/*!*******************************************!*\
  !*** ./assets/src/js/query-loop-block.js ***!
  \*******************************************/
/***/ (function() {

eval("const MY_VARIATION_NAME = 'koko-analytics/most-viewed-pages';\n\nwindow.wp.blocks.registerBlockVariation( 'core/query', {\n    name: MY_VARIATION_NAME,\n    title: 'Most Viewed Post Type',\n    description: 'Displays a list of your most viewed posts, pages or other post types.',\n    isActive: [ 'namespace' ],\n    icon: '',\n    attributes: {\n        namespace: MY_VARIATION_NAME,\n    },\n    scope: [ 'inserter' ],\n    allowedControls: [ 'postType'],\n    }\n);\n\n\n//# sourceURL=webpack://koko-analytics/./assets/src/js/query-loop-block.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./assets/src/js/query-loop-block.js"]();
/******/ 	
/******/ })()
;