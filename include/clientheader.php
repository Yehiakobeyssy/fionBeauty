<?php
   



    if(isset($_SESSION['cart'])){
        $cart_count = array_sum($_SESSION['cart']);
    }else{
        $cart_count = 0;
    }

    if($cart_count > 0 ){
        $dislay= 'block';
    }else{
        $dislay = 'none';
    }
?>

<link href='https://fonts.googleapis.com/css?family=Baloo Bhaijaan' rel='stylesheet'>
<link href='https://fonts.googleapis.com/css?family=Outfit' rel='stylesheet'>
<style>


.fion-header{
width:100%;
background:#fff;
border-top:3px solid var(--color-primary);
border-bottom:1px solid #ececec;
}

.container-fion{
width:80%;
margin:auto;
padding:25px 30px;
}

.header-row{
display:flex;
justify-content:space-between;
align-items:flex-start;
gap:60px;
}


/* logo left */

.brand-logo{
display:flex;align-items:center;
gap:20px;
}
.brand-logo img{
    width: 120px;
    height: 120px;
}

.brand-logo h1{
font-size:60px;
line-height:1;
letter-spacing:12px;
font-weight:700;
color:var(--color-primary);
}

.brand-logo span{
display:block;
margin-top:8px;
font-size:13px;
letter-spacing:5px;
color:#555;
}



/* right block */

.right-side{
flex:1;
display:flex;
flex-direction:column;
align-items:flex-end;
}



/* nav top */

.utility-nav ul{
list-style:none;
display:flex;
gap:35px;
margin-bottom:20px;
}

.utility-nav a{
text-decoration:none;
color:#111;
font-size:15px;
font-weight:600;
position:relative;
}

.utility-nav a:after{
content:'';
position:absolute;
left:0;
bottom:-6px;
width:0;
height:2px;
background:var(--color-primary);
transition:.3s;
}

.utility-nav a:hover:after{
width:100%;
}



/* search row */

.search-row{
display:flex;
align-items:center;
justify-content:flex-end;
gap:30px;
width:100%;
}

.search-wrap{
width:620px;
max-width:100%;
position:relative;
display:flex;
height:52px;
border:1px solid #ddd;
background:#fff;
}

.search-wrap input{
flex:1;
border:0;
outline:none;
padding:0 20px;
font-size:15px;
}

.search-wrap button{
width:120px;
border:0;
background:#111;
color:#fff;
font-weight:600;
cursor:pointer;
transition:.3s;
}

.search-wrap button:hover{
background:var(--color-primary);
}



/* autocomplete */

.dropdownsmartsearch{
display:none;
position:absolute;
top:100%;
left:0;
width:100%;
background:#fff;
border:1px solid #eee;
box-shadow:0 8px 20px rgba(0,0,0,.08);
z-index:999;
}

.result-item{
display:block;
padding:14px 18px;
text-decoration:none;
color:#222;
border-bottom:1px solid #f1f1f1;
transition:.3s;
}

.result-item:hover{
background:#fafafa;
color:var(--color-primary);
}



/* cart */

.header-actions{
display:flex;
align-items:center;
}

.cart-box{
position:relative;
font-size:32px;
cursor:pointer;
}

.cart-badge{
position:absolute;
top:-8px;
right:-10px;
background:var(--color-primary);
width:24px;
height:24px;
border-radius:50%;
display:flex;
justify-content:center;
align-items:center;
font-size:12px;
font-weight:700;
color:#fff;
}



/* mobile */

.hamburger{
display:none;
font-size:34px;
cursor:pointer;
margin-left:auto;
}

.mobile-menu{
position:fixed;
top:0;
right:-320px;
width:300px;
height:100%;
background:#fff;
box-shadow:-4px 0 25px rgba(0,0,0,.12);
padding:40px 30px;
transition:.4s;
z-index:9999;
}

.mobile-menu.active{
right:0;
}

.close-menu{
font-size:38px;
cursor:pointer;
}

.mobile-menu ul{
list-style:none;
margin-top:45px;
}

.mobile-menu li{
margin-bottom:25px;
}

.mobile-menu a{
text-decoration:none;
font-size:22px;
font-weight:600;
color:#111;
}



/* responsive */

@media(max-width:950px){

.utility-nav{
display:none;
}

.hamburger{
display:block;
}

.header-row{
flex-wrap:wrap;
}

.right-side{
width:100%;
}

.search-row{
width:100%;
}

.search-wrap{
width:100%;
}

.brand-logo h1{
font-size:42px;
letter-spacing:8px;
}

}

@media(max-width:600px){

.container-fion{
width:95%;
padding:20px 15px;
}

.brand-logo h1{
font-size:34px;
}

.search-wrap{
height:46px;
}

.search-wrap button{
width:95px;
}

}

