$(document).ready(function() {
    
    // ========== SLIDESHOW ==========
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

    // ========== PRICE SLIDER ==========
    priceSlider.noUiSlider.on('update', function(values, handle) {
        let value = Math.round(values[handle]); // round for cleaner numbers
        if (handle === 0) {
            minValueInput.value = value;
            minPriceSpan.textContent = value;
        } else {
            maxValueInput.value = value;
            maxPriceSpan.textContent = value;
        }
    });

    // ========== FILTER ITEMS FUNCTION ==========
    function filterItems() {
        $.ajax({
            url: "ajax/items_category.php",
            type: "POST",
            data: {
                categoryId: $("input[name=category]:checked").val(),
                minPrice: $("#minValue").val(),
                maxPrice: $("#maxValue").val(),
                rating: $("input[name=rating]:checked").val(),
                brandId: $("input[name=brand]:checked").val()
            },
            dataType: "json",
            success: function(items) {
    let html = '<div class="items_cards">';
    if(items.length > 0){
        items.forEach(item => {
            let desc = item.itmDesc.length > 60 
                ? item.itmDesc.substring(0, 60) + "..." 
                : item.itmDesc;

            let priceHtml = '';

            if (item.sellPrice !== undefined) {
                if (item.promotional && item.promotional > 0) {
                    const discountedPrice = (item.sellPrice * (1 - item.promotional / 100)).toFixed(2);
                    priceHtml = `
                        <div class="price">
                            <span class="original-price">$${parseFloat(item.sellPrice).toFixed(2)}</span>
                            <span class="discounted-price">$${discountedPrice}</span>
                        </div>
                    `;
                } else {
                    priceHtml = `<div class="price">$${parseFloat(item.sellPrice).toFixed(2)}</div>`;
                }
            } else {
                priceHtml = `<div class="price login-required">Login to see price</div>`;
            }


            html += `
                <div class="card itm_daitail" data-index="${item.itmId}">
                    ${item.promotional > 0 ? `<div class="promotional">${item.promotional}%</div>` : ''}
                    <div class="card-image">
                        <img src="images/items/${item.mainpic}" alt="${item.itmName}">
                    </div>
                    <div class="card-body">
                        <div class="item-title">
                            <span class="name">${item.itmName}</span>
                            <span class="item-rating">${parseFloat(item.avgRating).toFixed(1)} ⭐</span>
                        </div>
                        <p class="item-desc">${desc}</p>
                        ${priceHtml}
                        <button class="btn-cart"  data-id="${item.itmId}">Add to Cart</button>
                    </div>
                </div>
            `;
        });
    } else {
        html += '<p class="no-items">No items found.</p>';
    }
    html += '</div>';
    $(".container_items").html(html);
}


        });
    }

    // ========== EVENT LISTENERS ==========
    // عند تغيير الفلاتر
    $("input[name=category], input[name=rating], input[name=brand]").on("change", filterItems);
    $("#minValue, #maxValue").on("change", filterItems);
    priceSlider.noUiSlider.on('update', function(values){
        $("#minValue").val(values[0]);
        $("#maxValue").val(values[1]);
        filterItems();
    });

    // استدعاء أول مرة عند تحميل الصفحة
    filterItems();

    // كليك على الكارد لعرض الـ itemId
    $(document).on("click", ".itm_daitail", function(){
        let itmID = $(this).data("index");
        location.href="daitailitem.php?itemid="+itmID;
    });

    $(document).on("click", ".btn-cart", function(e) {
        e.preventDefault();
        e.stopPropagation();

        let itmID = $(this).data('id');

        $.post("ajax/addtocart.php", { itemId: itmID }, function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                if(res.cart_count > 0){
                    $('.cartnumber').show();
                }
                $('#numberofitems').html(res.cart_count);
                alert('✅ Successfully added to cart.')
            } else {
                alert('❌ ' + res.message);
            }
        });

        
    });
    
});
