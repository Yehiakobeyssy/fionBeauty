$(document).ready(function () {
    /* ======================================================
       🧩 CLIENTS TABLE
    ====================================================== */
    const perPageClients = 10;
    let allClients = [];
    let currentClientPage = 1;

    // Load clients on page start & when filters change
    loadClients();
    $('#search, #status, #block, #date').on('change keyup', function () {
        loadClients();
    });

    function loadClients() {
        const search = $('#search').val();
        const status = $('#status').val();
        const block = $('#block').val();
        const date = $('#date').val();

        $.ajax({
            url: 'ajaxadmin/allclients.php',
            method: 'GET',
            dataType: 'json',
            data: { search, status, block, date },
            success: function (response) {
                if (response.success) {
                    allClients = response.data;
                    currentClientPage = 1;
                    renderClientsTable();
                } else {
                    $('#tblresult').html(`<tr><td colspan="9" class="text-center text-danger">No clients found</td></tr>`);
                }
            },
            error: function (err) {
                console.error('AJAX Error:', err);
            }
        });
    }

    function renderClientsTable() {
        const start = (currentClientPage - 1) * perPageClients;
        const end = start + perPageClients;
        const pageData = allClients.slice(start, end);

        let rows = '';

        if (pageData.length === 0) {
            rows = `<tr><td colspan="9" class="text-center text-muted">No clients found</td></tr>`;
        } else {
            pageData.forEach(client => {
                const certIcon = client.certificate === "Have"
                    ? `<span style="color:green;">&#10004;</span>`
                    : `<span style="color:red;">&#10008;</span>`;

                const statusClass = client.status === "Active" ? "alert-success" : "alert-warning";
                const blockClass = client.block === "Blocked" ? "alert-danger" : "";

                rows += `
                    <tr>
                        <td>${client.fullName}</td>
                        <td>${client.phone}</td>
                        <td>${client.email}</td>
                        <td>${certIcon}</td>
                        <td>${client.orders}</td>
                        <td>$${client.balance}</td>
                        <td><span class="${statusClass}">${client.status}</span></td>
                        <td><span class="${blockClass}">${client.block}</span></td>
                        <td>
                            <button class="btnview" data-id="${client.clientID}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 4.16699C15.1085 4.16699 17.5258 7.59196 18.3768 9.19314C18.6477 9.70299 18.6477 10.2977 18.3768 10.8075C17.5258 12.4087 15.1085 15.8337 10.0002 15.8337C4.89188 15.8337 2.4746 12.4087 1.62363 10.8075C1.35267 10.2977 1.35267 9.70299 1.62363 9.19314C2.4746 7.59196 4.89188 4.16699 10.0002 4.16699ZM5.69716 7.06507C4.31361 7.98178 3.50572 9.20318 3.09536 9.97531C3.09078 9.98393 3.08889 9.98991 3.08807 9.9932C3.08724 9.99654 3.08708 10.0003 3.08708 10.0003C3.08708 10.0003 3.08724 10.0041 3.08807 10.0075C3.08889 10.0107 3.09078 10.0167 3.09536 10.0253C3.50572 10.7975 4.31361 12.0189 5.69716 12.9356C5.12594 12.0998 4.79188 11.0891 4.79188 10.0003C4.79188 8.91158 5.12594 7.90086 5.69716 7.06507ZM14.3033 12.9356C15.6868 12.0189 16.4947 10.7975 16.905 10.0253C16.9096 10.0167 16.9115 10.0107 16.9123 10.0075C16.9129 10.0053 16.9133 10.0022 16.9133 10.0022L16.9133 10.0003L16.913 9.99666L16.9123 9.9932C16.9115 9.98991 16.9096 9.98393 16.905 9.97531C16.4947 9.20318 15.6868 7.98179 14.3033 7.06508C14.8745 7.90086 15.2085 8.91158 15.2085 10.0003C15.2085 11.0891 14.8745 12.0998 14.3033 12.9356ZM6.45854 10.0003C6.45854 8.04432 8.0442 6.45866 10.0002 6.45866C11.9562 6.45866 13.5419 8.04432 13.5419 10.0003C13.5419 11.9563 11.9562 13.542 10.0002 13.542C8.0442 13.542 6.45854 11.9563 6.45854 10.0003Z" fill="#667085"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#tblresult').html(rows);
        renderClientsPagination();
    }

    function renderClientsPagination() {
        const totalRecords = allClients.length;
        const totalPages = Math.ceil(totalRecords / perPageClients);
        const startRecord = (currentClientPage - 1) * perPageClients + 1;
        const endRecord = Math.min(currentClientPage * perPageClients, totalRecords);

        let infoHtml = `<div class="showing">Showing ${startRecord}-${endRecord} from ${totalRecords}</div>`;
        let paginationHtml = '<div class="pagination">';

        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentClientPage ? 'active' : '';
            paginationHtml += `<button class="page-btn-client ${activeClass}" data-page="${i}">${i}</button>`;
        }
        paginationHtml += '</div>';

        $('.tblClients').next('.pagination-container').remove();
        $('.tblClients').after(`<div class="pagination-container">${infoHtml}${paginationHtml}</div>`);

        $('.page-btn-client').on('click', function () {
            currentClientPage = parseInt($(this).data('page'));
            renderClientsTable();
        });
    }

    $(document).on('click', '.btnview', function () {
        const clientID = $(this).data('id');
        window.location.href = `manageClients.php?do=view&clientID=${clientID}`;
    });

    /* ======================================================
       🧩 EMAIL CHECKER
    ====================================================== */
    $("#txtemail").on("blur keyup", function () {
        const email = $(this).val().trim();
        const $error = $("#erroremail");

        $error.text("");
        if (email === "") return;

        $.ajax({
            url: "../ajax/check_email.php",
            method: "POST",
            data: { email },
            dataType: "json",
            success: function (response) {
                if (response.exists) {
                    $error.text("User exists, please change the email.").css({ color: "red", fontWeight: "bold" });
                } else if (response.error) {
                    $error.text(response.error).css({ color: "orange", fontWeight: "bold" });
                } else {
                    $error.text("Email is available.").css({ color: "green", fontWeight: "bold" });
                }
            },
            error: function () {
                $error.text("Error checking email.").css("color", "red");
            }
        });
    });

    /* ======================================================
       🧾 ORDERS TABLE
    ====================================================== */
    const perPageOrders = 5;
    let allOrders = [];
    let currentOrderPage = 1;
    const clientID = $("#clientID").val();

    if (clientID) loadOrders();

    function loadOrders() {
        $.ajax({
            url: 'ajaxadmin/userorders.php',
            method: 'GET',
            dataType: 'json',
            data: { clientID },
            success: function (response) {
                if (response.success) {
                    allOrders = response.data;
                    currentOrderPage = 1;
                    renderOrdersTable();
                } else {
                    $('#tblordershistory').html(`<tr><td colspan="8" class="text-center text-danger">No orders found.</td></tr>`);
                }
            },
            error: function (err) {
                console.error('AJAX Error:', err);
            }
        });
    }

    function renderOrdersTable() {
        const start = (currentOrderPage - 1) * perPageOrders;
        const end = start + perPageOrders;
        const pageData = allOrders.slice(start, end);

        let html = '';
        if (pageData.length === 0) {
            html = `<tr><td colspan="8" class="text-center text-muted">No orders found.</td></tr>`;
        } else {
            pageData.forEach(order => {
                let statusClass = 'alert alert-secondary p-1 m-0';
                switch (order.statusName) {
                    case 'Order received': statusClass = 'alert alert-info p-1 m-0'; break;
                    case 'Processing': statusClass = 'alert alert-light p-1 m-0'; break;
                    case 'On the way': statusClass = 'alert alert-primary p-1 m-0'; break;
                    case 'Delivered': statusClass = 'alert alert-success p-1 m-0'; break;
                    case 'Cancelled': statusClass = 'alert alert-danger p-1 m-0'; break;
                    case 'Full Refund': statusClass = 'alert alert-warning p-1 m-0'; break;
                }

                const productInfo = order.firstItemName
                    ? `<div class="product-info">
                            <img src="../images/items/${order.firstItemPic}" alt="" class="product-img">
                            <div class="product-text">
                                <div class="product-name">${order.firstItemName}</div>
                                <div class="product-count">+${order.totalItems - 1} more items</div>
                            </div>
                        </div>`
                    : 'No products';

                html += `
                    <tr class="roworder" data-index="${order.invoiceID}">
                        <td style="color:var(--color-danger)">${order.invoiceCode}</td>
                        <td class="smallinfo">${formatTime(order.invoiceDate)}</td>
                        <td>${productInfo}</td>
                        <td class="smallinfo">$${parseFloat(order.invoiceAmount).toFixed(2)}</td>
                        <td class="smallinfo">$${parseFloat(order.invoiceCommition || 0).toFixed(2)}</td>
                        <td class="smallinfo">${order.transactionNO || '-'}</td>
                        <td><div class="${statusClass}" style="font-size:12px;">${order.statusName}</div></td>
                        <td><button class="btnvieworder" data-id="${order.invoiceID}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 4.16699C15.1085 4.16699 17.5258 7.59196 18.3768 9.19314C18.6477 9.70299 18.6477 10.2977 18.3768 10.8075C17.5258 12.4087 15.1085 15.8337 10.0002 15.8337C4.89188 15.8337 2.4746 12.4087 1.62363 10.8075C1.35267 10.2977 1.35267 9.70299 1.62363 9.19314C2.4746 7.59196 4.89188 4.16699 10.0002 4.16699ZM5.69716 7.06507C4.31361 7.98178 3.50572 9.20318 3.09536 9.97531C3.09078 9.98393 3.08889 9.98991 3.08807 9.9932C3.08724 9.99654 3.08708 10.0003 3.08708 10.0003C3.08708 10.0003 3.08724 10.0041 3.08807 10.0075C3.08889 10.0107 3.09078 10.0167 3.09536 10.0253C3.50572 10.7975 4.31361 12.0189 5.69716 12.9356C5.12594 12.0998 4.79188 11.0891 4.79188 10.0003C4.79188 8.91158 5.12594 7.90086 5.69716 7.06507ZM14.3033 12.9356C15.6868 12.0189 16.4947 10.7975 16.905 10.0253C16.9096 10.0167 16.9115 10.0107 16.9123 10.0075C16.9129 10.0053 16.9133 10.0022 16.9133 10.0022L16.9133 10.0003L16.913 9.99666L16.9123 9.9932C16.9115 9.98991 16.9096 9.98393 16.905 9.97531C16.4947 9.20318 15.6868 7.98179 14.3033 7.06508C14.8745 7.90086 15.2085 8.91158 15.2085 10.0003C15.2085 11.0891 14.8745 12.0998 14.3033 12.9356ZM6.45854 10.0003C6.45854 8.04432 8.0442 6.45866 10.0002 6.45866C11.9562 6.45866 13.5419 8.04432 13.5419 10.0003C13.5419 11.9563 11.9562 13.542 10.0002 13.542C8.0442 13.542 6.45854 11.9563 6.45854 10.0003Z" fill="#667085"/>
                            </svg>
                        </button></td>
                    </tr>
                `;
            });
        }

        $('#tblordershistory').html(html);
        renderOrdersPagination();
    }

    function renderOrdersPagination() {
        const totalRecords = allOrders.length;
        const totalPages = Math.ceil(totalRecords / perPageOrders);
        const startRecord = (currentOrderPage - 1) * perPageOrders + 1;
        const endRecord = Math.min(currentOrderPage * perPageOrders, totalRecords);

        let infoHtml = `<div class="showing">Showing ${startRecord}-${endRecord} of ${totalRecords}</div>`;
        let paginationHtml = '<div class="pagination">';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentOrderPage ? 'active' : '';
            paginationHtml += `<button class="page-btn-order ${activeClass}" data-page="${i}">${i}</button>`;
        }
        paginationHtml += '</div>';

        $('.tblhistory').next('.pagination-container').remove();
        $('.tblhistory').after(`<div class="pagination-container">${infoHtml}${paginationHtml}</div>`);

        $('.page-btn-order').on('click', function () {
            currentOrderPage = parseInt($(this).data('page'));
            renderOrdersTable();
        });
    }

    $(document).on('click', '.btnvieworder', function () {
        const invoiceID = $(this).data('id');
        window.location.href = `manageOrders.php?do=order&orderID=${invoiceID}`;
    });

    function formatTime(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }



    $('.btngotoactive').click(function(){
        let userID = $(this).val();
        location.href= "manageClients.php?do=active&clientID="+userID
    });

    $('.btngotoblock').click(function(){
        let userID = $(this).val();
        location.href= "manageClients.php?do=block&clientID="+userID
    });
});
