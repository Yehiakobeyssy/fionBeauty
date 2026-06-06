<style>
/* ============================
   Main Layout
============================ */


/* ============================
   Aside Category Menu
============================ */
.category_aside{
    width: 250px;
    min-width: 250px;
    background: var(--color-card);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    overflow-x: auto !important;
    border: 1px solid rgba(0,0,0,0.05);
   

}

/* Aside Header */
.category_header{
    padding: 18px 20px;
    background: var(--color-primary);
    color: var(--color-white);
    font-size: 16px;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
}

/* Category Item */
.category_item{
    border-bottom: 1px solid rgba(0,0,0,0.06);
}

.category_item:last-child{
    border-bottom: none;
}

/* Main Category Button */
.category_btn{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    width: 100%;
    padding: 14px 16px;
    cursor: pointer;
    transition: 0.2s ease;
    background: transparent;
    border: none;
}

.category_btn:hover{
    background: rgba(0,0,0,0.03);
}

/* Left Side */
.category_left{
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Category Image */
.category_left img{
    width: 45px;
    height: 45px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid rgba(0,0,0,0.08);
}

/* Category Name */
.category_left span{
    font-size: 12px;
    font-weight: 500;
    color: var(--color-dark);
    font-family: 'Outfit', sans-serif;
}

/* Arrow */
.category_arrow{
    transition: 0.3s ease;
    flex-shrink: 0;
}

.category_item.active .category_arrow{
    transform: rotate(180deg);
}

/* ============================
   Subcategories
============================ */
.subcategories{ display: none; background: rgba(0,0,0,0.02); padding: 0 0 10px 0; }
.subcategories.show{
    display: block !important;
}
.subcategories a{
    display: block;
    padding: 10px 20px 10px 72px;
    text-decoration: none;
    color: var(--color-icon);
    font-size: 14px;
    transition: 0.2s ease;
    font-family: 'Outfit', sans-serif;
}

.subcategories a:hover{
    background: rgba(0,0,0,0.04);
    color: var(--color-primary);
    padding-left: 78px;
}

/* ============================
   Main Content
============================ */
.main_content{
    flex: 1;
    width: 100%;
}

/* ============================
   Responsive
============================ */
@media (max-width: 768px){

    .fion_container{
        flex-direction: column;
    }

    .category_aside{
        width: 100%;
        min-width: 100%;
    }

    .main_content{
        width: 100%;
    }
}
@media (max-width:750px) {
    /* Horizontal Scroll */
.category_aside{
    display: flex;
    flex-wrap: nowrap;

    overflow-x: auto !important;
    overflow-y: hidden ;

    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;

    gap: 12px;
    padding: 5px 2px 10px;

    scrollbar-width: none;
}

    .subcategories{
        display: none;

        position: fixed;
        z-index: 99999;

        width: 220px;

        background: var(--color-card);
        border-radius: 12px;

        box-shadow: 0 10px 25px rgba(0,0,0,0.2);

        border: 1px solid rgba(0,0,0,0.08);

        padding: 6px 0;
    }

    .subcategories.show{
        display: block !important;
    }
.category_aside::-webkit-scrollbar{
    display: none;
}

/* Each category card */
.category_item{
        position: static;

    flex: 0 0 auto;
    width: 180px;
}

}
</style>


<div class="fion_container">

    <!-- =========================
         CATEGORY ASIDE
    ========================== -->
    <aside class="category_aside">


        <?php
        $sql = $con->prepare("
                                SELECT categoryId, catName, carImg
                                FROM tblcategory
                                WHERE catActive = 1
                                ORDER BY
                                    CASE LOWER(catName)
                                        WHEN 'skin care' THEN 1
                                        WHEN 'clinic program' THEN 2
                                        WHEN 'skin booster' THEN 3
                                        WHEN 'medical equipment' THEN 4
                                        WHEN 'equipment' THEN 5
                                        WHEN 'furniture' THEN 6
                                        ELSE 999
                                    END,
                                    catName ASC
                            ");
        $sql->execute();
        $categories = $sql->fetchAll();

        foreach($categories as $cat){

            // Get Subcategories
            $subSql = $con->prepare("
                SELECT subCatID, subCatName 
                FROM tblsubcategory 
                WHERE catID = ? 
                AND subCatActive = 1 
                ORDER BY subCatName
            ");
            $subSql->execute([$cat['categoryId']]);
            $subs = $subSql->fetchAll();

            $hasSub = count($subs) > 0;

            echo '<div class="category_item">';

                // Category Button
                echo '
                <div class="category_btn" 
                     data-link="category.php?cat='.$cat['categoryId'].'">

                    <div class="category_left">


                        <span>'.$cat['catName'].'</span>

                    </div>
                ';

                // Arrow if has subcategories
                if($hasSub){
                    echo '
                    <svg class="category_arrow"
                         xmlns="http://www.w3.org/2000/svg"
                         width="20"
                         height="20"
                         viewBox="0 0 24 24"
                         fill="none">

                        <path d="M7 10L12 15L17 10"
                              stroke="currentColor"
                              stroke-width="2"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                    ';
                }

                echo '</div>';

                // Subcategories
                if($hasSub){

                    echo '<div class="subcategories">';

                    foreach($subs as $sub){

                        echo '
                        <a href="category.php?subcat='.$sub['subCatID'].'">
                            '.$sub['subCatName'].'
                        </a>
                        ';
                    }

                    echo '</div>';
                }

            echo '</div>';
        }
        ?>

    </aside>


    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <div class="main_content">

        <!-- YOUR MAIN CONTENT HERE -->

    </div>

</div>


<script>
$(document).ready(function(){

    $('.category_btn').click(function(e){

        let parent = $(this).closest('.category_item');
        let subMenu = parent.find('.subcategories');

        // =========================
        // DESKTOP (>500px)
        // =========================
        if(window.innerWidth > 500){

            if(subMenu.length){

                e.preventDefault();

                $('.subcategories').not(subMenu).slideUp(200);
                $('.category_item').not(parent).removeClass('active');

                subMenu.stop(true,true).slideToggle(200);
                parent.toggleClass('active');
            }

            return;
        }

        // =========================
        // MOBILE (<=500px)
        // =========================
        if(subMenu.length){

            e.preventDefault();

            let isOpen = subMenu.hasClass('show');

            $('.subcategories').removeClass('show');

            if(!isOpen){

                let rect = this.getBoundingClientRect();

                subMenu.css({
                    top: rect.bottom + 8 + "px",
                    left: rect.left + "px"
                });

                subMenu.addClass('show');
            }

        }else{

            window.location.href = $(this).data('link');
        }

    });

    // close mobile popup
    $(document).on('click', function(e){

        if(window.innerWidth <= 500){

            if(!$(e.target).closest('.category_item').length){
                $('.subcategories').removeClass('show');
            }
        }

    });

});
</script>