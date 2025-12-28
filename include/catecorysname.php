<style>
/* ============================
   Category Buttons / Dropdown
============================ */
.catgeorybtns {
    display: flex;
    overflow-x: auto;
    margin: var(--space-5);
    padding: var(--space-4);
    position: relative;
    height: 100px;
    gap: var(--space-3);
}

/* Custom Scrollbar */
.catgeorybtns::-webkit-scrollbar {
    width: 7px;
}
.catgeorybtns::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 40px;
}
.catgeorybtns::-webkit-scrollbar-track {
    background-color: rgba(0,0,0,0.05);
}

/* All Categories button */
.allcat {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: var(--space-3);
    height: 34px;
    padding: 16px 20px;
    border-radius: var(--radius);
    background-color: var(--color-primary);
    color: var(--color-white);
    font-weight: 600;
    cursor: pointer;
    box-shadow: var(--shadow-sm);
    transition: transform .08s ease, box-shadow .12s ease, background-color .12s ease;
}
.allcat:hover,
.allcat:focus {
    background-color: var(--color-primary-variant);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Individual category buttons */
.btncate {
    display: flex;
    justify-content: center;
    align-items: center;
    white-space: nowrap;
    height: 34px;
    padding: 16px 20px;
    border-radius: var(--radius);
    border: 1px solid var(--color-primary);
    background-color: transparent;
    color: var(--color-dark);
    cursor: pointer;
    transition: all .2s ease;
}
.btncate:hover {
    background-color: var(--color-primary);
    color: var(--color-white);
    box-shadow: var(--shadow-sm);
}

.dropdown-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* First 3 categories in a row */
    grid-auto-rows: auto;                 /* Rest flow vertically */
    gap: 20px 40px;                       /* Row and column gaps */
    position: absolute;
    top: 230px;
    left: 40px;
    width: 750px;
    max-height: 500px;
    overflow-y: auto;
    background: var(--color-card);
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: var(--shadow-md);
    z-index: 9000;
}

/* Prevent individual category from breaking columns */
.dropdown-section {
    break-inside: avoid;
}


/* Category section with spacing */
.dropdown-section {
    break-inside: avoid;   /* Prevent breaking category across columns */
    margin-bottom: 20px;
}

/* Category name */
.dropdown-cat {
    font-weight: 700;
    margin-bottom: 10px;
    font-size: 1.1rem;
    color: var(--color-dark);
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding-bottom: 5px;
}

/* Subcategory list */
.dropdown-subcats {
    list-style: none;
    margin: 10px 0 0 0;
    padding-left: 0;
}

.dropdown-subcats li {
    margin-bottom: 5px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: color 0.2s ease;
}

.dropdown-subcats li:hover {
    color: var(--color-primary);
    text-decoration: underline;
}

/* Category heading style */
.dropdown-cat a {
    color: var(--color-dark);
    font-family: 'Outfit', sans-serif;
    font-size: 20px;
    font-style: normal;
    font-weight: 600;
    line-height: normal;
    text-decoration: none; /* Remove underline */
}

.dropdown-cat a:hover {
    color: var(--color-primary); /* Optional hover color */
}

/* Subcategory list style */
.dropdown-subcats li a {
    color: var(--color-icon);
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
    font-style: normal;
    font-weight: 400;
    line-height: 18px; /* 150% */
    letter-spacing: 0.06px;
    text-decoration: none; /* Remove underline */
    transition: color 0.2s ease;
}

.dropdown-subcats li a:hover {
    color: var(--color-primary);
    text-decoration: underline;
}

/* Responsive tweaks */
@media (max-width: 768px) {
    .dropdown-content { top: 340px; }
    .allcat { font-size: 0.9rem; }
    .btncate { font-size: 0.9rem; }
}

        </style>
    <div class="catgeorybtns">
        <button class="allcat" id="btn1">
            Categories 
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path d="M10.3034 10.7917L14.4284 6.66667L15.6067 7.845L10.3034 13.1483L5.00002 7.845L6.17836 6.66667L10.3034 10.7917Z" fill="white"/>
            </svg>
        </button>
        <?php
            $sql=$con->prepare('SELECT categoryId,catName FROM tblcategory WHERE catActive = 1 ORDER BY catName ');
            $sql->execute();
            $cats = $sql->fetchAll();
            foreach($cats as $cat){
                echo '
                    <button class="btncate" data-index="'.$cat['catName'].'">
                    '.$cat['catName'].'
                    </button>
                ';
            }
        ?>
    </div>
    <div class="dropdown-content" id="dropdown1">
        <?php
        $sql = $con->prepare('SELECT categoryId, catName FROM tblcategory WHERE catActive = 1 ORDER BY catName');
        $sql->execute();
        $categories = $sql->fetchAll();

        foreach ($categories as $cat) {
            $subSql = $con->prepare('SELECT subCatName FROM tblsubcategory WHERE catID = ? AND subCatActive = 1 ORDER BY subCatName');
            $subSql->execute([$cat['categoryId']]);
            $subcats = $subSql->fetchAll();

            echo '<div class="dropdown-section">';
            // Category link
            $catLink = 'category.php?cat=' . urlencode($cat['catName']);
            echo '<h3 class="dropdown-cat"><a href="'.$catLink.'">'.$cat['catName'].'</a></h3>';

            echo '<ul class="dropdown-subcats">';
            foreach ($subcats as $sub) {
                // Subcategory link
                $subLink = 'category.php?subcat=' . urlencode($sub['subCatName']);
                echo '<li><a href="'.$subLink.'">'.$sub['subCatName'].'</a></li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>




    <script>
     $('#btn1').click(function () {
        $('#dropdown1').toggle();
    });

    $('#dropdown1').on('mouseleave', function () {
        $('#dropdown1').hide();
    });

    $('#dropdown1').hide()

    $('.btncate').click(function(){
        let cat = $(this).attr('data-index');
        location.href = "category.php?cat=" + encodeURIComponent(cat);
    });
    $('.dropdownlist a').hover(
            function () {
                // Hover in
                var catName = $(this).data('index');
                
                // Make an Ajax request to your API
                $.ajax({
                    url: 'ajax/dislaplyimg.php', // Replace with your API endpoint
                    method: 'GET',
                    data: { catID: catName },
                    success: function (response) {
                        // Assuming the API response contains the image URL
                        var imagePath = 'images/items/' + response.imgsource; // Adjust based on your API response
                        $('#categoryImage').attr('src', imagePath);
                        console.log(imagePath)
                    },
                    error: function (xhr, status, error) {
                        // Handle errors
                        console.error('Ajax Request Failed:', status, error);
                    }
                });
            },
            function () {
                // Hover out (optional: reset to a default image if needed)
                $('#categoryImage').attr('src', 'img/items/default.jpg');
            }
        );
    </script>

