(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _js_quiz_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./js/quiz.js */ "./assets/js/quiz.js");
/* harmony import */ var _js_quiz_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_js_quiz_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_app_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles/app.scss */ "./assets/styles/app.scss");
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");

__webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.esm.js");


/***/ }),

/***/ "./assets/js/quiz.js":
/*!***************************!*\
  !*** ./assets/js/quiz.js ***!
  \***************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");
__webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
__webpack_require__(/*! core-js/modules/es.object.to-string.js */ "./node_modules/core-js/modules/es.object.to-string.js");
$(document).ready(function () {
  $('#quiz-form').on('submit', function (event) {
    event.preventDefault(); // Verhindert das Standard-Formular-Absenden

    var form = $('#quiz-form');
    var url = form.attr('action');
    $.ajax({
      type: form.attr('method'),
      url: url,
      data: form.serialize(),
      // Serialisiert die Formulardaten
      success: function success(response) {
        // Ersetzt den HTML-Code mit der neuen Frage
        $('.quiz-container').html($(response).find('.quiz-container').html());
      },
      error: function error(xhr, status, _error) {
        console.error('AJAX Error:', status, _error);
      }
    });
  });
  $('.answer__radio').on('click', function () {
    // Enable the submit button
    $('#submit-btn').prop('disabled', false);
  });
});

