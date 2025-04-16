async function getStockPrice(symbol) {
    let response = await fetch(`https://www.alphavantage.co/query?function=GLOBAL_QUOTE&symbol=${symbol}&apikey=YOUR_API_KEY`);
    let data = await response.json();
    console.log("Stock Price: ", data["Global Quote"]["05. price"]);
}
getStockPrice("AAPL");
