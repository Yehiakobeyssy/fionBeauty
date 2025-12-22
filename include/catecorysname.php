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

/* Dropdown container */
.dropdown-content {
    display: none;
    position: absolute;
    top: 215px;
    left: 40px;
    height: 250px;
    width: 30vw;
    z-index: 5;
    border-radius: var(--radius);
    background: var(--color-card);
    box-shadow: var(--shadow-md);
    display: flex;
}

/* Left list and right image */
.dropdownlist {
    width: 30%;
    height: 100%;
    overflow-y: auto;
    border-right: 1px solid rgba(0,0,0,0.05);
    background: rgba(0,0,0,0.03);
}

.imgstyle {
    width: 70%;
    padding: var(--space-3);
}
.dropdown-content img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius);
}

/* Dropdown links */
.dropdownlist a {
    display: block;
    padding: var(--space-2) var(--space-3);
    font-family: var(--font-sans);
    font-size: 1rem;
    color: var(--color-dark);
    text-decoration: none;
    transition: color .2s ease, background .2s ease;
}
.dropdownlist a:hover {
    color: var(--color-primary);
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
        <div class="dropdownlist">
            <?php
            $sql=$con->prepare('SELECT categoryId,catName FROM tblcategory WHERE catActive = 1 ORDER BY catName ');
            $sql->execute();
            $cats = $sql->fetchAll();
            foreach($cats as $cat){
                echo '<a href="category.php?cat='.$cat['catName'].'"  data-index="'.$cat['categoryId'].'">'.$cat['catName'].'</a>';
            }
            ?>
        </div>
        <div class="imgstyle">
            <img src="img/items/" alt="" srcset="" id="categoryImage">
        </div>
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

