import { startStimulusApp } from '@symfony/stimulus-bridge';

document.addEventListener('DOMContentLoaded', function () {
    // Hier startest du die Stimulus-Anwendung nach dem Laden des DOMs
    const app = startStimulusApp(require.context(
        '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
        true,
        /\.[jt]sx?$/
    ));
});