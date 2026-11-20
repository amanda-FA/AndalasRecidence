// A page restored from browser history may still contain a token from before login/logout.
window.addEventListener('pageshow', event => {
    if (event.persisted && document.querySelector('form input[name="_token"]')) {
        window.location.reload();
    }
});
