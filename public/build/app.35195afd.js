(self.webpackChunk=self.webpackChunk||[]).push([[524],{4028:(r,e,o)=>{"use strict";o(9582),o(4692);o(9336)},9582:(r,e,o)=>{var n=o(4692);o(113),o(6099),n((function(){n("#quiz-form").on("submit",(function(r){r.preventDefault();var e=n("#quiz-form"),o=e.attr("action");n.ajax({type:e.attr("method"),url:o,data:e.serialize(),success:function(r){var e=n(r).find(".quiz-container").html();n(".quiz-container").fadeOut(250,(function(){n(this).html(e),n(this).fadeIn(250)}))},error:function(r,e){console.error("AJAX Error:",r,e)}})})),n(".answer__radio").on("click",(function(){n("#submit-btn").prop("disabled",!1)}));var r=n("#codeBoolSelf"),e=n("#quizcode");n(".code__bool").on("change",(function(){r.is(":checked")?(e.prop("disabled",!1),e.prop("required",!0)):(e.prop("disabled",!0),e.prop("required",!1))}))}))}},r=>{r.O(0,[678],(()=>{return e=4028,r(r.s=e);var e}));r.O()}]);