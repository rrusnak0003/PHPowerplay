new Chartist.Bar('#average-operational-performance', {
  labels: ["Commercial", "Industrial", "Residential"],
  series: average_operational_performance,
}, {
  distributeSeries: true,
  plugins: [
        Chartist.plugins.legend({
            legendNames:["Commercial", "Industrial", "Residential"],
            position: 'bottom'
        })
    ]
});

new Chartist.Bar('#average-economic-performance', {
  labels: average_economic_performance[0],
  series: [average_economic_performance[1],average_economic_performance[2]],
}, {
  plugins: [
        Chartist.plugins.legend({
            legendNames:['Cost', 'Rate of Return'],
            position: 'bottom'
        })
    ]
});

new Chartist.Bar('#average-environmental-performance', {
  labels: average_environmental_performance[0],
  series: [average_environmental_performance[1], 
           average_environmental_performance[2],
           average_environmental_performance[3],
           average_environmental_performance[4]
          ],
}, {
  chartPadding: {
        left: 40
    },
  plugins: [
        Chartist.plugins.legend({
            legendNames:['Carbon Dioxide', 'Nitrous Oxide', 'Carbon Monoxide', 'Sulfur Dioxide' ],
            position: 'bottom'
        })
    ]
});

new Chartist.Line('#fuel-forecast', {
    labels: years,
    series: fuel_forecast,
  
}, {
   // fullWidth: true,
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

jQuery(document).ready(function(){
  
  jQuery("#toggleReliability").click(function(){
    jQuery("#operational-performance-graph").toggle("slow");
  });
  jQuery("#toggleEconomics").click(function(){
    jQuery("#economic-performance-graph").toggle("slow");
  });
  jQuery("#toggleEnvironment").click(function(){
    jQuery("#environmental-performance-graph").toggle("slow");
  });
  jQuery("#toggleFuel").click(function(){
    jQuery("#fuel-forecast-graph").toggle("slow");
  });
  jQuery("#toggleProduction").click(function(){
    jQuery("#production-commit-graph").toggle("slow");
  });
  
});