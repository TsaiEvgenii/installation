define([], function() {

    var loader = null;

    function initLoader(selector) {
        if(selector) {
            loader = document.querySelector(selector);
        }
        return loader;
    }

    function stopLoader() {
        if(loader) loader.remove();
    }

    return {
        init: initLoader,
        stop: stopLoader
    };
});
