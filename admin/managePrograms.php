<?php
session_start();
include '../settings/connect.php';

    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();  
    }else{
        $admin_id = $_SESSION['admin_id'];
        $sql=$con->prepare('SELECT fName, lName FROM  tbladmin WHERE adminID  = ?');
        $sql->execute([$admin_id]);
        $result =  $sql->fetch();
        $admin_name = $result['fName'].' ' . $result['lName'];
    }
include '../common/head.php';

$do = $_GET['do'] ?? 'manage';
$pid = isset($_GET['pid']) ? (int)$_GET['pid'] : 0;
$sectionId = isset($_GET['sid']) ? (int)$_GET['sid'] : 0;
$itemId = isset($_GET['iid']) ? (int)$_GET['iid'] : 0;
?>

<link rel="stylesheet" href="../common/root.css">
<link rel="stylesheet" href="css/managePrograms.css">

<body>

<?php include 'include/adminheader.php'; ?>

<main>
<?php include 'include/adminaside.php'; ?>

<div class="container_info">
<h1 class="page_title">Programs Management</h1>
<?php
/* =========================================================
   MANAGE (LIST)
========================================================= */
if ($do === 'manage') {
?>

<div class="btnadd">
    <a href="managePrograms.php?do=add" class="btn btn-success">+ New Program</a>
</div>

<table class="program_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Program</th>
            <th>Sections</th>
            <th>Items</th>
            <th>Active</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody id="fetchPrograms"></tbody>
</table>
<?php
}

/* =========================================================
   ADD PROGRAM
========================================================= */
elseif ($do === 'add') {

if (isset($_POST['saveProgram'])) {

    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';

    $logo = "";
    $slideshow = "";

    // =========================
    // LOGO UPLOAD
    // =========================
    if (!empty($_FILES['logo']['name'])) {

        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = time()  . $ext;

        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            "../images/programs/" . $logo
        );
    }

    // =========================
    // SLIDESHOW (ONLY ONE FILE)
    // =========================
    if (!empty($_FILES['slideshow']['name'])) {

        $ext2 = pathinfo($_FILES['slideshow']['name'], PATHINFO_EXTENSION);
        $slideshow = time()  . $ext2;

        move_uploaded_file(
            $_FILES['slideshow']['tmp_name'],
            "../images/programs/" . $slideshow
        );
    }

    // =========================
    // INSERT DB
    // =========================
    $stmt = $con->prepare("
        INSERT INTO tblprogramm 
        (ProgramName, ProgramDiscription, ProgramLogo, ProgramSlideshow, ProgramActive)
        VALUES (?, ?, ?, ?, 1)
    ");

    $stmt->execute([
        $name,
        $desc,
        $logo,
        $slideshow
    ]);

    echo "<script>location.href='managePrograms.php?do=manage';</script>";
    exit();
}
?>

<form method="post" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Program Name" required>

    <textarea name="desc" placeholder="Program Description"></textarea>

    <!-- ================= LOGO ================= -->
    <label>Program Logo</label>
    <input type="file" name="logo" accept="image/*">

    <!-- ================= SLIDESHOW ================= -->
    <label>Program Slideshow</label>
    <input type="file" name="slideshow" accept="image/*" multiple>

    <button type="submit" name="saveProgram">Save</button>

</form>

<?php
}

