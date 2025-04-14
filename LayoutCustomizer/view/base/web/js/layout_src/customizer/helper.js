define([
    '../ui/helper/html',
    '../measurements/widgets/measurement-input',
    'mage/translate'
], function(HtmlHelper, MeasurementInput, $t) {

    function makeInput(context, drawer, measurement) {
        let input = new MeasurementInput.Widget(context, drawer, measurement);
        if(!measurement._isCustomizable) {
            input.element.classList.add("non-customizable");
            input.element.getElementsByTagName('input')[0].setAttribute('readonly', true);
        } else {
            if(measurement.name === 'width') {
                createCustomerTooltip(input.element);
            }
            input._input.addEventListener('focus', hideCustomerTooltip.bind(drawer.canvas.parentNode));
        }
        drawer.canvas.parentNode.appendChild(input.element);
        return input;
    }

    function getInput(context, drawer, measurement) {
        if (!measurement.input) {
            measurement.input = makeInput(context, drawer, measurement);
            measurement.input.resetValue();
        }

        // measurement.input.resetValue();
        return measurement.input;
    }

    function createCustomerTooltip(element) {
        element.classList.add('customer-tooltip');
        element.setAttribute('data-customer-tooltip', $t('Enter your measurements in the fields'));
    }
    function hideCustomerTooltip() {
        let customerTooltipEl = this.querySelector('.customer-tooltip');
        if(customerTooltipEl) {
            customerTooltipEl.classList.remove('customer-tooltip');
            customerTooltipEl.removeAttribute('data-customer-tooltip');
        }
    }

    return {getInput: getInput};
});
