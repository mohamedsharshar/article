document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('articlesChart')) {
    const ctx = document.getElementById('articlesChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو'],
        datasets: [{
          label: 'عدد المقالات',
          data: [12, 19, 3, 5, 2], 
          backgroundColor: '#3a86ff'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });
  }
});