/***/ }),

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_bootstrap_dist_js_bootstrap_esm_js-node_modules_core-js_modules_es_array-eca6b1"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSxJQUFNQSxDQUFDLEdBQUdDLG1CQUFPLENBQUMsb0RBQVEsQ0FBQztBQUVMO0FBRXRCQSxtQkFBTyxDQUFDLG9FQUFXLENBQUM7Ozs7Ozs7Ozs7Ozs7O0FDWnBCRCxDQUFDLENBQUNFLFFBQVEsQ0FBQyxDQUFDQyxLQUFLLENBQUMsWUFBVztFQUN6QkgsQ0FBQyxDQUFDLFlBQVksQ0FBQyxDQUFDSSxFQUFFLENBQUMsUUFBUSxFQUFFLFVBQVNDLEtBQUssRUFBRTtJQUN6Q0EsS0FBSyxDQUFDQyxjQUFjLENBQUMsQ0FBQyxDQUFDLENBQUM7O0lBRXhCLElBQUlDLElBQUksR0FBR1AsQ0FBQyxDQUFDLFlBQVksQ0FBQztJQUMxQixJQUFJUSxHQUFHLEdBQUdELElBQUksQ0FBQ0UsSUFBSSxDQUFDLFFBQVEsQ0FBQztJQUU3QlQsQ0FBQyxDQUFDVSxJQUFJLENBQUM7TUFDSEMsSUFBSSxFQUFFSixJQUFJLENBQUNFLElBQUksQ0FBQyxRQUFRLENBQUM7TUFDekJELEdBQUcsRUFBRUEsR0FBRztNQUNSSSxJQUFJLEVBQUVMLElBQUksQ0FBQ00sU0FBUyxDQUFDLENBQUM7TUFBRTtNQUN4QkMsT0FBTyxFQUFFLFNBQUFBLFFBQVNDLFFBQVEsRUFBRTtRQUN4QjtRQUNBZixDQUFDLENBQUMsaUJBQWlCLENBQUMsQ0FBQ2dCLElBQUksQ0FBQ2hCLENBQUMsQ0FBQ2UsUUFBUSxDQUFDLENBQUNFLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDRCxJQUFJLENBQUMsQ0FBQyxDQUFDO01BQ3pFLENBQUM7TUFDREUsS0FBSyxFQUFFLFNBQUFBLE1BQVNDLEdBQUcsRUFBRUMsTUFBTSxFQUFFRixNQUFLLEVBQUU7UUFDaENHLE9BQU8sQ0FBQ0gsS0FBSyxDQUFDLGFBQWEsRUFBRUUsTUFBTSxFQUFFRixNQUFLLENBQUM7TUFDL0M7SUFDSixDQUFDLENBQUM7RUFDTixDQUFDLENBQUM7RUFFRmxCLENBQUMsQ0FBQyxnQkFBZ0IsQ0FBQyxDQUFDSSxFQUFFLENBQUMsT0FBTyxFQUFFLFlBQVc7SUFDdkM7SUFDQUosQ0FBQyxDQUFDLGFBQWEsQ0FBQyxDQUFDc0IsSUFBSSxDQUFDLFVBQVUsRUFBRSxLQUFLLENBQUM7RUFDNUMsQ0FBQyxDQUFDO0FBQ04sQ0FBQyxDQUFDOzs7Ozs7Ozs7Ozs7QUN6QkYiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvYXBwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9qcy9xdWl6LmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zdHlsZXMvYXBwLnNjc3MiXSwic291cmNlc0NvbnRlbnQiOlsiLypcbiAqIFdlbGNvbWUgdG8geW91ciBhcHAncyBtYWluIEphdmFTY3JpcHQgZmlsZSFcbiAqXG4gKiBXZSByZWNvbW1lbmQgaW5jbHVkaW5nIHRoZSBidWlsdCB2ZXJzaW9uIG9mIHRoaXMgSmF2YVNjcmlwdCBmaWxlXG4gKiAoYW5kIGl0cyBDU1MgZmlsZSkgaW4geW91ciBiYXNlIGxheW91dCAoYmFzZS5odG1sLnR3aWcpLlxuICovXG5cbi8vIGFueSBDU1MgeW91IGltcG9ydCB3aWxsIG91dHB1dCBpbnRvIGEgc2luZ2xlIGNzcyBmaWxlIChhcHAuY3NzIGluIHRoaXMgY2FzZSlcbmNvbnN0ICQgPSByZXF1aXJlKCdqcXVlcnknKTtcblxuaW1wb3J0ICcuL2pzL3F1aXouanMnO1xuXG5yZXF1aXJlKCdib290c3RyYXAnKTtcbmltcG9ydCAnLi9zdHlsZXMvYXBwLnNjc3MnO1xuIiwiJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XG4gICAgJCgnI3F1aXotZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpOyAvLyBWZXJoaW5kZXJ0IGRhcyBTdGFuZGFyZC1Gb3JtdWxhci1BYnNlbmRlblxuXG4gICAgICAgIGxldCBmb3JtID0gJCgnI3F1aXotZm9ybScpO1xuICAgICAgICBsZXQgdXJsID0gZm9ybS5hdHRyKCdhY3Rpb24nKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdHlwZTogZm9ybS5hdHRyKCdtZXRob2QnKSxcbiAgICAgICAgICAgIHVybDogdXJsLFxuICAgICAgICAgICAgZGF0YTogZm9ybS5zZXJpYWxpemUoKSwgLy8gU2VyaWFsaXNpZXJ0IGRpZSBGb3JtdWxhcmRhdGVuXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIC8vIEVyc2V0enQgZGVuIEhUTUwtQ29kZSBtaXQgZGVyIG5ldWVuIEZyYWdlXG4gICAgICAgICAgICAgICAgJCgnLnF1aXotY29udGFpbmVyJykuaHRtbCgkKHJlc3BvbnNlKS5maW5kKCcucXVpei1jb250YWluZXInKS5odG1sKCkpO1xuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbih4aHIsIHN0YXR1cywgZXJyb3IpIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmVycm9yKCdBSkFYIEVycm9yOicsIHN0YXR1cywgZXJyb3IpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9KTtcbiAgICB9KTtcblxuICAgICQoJy5hbnN3ZXJfX3JhZGlvJykub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG4gICAgICAgIC8vIEVuYWJsZSB0aGUgc3VibWl0IGJ1dHRvblxuICAgICAgICAkKCcjc3VibWl0LWJ0bicpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xuICAgIH0pO1xufSk7IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307Il0sIm5hbWVzIjpbIiQiLCJyZXF1aXJlIiwiZG9jdW1lbnQiLCJyZWFkeSIsIm9uIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImZvcm0iLCJ1cmwiLCJhdHRyIiwiYWpheCIsInR5cGUiLCJkYXRhIiwic2VyaWFsaXplIiwic3VjY2VzcyIsInJlc3BvbnNlIiwiaHRtbCIsImZpbmQiLCJlcnJvciIsInhociIsInN0YXR1cyIsImNvbnNvbGUiLCJwcm9wIl0sInNvdXJjZVJvb3QiOiIifQ==