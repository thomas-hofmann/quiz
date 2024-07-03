$(function() {
    // Wähle alle Flash-Messages aus
    $('.flash-message-wrapper').each(function() {
        var $message = $(this);

        // Setze ein Timeout, um die Nachricht nach 5 Sekunden auszublenden
        setTimeout(function() {
            $message.css({
                transition: 'transform 1s ease, opacity 1s ease',
                transform: 'translateY(-100%)',  // Verschiebe die Nachricht um 100% der eigenen Höhe nach oben
                opacity: '0'
            });
            setTimeout(function() {
                $message.remove();
            }, 500); // 1s nach der Animation
        }, 1000); // 5s bis zum Start der Ausblendung
    });
});
