const ctx = document.getElementById('grafico_vend').getContext('2d');
    const meuGrafico = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        datasets: [{
          label: 'Vendas',
          data: window.data_grafico,
          borderColor: 'rgba(39, 108, 219, 1)',
          backgroundColor: 'rgba(71, 110, 216, 0.23)',
          fill: true,
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
          title: {
            display: true,
            text: 'Vendas do ano'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
