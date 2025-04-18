@extends('layouts.app')
@section('title', 'OSave | Dashboard')

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hi {{ Auth::user()->name }}, {{ $greeting }}! </h3>
                        <p class="mb-0 mr-4">Your dashboard gives you views of key performance or business process.</p>
                    </div>

                </div>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div>
                                        <p class="mb-2">Total Purchase Orders</p>
                                        <h4 id="summaryTotalOrders">Loading...</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span id="progressOrders" class="bg-info iq-progress progress-1"
                                        style="width: 0%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div>
                                        <p class="mb-2">Total Products Sold</p>
                                        <h4 id="totalProducts">Loading...</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span id="progressProducts" class="bg-danger iq-progress progress-1"
                                        style="width: 0%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div>
                                        <p class="mb-2">Total Revenue</p>
                                        <h4 id="totalAmount">Loading...</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span id="progressAmount" class="bg-success iq-progress progress-1"
                                        style="width: 0%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-3">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div>
                                        <p class="mb-2">Average Revenue Per Order</p>
                                        <h4 id="averageRevenue">Loading...</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span id="progressAverageRevenue" class="bg-warning iq-progress progress-1"
                                        style="width: 0%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>



            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Total Sales</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlySalesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Daily Completed Orders</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="dailyOrdersChart"></canvas>
                    </div>
                </div>
            </div>



        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Fetch Total Purchase Orders
            fetch("https://logistics.pup-qc-retail.online/api/getTotalOrdersForFinance")
                .then(response => response.json())
                .then(data => {
                    if (data && data.data) {
                        let totalOrders = data.data.total_orders;
                        document.getElementById("summaryTotalOrders").textContent = totalOrders;

                        let maxOrders = 500;
                        let ordersPercent = Math.min((totalOrders / maxOrders) * 100, 100);
                        document.getElementById("progressOrders").style.width = ordersPercent + "%";
                    }
                })
                .catch(error => {
                    console.error("Error fetching summary total orders:", error);
                });

            // Fetch Total Orders Summary
            fetch("https://pos.pup-qc-retail.online/api/getTotalOrdersSummary")
                .then(response => response.json())
                .then(data => {
                    console.log("API Response:", data); // Debugging response

                    if (data && data.data) {
                        let totalProducts = data.data.total_products || 0;
                        let totalAmount = parseFloat(data.data.total_amount) || 0;
                        let averageRevenue = parseFloat(data.data.average_revenue_per_order) || 0;

                        console.log("Total Products:", totalProducts);
                        console.log("Total Amount:", totalAmount);
                        console.log("Average Revenue:", averageRevenue);

                        document.getElementById("totalProducts").textContent = totalProducts;
                        document.getElementById("totalAmount").textContent = "₱ " + totalAmount.toLocaleString(
                            "en-PH", {
                                minimumFractionDigits: 2
                            });
                        document.getElementById("averageRevenue").textContent = "₱ " + averageRevenue
                            .toLocaleString("en-PH", {
                                minimumFractionDigits: 2
                            });

                        let maxProducts = 5000;
                        let maxAmount = 100000;
                        let maxAverageRevenue = 5000;

                        let productsPercent = Math.min((totalProducts / maxProducts) * 100, 100);
                        let amountPercent = Math.min((totalAmount / maxAmount) * 100, 100);
                        let avgRevenuePercent = Math.min((averageRevenue / maxAverageRevenue) * 100, 100);

                        document.getElementById("progressProducts").style.width = productsPercent + "%";
                        document.getElementById("progressAmount").style.width = amountPercent + "%";
                        document.getElementById("progressAverageRevenue").style.width = avgRevenuePercent + "%";
                    } else {
                        console.error("Invalid response structure from API:", data);
                    }
                })
                .catch(error => {
                    console.error("Error fetching total orders summary:", error);
                });
        });
    </script>




    <script>
        const predefinedColors = [
            'rgba(255, 99, 132, 0.7)', // Red
            'rgba(54, 162, 235, 0.7)', // Blue
            'rgba(255, 206, 86, 0.7)', // Yellow
            'rgba(75, 192, 192, 0.7)', // Teal
            'rgba(153, 102, 255, 0.7)', // Purple
            'rgba(255, 159, 64, 0.7)', // Orange
            'rgba(46, 204, 113, 0.7)', // Green
            'rgba(231, 76, 60, 0.7)', // Dark Red
            'rgba(52, 152, 219, 0.7)', // Light Blue
            'rgba(155, 89, 182, 0.7)' // Dark Purple
        ];

        const getConsistentColors = (count) => {
            return Array.from({
                length: count
            }, (_, i) => predefinedColors[i % predefinedColors.length]);
        };

        fetch('https://pos.pup-qc-retail.online/api/dailyCompletedOrders')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('dailyOrdersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Daily Completed Orders',
                            data: data.data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Orders Count'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Days'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));

        fetch('https://pos.pup-qc-retail.online/api/getMonthlySalesOrders')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('monthlySalesChart').getContext('2d');
                const colors = ['#36A2EB', '#FF6384'];

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                                label: 'Total Orders',
                                data: data.total_orders,
                                borderColor: colors[0],
                                backgroundColor: colors[0],
                                borderWidth: 2,
                                fill: false
                            },
                            {
                                label: 'Total Revenue (PHP)',
                                data: data.total_revenue,
                                borderColor: colors[1],
                                backgroundColor: colors[1],
                                borderWidth: 2,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Count / Revenue (PHP)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Year - Month'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    </script>

@endsection
