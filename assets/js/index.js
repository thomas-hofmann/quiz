
$(function() {
    const input = document.getElementById('codeInput');
    const basePlaceholder = "Beitrittscode";
    let charCount = 1; // Start bei 1, um mit "B" zu beginnen
    let increment = true;

    const intervalId = setInterval(() => {
    // Erstelle den Placeholder mit dem fortlaufenden Wort
    input.setAttribute('placeholder', basePlaceholder.slice(0, charCount));

    // Buchstaben hochzählen
    if (increment) {
        charCount++;
        if (charCount > basePlaceholder.length) {
        increment = false; // Wenn das ganze Wort erreicht ist, stoppen
        clearInterval(intervalId); // Stoppe die Animation
        }
    }

    }, 75); // Intervall von 500ms
});