define(function() {

    function _getFromList(list, path, id) {
        for (let i = 0; i < list.length; ++i) {
            let root = list[i],
                option = _get(path, root, id);
            if (option) {
                return option;
            }
        }
        return null;
    }

    function _get(path, root, id) {
        path += (path.length == 0 ? root.label : '/' + root.label);
        return (root.id !== undefined && root.id == id)
            ? {id: root.id, label: root.label, path: path}
            : _getFromList(root.children || [], path, id);
    }

    function _toValueTree(extOption) {
        return {
            label: extOption.label,
            value: extOption.id,
            children: (extOption.children || []).map(_toValueTree)
        }
    }

    function get(extOptions, id) {
        return _getFromList(extOptions, '', id);
    }

    function toValueTree(extOptions) {
        return extOptions.map(_toValueTree);
    }

    return {
        get: get,
        toValueTree: toValueTree
    };
});
