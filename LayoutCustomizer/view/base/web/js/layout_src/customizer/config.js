define(function() {
    return {
        smoothing: false,
        scale: 1.0,
        scaleMultiplier: 1.5,
        font: {
            family: 'sans-serif',
            size: 20,
            mobileSize: 28
        },
        mobileBreakpoint: 600,
        background: '#f3f8f8',
        canvas: {
            width: '800',
            height: '600'
        },
        toolbar: [],
        scaleOptions: [
            {name: '200%', value: 2.0},
            {name: '150%', value: 1.5},
            {name: '100%', value: 1.0},
            {name: '75%', value: 0.75},
            {name: '50%', value: 0.5},
            {name: '25%', value: 0.25}
        ]
    };
})
