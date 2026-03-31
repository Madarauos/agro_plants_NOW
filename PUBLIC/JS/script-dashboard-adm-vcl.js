const ctx = document.getElementById('grafico_adm').getContext('2d');
    const meuGrafico = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        datasets: [{
          label: 'Vendas',
          data: window.data_grafico,
          borderColor: 'rgba(39, 219, 54, 1)',
          backgroundColor: 'rgba(44, 171, 54, 0.23)',
          fill: true,
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
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