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
});
