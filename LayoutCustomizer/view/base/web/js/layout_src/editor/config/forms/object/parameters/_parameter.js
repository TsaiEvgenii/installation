define(function() {
    return {
        onReset: [
            function(form) {
                // Add option list

                // Remove field
                form.removeField('options');

                let parameter = form.getObject();
                if (parameter) {
                    let oh = form.context.objectHelper,
                        block = oh.getParent(parameter),
                        params = oh.getParams(block),
                        param = params[parameter.name];

                    // Option field description
                    let field = {
                        name: 'options',
                        label: 'Options',
                        type: 'ext-option-list',
                        params: {
                            valueInput: param.type || 'text',
                            valueOptions: (param.type == 'select' ? (param.options || []) : [])
                        }
                    };

                    // Add field
                    form.addField(field.name, field.label, field.type, field.params);
                }
            }
        ]
    }
});
