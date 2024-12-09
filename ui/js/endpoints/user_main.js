(function() {
    const emulateButtonClick = () => {
        // Get the submit button
        let button = document.getElementById('submit-button');

        // Apply a "clicked" style to the button
        button.classList.add('clicked');

        // Programmatically "click" the button
        button.click();

        // Remove the "clicked" style after a short delay to give a click effect
        setTimeout(() => {
            button.classList.remove('clicked');
        }, 200);
    };

    const onEnterKeyDown = e => {
        if (e.keyCode === 13) { // 13 is the key code for Enter
            emulateButtonClick();
        }
    };

    document.addEventListener('keydown', onEnterKeyDown, false);
})();
