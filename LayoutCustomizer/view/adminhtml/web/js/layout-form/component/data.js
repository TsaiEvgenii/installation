define(function() {

    // Import
    function Import() {}

    Import.prototype.process = function(data) {
        return data;
    }

    // Export
    function Export() {}

    Export.prototype.process = function(data) {
        return data;
    }

    return {
        Import: Import,
        Export: Export
    };
});
