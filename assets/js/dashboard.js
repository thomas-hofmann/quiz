import 'jquery-ui/ui/widgets/sortable';
import 'jquery-ui/ui/disable-selection';

$(function() {
    if ($("#sortable").length) {
        // Aktivieren der Sortierbarkeit mit jQuery UI
        $("#sortable").sortable();
        $("#sortable").disableSelection();

        // Klick-Event für den Speichern-Button
        $('#save-order').on('click', function(event) {
            event.preventDefault();

            // Positionen der Fragen erfassen
            let positions = {};
            $('#sortable li').each(function(index) {
                positions[$(this).data('id')] = index + 1;
            });

            // AJAX-Anfrage zum Speichern der neuen Reihenfolge
            $.ajax({
                url: '/question-sort',
                method: 'POST',
                data: { positions: positions },
                success: function() {
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Fehler beim Speichern der Sortierung: ' + error);
                }
            });
        });
    }
    if ($(".toggle-quiz").length) {
        $('.toggle-quiz').on('change', function() {
            const url = $(this).data('url');
            const isEnabled = $(this).is(':checked');
            const label = $(this).next('label');
        
            $.ajax({
                url: url,
                type: 'POST',
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token("toggle_quiz") }}'
                },
                data: JSON.stringify({ isEnabled: isEnabled }),
                success: function(data) {
                    if (data.success) {
                        // Ändere den Text des Labels basierend auf dem isEnabled-Wert
                        if (isEnabled) {
                            label.text('Quiz ist aktiviert');
                        } else {
                            label.text('Quiz ist deaktiviert');
                        }
                    } else {
                        alert('Fehler beim Aktualisieren des Quiz-Status.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Fehler:', error);
                }
            });
        });
    }
    if ($("#show-answers-toggle").length) {
        $('#show-answers-toggle').on('change', function() {
            const showStats = $(this).is(':checked');

            $('.show-question-stats-button').each(function() {
                const $toggle = $(this);

                if ($toggle.is(':checked') !== showStats) {
                    $toggle.prop('checked', showStats).trigger('change');
                }
            });
        });
    }
    if ($("#answer-stats-container").length) {
        function updateAllAnswersToggle() {
            const allToggles = $('.show-question-stats-button').length;
            const checkedToggles = $('.show-question-stats-button:checked').length;
            $('#show-answers-toggle').prop('checked', allToggles > 0 && allToggles === checkedToggles);
        }

        function waitForImages($element) {
            const images = $element.find('img').toArray();

            if (!images.length) {
                return $.Deferred().resolve().promise();
            }

            return $.when.apply($, images.map(function(image) {
                const deferred = $.Deferred();
                const preloadImage = new Image();

                preloadImage.onload = preloadImage.onerror = function() {
                    deferred.resolve();
                };
                preloadImage.src = image.src;

                return deferred.promise();
            }));
        }

        $('#answer-stats-container').on('change', '.show-question-stats-button', function() {
            const $button = $(this);
            const url = $button.attr('data-href');
            const target = $button.attr('data-target');
            const $slot = $(target);
            const $defaultContent = $slot.find('.question-stat-default');
            const $resultContent = $slot.find('.question-stat-result');

            if (!$button.is(':checked')) {
                $resultContent.addClass('d-none');
                $defaultContent.removeClass('d-none');
                $button.prop('disabled', false);
                updateAllAnswersToggle();
                return;
            }

            if ($resultContent.children().length) {
                $defaultContent.addClass('d-none');
                $resultContent.removeClass('d-none');
                updateAllAnswersToggle();
                return;
            }

            $button.prop('disabled', true);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    const $newContent = $(data);

                    waitForImages($newContent).always(function() {
                        $resultContent.html($newContent);

                        if ($button.is(':checked')) {
                            $defaultContent.addClass('d-none');
                            $resultContent.removeClass('d-none');
                        }

                        $button.prop('disabled', false);
                        updateAllAnswersToggle();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Fehler:', error);
                    $button.prop('checked', false).prop('disabled', false);
                    updateAllAnswersToggle();
                }
            });

            updateAllAnswersToggle();
        });
    }
    if ($('.code__bool').length) {
        const $codeBoolSelf = $('#codeBoolSelf');
        const $quizcode = $('#quizcode');
        $('.code__bool').on('change', function() {
            // Check if the radio button is checked
            if ($codeBoolSelf.is(':checked')) {
                // Enable the input field
                $quizcode.prop('disabled', false);
                $quizcode.prop('required', true);
            } else {
                // Disable the input field (in case of multiple radio buttons affecting this field)
                $quizcode.prop('disabled', true);
                $quizcode.prop('required', false);
            }
        });
    }
});
