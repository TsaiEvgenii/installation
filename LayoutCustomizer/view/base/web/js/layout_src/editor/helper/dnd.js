define(function() {

    function isEventInUpperHalf(event, element = null) {
        let rect = (element || event.target).getBoundingClientRect();
        return event.clientY < rect.y + rect.height / 2;
    }

    return {isEventInUpperHalf: isEventInUpperHalf};
});
