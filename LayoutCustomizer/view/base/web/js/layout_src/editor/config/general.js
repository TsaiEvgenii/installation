define(function() {
    return {
        smoothing: false,
        scaleMultiplier: 1.5,
        scale: 1.0,
        font: {
            family: 'sans-serif',
            size: 20
        },
        scaleOptions: [
            {name: '200%', value: 2.0},
            {name: '150%', value: 1.5},
            {name: '100%', value: 1.0},
            {name: '75%', value: 0.75},
            {name: '50%', value: 0.5},
            {name: '25%', value: 0.25}
        ],
        background: '#83839d',
        canvas: {
            width: '800',
            height: '600'
        },
        toolbar: ['scale', 'command-history'],
        commandHistoryLimit: 100
    };
});
