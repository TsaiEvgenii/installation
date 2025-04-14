define([
    '../../helper/html'
], function(HtmlHelper) {

    function isEventInUpperHalf(event, element = null) {
        let rect = (element || event.target).getBoundingClientRect();
        return event.clientY < rect.y + rect.height / 2;
    }

    function removeTargetClassNames(element) {
        ['drop-before', 'drop-after'].forEach(
            HtmlHelper.removeClassName.bind(null, element));

    }

    function setDraggedIdx(event, idx) {
        event.dataTransfer.setData('text/plain', idx);
    }

    function getDraggedIdx(event) {
        return event.dataTransfer.getData('text/plain');
    }

    function isDragged(event, row) {
        return HtmlHelper.index(row) == getDraggedIdx(event);
    }

    function dragStart(event) {
        let row = this;
        HtmlHelper.addClassName(row, 'dragged');
        event.dataTransfer.effectAllowed = 'move';
        setDraggedIdx(event, HtmlHelper.index(row));
    }

    function dragEnd(event) {
        let row = this;
        HtmlHelper.removeClassName(row, 'dragged');
    }

    function dragEnter(event) {
        event.preventDefault();
    }

    function dragLeave(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'none';
        // Show drop target
        let targetRow = this;
        removeTargetClassNames(targetRow);
    }

    function dragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        // Show drop target
        let targetRow = this;
        removeTargetClassNames(targetRow);
        if (HtmlHelper.index(targetRow) != getDraggedIdx(event)) {
            let className = isEventInUpperHalf(event, targetRow)
                ? 'drop-before'
                : 'drop-after';
            HtmlHelper.addClassName(targetRow, className);
        }
    }

    function drop(event, callback) {
        event.preventDefault();
        let targetRow = this,
            targetRowIdx = HtmlHelper.index(targetRow),
            draggedRowIdx = getDraggedIdx(event);
        if (targetRowIdx != draggedRowIdx) {
            callback(draggedRowIdx, targetRowIdx, isEventInUpperHalf(event, targetRow));
        }
        // Show drop target
        removeTargetClassNames(targetRow);
    }

    function initRow(row, callback) {
        row.ondragstart = dragStart;
        row.ondragend = dragEnd;
        // row.ondragenter = dragEnter;
        row.ondragleave = dragLeave;
        row.ondragover = dragOver;
        row.ondrop = function(event) {
            drop.call(this, event, callback);
        }
    }

    return {initRow: initRow};
});
