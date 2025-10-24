$(document).ready(function() {
    applyFilterManageCat()
    // Main filter function
function applyFilterManageCat(page = 1) {
    let duration = $("#duration").val();
    let search = $("#search").val().trim();
    let date = $("#date").val();

    $.ajax({
        url: 'ajaxadmin/manageviewCategory.php',
        type: 'POST',
        dataType: 'json',
        data: { action: 'filterCategories', duration, search, date, page },
        success: function(data) {
            let tbody = $("#viewManageCategory");
            tbody.empty(); 

            if(data.error) {
                tbody.append('<tr><td colspan="5">Error: '+data.error+'</td></tr>');
                return;
            } 

            if(data.categories.length === 0) {
                tbody.append('<tr><td colspan="5">No categories found.</td></tr>');
                $("#paginationContainer").empty();
                return;
            }

            data.categories.forEach(cat => {
                let dateObj = new Date(cat.catInputDate);
                let formattedDate = dateObj.toLocaleDateString('en-GB', { day:'numeric', month:'short', year:'numeric' });
                let categoryTd = `
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <img src="../images/items/${cat.carImg || 'no-image.png'}" alt="${cat.catName}" style="width:50px;height:50px;object-fit:cover;border-radius:5px;">
                            <div>
                                <strong>${cat.catName}</strong><br>
                                <small>${cat.catDescription}</small>
                            </div>
                        </div>
                    </td>
                `;
                let row = `
                    <tr>
                        ${categoryTd}
                        <td>${cat.totalOrders}</td>
                        <td>${cat.totalItems}</td>
                        <td>
                        ${cat.shippingfree_accepted == 1 
                            ? '<span style="color:green;">&#10003;</span>' 
                            : '<span style="color:red;">&#10007;</span>'}
                        </td>
                        <td>${formattedDate}</td>
                        <td>
                            <button class="view-btn" data-id="${cat.categoryId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="12" viewBox="0 0 18 12" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M8.99996 0.166992C14.1083 0.166992 16.5256 3.59196 17.3765 5.19314C17.6475 5.70299 17.6475 6.29766 17.3765 6.80751C16.5256 8.40869 14.1083 11.8337 8.99996 11.8337C3.89164 11.8337 1.47436 8.40869 0.623389 6.80751C0.352425 6.29766 0.352425 5.70299 0.62339 5.19314C1.47436 3.59196 3.89164 0.166992 8.99996 0.166992ZM4.69692 3.06507C3.31336 3.98178 2.50548 5.20318 2.09512 5.97531C2.09054 5.98393 2.08865 5.98991 2.08783 5.9932C2.08699 5.99654 2.08683 6.00033 2.08683 6.00033C2.08683 6.00033 2.08699 6.00411 2.08783 6.00745C2.08865 6.01074 2.09054 6.01672 2.09512 6.02534C2.50548 6.79747 3.31336 8.01887 4.69692 8.93558C4.1257 8.09979 3.79163 7.08907 3.79163 6.00033C3.79163 4.91158 4.1257 3.90086 4.69692 3.06507ZM13.303 8.93557C14.6866 8.01887 15.4944 6.79747 15.9048 6.02534C15.9094 6.01672 15.9113 6.01074 15.9121 6.00745C15.9126 6.00529 15.913 6.00223 15.913 6.00223L15.9131 6.00033L15.9128 5.99666L15.9121 5.9932C15.9113 5.98991 15.9094 5.98393 15.9048 5.97531C15.4944 5.20318 14.6866 3.98179 13.303 3.06508C13.8742 3.90086 14.2083 4.91158 14.2083 6.00033C14.2083 7.08907 13.8742 8.09979 13.303 8.93557ZM5.4583 6.00033C5.4583 4.04432 7.04396 2.45866 8.99997 2.45866C10.956 2.45866 12.5416 4.04432 12.5416 6.00033C12.5416 7.95633 10.956 9.54199 8.99997 9.54199C7.04396 9.54199 5.4583 7.95633 5.4583 6.00033Z" fill="#667085"/>
                                </svg>
                            </button>
                            <button class="edit-btn" data-id="${cat.categoryId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M17.3047 6.81991C18.281 5.8436 18.281 4.26069 17.3047 3.28438L16.7155 2.69512C15.7391 1.71881 14.1562 1.71881 13.1799 2.69512L3.69097 12.1841C3.34624 12.5288 3.10982 12.9668 3.01082 13.4442L2.34111 16.6735C2.21932 17.2607 2.73906 17.7805 3.32629 17.6587L6.55565 16.989C7.03302 16.89 7.47103 16.6536 7.81577 16.3089L17.3047 6.81991ZM16.1262 4.46289L15.5369 3.87363C15.2115 3.5482 14.6839 3.5482 14.3584 3.87363L13.4745 4.75755L15.2423 6.52531L16.1262 5.6414C16.4516 5.31596 16.4516 4.78833 16.1262 4.46289ZM14.0638 7.70382L12.296 5.93606L4.86948 13.3626C4.75457 13.4775 4.67577 13.6235 4.64277 13.7826L4.23082 15.769L6.21721 15.3571C6.37634 15.3241 6.52234 15.2453 6.63726 15.1303L14.0638 7.70382Z" fill="#FFAD33"/>
                                </svg>
                            </button>
                            <button class="delete-btn" data-id="${cat.categoryId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M16.6667 4.64286H4.44449L5.5556 17.5H14.4445L15.5556 4.64286H3.33337M10 7.85714V14.2857M12.7778 7.85714L12.2223 14.2857M7.22226 7.85714L7.77782 14.2857M7.77782 4.64286L8.33337 2.5H11.6667L12.2223 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });

            // Pagination
            let pagination = $("#paginationContainer");
            pagination.empty();

            let totalPages = Math.ceil(data.total / data.limit);
            let start = ((data.page - 1) * data.limit) + 1;
            let end = Math.min(data.page * data.limit, data.total);

            // Create container divs
            let leftInfo = $(`<div class="pagination-info">Showing ${start}-${end} of ${data.total}</div>`);
            let rightButtons = $('<div class="pagination-buttons"></div>');

            // Create buttons inside right div
            for (let i = 1; i <= totalPages; i++) {
                let btn = $(`<button class="page-btn">${i}</button>`);
                if (i === data.page) btn.attr('disabled', true).addClass('active');
                btn.on('click', function() { applyFilterManageCat(i); });
                rightButtons.append(btn);
            }

            // Append both sides to the container
            pagination.append(leftInfo).append(rightButtons);
        },
        error: function(xhr, status, error) {
            $("#viewManageCategory").html('<tr><td colspan="5">AJAX Error</td></tr>');
            console.error(error);
        }
    });
}


    // Duration button click
    $(".filter_manage_category .duration-btn").on("click", function() {
        $(".filter_manage_category .duration-btn").removeClass("active");
        $(this).addClass("active");
        $("#duration").val($(this).data("value"));
        applyFilterManageCat();
    });

    // Search typing delay
    let typingTimer;
    $("#search").on("keyup", function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(applyFilterManageCat, 400);
    });

    // Date change
    $("#date").on("change", function() {
        applyFilterManageCat();
    });

    $(document).on('click','.view-btn',function(){
        let catId = $(this).data('id');
        location.href= 'manageproducts.php?do=viewcat&catid='+catId;
    })
    
    $(document).on('click','.edit-btn',function(){
        let catId = $(this).data('id');
        location.href= 'manageproducts.php?do=editcat&catid='+catId;
    })

    $(document).on('click','.delete-btn',function(){
        let catId = $(this).data('id');
        location.href= 'manageproducts.php?do=deletecat&catid='+catId;
    })
});
