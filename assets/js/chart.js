function initPortfolioChart() {
    const ctx = document.getElementById('portfolioChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Stocks', 'Crypto', 'Gold'],
            datasets: [{
                data: [portfolioData * 0.5, portfolioData * 0.3, portfolioData * 0.2],
                backgroundColor: ['blue', 'orange', 'gold']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}
