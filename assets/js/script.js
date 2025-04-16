document.addEventListener("DOMContentLoaded", () => {
    fetchLiveData();
    loadWatchlist();
});

/**
 * Fetch Live Stock Prices
 */
async function fetchLiveData() {
    try {
        const response = await fetch('../api/live_data.php');
        const data = await response.json();

        // Update Most Traded Stocks
        document.getElementById("apple-price").textContent = `₹${data.stocks.aapl}`;
        document.getElementById("btc-price").textContent = `₹${data.crypto.btc}`;
        document.getElementById("gold-price").textContent = `₹${data.commodities.gold}`;
        document.getElementById("msft-price").textContent = `₹${data.stocks.msft}`;
    } catch (error) {
        console.error("Error fetching live data:", error);
    }
}

/**
 * Load Watchlist from Local Storage
 */
function loadWatchlist() {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    const watchlistContainer = document.getElementById("watchlist");
    watchlistContainer.innerHTML = "";

    watchlist.forEach(stock => {
        const stockItem = document.createElement("div");
        stockItem.classList.add("watchlist-item");
        stockItem.innerHTML = `
            ${stock.name} 
            <span class="price">₹${stock.price}</span>
            <button class="remove-btn" onclick="removeFromWatchlist('${stock.name}')">-</button>
        `;
        watchlistContainer.appendChild(stockItem);
    });
}

/**
 * Add Stock to Watchlist
 */
function addToWatchlist(name, price) {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    
    // Prevent duplicates
    if (watchlist.some(stock => stock.name === name)) {
        alert("Stock is already in your watchlist!");
        return;
    }

    watchlist.push({ name, price });
    localStorage.setItem("watchlist", JSON.stringify(watchlist));
    loadWatchlist();
}

/**
 * Remove Stock from Watchlist
 */
function removeFromWatchlist(name) {
    let watchlist = JSON.parse(localStorage.getItem("watchlist")) || [];
    watchlist = watchlist.filter(stock => stock.name !== name);
    localStorage.setItem("watchlist", JSON.stringify(watchlist));
    loadWatchlist();
}
