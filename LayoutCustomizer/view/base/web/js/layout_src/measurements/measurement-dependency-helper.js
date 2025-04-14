define(function() {

    function checkMeasurementDependency(obj, om, value, dimension, measurement) {
        if(obj.hasMeasurementDependency()){
            let breakPoint = obj.getMeasurementDependencyBreakPoint(),
                otherDimension = dimension === 'width' ? 'height' : 'width';
            if(value < breakPoint) {
                let otherMeasurement = obj.getBlocksMeasurementByDimension(otherDimension, om);
                let otherValue = otherMeasurement._measurement.getValue();
                if(otherValue < breakPoint) {
                    if(otherMeasurement._measurement._input._errorCode === 0) {
                        measurement._currentMin = breakPoint;
                        measurement._errorCode = 1;
                        measurement._tooltip._updateMinMax();
                        measurement._processError(measurement._errorCode);
                    }
                } else {
                    //need to check if measurement (block) have other children to show its tooltip anyway
                    if(measurement._measurement.parent.parent.children.length < 2) {
                        measurement._currentMin = measurement._measurement.min;
                        measurement._errorCode = 0;
                        measurement._tooltip._updateMinMax();
                        measurement._processError(measurement._errorCode);
                    }
                }
            } else {
                let otherMeasurement = obj.getBlocksMeasurementByDimension(otherDimension, om);
                if(otherMeasurement._measurement._input) {
                    let otherMeasurementObj = otherMeasurement._measurement;
                    if(!otherMeasurementObj._input._context.linkManager.hasObjectId(obj.objectId) && otherMeasurementObj._input._errorCode !== 0) {
                        otherMeasurementObj._input._currentMin = otherMeasurementObj.min;
                        let errorCode = otherMeasurementObj._input._errorCode = 0;
                        otherMeasurementObj._input._processError(errorCode);
                    }
                }
            }
        }
        obj.getNonMeasurementChildrenByDimension(dimension).forEach(child => {
            checkMeasurementDependency(child, om, value, dimension, measurement);
        });
        if(measurement._context && measurement._context.linkManager.hasObjectId(obj.objectId)) {
            let linkedObjs = measurement.getLinkedObjects(dimension, obj.objectId);
            if(linkedObjs.length > 0) {
                linkedObjs.forEach(obj => {
                    checkMeasurementDependency(obj, om, value, dimension, measurement);
                });
            }
        }
    }

    return {
        checkMeasurementDependency: checkMeasurementDependency
    };
})
