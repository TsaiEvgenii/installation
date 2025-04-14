define(function() {

    function addClassName(element, className) {
        element.classList.add(className);
    }

    function removeClassName(element, className) {
        element.classList.remove(className);
    }

    function hide(element) {
        element.style.display = 'none';
    }

    function show(element) {
        element.style.display = '';
    }

    function isHidden(element) {
        return element.style.display == 'none';
    }

    function index(element) {
        let idx = 0;
        while (element = element.previousElementSibling)
            ++idx;
        return idx;
    }

    return {
        addClassName: addClassName,
        removeClassName: removeClassName,
        hide: hide,
        show: show,
        isHidden: isHidden,
        index: index
    };
})
