$(document).ready(function() {

    // ================= GET URL PARAMETERS =================
    function getUrlParams() {
        const params = {};
        window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(_, key, value) {
            params[key] = decodeURIComponent(value.replace(/\+/g, ' '));
        });
        return params;
    }
    const urlParams = getUrlParams();

    // <input type="text" name="searchCatId" id="searchCatID">
    // <input type="text" name="serachsubcat" id="searchsubCatID"></input>
    $.ajax({
        url: 'ajax/searchcategroypage.php',
        type: 'GET',
        dataType: 'json',
        data: {
            cat: urlParams.cat,
            subcat: urlParams.subcat
        },
        success: function(response) {
            if(response.status === 'success') {
                $('#searchCatID').val(response.catID);
                $('#searchsubCatID').val(response.subCatID);
            } else {
                $('#searchCatID').val('');
                $('#searchsubCatID').val('');
                console.log('Category/Subcategory not found');
            }
            filterItems();
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });

    // ================= SLIDESHOW =================
    let slideIndex = 0;
    const slides = $(".mySlide");
    const totalSlides = slides.length;

    function showSlides() {
        slides.hide();
        slideIndex++;
        if (slideIndex > totalSlides) slideIndex = 1;
        slides.eq(slideIndex - 1).fadeIn();
        setTimeout(showSlides, 5000);
    }
    showSlides();

    // ================= PRICE SLIDER =================
    priceSlider.noUiSlider.on('update', function(values, handle) {
        const value = Math.round(values[handle]);
        if (handle === 0) {
            minValueInput.value = value;
            minPriceSpan.textContent = value;
        } else {
            maxValueInput.value = value;
            maxPriceSpan.textContent = value;
        }
    });

    // ================= CATEGORY TOGGLE =================
    $(".cat-header").click(function() {
        $(this).toggleClass("active");
        $(this).next(".subcats").slideToggle();
    });

    // ================= PRESELECT FILTERS FROM URL =================
    if (urlParams.cat) {
        $("input[name=category][value='" + urlParams.cat + "']").prop('checked', true);
    }
    if (urlParams.subcat) {
        $("input[name=subcategory][value='" + urlParams.subcat + "']").prop('checked', true);
    }
    if (urlParams.keyword) {
        $("#searchInput").val(urlParams.keyword);
    }

    // ================= HELPER FUNCTION TO RENDER ITEMS =================
    function renderItems(items) {
        let html = '<div class="items_cards">';
        if (items.length > 0) {
            items.forEach(item => {
                const desc = item.itmDesc.length > 60 ? item.itmDesc.substring(0, 60) + "..." : item.itmDesc;
                let priceHtml = '';

                if (item.sellPrice !== undefined) {
                    if (item.promotional && item.promotional > 0) {
                        const discounted = (item.sellPrice * (1 - item.promotional / 100)).toFixed(2);
                        priceHtml = `
                            <div class="price">
                                <span class="original-price">$${parseFloat(item.sellPrice).toFixed(2)}</span>
                                <span class="discounted-price">$${discounted}</span>
                            </div>`;
                    } else {
                        priceHtml = `<div class="price">$${parseFloat(item.sellPrice).toFixed(2)}</div>`;
                    }
                } else {
                    priceHtml = `<div class="price login-required">Login to see price</div>`;
                }

                const name = item.itmName.length > 35 ? item.itmName.substring(0, 33) + "..." : item.itmName;

                html += `
                    <div class="card itm_daitail" data-index="${item.itmId}">
                        ${item.promotional > 0 ? `<div class="promotional">${item.promotional}%</div>` : ''}
                        <div class="card-image">
                            <img src="images/items/${item.mainpic}" alt="${item.itmName}">
                        </div>
                        <div class="card-body">
                            <div class="item-title">
                                <span class="name">${name}</span>
                                <span class="item-rating">${parseFloat(item.avgRating).toFixed(1)} ⭐</span>
                            </div>
                            <p class="item-desc">${desc}</p>
                            ${priceHtml}
                            <button class="btn-cart" data-id="${item.itmId}">Add to Cart</button>
                        </div>
                    </div>`;
            });
        } else {
            html += '<p class="no-items">No items found.</p>';
        }
        html += '</div>';
        $(".container_items").html(html);
    }

    // ================= LOAD ITEMS ON PAGE LOAD =================


    // ================= USER-DRIVEN FILTER FUNCTION =================
    function filterItems() {
        let categoryId = $("input[name=category]:checked").val() || $('#searchCatID').val() || 0;
        let subCatId  = $("input[name=subcategory]:checked").val() || $('#searchsubCatID').val() || 0;

        categoryId = parseInt(categoryId) || 0;
        subCatId = parseInt(subCatId) || 0;

        $.ajax({
            url: "ajax/items_category.php",
            type: "POST",
            data: {
                categoryId: categoryId,
                subCatId: subCatId,
                keyword: $("#searchInput").val() || '',
                minPrice: $("#minValue").val(),
                maxPrice: $("#maxValue").val(),
                rating: $("input[name=rating]:checked").val() || 0,
                brandId: $("input[name=brand]:checked").val() || 0
            },
            dataType: "json",
            success: function(items) {
                renderItems(items);
            }
        });
    }

    // ================= INITIAL LOAD =================


    // ================= EVENT LISTENERS =================
    $(document).on("change", "input[name=category], input[name=subcategory], input[name=rating], input[name=brand]", function() {
    // Clear the text inputs
    $('#searchCatID').val('');
    $('#searchsubCatID').val('');

    // Call the filter function
    filterItems();
    });
    $("#minValue, #maxValue").on("change", filterItems);
    priceSlider.noUiSlider.on('update', function(values) {
        $("#minValue").val(values[0]);
        $("#maxValue").val(values[1]);
        filterItems();
    });
    $(document).on("input", "#searchInput", filterItems);

    // ================= ITEM CARD CLICK =================
    $(document).on("click", ".itm_daitail", function() {
        const itmID = $(this).data("index");
        location.href = "daitailitem.php?itemid=" + itmID;
    });

    // ================= ADD TO CART =================
    $(document).on("click", ".btn-cart", function(e) {
        e.preventDefault();
        e.stopPropagation();

        const itmID = $(this).data('id');

        $.post("ajax/addtocart.php", { itemId: itmID }, function(response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                if (res.cart_count > 0) $('.cartnumber').show();
                $('#numberofitems').html(res.cart_count);
                alert('✅ Successfully added to cart.');
            } else {
                alert('❌ ' + res.message);
            }
        });
    });

});