</style>



<header class="fion-header">

<div class="container-fion">

<div class="header-row">


<div class="brand-logo">
<img src="images/logo.png" alt="" srcset="">
<div class="discription-logo">
    <h1>FION</h1>
    <span>BEAUTY SUPPLIES</span>
</div>
</div>



<div class="right-side">

<nav class="utility-nav">
<ul>
<li><a href="index.php">Home</a></li>
<li><a href="contactus.php">Contact</a></li>
<li><a href="aboutus.php">About</a></li>
<li><a href="workshops.php">Training</a></li>
<li><a href="login.php">Register</a></li>
</ul>
</nav>



<div class="search-row">

<div class="search-wrap">

<input
type="text"
id="txtsearch"
placeholder="Search beauty products..."
>

<button id="searchButton">
Search
</button>

<div class="dropdownsmartsearch">
<div id="itemsContainer"></div>
</div>

</div>


<div class="header-actions">

<div
class="cart-box"
onclick="location.href='cart.php'"
>

🛒

<?php if($cart_count>0){ ?>
<div class="cart-badge">
<?= $cart_count ?>
</div>
<?php } ?>

</div>

</div>

</div>

</div>


<div
class="hamburger"
id="openMenu"
>
☰
</div>


</div>

</div>

</header>



<div
class="mobile-menu"
id="mobileMenu"
>

<div
class="close-menu"
id="closeMenu"
>
×
</div>

<ul>
<li><a href="index.php">Home</a></li>
<li><a href="contactus.php">Contact</a></li>
<li><a href="aboutus.php">About</a></li>
<li><a href="workshops.php">Training</a></li>
<li><a href="login.php">Register</a></li>
<li><a href="cart.php">Cart</a></li>
</ul>

</div>




<script>

const openMenu=
document.getElementById('openMenu');

const closeMenu=
document.getElementById('closeMenu');

const mobileMenu=
document.getElementById('mobileMenu');


openMenu.onclick=function(){
mobileMenu.classList.add('active');
};

closeMenu.onclick=function(){
mobileMenu.classList.remove('active');
};

window.addEventListener(
'click',
function(e){
if(
!mobileMenu.contains(e.target)
&&
!openMenu.contains(e.target)
){
mobileMenu.classList.remove('active');
}
}
);



$(function(){

let historyItems=
JSON.parse(
localStorage.getItem('smartsearch')
) || [];


/* live search */

$('#txtsearch').on(
'keyup',
function(){

let searchTerm=
$(this).val().trim();

if(!searchTerm){
$('.dropdownsmartsearch').hide();
return;
}


$.ajax({

url:'ajax/displayitems.php',

method:'GET',

data:{
keyword:searchTerm
},

dataType:'json',

success:function(resp){

let apiItems=
resp.data || resp || [];

let dbItems=
apiItems
.map(
item=>item.itmName
)
.filter(
name=>
name
.toLowerCase()
.startsWith(
searchTerm.toLowerCase()
)
);


let filteredHistory=
historyItems.filter(
item=>
item
.toLowerCase()
.startsWith(
searchTerm.toLowerCase()
)
);

let combined=
Array.from(
new Set(
dbItems.concat(
filteredHistory
)
)
);

displayDropdown(
combined
);

}

});

}
);



function displayDropdown(items){

let container=
$("#itemsContainer");

container.empty();

if(!items.length){
$('.dropdownsmartsearch').hide();
return;
}

items.slice(0,10)
.forEach(function(item){

container.append(
'<a class="result-item" href="category.php?keyword='
+
encodeURIComponent(item)
+
'">'
+
item
+
'</a>'
);

});

$('.dropdownsmartsearch').show();

}



/* history */

$('#txtsearch').on(
'blur',
function(){

let val=
$(this).val().trim();

if(
val &&
!historyItems.includes(val)
){
historyItems.unshift(val);

historyItems=
historyItems.slice(0,20);

localStorage.setItem(
'smartsearch',
JSON.stringify(historyItems)
);

}

}
);



/* enter search */

$('#txtsearch').keypress(
function(e){

if(e.which==13){

e.preventDefault();

let val=
$(this).val().trim();

if(val!=''){
window.location=
'category.php?keyword='
+
encodeURIComponent(val);
}

}

}
);



/* button search */

$('#searchButton').click(
function(){

let val=
$('#txtsearch').val().trim();

if(val!=''){
window.location=
'category.php?keyword='
+
encodeURIComponent(val);
}

}
);



$(document).click(
function(e){
if(
!$(e.target)
.closest('.search-wrap')
.length
){
$('.dropdownsmartsearch').hide();
}
}
);

});

</script>