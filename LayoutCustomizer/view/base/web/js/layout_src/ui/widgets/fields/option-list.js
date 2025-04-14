define([
    '../field',
    '../dnd/table',
    '../../helper/html',
    '../../../data/helper'
], function(Field, DndTable, HtmlHelper, DataHelper) {

    class OptionList extends Field.Base {
        constructor(context, params) {
            super('option-table', context, params, 'table');

            // header
            this.element.appendChild(this._makeHeader());
            // body
            this._body = this._makeBody();
            this.element.appendChild(this._body);
            // footer
            this.element.appendChild(this._makeFooter());

            this._options = [];
        }

        getValue() {
            return this._options;
        }

        setValue(value) {
            this._options = (value || []);
            this._updateTable();
        }

        onSort(idx, targetIdx, before) {
            // get moved option
            let option = this._options[idx];
            // remove from list
            this._options.splice(idx, 1);
            // adjust target index
            if (targetIdx > idx) {
                --targetIdx;
            }
            this._options.splice((before ? targetIdx : targetIdx + 1), 0, option);
            this._updateTable();
        }

        reset() {
            this.setValue([]);
        }

        _makeHeader() {
            let ef = this.context.elementFactory,
                header = ef.make('thead'),
                row = ef.make('tr');
            header.appendChild(row);
            row.appendChild(ef.make('th'));
            row.appendChild(ef.make('th'));
            row.appendChild(ef.make('th', {textContent: 'Label'}));
            row.appendChild(ef.make('th', {textContent: 'Value'}));
            return header;
        }

        _makeBody() {
            return this.context.elementFactory.make('tbody');
        }

        _makeFooter() {
            let ef = this.context.elementFactory,
                footer = ef.make('tfoot'),
                row = ef.make('tr'),
                col = ef.make('td', {colSpan: 5});
            footer.appendChild(row);
            row.appendChild(col);
            col.appendChild(this._makeAddButton());
            return footer;
        }

        _makeAddButton() {
            this._addButton = this.context.elementFactory.make('button', {
                type: 'button',
                className: 'add',
                textContent: 'Add Option',
                onclick: this._addNewOption.bind(this)
            });
            return this._addButton;
        }

        _updateTable() {
            let rows = this._body.children,
                optionIdx = 0;
            // add or update rows
            for (; optionIdx < this._options.length; ++optionIdx) {
                let option = this._options[optionIdx];

                if (optionIdx < rows.length) {
                    this._updateRow(rows[optionIdx], option);
                } else {
                    // adding new row to table
                    //check if new option is composite
                    if(option.key_family && option.parent_key_family === undefined) {
                        //TODO: add function to check existing TR (TD) with 'key_family'=value
                        let optRow = this._makeOptionRow(option);
                        if(option.key_family) {
                            optRow.setAttribute('key-family', option.key_family);
                        }
                        this._body.appendChild(optRow);

                    } else {
                        this._addOptionRow(option);
                    }
                }
            }
            // remove unused rows
            for (let i = rows.length - 1; i >= optionIdx; --i) {
                this._body.removeChild(rows[i]);
            }
        }

        _addNewOption() {
            this._addOption({value: '', label: ''});
        }

        _addOneOption(row) {
            this._addOneMoreOption({value: '', label: ''});
        }

        _addOneMoreOption(option, row){
            let newRow = this._makeOptionRow(option);

            if(!row.classList.contains('main-row')) {
                row.classList.add('main-row');
            }

            //span rows with value to make it general for options in same key_family
            this._spanValueRows(row);

            //add new option row
            this._body.insertBefore(newRow, row.nextSibling);


            let currentOption = this._getRowOption(row),
                keyFamilyInd = this._getRowOptionIndex(row) + 1;
            keyFamilyInd = this._getLastKeyFamilyInd() + 1 || 0;
            option = DataHelper.merged(option, {value: currentOption.value});

            //TODO: code refactoring - create function
            if(currentOption.key_family === undefined) {
                currentOption.key_family = keyFamilyInd;
                currentOption.parent_key_family = currentOption.key_family;
                row.setAttribute('key-family', currentOption.key_family);
            }
            option.key_family = currentOption.key_family;
            newRow.setAttribute('key-family', currentOption.key_family);


            let mainRowInd = this._getRowOptionIndex(row) + 1;
            this._options.splice(mainRowInd, 0, option);
            this.change();
            // console.log(this._options);
        }
        _makeOptionRow(option) {
            let ef = this.context.elementFactory,
                newRow = ef.make('tr', {
                    className: 'added-row'
                }),
                dndRow = ef.make('td'),
                btnRow = ef.make('td'),
                newOption = ef.make('td', {
                    textContent: option.label,
                    title: option.path
                }),
                deleteBtn = ef.make('button', {
                    type: 'button',
                    textContent: 'X',
                    className: 'remove',
                    onclick: this._deleteAddedRow.bind(this, newRow)
                }),
                deleteBtnRow = ef.make('td');
            newOption.appendChild(deleteBtn);
            newRow.append(dndRow, btnRow, newOption, deleteBtnRow);
            return newRow;
        }
        _spanValueRows(row) {
            let valueRow = row.getElementsByTagName('td')[3],
                currentRowSpan = valueRow.getAttribute('rowspan'),
                rowSpanValue = currentRowSpan ? ++currentRowSpan : 2;
            valueRow.setAttribute('rowspan', rowSpanValue);
            //TODO: add function for _deleteSpanValueRows
        }
        _changeSpanValueRows(row, rowSpanValue) {
            let valueRow = row.getElementsByTagName('td')[3];
            valueRow.setAttribute('rowspan', rowSpanValue);
        }
        _deleteAddedRow(row) {
            let keyFamily = row.getAttribute('key-family');
            let keyFamilyOptionRows = this._getOptionRowsByFamilyKey(row, keyFamily);
            //if we have
            if(keyFamilyOptionRows.length < 3) {
                let parentRow = this._getParentOptionRowByFamilyKey(row, keyFamily)[0];
                if(parentRow) {
                    let parentOption = this._getRowOption(parentRow);
                    delete parentOption.key_family;
                    delete parentOption.parent_key_family;
                }
            }
            this._deleteRow(row);
        }
        _getLastKeyFamilyInd() {
            return Math.max(...this._options.map((opt) => { return opt.key_family ? opt.key_family : 0; }));
        }


        _addOption(option) {
            this._options.push(option);
            this._updateTable();
            this.change();
        }

        _addOptionRow(option) {
            // make element
            let row = this._makeRow();

            //TODO: function _setFamilyKeyClasses(row)
            if(option.key_family) {
                row.setAttribute('key-family', option.key_family);
                if(option.parent_key_family === undefined)
                    row.classList.add('added-row');
                else {
                    row.classList.add('main-row');
                    let optionsFamilyKey = this._options.filter(opt => {
                        return opt.key_family === option.parent_key_family;
                    })
                    this._changeSpanValueRows(row, optionsFamilyKey.length);
                }
            }

            // set values
            this._updateRow(row, option);
            // add to table
            this._body.appendChild(row);
            // init drag-and-drop
            DndTable.initRow(row, this.onSort.bind(this));
        }

        _makeRow() {
            let ef = this.context.elementFactory,
                row = ef.make('tr', {draggable: true});
            row.appendChild(this._makeDndHandleColumn(row));

            row.appendChild(this._makeAddButtonColumn(row));

            row.appendChild(this._makeLabelColumn(row));
            row.appendChild(this._makeValueColumn(row));
            row.appendChild(this._makeDeleteButtonColumn(row));
            return row;
        }

        _makeDndHandleColumn(row) {
            let col = this.context.elementFactory.make('td', {
                className: 'dnd-handle'
            });
            return col;
        }

        _makeValueColumn(row) {
            let ef = this.context.elementFactory,
                col = ef.make('td'),
                input = ef.make('input', {type: 'text'});
            input.onchange = function() {
                this._changeRow(row, 'value', input.value);
            }.bind(this);
            col.appendChild(input);
            return col;
        }

        _makeLabelColumn(row) {
            let ef = this.context.elementFactory,
                col = ef.make('td'),
                input = ef.make('input', {type: 'text'});
            input.onchange = function() {
                this._changeRow(row, 'label', input.value);
            }.bind(this);
            col.appendChild(input);
            return col;
        }

        _makeAddButtonColumn(row) {
            let ef = this.context.elementFactory,
                col = ef.make('td'),
                _addOptionButton = ef.make('button', {
                    type: 'button',
                    className: 'add-option',
                    textContent: '+',
                    onclick: this._addOneOption.bind(this, row)
                });
            col.appendChild(_addOptionButton);
            return col;
        }

        _makeDeleteButtonColumn(row) {
            let ef = this.context.elementFactory,
                col = ef.make('td'),
                deleteButton = ef.make('button', {
                    type: 'button',
                    className: 'remove',
                    textContent: 'X',
                    onclick: this._deleteRow.bind(this, row)
                });
            col.appendChild(deleteButton);
            return col;
        }

        _changeRow(row, field, value) {
            let option = this._getRowOption(row),
                familyKey = row.getAttribute('key-family');
            if(familyKey) {
                this._getOptionRowsByFamilyKey(row, familyKey).forEach(function(otherRow) {
                    let otherOption = this._getRowOption(otherRow);
                    otherOption[field] = value;
                }, this);
            }
            option[field] = value;
            this.change();
        }

        _getOptionRowsByFamilyKey(row, familyKey) {
            return row.parentNode.querySelectorAll('[key-family="' + familyKey + '"]');
        }
        _getParentOptionRowByFamilyKey(row, familyKey) {
            return row.parentNode.querySelectorAll('.main-row[key-family="' + familyKey + '"]');
        }


        _updateRow(row, option) {
            let inputs = row.getElementsByTagName('input'),
                values = [option.label, option.value];
            for (let i = 0; i < inputs.length; ++i) {
                let value = values[i];
                inputs[i].value = (value !== undefined) ? value : '';
            }
        }

        _getRowOptionIndex(row) {
            return HtmlHelper.index(row);
        }

        _getRowOption(row) {
            let idx = this._getRowOptionIndex(row);
            return this._options[idx];
        }

        _deleteRow(row) {
            let familyKey = row.getAttribute('key-family');
            if(familyKey) {
                this._getOptionRowsByFamilyKey(row, familyKey).forEach((rowOpt) => {
                    if(!rowOpt.classList.contains('main-row')) {
                        let idOpt = this._getRowOptionIndex(rowOpt);
                        this._options.splice(idOpt, 1);
                    } else {
                        let parentOption = this._getRowOption(rowOpt);
                        delete parentOption.key_family;
                        delete parentOption.parent_key_family;
                    }
                });
            }
            let idx = this._getRowOptionIndex(row);
            this._options.splice(idx, 1);
            this.change();
            this._updateTable();
        }
    }

    return {Widget: OptionList};
});
