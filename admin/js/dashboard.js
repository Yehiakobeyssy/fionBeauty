$(document).ready(function() {
    var timeinterval = 999999; // default
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawAllCharts);

    
    loadDashboard();


    $('.stat-card').on('click', function () {
        const type = $(this).data('type');
        $('.stat-card').removeClass('active_card');
        $(this).addClass('active_card');
        showDetailChart(type);
    });

    $('.timeinterval .times span').on('click', function() {
        $('.timeinterval .times span').removeClass('Activetime');
        $(this).addClass('Activetime');

        timeinterval = $(this).data('time');
        $('#lblRangeTitle').text('Last '+timeinterval + ' Days')
        console.log("⏱ Selected Time Interval:", timeinterval);

        google.charts.setOnLoadCallback(drawAllCharts);
        loadDashboard(); 
    });

    function loadDashboard() {
        $.ajax({
            url: 'ajaxadmin/statsticdaschboard.php',
            type: 'POST',
            data: { days: timeinterval },
            dataType: 'json',
            success: function(response) {
                console.log("✅ Response received:", response);
                $('#lbltotalRevenue').text(response.totalRevenue ?? 0);
                $('#totalComtion').text(response.totalCommission ?? 0);
                $('#lbltotalcustomer').text(response.totalClients ?? 0);
                $('#lblallorders').text(response.totalOrders ?? 0);
                $('#lblProcessing').text(response.processingOrders ?? 0);
                $('#lblComplte').text(response.completeOrders ?? 0);
            },
            error: function(xhr, status, error) {
                console.error("❌ AJAX Error:", error);
                console.log("Response Text:", xhr.responseText);
            }
        });
    }



    function drawAllCharts() {
        drawChart();
        drawChart1();
        loadDashboardReport();
        showDetailChart('customers');
    }

    function drawChart() {
    console.log("📊 Loading chart for", timeinterval, "days");

    $.ajax({
        url: 'ajaxadmin/overvieworders.php',
        type: 'POST',
        dataType: 'json',
        data: { days: timeinterval },
        success: function (data) {
            console.log("✅ Chart data received:", data);

            // Check if valid array
            if (!Array.isArray(data) || data.length < 2) {
                $('#chart_div').html('<div style="text-align:center;padding:20px;">No data available</div>');
                return;
            }

            try {
                // Ensure numeric conversion (skip header row)
                for (let i = 1; i < data.length; i++) {
                    for (let j = 1; j < data[i].length; j++) {
                        data[i][j] = parseFloat(data[i][j]) || 0;
                    }
                }

                var dataTable = google.visualization.arrayToDataTable(data);

                var options = {
                    title: '💰 Revenue Overview',
                    curveType: 'function',
                    legend: { position: 'top', alignment: 'center' },
                    hAxis: { title: data[0][0] },
                    vAxis: { title: 'Revenue ($)' },
                    colors: ['#28a745']
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(dataTable, options);
            } catch (err) {
                console.error("⚠️ Chart drawing error:", err);
                $('#chart_div').html('<div style="text-align:center;padding:20px;">Invalid data format for chart</div>');
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ AJAX error:", error);
            console.log("Response:", xhr.responseText);
            $('#chart_div').html('<div style="text-align:center;padding:20px;">Error loading chart data</div>');
        }
    });
}


    function drawChart1() {
        console.log("📦 Loading top categories for", timeinterval, "days");

        $.ajax({
            url: 'ajaxadmin/CounttheCategories.php',
            type: 'POST',
            dataType: 'json',
            data: { days: timeinterval },
            success: function (data) {
                console.log("✅ Category data:", data);

                if (!Array.isArray(data) || data.length <= 1) {
                    $('#piechart_3d').html('<div style="text-align:center;padding:20px;">No category data</div>');
                    return;
                }

                var dataTable = google.visualization.arrayToDataTable(data);
                var options = {
                    title: '🛍️ Top Selling Categories',
                    pieHole: 0.4,
                    legend: { position: 'top', alignment: 'center' },
                    chartArea: { width: '90%', height: '80%' }
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                chart.draw(dataTable, options);
            },
            error: function (xhr, status, error) {
                console.error("❌ AJAX Error:", error);
                console.log("Response:", xhr.responseText);
            }
        });
    }

    function loadDashboardReport() {
        $.ajax({
            url: 'ajaxadmin/dashboardreport.php',
            type: 'POST',
            dataType: 'json',
            data: { days: timeinterval },
            success: function (res) {
                // --- Update summary cards ---
                $('#lblCustomers').text(res.summary.customers);
                $('#lblItems').text(res.summary.items);
                $('#lblStock').text(res.summary.inStock);
                $('#lblOutStock').text(res.summary.outStock);
                $('#lblRevenue').text((res.summary.revenue ?? 0).toFixed(2));

                // --- Draw line chart ---
                var data = google.visualization.arrayToDataTable(res.chart);
                var options = {
                    title: 'Revenue Trend',
                    curveType: 'function',
                    legend: { position: 'none' },
                    colors: ['#ff3b30'],
                    chartArea: { width: '90%', height: '75%' },
                    vAxis: { title: 'Revenue' },
                    hAxis: { title: res.chart[0][0] }
                };
                // var chart = new google.visualization.LineChart(document.getElementById('reportChart'));
                // chart.draw(data, options);
            },
            error: function (xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                console.log(xhr.responseText);
            }
        });
    }


    function showDetailChart(type) {
        $.ajax({
            url: 'ajaxadmin/dashboardreport.php',
            type: 'POST',
            dataType: 'json',
            data: { type: type, days: timeinterval },
            success: function(res) {

                if (!res.chart || res.chart.length <= 1) {
                    $('#detailChartContainer').html('<div style="text-align:center;padding:20px;">No data available</div>');
                    return;
                }

                $('#detailTitle').text(res.title ?? 'Detail Report');

                const data = google.visualization.arrayToDataTable(res.chart);
                const options = {
                    title: '',
                    curveType: 'function',
                    legend: { position: 'none' },
                    chartArea: { width: '90%', height: '75%' },
                    colors: ['#007bff'],
                    vAxis: { title: 'Value' },
                    hAxis: { title: res.chart[0][0] }
                };

                const chart = new google.visualization.LineChart(document.getElementById('detailChart'));
                chart.draw(data, options);
            },
            error: function(xhr,status,error){
                console.error('Detail AJAX Error:', error);
            }
        });
    }

    $('.order_card').click(function(){
        let orderID = $(this).data('index');
        alert(orderID)
    })

}); 