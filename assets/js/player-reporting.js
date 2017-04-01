new Chartist.Bar('#bar', {
  labels: current_economic_data[0],
  series: current_economic_data[2]
}, {
  distributeSeries: true
});