/* =========================================================
   EDIT PROGRAM
========================================================= */
elseif ($do === 'edit') {

$stmt = $con->prepare("SELECT * FROM tblprogramm WHERE ProgramID=?");
$stmt->execute([$pid]);
$p = $stmt->fetch();

if (isset($_POST['updateProgram'])) {

    $name = $_POST['name'] ?? '';
    $desc = $_POST['desc'] ?? '';

    // keep old values
    $logo = $p['ProgramLogo'];
    $slideshow = $p['ProgramSlideshow'];

    // =========================
    // UPDATE LOGO
    // =========================
    if (!empty($_FILES['logo']['name'])) {

        if (!empty($logo) && file_exists("../images/programs/" . $logo)) {
            unlink("../images/programs/" . $logo);
        }

        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $logo = time() . "_logo." . $ext;

        move_uploaded_file(
            $_FILES['logo']['tmp_name'],
            "../images/programs/" . $logo
        );
    }

    // =========================
    // UPDATE SLIDESHOW
    // =========================
    if (!empty($_FILES['slideshow']['name'])) {

        if (!empty($slideshow) && file_exists("../images/programs/" . $slideshow)) {
            unlink("../images/programs/" . $slideshow);
        }

        $ext2 = pathinfo($_FILES['slideshow']['name'], PATHINFO_EXTENSION);
        $slideshow = time() . "_slide." . $ext2;

        move_uploaded_file(
            $_FILES['slideshow']['tmp_name'],
            "../images/programs/" . $slideshow
        );
    }

    // =========================
    // UPDATE DB
    // =========================
    $stmt = $con->prepare("
        UPDATE tblprogramm 
        SET ProgramName = ?, 
            ProgramDiscription = ?, 
            ProgramLogo = ?, 
            ProgramSlideshow = ?
        WHERE ProgramID = ?
    ");

    $stmt->execute([
        $name,
        $desc,
        $logo,
        $slideshow,
        $pid
    ]);

    echo "<script>location.href='managePrograms.php?do=manage';</script>";
    exit();
}?>
<form method="post" enctype="multipart/form-data">

    <!-- ================= NAME ================= -->
    <label>Program Name</label>
    <input type="text" name="name" value="<?= htmlspecialchars($p['ProgramName']) ?>" required>

    <!-- ================= DESCRIPTION ================= -->
    <label>Description</label>
    <textarea name="desc"><?= htmlspecialchars($p['ProgramDiscription']) ?></textarea>

    <hr>

    <!-- ================= CURRENT LOGO ================= -->
    <label>Current Logo</label><br>
    <?php if (!empty($p['ProgramLogo'])) { ?>
        <img src="../images/programs/<?= $p['ProgramLogo'] ?>" width="120">
    <?php } else { ?>
        <p>No logo</p>
    <?php } ?>

    <br><br>

    <label>Change Logo</label>
    <input type="file" name="logo" accept="image/*">

    <hr>

    <!-- ================= CURRENT SLIDESHOW ================= -->
    <label>Current Slideshow</label><br>
    <?php if (!empty($p['ProgramSlideshow'])) { ?>
        <img src="../images/programs/<?= $p['ProgramSlideshow'] ?>" width="120">
    <?php } else { ?>
        <p>No slideshow</p>
    <?php } ?>

    <br><br>

    <label>Change Slideshow</label>
    <input type="file" name="slideshow" accept="image/*">

    <hr>

    <!-- ================= BUTTONS ================= -->
    <button type="submit" name="updateProgram">Update</button>

    <a href="managePrograms.php?do=manage" style="margin-left:10px;">
        Cancel
    </a>

</form>
<?php
}

/* =========================================================
   DELETE PROGRAM
========================================================= */
elseif ($do === 'delete') {

$stmt = $con->prepare("DELETE FROM tblprogramm WHERE ProgramID=?");
$stmt->execute([$pid]);

echo "<script>location.href='managePrograms.php';</script>";
exit();
}

/* =========================================================
   VIEW PROGRAM (SECTIONS + ITEMS)
========================================================= */
elseif ($do === 'view') {

$stmt = $con->prepare("SELECT * FROM tblprogramm WHERE ProgramID=?");
$stmt->execute([$pid]);
$p = $stmt->fetch();
?>

<h2><?= $p['ProgramName'] ?></h2>

<p><?= $p['ProgramDiscription'] ?></p>

<hr>

<!-- ================= SECTIONS ================= -->
<h3>Sections</h3>

<a href="managePrograms.php?do=addSection&pid=<?= $pid ?>" >
    + Add Section
</a>

<div class="sections-wrapper">

<?php
$stmt = $con->prepare("SELECT * FROM tblprogramsection WHERE ProgramID=?");
$stmt->execute([$pid]);

while ($s = $stmt->fetch()) {
?>

    <div class="section-card">

        <!-- IMAGE LEFT -->
        <div class="section-image">
            <?php if (!empty($s['SectionPhoto'])) { ?>
                <img src="../images/programs/<?= $s['SectionPhoto'] ?>" alt="">
            <?php } else { ?>
                <img src="../images/programs/default.jpg" alt="">
            <?php } ?>
        </div>

        <!-- CONTENT RIGHT -->
        <div class="section-content">

            <h4><?= htmlspecialchars($s['SectionTitle']) ?></h4>

            <p><?= htmlspecialchars($s['SectionDiscription']) ?></p>

            <div class="section-actions">
                <a href="managePrograms.php?do=editSection&sid=<?= $s['SectionID'] ?>&pid=<?= $pid ?>">Edit</a>
                <a href="managePrograms.php?do=deleteSection&sid=<?= $s['SectionID'] ?>&pid=<?= $pid ?>">Delete</a>
            </div>

        </div>

    </div>

<?php } ?>

</div>

<hr>

<!-- ================= ITEMS ================= -->
<h3>Items in Program</h3>

<a href="managePrograms.php?do=addItemToProgram&pid=<?= $pid ?>">+ Add Item</a>

<div class="items-grid">

<?php
$stmt = $con->prepare("
    SELECT i.*
    FROM tblitems i
    JOIN tblitemprogram ip ON ip.ItemID = i.itmId
    WHERE ip.ProgramID=?
");
$stmt->execute([$pid]);

while ($i = $stmt->fetch()) {
?>

    <div class="item-card">

        <!-- IMAGE -->
        <div class="item-img">
            <img src="../images/items/<?= $i['mainpic'] ?>" alt="">
        </div>

        <!-- INFO -->
        <div class="item-info">
            <h4><?= htmlspecialchars($i['itmName']) ?></h4>
        </div>

        <!-- ACTION -->
        <div class="item-action">
            <a href="managePrograms.php?do=removeItemFromProgram&pid=<?= $pid ?>&iid=<?= $i['itmId'] ?>"
               onclick="return confirm('Remove this item?')">
                Remove
            </a>
        </div>

    </div>

<?php } ?>

</div>

<?php
}

/* =========================================================
   ADD SECTION
========================================================= */
elseif ($do === 'addSection') {

if (isset($_POST['saveSection'])) {

    $name = $_POST['name'] ?? '';

    $photo = "";

    // =========================
    // IMAGE UPLOAD
    // =========================
    if (!empty($_FILES['SectionPhoto']['name'])) {

        $ext = pathinfo($_FILES['SectionPhoto']['name'], PATHINFO_EXTENSION);

        // time.ext ONLY
        $photo = time() . "." . $ext;

        move_uploaded_file(
            $_FILES['SectionPhoto']['tmp_name'],
            "../images/programs/" . $photo
        );
    }

    // =========================
    // INSERT
    // =========================
    $stmt = $con->prepare("
        INSERT INTO tblprogramsection 
        (ProgramID, SectionTitle, SectionPhoto)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$pid, $name, $photo]);

    echo "<script>location.href='managePrograms.php?do=view&pid=$pid';</script>";
    exit();
}
?>

<form method="post" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Section Name" required>

    <label>Section Photo</label>
    <input type="file" name="SectionPhoto" accept="image/*">

    <button name="saveSection">Save</button>

</form>

<?php
}

/* =========================================================
   EDIT SECTION
========================================================= */
elseif ($do === 'editSection') {

$stmt = $con->prepare("SELECT * FROM tblprogramsection WHERE SectionID=?");
$stmt->execute([$sectionId]);
$s = $stmt->fetch();

if (isset($_POST['updateSection'])) {

    $name = $_POST['name'] ?? '';

    $photo = $s['SectionPhoto']; // keep old by default

    // =========================
    // NEW IMAGE UPLOAD
    // =========================
    if (!empty($_FILES['SectionPhoto']['name'])) {

        // DELETE OLD IMAGE
        if (!empty($s['SectionPhoto'])) {
            $oldPath = "../images/programs/" . $s['SectionPhoto'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // UPLOAD NEW IMAGE (time.ext)
        $ext = pathinfo($_FILES['SectionPhoto']['name'], PATHINFO_EXTENSION);
        $photo = time() . "." . $ext;

        move_uploaded_file(
            $_FILES['SectionPhoto']['tmp_name'],
            "../images/programs/" . $photo
        );
    }

    // =========================
    // UPDATE DB
    // =========================
    $stmt = $con->prepare("
        UPDATE tblprogramsection 
        SET SectionTitle=?, SectionPhoto=?
        WHERE SectionID=?
    ");

    $stmt->execute([$name, $photo, $sectionId]);

    echo "<script>location.href='managePrograms.php?do=view&pid=$pid';</script>";
    exit();
}
?>

<form method="post" enctype="multipart/form-data">

    <input type="text" name="name" value="<?= $s['SectionTitle'] ?>">

    <label>Section Photo</label>
    <input type="file" name="SectionPhoto" accept="image/*">

    <?php if (!empty($s['SectionPhoto'])): ?>
        <img src="../images/programs/<?= $s['SectionPhoto'] ?>" width="120">
    <?php endif; ?>

    <button name="updateSection">Update</button>

</form>

<?php
}

/* =========================================================
   DELETE SECTION
========================================================= */
elseif ($do === 'deleteSection') {

$stmt = $con->prepare("DELETE FROM tblprogramsection WHERE SectionID=?");
$stmt->execute([$sectionId]);

echo "<script>location.href='managePrograms.php?do=view&pid=$pid';</script>";
exit();
}

/* =========================================================
   ADD ITEM TO PROGRAM
========================================================= */
elseif ($do === 'addItemToProgram') {

if (isset($_POST['addItem'])) {
    $itemId = $_POST['itemId'];

    $stmt = $con->prepare("INSERT INTO tblitemprogram (ProgramID, ItemID) VALUES (?, ?)");
    $stmt->execute([$pid, $itemId]);

   echo "<script>location.href='managePrograms.php?do=view&pid=$pid';</script>";
    exit();
}
?>

<form method="post">

    <select name="itemId" id="itemSelect">

        <option value="">-- Select Item --</option>

        <?php
        $items = $con->query("SELECT * FROM tblitems");

        while ($i = $items->fetch()) {
        ?>
            <option 
                value="<?= $i['itmId'] ?>"
                data-img="<?= $i['mainpic'] ?>"
            >
                <?= $i['itmName'] ?>
            </option>
        <?php } ?>

    </select>

    <!-- IMAGE PREVIEW -->
    <div style="margin-top:10px;">
        <img id="itemPreview" src="" width="80" style="display:none; border-radius:10px;">
    </div>

    <button name="addItem">Add</button>

</form>
<script>document.addEventListener("DOMContentLoaded", function () {

    const select = document.getElementById("itemSelect");
    const preview = document.getElementById("itemPreview");

    if (!select) return;

    select.addEventListener("change", function () {

        const option = this.options[this.selectedIndex];
        const img = option.getAttribute("data-img");

        if (img) {
            preview.src = "../images/items/" + img;
            preview.style.display = "block";
        } else {
            preview.style.display = "none";
        }

    });

});</script>
<?php
}

/* =========================================================
   REMOVE ITEM FROM PROGRAM
========================================================= */
elseif ($do === 'removeItemFromProgram') {

$stmt = $con->prepare("DELETE FROM tblitemprogram WHERE ProgramID=? AND ItemID=?");
$stmt->execute([$pid, $itemId]);

echo "<script>location.href='managePrograms.php?do=view&pid=$pid';</script>";
exit();
}
?>

</div>
</main>
<script src="js/managePrograms.js"></script>
</body>