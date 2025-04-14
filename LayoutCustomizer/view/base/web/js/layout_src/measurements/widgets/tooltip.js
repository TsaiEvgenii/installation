define([
    '../../ui/widget',
    '../../ui/helper/html',
    'mage/translate'
], function(Widget, HtmlHelper, $t) {

    let HighlighClass = 'highlight';

    function highlight(element) {
        HtmlHelper.addClassName(element, HighlighClass);
    }

    function unhighlight(element) {
        HtmlHelper.removeClassName(element, HighlighClass);
    }

    class Tooltip extends Widget.Base {
        constructor(context, measurement) {
            super('measurement-tooltip', context, 'div');
            this._measurement = measurement;

            this._initElements();

            // subscribe to all object changes
            this.context.eventManager.subscribe(this, 'object', 'changed');
        }

        onEvent(event) {
            this._updateMinMax();
        }

        _initElements() {
            let ef = this.context.elementFactory;

            // Min
            this._minWrapper = ef.make('span', {textContent: 'min '});
            this._minElement = ef.make('em', {className: 'min'});
            this._minWrapper.appendChild(this._minElement);
            this.element.appendChild(this._minWrapper);

            // Separator
            this._separator = ef.make('span', {textContent: ' / '});
            this.element.appendChild(this._separator);

            // Min
            this._maxWrapper = ef.make('span', {textContent: 'max '});
            this._maxElement = ef.make('em', {className: 'max'});
            this._maxWrapper.appendChild(this._maxElement);
            this.element.appendChild(this._maxWrapper);

            this.invalidTypeTooltip = ef.make('span', {textContent: $t('Invalid type.')});
            this.element.appendChild(this.invalidTypeTooltip);
            this._isInvalidType = false;

            // Update
            this._updateMinMax();
        }

        _updateMinMax() {
            let min = this._measurement.getMin(),
                max = this._measurement.getMax();

            // Update min
            if (min === null) { min = 0; }
            if (min !== null) {
                this._minElement.textContent = min;
                HtmlHelper.show(this._minWrapper);
            } else {
                HtmlHelper.hide(this._minWrapper);
            }

            // Update max
            if (max || max === null) {
                this._maxElement.textContent = max;
                HtmlHelper.show(this._maxWrapper);
            } else {
                HtmlHelper.hide(this._maxWrapper);
            }

            // Update separator
            if (min !== null && max !== null) {
                HtmlHelper.show(this._separator);
            } else {
                HtmlHelper.hide(this._separator);
            }

            if(this._isInvalidType)
                this.highlightInvalidType();
            else
                this.unhighlightInvalidType();
        }

        highlightMin() {
            this.unhighlight();
            if (this._minElement) {
                highlight(this._minElement);
            }
        }

        highlightMax() {
            this.unhighlight();
            if (this._maxElement) {
                highlight(this._maxElement);
            }
        }

        unhighlight() {
            if (this._minElement) {
                unhighlight(this._minElement);
            }
            if (this._maxElement) {
                unhighlight(this._maxElement);
            }
            if(this.invalidTypeTooltip) {
                unhighlight(this.invalidTypeTooltip);
            }
        }

        highlightInvalidType() {
            this._isInvalidType = true;
            this.unhighlight();
            if(this.invalidTypeTooltip) {
                HtmlHelper.hide(this._maxWrapper);
                HtmlHelper.hide(this._minWrapper);
                HtmlHelper.hide(this._separator);
                HtmlHelper.show(this.invalidTypeTooltip);
                highlight(this.invalidTypeTooltip);
            }
        }

        unhighlightInvalidType() {
            this._isInvalidType = false;
            this.unhighlight();
            if(this.invalidTypeTooltip) {
                HtmlHelper.hide(this.invalidTypeTooltip);
                unhighlight(this.invalidTypeTooltip);
            }
        }

        get minElement() { return this._minElement; }
        get maxElement() { return this._maxElement; }
        get invalidTypeElement() { return this.invalidTypeTooltip; }
    }

    return {Widget: Tooltip};
});
