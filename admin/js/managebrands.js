$(document).ready(function () {
    let currentPage = 1;
    let perPage = 1;

    // Initial Load
    fetchBrands();
 
    // Event Listeners
    $('#search, #status, #date').on('input change', function () {
        currentPage = 1;
        fetchBrands();
    }); 

    // Fetch Data
    function fetchBrands() {
        $.ajax({
            url: 'ajaxadmin/fetchbrand.php',
            type: 'POST',
            dataType: 'json',
            data: {
                search: $('#search').val(),
                status: $('#status').val(),
                date: $('#date').val(),
                page: currentPage
            },
            success: function (response) {
                renderBrandTable(response.brands);
                renderBrandPagination(response.total, response.limit);
            }
        });
    }

    // Render Table
    function renderBrandTable(brands) {
        let html = '';
        if (brands.length > 0) {
            $.each(brands, function (i, brand) {
                // Format Date (e.g., "2025-09-25" → "25 Sept 2025")
                const dateObj = new Date(brand.brandInputDate);
                const options = { day: '2-digit', month: 'short', year: 'numeric' };
                const formattedDate = dateObj.toLocaleDateString('en-GB', options);

                // Status style
                let statusLabel = brand.brandActive == 1
                    ? `<div class="alert alert-success" style="padding:4px 8px; margin:0;">Active</div>`
                    : `<div class="alert alert-danger" style="padding:4px 8px; margin:0;">Inactive</div>`;

                html += `
                    <tr>
                        <td><img src="../images/brands/${brand.brandIcon}" alt="logo" style="width:40px;height:40px;border-radius:50%;"></td>
                        <td>${brand.brandName}</td>
                        <td>${formattedDate}</td>
                        <td>${statusLabel}</td>
                        <td>
                            <button class="btnview" data-id="${brand.brandId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 4.16699C15.1085 4.16699 17.5258 7.59196 18.3768 9.19314C18.6477 9.70299 18.6477 10.2977 18.3768 10.8075C17.5258 12.4087 15.1085 15.8337 10.0002 15.8337C4.89188 15.8337 2.4746 12.4087 1.62363 10.8075C1.35267 10.2977 1.35267 9.70299 1.62363 9.19314C2.4746 7.59196 4.89188 4.16699 10.0002 4.16699ZM5.69716 7.06507C4.31361 7.98178 3.50572 9.20318 3.09536 9.97531C3.09078 9.98393 3.08889 9.98991 3.08807 9.9932C3.08724 9.99654 3.08708 10.0003 3.08708 10.0003C3.08708 10.0003 3.08724 10.0041 3.08807 10.0075C3.08889 10.0107 3.09078 10.0167 3.09536 10.0253C3.50572 10.7975 4.31361 12.0189 5.69716 12.9356C5.12594 12.0998 4.79188 11.0891 4.79188 10.0003C4.79188 8.91158 5.12594 7.90086 5.69716 7.06507ZM14.3033 12.9356C15.6868 12.0189 16.4947 10.7975 16.905 10.0253C16.9096 10.0167 16.9115 10.0107 16.9123 10.0075C16.9129 10.0053 16.9133 10.0022 16.9133 10.0022L16.9133 10.0003L16.913 9.99666L16.9123 9.9932C16.9115 9.98991 16.9096 9.98393 16.905 9.97531C16.4947 9.20318 15.6868 7.98179 14.3033 7.06508C14.8745 7.90086 15.2085 8.91158 15.2085 10.0003C15.2085 11.0891 14.8745 12.0998 14.3033 12.9356ZM6.45854 10.0003C6.45854 8.04432 8.0442 6.45866 10.0002 6.45866C11.9562 6.45866 13.5419 8.04432 13.5419 10.0003C13.5419 11.9563 11.9562 13.542 10.0002 13.542C8.0442 13.542 6.45854 11.9563 6.45854 10.0003Z" fill="#667085"/>
                                </svg>
                            </button>
                            <button class="btnedit" data-id="${brand.brandId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.3047 5.81991C16.281 4.8436 16.281 3.26069 15.3047 2.28438L14.7155 1.69512C13.7391 0.718813 12.1562 0.718813 11.1799 1.69512L1.69097 11.1841C1.34624 11.5288 1.10982 11.9668 1.01082 12.4442L0.341106 15.6735C0.219324 16.2607 0.739063 16.7805 1.32629 16.6587L4.55565 15.989C5.03302 15.89 5.47103 15.6536 5.81577 15.3089L15.3047 5.81991ZM14.1262 3.46289L13.5369 2.87363C13.2115 2.5482 12.6839 2.5482 12.3584 2.87363L11.4745 3.75755L13.2423 5.52531L14.1262 4.6414C14.4516 4.31596 14.4516 3.78833 14.1262 3.46289ZM12.0638 6.70382L10.296 4.93606L2.86948 12.3626C2.75457 12.4775 2.67577 12.6235 2.64277 12.7826L2.23082 14.769L4.21721 14.3571C4.37634 14.3241 4.52234 14.2453 4.63726 14.1303L12.0638 6.70382Z" fill="#FFAD33"/>
                                </svg>
                            </button>
                            <button class="btndelete" data-id="${brand.brandId}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M16.6666 4.64286H4.44442L5.55554 17.5H14.4444L15.5555 4.64286H3.33331M9.99998 7.85714V14.2857M12.7778 7.85714L12.2222 14.2857M7.2222 7.85714L7.77776 14.2857M7.77776 4.64286L8.33331 2.5H11.6666L12.2222 4.64286" stroke="#E01212" stroke-width="1.56" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button> 
                        </td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="5" style="text-align:center;">No brands found</td></tr>';
        }

        $('#tblresult').html(html);
    }


    // Render Pagination
    function renderBrandPagination(totalRecords, perPage) {
        const totalPages = Math.ceil(totalRecords / perPage);
        const startRecord = (currentPage - 1) * perPage + 1;
        const endRecord = Math.min(currentPage * perPage, totalRecords);

        let infoHtml = `<div class="showing">Showing ${startRecord}-${endRecord} of ${totalRecords}</div>`;
        let paginationHtml = '<div class="pagination">';
        for (let i = 1; i <= totalPages; i++) {
            const activeClass = i === currentPage ? 'active' : '';
            paginationHtml += `<button class="page-btn-brand ${activeClass}" data-page="${i}">${i}</button>`;
        }
        paginationHtml += '</div>';

        $('.tblbrands').next('.pagination-container').remove();
        $('.tblbrands').after(`<div class="pagination-container">${infoHtml}${paginationHtml}</div>`);

        $('.page-btn-brand').on('click', function () {
            currentPage = parseInt($(this).data('page'));
            fetchBrands();
        });
    }

    //brandId
    $(document).on('click','.btnedit',function(){
        let brandId = $(this).data('id');
        location.href= 'managebrands.php?do=edid&brandId='+brandId;
    })

    $(document).on('click','.btndelete',function(){
        let brandId = $(this).data('id');
        location.href= 'managebrands.php?do=delete&brandId='+brandId;
    })
    $(document).on('click','.btnview',function(){
        let brandId = $(this).data('id');
        location.href= 'manageBrandtamplate.php?bid='+brandId;
    })
});
