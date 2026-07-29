/* Template Name: Techwind - Tailwind CSS Multipurpose Landing & Admin Dashboard Template
   Author: Shreethemes
   Email: support@shreethemes.in
   Website: https://shreethemes.in
   Version: 3.2.0
   Created: May 2022
   File Description: For Apex Chart
*/

if (document.querySelector("#mainchart")) {
try {
    var options1 = {
        series: [{
            name: 'Profit',
            data: [500, 653, 548, 482, 553, 570, 560, 610, 580, 854, 945, 1150],
        }, {
            name: 'Expenses',
            data: [246, 379, 521, 453, 243, 264, 333, 246, 468, 222, 456, 789]
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false }
        },
        grid: { strokeDashArray: 5 },
        plotOptions: {
            bar: {
                borderRadius: 5,
                columnWidth: '40%',
                endingShape: 'rounded'
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 2, colors: ['transparent'] },
        colors: ['#4f46e5', '#10b981'],
        xaxis: {
            categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
        },
        yaxis: {
            title: {
                text: 'Profit / Expenses (USD)',
                style: {
                    colors: ['#8492a6'],
                    fontSize: '16px',
                    fontFamily: 'Nunito, sans-serif',
                    fontWeight: 600
                }
            }
        },
        fill: { opacity: 1 },
        tooltip: {
            y: { formatter: val => "$" + val }
        }
    };

    new ApexCharts(document.querySelector("#mainchart"), options1).render();
} catch (e) {}
}

//E-Commerce Dashboard
if (document.querySelector("#ecommerce-chart")) {
try {
    var options = {
        chart: {
            height: 314,
            type: 'area',
            stacked: true,
            toolbar: { show: false }
        },
        colors: ['#4f46e5', '#16a34a'],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: [1, 1],
            dashArray: [3, 3]
        },
        series: [
            { name: 'Item Sales', data: [0,100,40,110,60,140,55,130,65,180,75,115] },
            { name: 'Revenue', data: [0,45,10,75,35,94,40,115,30,105,65,110] }
        ],
        xaxis: {
            categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 0.8,
                opacityFrom: 0.5,
                opacityTo: 0.3,
                stops: [0, 80, 100]
            }
        },
        legend: { position: 'bottom' }
    };

    new ApexCharts(document.querySelector("#ecommerce-chart"), options).render();
} catch (e) {}
}

if (document.querySelector("#top-product-chart")) {
try {
    var options = {
        chart: { type: 'donut', height: 320 },
        series: [45, 21, 23, 28],
        labels: ["Item 1", "Item 2", "Item 3", "Item 4"],
        legend: { position: 'bottom', offsetY: 0, },
        dataLabels: {
            enabled: true,
            dropShadow: {
                enabled: false,
            }
        },
        stroke: {
            show: true,
            colors: ['transparent'],
        },
        theme: {
            monochrome: { enabled: true, color: '#4f46e5' }
        }
    };

    new ApexCharts(document.querySelector("#top-product-chart"), options).render();
} catch (e) {}
}

//CryptoCurrency

/* ===== SMALL CHARTS ===== */
function initSmallChart(id, data, color) {
    if (!document.querySelector(id)) return;

    try {
        new ApexCharts(document.querySelector(id), {
            series: [{ data }],
            chart: {
                type: 'area',
                height: 90,
                sparkline: { enabled: true }
            },
            stroke: { curve: 'smooth', width: 3 },
            colors: [color],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                },
            },
            tooltip: {
                fixed: { enabled: false },
                x: { show: false },
                y: { title: { formatter: () => '' } },
                marker: { show: false }
            }
        }).render();
    } catch (e) {}
}

initSmallChart("#chart-1", [20,45,40,64,35,25,35], "#059669");
initSmallChart("#chart-2", [10,25,30,54,45,39,15], "#dc2626");
initSmallChart("#chart-3", [15,20,10,45,20,10,5], "#059669");
initSmallChart("#chart-4", [3,5,7,11,8,5,7], "#dc2626");
initSmallChart("#chart-5", [20,14,24,30,16,12,8], "#dc2626");
initSmallChart("#chart-6", [4,7,15,10,8,12,18], "#059669");