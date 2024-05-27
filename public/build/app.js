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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSxJQUFNQSxDQUFDLEdBQUdDLG1CQUFPLENBQUMsb0RBQVEsQ0FBQztBQUVMO0FBRXRCQSxtQkFBTyxDQUFDLG9FQUFXLENBQUM7Ozs7Ozs7Ozs7Ozs7O0FDWnBCRCxDQUFDLENBQUNFLFFBQVEsQ0FBQyxDQUFDQyxLQUFLLENBQUMsWUFBVztFQUN6QkgsQ0FBQyxDQUFDLFlBQVksQ0FBQyxDQUFDSSxFQUFFLENBQUMsUUFBUSxFQUFFLFVBQVNDLEtBQUssRUFBRTtJQUN6Q0EsS0FBSyxDQUFDQyxjQUFjLENBQUMsQ0FBQyxDQUFDLENBQUM7O0lBRXhCLElBQUlDLElBQUksR0FBR1AsQ0FBQyxDQUFDLFlBQVksQ0FBQztJQUMxQixJQUFJUSxHQUFHLEdBQUdELElBQUksQ0FBQ0UsSUFBSSxDQUFDLFFBQVEsQ0FBQztJQUU3QlQsQ0FBQyxDQUFDVSxJQUFJLENBQUM7TUFDSEMsSUFBSSxFQUFFSixJQUFJLENBQUNFLElBQUksQ0FBQyxRQUFRLENBQUM7TUFDekJELEdBQUcsRUFBRUEsR0FBRztNQUNSSSxJQUFJLEVBQUVMLElBQUksQ0FBQ00sU0FBUyxDQUFDLENBQUM7TUFBRTtNQUN4QkMsT0FBTyxFQUFFLFNBQUFBLFFBQVNDLFFBQVEsRUFBRTtRQUN4QjtRQUNBZixDQUFDLENBQUMsaUJBQWlCLENBQUMsQ0FBQ2dCLElBQUksQ0FBQ2hCLENBQUMsQ0FBQ2UsUUFBUSxDQUFDLENBQUNFLElBQUksQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDRCxJQUFJLENBQUMsQ0FBQyxDQUFDO01BQ3pFLENBQUM7TUFDREUsS0FBSyxFQUFFLFNBQUFBLE1BQVNDLEdBQUcsRUFBRUMsTUFBTSxFQUFFRixNQUFLLEVBQUU7UUFDaENHLE9BQU8sQ0FBQ0gsS0FBSyxDQUFDLGFBQWEsRUFBRUUsTUFBTSxFQUFFRixNQUFLLENBQUM7TUFDL0M7SUFDSixDQUFDLENBQUM7RUFDTixDQUFDLENBQUM7QUFDTixDQUFDLENBQUM7Ozs7Ozs7Ozs7OztBQ3BCRiIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9hcHAuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2pzL3F1aXouanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9hcHAuc2NzcyJdLCJzb3VyY2VzQ29udGVudCI6WyIvKlxuICogV2VsY29tZSB0byB5b3VyIGFwcCdzIG1haW4gSmF2YVNjcmlwdCBmaWxlIVxuICpcbiAqIFdlIHJlY29tbWVuZCBpbmNsdWRpbmcgdGhlIGJ1aWx0IHZlcnNpb24gb2YgdGhpcyBKYXZhU2NyaXB0IGZpbGVcbiAqIChhbmQgaXRzIENTUyBmaWxlKSBpbiB5b3VyIGJhc2UgbGF5b3V0IChiYXNlLmh0bWwudHdpZykuXG4gKi9cblxuLy8gYW55IENTUyB5b3UgaW1wb3J0IHdpbGwgb3V0cHV0IGludG8gYSBzaW5nbGUgY3NzIGZpbGUgKGFwcC5jc3MgaW4gdGhpcyBjYXNlKVxuY29uc3QgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xuXG5pbXBvcnQgJy4vanMvcXVpei5qcyc7XG5cbnJlcXVpcmUoJ2Jvb3RzdHJhcCcpO1xuaW1wb3J0ICcuL3N0eWxlcy9hcHAuc2Nzcyc7XG4iLCIkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpIHtcbiAgICAkKCcjcXVpei1mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGV2ZW50KSB7XG4gICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7IC8vIFZlcmhpbmRlcnQgZGFzIFN0YW5kYXJkLUZvcm11bGFyLUFic2VuZGVuXG5cbiAgICAgICAgbGV0IGZvcm0gPSAkKCcjcXVpei1mb3JtJyk7XG4gICAgICAgIGxldCB1cmwgPSBmb3JtLmF0dHIoJ2FjdGlvbicpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICB0eXBlOiBmb3JtLmF0dHIoJ21ldGhvZCcpLFxuICAgICAgICAgICAgdXJsOiB1cmwsXG4gICAgICAgICAgICBkYXRhOiBmb3JtLnNlcmlhbGl6ZSgpLCAvLyBTZXJpYWxpc2llcnQgZGllIEZvcm11bGFyZGF0ZW5cbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG4gICAgICAgICAgICAgICAgLy8gRXJzZXR6dCBkZW4gSFRNTC1Db2RlIG1pdCBkZXIgbmV1ZW4gRnJhZ2VcbiAgICAgICAgICAgICAgICAkKCcucXVpei1jb250YWluZXInKS5odG1sKCQocmVzcG9uc2UpLmZpbmQoJy5xdWl6LWNvbnRhaW5lcicpLmh0bWwoKSk7XG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uKHhociwgc3RhdHVzLCBlcnJvcikge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ0FKQVggRXJyb3I6Jywgc3RhdHVzLCBlcnJvcik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH0pO1xufSk7IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307Il0sIm5hbWVzIjpbIiQiLCJyZXF1aXJlIiwiZG9jdW1lbnQiLCJyZWFkeSIsIm9uIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImZvcm0iLCJ1cmwiLCJhdHRyIiwiYWpheCIsInR5cGUiLCJkYXRhIiwic2VyaWFsaXplIiwic3VjY2VzcyIsInJlc3BvbnNlIiwiaHRtbCIsImZpbmQiLCJlcnJvciIsInhociIsInN0YXR1cyIsImNvbnNvbGUiXSwic291cmNlUm9vdCI6IiJ9