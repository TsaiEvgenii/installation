define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    return function (options) {
        let typesFieldSelector = options.typesFieldSelector;
        let valuesFieldSelector = options.valuesFieldSelector;
        let optionCodeSelector = options.optionCodeSelector || 'select[id$="_option_code"]';

        // Function to update option type selects
        function updateOptionTypeSelects() {
            const types = {};

            // Get all type rows
            $(typesFieldSelector + " tbody tr").each(function() {
                const codeInput = $(this).find("input[name$='[code]']");
                const labelInput = $(this).find("input[name$='[label]']");

                if (codeInput.length && labelInput.length) {
                    const code = codeInput.val();
                    const label = labelInput.val();

                    if (code && label) {
                        types[code] = label;
                    }
                }
            });

            // Update all selects
            $(valuesFieldSelector + " " + optionCodeSelector).each(function() {
                const select = $(this);
                const currentValue = select.val();

                // Save current selection and clear
                select.empty();

                // Add empty option
                select.append($("<option>", {
                    value: "",
                    text: "-- Please Select --"
                }));

                // Add all option types
                $.each(types, function(code, label) {
                    select.append($("<option>", {
                        value: code,
                        text: label
                    }));
                });

                // Restore selection if still exists
                if (types[currentValue]) {
                    select.val(currentValue);
                }
            });
        }

        // Initialize on page load
        $(function() {
            // Find the grid ID from the parent container
            const gridIdMatch = $(typesFieldSelector).closest('.design_theme_ua_regexp').attr('id');
            let gridId = '';

            if (gridIdMatch) {
                // Extract just the ID part after "grid_"
                gridId = gridIdMatch.replace('grid_', '');
            }

            // Override the delete method in the arrayRow object
            setTimeout(function() {
                // Find the correct arrayRow object based on the grid ID
                const arrayRowName = 'arrayRow_' + gridId;

                if (window[arrayRowName]) {
                    // Save the original del function
                    const originalDel = window[arrayRowName].del;

                    // Override with our version
                    window[arrayRowName].del = function(rowId) {
                        // Get option code before deletion
                        const row = $('#' + rowId);
                        const optionCode = row.find("input[name$='[code]']").val();

                        // Call original delete function
                        originalDel.apply(this, arguments);

                        // Update selects after deletion
                        setTimeout(function() {
                            updateOptionTypeSelects();
                        }, 300);
                    };
                } else {
                    // Try to find any arrayRow objects in window with the correct format
                    for (let key in window) {
                        if (key.indexOf('arrayRow_') === 0 && typeof window[key] === 'object' && window[key].del) {
                            // Save the original del function
                            const originalDel = window[key].del;

                            // Override with our version
                            window[key].del = function(rowId) {
                                // Call original delete function
                                originalDel.apply(this, arguments);

                                // Update selects after deletion
                                setTimeout(function() {
                                    updateOptionTypeSelects();
                                }, 300);
                            };
                        }
                    }
                }
            }, 1000); // Wait for arrayRow to be defined

            // Standard event handlers
            $(document).on("change", typesFieldSelector + " input", function() {
                updateOptionTypeSelects();
            });

            $(document).on("click", typesFieldSelector + " .action-add", function() {
                setTimeout(function() {
                    updateOptionTypeSelects();
                }, 300);
            });

            $(document).on("click", valuesFieldSelector + " .action-add", function() {
                setTimeout(function() {
                    updateOptionTypeSelects();
                }, 300);
            });

            // Initial update
            setTimeout(function() {
                updateOptionTypeSelects();
            }, 500);
        });
    };
});