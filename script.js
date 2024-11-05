// The waitForPageLoad function ensures that the callback is executed only after the page has fully loaded.
function waitForPageLoad(callback) {
    if (document.readyState === 'complete') {
        callback();
    } else {
        window.addEventListener('load', callback);
    }
}

waitForPageLoad(function() {
    var menu_button = document.getElementById('menu_button');
    menu_button.addEventListener('click', function() {
        var header_nav = document.getElementById('header_nav');
        var header_buttons = document.getElementById('header_buttons');
        header_nav.classList.toggle('open');
        header_buttons.classList.toggle('open');
    });
});