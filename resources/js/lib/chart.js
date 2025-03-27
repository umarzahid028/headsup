import Chart from "chart.js/auto";

// Make Chart available globally
window.Chart = Chart;

// Function to initialize charts
function initializeCharts() {
    // Find all chart canvases with data attributes
    const chartElements = document.querySelectorAll("canvas[data-chart]");

    chartElements.forEach((canvas) => {
        try {
            const ctx = canvas.getContext("2d");
            const chartType = canvas.getAttribute("data-chart-type") || "line";
            const chartData = JSON.parse(
                canvas.getAttribute("data-chart-data") || "{}"
            );
            const chartOptions = JSON.parse(
                canvas.getAttribute("data-chart-options") || "{}"
            );

            new Chart(ctx, {
                type: chartType,
                data: chartData,
                options: chartOptions,
            });
        } catch (error) {
            console.error("Error initializing chart:", error);
        }
    });
}

// Run after DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    initializeCharts();
});

export default Chart;
