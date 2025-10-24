$(document).ready(function() {

    /*********************
     * SLIDER LOGIC
     *********************/
    let slideIndex = 0;
    let slides = $(".mySlide");
    let totalSlides = slides.length;

    function showSlides() {
        slides.hide();
        slideIndex++;
        if (slideIndex > totalSlides) { slideIndex = 1; }
        slides.eq(slideIndex - 1).fadeIn();
        setTimeout(showSlides, 5000); // 5 seconds per slide
    }
    showSlides();

    /*********************
     * HELPER FUNCTION TO RENDER CARDS
     *********************/
    function renderCards(items, clientActive) {
        let html = "";
        items.forEach(item => {
            let stars = "";
            for (let i = 1; i <= 5; i++) {
                stars += `<i class="fa${i <= Math.round(item.rating) ? "s" : "r"} fa-star text-warning"></i>`;
            }

            let priceHtml = '';
            if (clientActive == 1) {
                if (item.promotional > 0) {
                    let discountedPrice = (item.sellPrice * (1 - item.promotional / 100)).toFixed(2);
                    priceHtml = `
                        <h4 class="price">
                            <span class="original-price" style="text-decoration: line-through; color: #888;">$${parseFloat(item.sellPrice).toFixed(2)}</span>
                            <span class="discounted-price" style="color: red; margin-left: 8px;">$${discountedPrice}</span>
                        </h4>`;
                } else {
                    priceHtml = `<h4 class="price">$${parseFloat(item.sellPrice).toFixed(2)}</h4>`;
                }
            } else {
                priceHtml = `<div class="stars-block">${'<i class="fas fa-star text-muted"></i>'.repeat(5)}</div>`;
            }

            html += `
                <div class="card">
                    ${item.promotional > 0 ? `<div class="promotional">${item.promotional}%</div>` : ''}
                    <img src="images/items/${item.mainpic}" alt="${item.itmName}">
                    <div class="card-body itm_daitail" data-index="${item.itmId}">
                        <h5 class="item-title">
                            <span class="name">${item.itmName}</span>
                            <span class="rating">${stars}</span>
                        </h5>
                        <p>${item.itmDesc}...</p>
                        ${priceHtml}
                        <button class="btn-cart" data-id="${item.itmId}">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>`;
        });

        return html;
    }

    /*********************
     * PROMOTIONAL ITEMS
     *********************/
    let promoSection = $(".flashsale");
    function loadPromotionalItems(page) {
        let itemsContainer = promoSection.find(".items_cards");
        itemsContainer.html('<p>Loading...</p>');

        $.getJSON("ajax/promotional_items.php?page=" + page, function(data) {
            if (data.items.length > 0) {
                let html = renderCards(data.items, data.clientActive);
                itemsContainer.html(html);
                promoSection.data("currentPage", data.page);
                promoSection.data("totalPages", data.totalPages);
            } else {
                itemsContainer.html('<p class="text-center alert alert-warning">No promotional items available.</p>');
            }
        });
    }

    $("#btnNext_pro").on("click", function(e) {
        e.preventDefault();
        let currentPage = promoSection.data("currentPage") || 1;
        let totalPages = promoSection.data("totalPages") || 1;
        let nextPage = currentPage + 1;

        // Prevent requesting beyond last page
        if (nextPage > totalPages) return; 

        loadPromotionalItems(nextPage);
    });

    $("#btnBack_pro").on("click", function(e) {
        e.preventDefault();
        let currentPage = promoSection.data("currentPage") || 1;
        let prevPage = currentPage - 1;

        // Prevent requesting before first page
        if (prevPage < 1) return;

        loadPromotionalItems(prevPage);
    });

    loadPromotionalItems(1);

    /*********************
     * FOR YOU SECTION
     *********************/
    let forYouSection = $(".foryou");

    function loadItems(page) {
        let itemsContainer = forYouSection.find(".items_cards");
        itemsContainer.html('<p>Loading...</p>');

        $.getJSON("ajax/items.php?page=" + page, function(data) {
            if (data.items.length > 0) {
                let html = renderCards(data.items, data.clientActive);
                itemsContainer.html(html);
                forYouSection.data("currentPage", data.page);
                forYouSection.data("totalPages", data.totalPages);
            } else {
                itemsContainer.html('<p class="text-center alert alert-warning">No items available.</p>');
            }
        });
    }

    $("#btnNext").on("click", function(e) {
        e.preventDefault();
        let currentPage = forYouSection.data("currentPage") || 1;
        let totalPages = forYouSection.data("totalPages") || 1;
        let nextPage = currentPage + 1;
        if (nextPage > totalPages) nextPage = totalPages;
        loadItems(nextPage);
    });

    $("#btnBack").on("click", function(e) {
        e.preventDefault();
        let currentPage = forYouSection.data("currentPage") || 1;
        let prevPage = currentPage - 1;
        if (prevPage < 1) prevPage = 1;
        loadItems(prevPage);
    });

    loadItems(1);

    /*********************
     * CATEGORIES SECTION
     *********************/
    $.getJSON("ajax/get_categories.php", function(categories) {
        categories.forEach(cat => {
            let sectionHtml = `
                <div class="foryou section_index" data-category="${cat.categoryId}">
                    <div class="sectiontitle"><h4>${cat.catName}</h4></div>
                    <div class="title"><h2>${cat.catName}</h2></div>
                    <div class="items_cards"></div>
                    <div class="pagination">
                        <button class="btnBack">Back</button>
                        <button class="btnNext">Next</button>
                    </div>
                </div>`;
            $("#categories_container").append(sectionHtml);
            loadItemscat(cat.categoryId, 1);
        });
    });

    function loadItemscat(categoryId, page) {
        let section = $(`.foryou[data-category='${categoryId}']`);
        let itemsContainer = section.find(".items_cards");
        itemsContainer.html('<p>Loading...</p>');

        $.getJSON("ajax/category_items.php", { categoryId: categoryId, page: page }, function(data) {
            if (data.items.length > 0) {
                let html = renderCards(data.items, data.clientActive);
                itemsContainer.html(html);
                section.data("currentPage", data.page);
                section.data("totalPages", data.totalPages);
            } else {
                itemsContainer.html('<p class="text-center alert alert-warning">No items available.</p>');
            }
        });
    }

    $(document).on("click", ".btnNext", function(e) {
        e.preventDefault();
        let section = $(this).closest(".foryou");
        let currentPage = section.data("currentPage") || 1;
        let totalPages = section.data("totalPages") || 1;
        let nextPage = currentPage + 1;
        if (nextPage > totalPages) nextPage = totalPages;
        loadItemscat(section.data("category"), nextPage);
    });

    $(document).on("click", ".btnBack", function(e) {
        e.preventDefault();
        let section = $(this).closest(".foryou");
        let currentPage = section.data("currentPage") || 1;
        let prevPage = currentPage - 1;
        if (prevPage < 1) prevPage = 1;
        loadItemscat(section.data("category"), prevPage);
    });

    /*********************
     * CLICK HANDLERS
     *********************/
    $(document).on("click", ".itm_daitail", function(){
        let itmID = $(this).data('index');
        location.href="daitailitem.php?itemid="+itmID;
    });

    $(document).on("click", ".btn-cart", function(e) {
        e.preventDefault();
        e.stopPropagation();
        let itmID = $(this).data('id');

        $.post("ajax/addtocart.php", { itemId: itmID }, function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                if(res.cart_count > 0) $('.cartnumber').show();
                $('#numberofitems').html(res.cart_count);
                alert('✅ Successfully added to cart.')
            } else {
                alert('❌ ' + res.message);
            }
        });
    });

});
