(self.webpackChunk=self.webpackChunk||[]).push([[524],{8003:(e,t,n)=>{"use strict";n(9582),n(8304),n(113),n(739),n(6099),n(2762),n(5285),n(724);var r=n(4692);r((function(){if(r("#sortable").length&&(r("#sortable").sortable(),r("#sortable").disableSelection(),r("#save-order").on("click",(function(e){var t={};r("#sortable li").each((function(e){t[r(this).data("id")]=e+1})),r.ajax({url:"/question-sort",method:"POST",data:{positions:t},success:function(e){alert(e)},error:function(e,t,n){alert("Fehler beim Speichern der Sortierung: "+n)}})}))),r(".toggle-quiz").length&&r(".toggle-quiz").on("change",(function(){var e=r(this).data("url"),t=r(this).is(":checked"),n=r(this).next("label");r.ajax({url:e,type:"POST",contentType:"application/json",headers:{"X-CSRF-TOKEN":'{{ csrf_token("toggle_quiz") }}'},data:JSON.stringify({isEnabled:t}),success:function(e){e.success?t?n.text("Quiz ist aktiviert"):n.text("Quiz ist deaktiviert"):alert("Fehler beim Aktualisieren des Quiz-Status.")},error:function(e,t,n){console.error("Fehler:",n)}})})),r("#show-answers-button").length&&r("#show-answers-button").on("click",(function(e){e.preventDefault();var t=r(this).attr("data-href");r("#show-answers-button").html('<i class="fa-solid fa-spinner fa-spin"></i></i> Ergebnisse aktualisieren'),r.ajax({url:t,method:"GET",success:function(e){var t=r(e).find("#answer-stats-container").html();r("#answer-stats-container").html(t),r("#show-answers-button .fa-solid").fadeOut(300,(function(){r("#show-answers-button").html('<i class="fa-solid fa-arrows-rotate"></i> Ergebnisse aktualisieren'),r(this).fadeIn(300)}))}})})),r(".code__bool").length){var e=r("#codeBoolSelf"),t=r("#quizcode");r(".code__bool").on("change",(function(){e.is(":checked")?(t.prop("disabled",!1),t.prop("required",!0)):(t.prop("disabled",!0),t.prop("required",!1))}))}r("#codeInput").length&&r("#codeInput").on("change",(function(){""!==r(this).val().trim()?r("#startQuizBtn").prop("disabled",!1):r("#startQuizBtn").prop("disabled",!0)}))}));n(4692);n(9336)},8304:(e,t,n)=>{var r=n(4692);n(113),n(6099),n(6031),r((function(){r(".leaderboard").length&&setInterval((function(){var e=r(".leaderboard").data("quiz");r.ajax({url:"/quiz-leaderboard/".concat(e),method:"GET",success:function(e){var t=r(e).find("#container-stats").html();r("#container-stats").html(t)},error:function(){console.error("Error updating leaderboard")}})}),2e3)}))},9582:(e,t,n)=>{var r=n(4692);n(113),n(6099),r((function(){r(".quiz__next-answer").on("click",(function(e){e.preventDefault();var t=r("#quiz-form"),n=t.attr("action"),a=r("#loading"),i=a.html();a.html('<i class="fa-solid fa-spinner fa-spin"></i>'),r.ajax({type:t.attr("method"),url:n,data:t.serialize(),success:function(e){var t=r(e).find(".quiz-container").html();r(".quiz-container").fadeOut(200,(function(){r(this).html(t),a.html(i),r(this).fadeIn(200)}))},error:function(e,t){console.error("AJAX Error:",e,t),a.html(i)}})})),r("#newName").on("click",(function(e){e.preventDefault();var t=r("#newName").attr("href");r.ajax({type:"get",url:t,success:function(e){r("#matrikelnummerChange").fadeOut(150,(function(){r(this).html(e.matrikelnummer),r(this).fadeIn(150)}))},error:function(e,t){console.error("AJAX Error:",e,t)}})})),r(".answer__radio").on("click",(function(){r("#submit-btn").prop("disabled",!1)}))}))}},e=>{e.O(0,[494],(()=>{return t=8003,e(e.s=t);var t}));e.O()}]);