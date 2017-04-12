new Chartist.Bar('#current-econ-performance-bar', {
  labels: current_economic_data[0],
  series: [current_economic_data[1], current_economic_data[2]]
}, {
  plugins: [
        Chartist.plugins.legend({
            legendNames: ['Cost', 'Rate of Return'],
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#previous-econ-performance-line', {
  labels: rounds,
  series: all_economic_ror
}, {
    //fullWidth: true,
    chartPadding: {
        right: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#previous-econ-performance-line-1', {
    labels: rounds,
    series: all_economic_data,
  
}, {
    //fullWidth: true,
    chartPadding: {
        right: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#current-operational-performance-line', {
    labels: zones,
    series: current_operational_performance,
  
}, {
    //fullWidth: true,
    chartPadding: {
        right: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#previous-operational-performance-line', {
    labels: rounds,
    series: historical_operational_performance,
  
}, {
    //fullWidth: true,
    chartPadding: {
        right: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Bar('#current-environmental-performance-line', {
    labels: zones,
    series: current_environmental_performance,
  
}, {
    //fullWidth: true,
    chartPadding: {
        left: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#previous-environmental-performance-line', {
    labels: rounds,
    series: historical_environmental_performance,
  
}, {
    //fullWidth: true,
    chartPadding: {
        left: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#fuel-forecast', {
    labels: years,
    series: fuel_forecast,
  
}, {
    //fullWidth: true,
    chartPadding: {
        left: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#yearly-production-commit', {
    labels: ['04-2017', '03-2017', '02-2017', '01-2017', '12-2016', '11-2016', '10-2016', '09-2016', '08-2016', '07-2016', '06-2016', '05-2016', '04-2016'],
    series: monthly_commit_data,
  
}, {
    //fullWidth: true,
    chartPadding: {
        left: 40
    },
    plugins: [
        Chartist.plugins.legend({
            position: 'bottom'
        })
    ]
});
