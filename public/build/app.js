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
$(function () {
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
        var newContent = $(response).find('.quiz-container').html();
        // Fade out the current content
        $('.quiz-container').fadeOut(250, function () {
          // Replace the HTML content
          $(this).html(newContent);
          // Fade in the new content
          $(this).fadeIn(250);
        });
      },
      error: function error(status, _error) {
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQSxJQUFNQSxDQUFDLEdBQUdDLG1CQUFPLENBQUMsb0RBQVEsQ0FBQztBQUVMO0FBRXRCQSxtQkFBTyxDQUFDLG9FQUFXLENBQUM7Ozs7Ozs7Ozs7Ozs7O0FDWnBCRCxDQUFDLENBQUMsWUFBVztFQUNUQSxDQUFDLENBQUMsWUFBWSxDQUFDLENBQUNFLEVBQUUsQ0FBQyxRQUFRLEVBQUUsVUFBU0MsS0FBSyxFQUFFO0lBQ3pDQSxLQUFLLENBQUNDLGNBQWMsQ0FBQyxDQUFDLENBQUMsQ0FBQzs7SUFFeEIsSUFBSUMsSUFBSSxHQUFHTCxDQUFDLENBQUMsWUFBWSxDQUFDO0lBQzFCLElBQUlNLEdBQUcsR0FBR0QsSUFBSSxDQUFDRSxJQUFJLENBQUMsUUFBUSxDQUFDO0lBRTdCUCxDQUFDLENBQUNRLElBQUksQ0FBQztNQUNIQyxJQUFJLEVBQUVKLElBQUksQ0FBQ0UsSUFBSSxDQUFDLFFBQVEsQ0FBQztNQUN6QkQsR0FBRyxFQUFFQSxHQUFHO01BQ1JJLElBQUksRUFBRUwsSUFBSSxDQUFDTSxTQUFTLENBQUMsQ0FBQztNQUFFO01BQ3hCQyxPQUFPLEVBQUUsU0FBQUEsUUFBU0MsUUFBUSxFQUFFO1FBQ3hCO1FBQ0EsSUFBSUMsVUFBVSxHQUFHZCxDQUFDLENBQUNhLFFBQVEsQ0FBQyxDQUFDRSxJQUFJLENBQUMsaUJBQWlCLENBQUMsQ0FBQ0MsSUFBSSxDQUFDLENBQUM7UUFDM0Q7UUFDQWhCLENBQUMsQ0FBQyxpQkFBaUIsQ0FBQyxDQUFDaUIsT0FBTyxDQUFDLEdBQUcsRUFBRSxZQUFXO1VBQ3pDO1VBQ0FqQixDQUFDLENBQUMsSUFBSSxDQUFDLENBQUNnQixJQUFJLENBQUNGLFVBQVUsQ0FBQztVQUN4QjtVQUNBZCxDQUFDLENBQUMsSUFBSSxDQUFDLENBQUNrQixNQUFNLENBQUMsR0FBRyxDQUFDO1FBQ3ZCLENBQUMsQ0FBQztNQUNOLENBQUM7TUFDREMsS0FBSyxFQUFFLFNBQUFBLE1BQVNDLE1BQU0sRUFBRUQsTUFBSyxFQUFFO1FBQzNCRSxPQUFPLENBQUNGLEtBQUssQ0FBQyxhQUFhLEVBQUVDLE1BQU0sRUFBRUQsTUFBSyxDQUFDO01BQy9DO0lBQ0osQ0FBQyxDQUFDO0VBQ04sQ0FBQyxDQUFDO0VBRUZuQixDQUFDLENBQUMsZ0JBQWdCLENBQUMsQ0FBQ0UsRUFBRSxDQUFDLE9BQU8sRUFBRSxZQUFXO0lBQ3ZDO0lBQ0FGLENBQUMsQ0FBQyxhQUFhLENBQUMsQ0FBQ3NCLElBQUksQ0FBQyxVQUFVLEVBQUUsS0FBSyxDQUFDO0VBQzVDLENBQUMsQ0FBQztBQUNOLENBQUMsQ0FBQzs7Ozs7Ozs7Ozs7O0FDaENGIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2FwcC5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvanMvcXVpei5qcyIsIndlYnBhY2s6Ly8vLi9hc3NldHMvc3R5bGVzL2FwcC5zY3NzIl0sInNvdXJjZXNDb250ZW50IjpbIi8qXG4gKiBXZWxjb21lIHRvIHlvdXIgYXBwJ3MgbWFpbiBKYXZhU2NyaXB0IGZpbGUhXG4gKlxuICogV2UgcmVjb21tZW5kIGluY2x1ZGluZyB0aGUgYnVpbHQgdmVyc2lvbiBvZiB0aGlzIEphdmFTY3JpcHQgZmlsZVxuICogKGFuZCBpdHMgQ1NTIGZpbGUpIGluIHlvdXIgYmFzZSBsYXlvdXQgKGJhc2UuaHRtbC50d2lnKS5cbiAqL1xuXG4vLyBhbnkgQ1NTIHlvdSBpbXBvcnQgd2lsbCBvdXRwdXQgaW50byBhIHNpbmdsZSBjc3MgZmlsZSAoYXBwLmNzcyBpbiB0aGlzIGNhc2UpXG5jb25zdCAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XG5cbmltcG9ydCAnLi9qcy9xdWl6LmpzJztcblxucmVxdWlyZSgnYm9vdHN0cmFwJyk7XG5pbXBvcnQgJy4vc3R5bGVzL2FwcC5zY3NzJztcbiIsIiQoZnVuY3Rpb24oKSB7XG4gICAgJCgnI3F1aXotZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihldmVudCkge1xuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpOyAvLyBWZXJoaW5kZXJ0IGRhcyBTdGFuZGFyZC1Gb3JtdWxhci1BYnNlbmRlblxuXG4gICAgICAgIGxldCBmb3JtID0gJCgnI3F1aXotZm9ybScpO1xuICAgICAgICBsZXQgdXJsID0gZm9ybS5hdHRyKCdhY3Rpb24nKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgdHlwZTogZm9ybS5hdHRyKCdtZXRob2QnKSxcbiAgICAgICAgICAgIHVybDogdXJsLFxuICAgICAgICAgICAgZGF0YTogZm9ybS5zZXJpYWxpemUoKSwgLy8gU2VyaWFsaXNpZXJ0IGRpZSBGb3JtdWxhcmRhdGVuXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xuICAgICAgICAgICAgICAgIC8vIEVyc2V0enQgZGVuIEhUTUwtQ29kZSBtaXQgZGVyIG5ldWVuIEZyYWdlXG4gICAgICAgICAgICAgICAgbGV0IG5ld0NvbnRlbnQgPSAkKHJlc3BvbnNlKS5maW5kKCcucXVpei1jb250YWluZXInKS5odG1sKCk7XG4gICAgICAgICAgICAgICAgLy8gRmFkZSBvdXQgdGhlIGN1cnJlbnQgY29udGVudFxuICAgICAgICAgICAgICAgICQoJy5xdWl6LWNvbnRhaW5lcicpLmZhZGVPdXQoMjUwLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgLy8gUmVwbGFjZSB0aGUgSFRNTCBjb250ZW50XG4gICAgICAgICAgICAgICAgICAgICQodGhpcykuaHRtbChuZXdDb250ZW50KTtcbiAgICAgICAgICAgICAgICAgICAgLy8gRmFkZSBpbiB0aGUgbmV3IGNvbnRlbnRcbiAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5mYWRlSW4oMjUwKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24oc3RhdHVzLCBlcnJvcikge1xuICAgICAgICAgICAgICAgIGNvbnNvbGUuZXJyb3IoJ0FKQVggRXJyb3I6Jywgc3RhdHVzLCBlcnJvcik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH0pO1xuXG4gICAgJCgnLmFuc3dlcl9fcmFkaW8nKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAgICAgICAgLy8gRW5hYmxlIHRoZSBzdWJtaXQgYnV0dG9uXG4gICAgICAgICQoJyNzdWJtaXQtYnRuJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG4gICAgfSk7XG59KTsiLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiXSwibmFtZXMiOlsiJCIsInJlcXVpcmUiLCJvbiIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJmb3JtIiwidXJsIiwiYXR0ciIsImFqYXgiLCJ0eXBlIiwiZGF0YSIsInNlcmlhbGl6ZSIsInN1Y2Nlc3MiLCJyZXNwb25zZSIsIm5ld0NvbnRlbnQiLCJmaW5kIiwiaHRtbCIsImZhZGVPdXQiLCJmYWRlSW4iLCJlcnJvciIsInN0YXR1cyIsImNvbnNvbGUiLCJwcm9wIl0sInNvdXJjZVJvb3QiOiIifQ==