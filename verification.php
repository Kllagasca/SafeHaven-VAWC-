<?php
include 'config/function.php';
include 'config/db_connect.php';

// verification.php?id=<survey_id>
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die('Invalid survey id');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';

    if ($name === '') $errors[] = 'Name is required';
    if ($gender === '') $errors[] = 'Gender is required';
    if ($barangay === '') $errors[] = 'Barangay is required';

    // Handle ID image upload
    $imagePath = null;
    if (isset($_FILES['id_image']) && $_FILES['id_image']['size'] > 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['id_image']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) {
            $errors[] = 'ID image must be JPG or PNG';
        } else {
            $uploadDir = __DIR__ . '/assets/uploads/verifications/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($_FILES['id_image']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $dest = $uploadDir . $filename;
            if (!move_uploaded_file($_FILES['id_image']['tmp_name'], $dest)) {
                $errors[] = 'Failed to move uploaded file';
            } else {
                // store web-accessible path
                $imagePath = 'assets/uploads/verifications/' . $filename;
            }
        }
    }

    if (empty($errors)) {
        $safeName = mysqli_real_escape_string($conn, $name);
        $safeGender = mysqli_real_escape_string($conn, $gender);
    $safeAddress = mysqli_real_escape_string($conn, $barangay);
        $safeImage = $imagePath ? mysqli_real_escape_string($conn, $imagePath) : null;

        $insSql = "INSERT INTO survey_verifications (survey_id, name, gender, address, image, status) VALUES ($id, '$safeName', '$safeGender', '$safeAddress', " . ($safeImage ? "'$safeImage'" : "NULL") . ", 'pending')";
        if (mysqli_query($conn, $insSql)) {
            $verId = mysqli_insert_id($conn);
            // remember in session so the survey page will attach responses to this verification
            if (!isset($_SESSION['survey_verification'])) $_SESSION['survey_verification'] = [];
            $_SESSION['survey_verification'][$id] = $verId;
            // redirect to survey
            header('Location: survey.php?id=' . $id);
            exit();
        } else {
            $errors[] = 'Failed to save verification';
        }
    }
}

// Fetch survey name for context
$surveyRes = mysqli_query($conn, "SELECT name FROM surveys WHERE id = $id LIMIT 1");
$survey = $surveyRes && mysqli_num_rows($surveyRes) ? mysqli_fetch_assoc($surveyRes) : null;

// Preserve selected barangay for form repopulation
$selectedBarangay = isset($_POST['barangay']) ? $_POST['barangay'] : '';
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/navbar.php'; ?>
<div class="container my-5">
    <div class="card p-4">
        <h3>Verification for: <?= htmlspecialchars($survey['name'] ?? 'Survey') ?></h3>
        <p>Please provide your name, gender, barangay and upload a photo of your ID to proceed to the survey. Your submission will be reviewed by an admin.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select">
                    <option value="">Select</option>
                    <option value="Male" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= (isset($_POST['gender']) && $_POST['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Barangay</label>
                <select name="barangay" class="form-select barangay-select">
                    <option value="">Select Barangay</option>
                    <option value="I-A (Sambat)" <?= $selectedBarangay === 'I-A (Sambat)' ? 'selected' : '' ?>>I-A (Sambat)</option>
                            <option value="I-B (City Sub Riverside)" <?= $selectedBarangay === 'I-B (City Sub Riverside)' ? 'selected' : '' ?>>I-B (City Sub Riverside)</option>
                            <option value="I-C (Bagong Bayan)" <?= $selectedBarangay === 'I-C (Bagong Bayan)' ? 'selected' : '' ?>>I-C (Bagong Bayan)</option>
                            <option value="II-A (Triangulo/ Guadalupe 2)" <?= $selectedBarangay === 'II-A (Triangulo/ Guadalupe 2)' ? 'selected' : '' ?>>II-A (Triangulo/ Guadalupe 2)</option>
                            <option value="II-B (Guadalupe 1)" <?= $selectedBarangay === 'II-B (Guadalupe 1)' ? 'selected' : '' ?>>II-B (Guadalupe 1)</option>
                            <option value="II-C (Unson)" <?= $selectedBarangay === 'II-C (Unson)' ? 'selected' : '' ?>>II-C (Unson)</option>
                            <option value="II-D (Bulante)" <?= $selectedBarangay === 'II-D (Bulante)' ? 'selected' : '' ?>>II-D (Bulante)</option>
                            <option value="II-E (San Anton)" <?= $selectedBarangay === 'II-E (San Anton)' ? 'selected' : '' ?>>II-E (San Anton)</option>
                            <option value="II-F (Villa Rey)" <?= $selectedBarangay === 'II-F (Villa Rey)' ? 'selected' : '' ?>>II-F (Villa Rey)</option>
                            <option value="III-A (Hermanos Belen)" <?= $selectedBarangay === 'III-A (Hermanos Belen)' ? 'selected' : '' ?>>III-A (Hermanos Belen)</option>
                            <option value="III-B" <?= $selectedBarangay === 'III-B' ? 'selected' : '' ?>>III-B</option>
                            <option value="III-C (Labak/De Roma)" <?= $selectedBarangay === 'III-C (Labak/De Roma)' ? 'selected' : '' ?>>III-C (Labak/De Roma)</option>
                            <option value="III-D (Villongco)" <?= $selectedBarangay === 'III-D (Villongco)' ? 'selected' : '' ?>>III-D (Villongco)</option>
                            <option value="III-E" <?= $selectedBarangay === 'III-E' ? 'selected' : '' ?>>III-E</option>
                            <option value="III-F (Balagtas)" <?= $selectedBarangay === 'III-F (Balagtas)' ? 'selected' : '' ?>>III-F (Balagtas)</option>
                            <option value="IV-A" <?= $selectedBarangay === 'IV-A' ? 'selected' : '' ?>>IV-A</option>
                            <option value="IV-B" <?= $selectedBarangay === 'IV-B' ? 'selected' : '' ?>>IV-B</option>
                            <option value="IV-C" <?= $selectedBarangay === 'IV-C' ? 'selected' : '' ?>>IV-C</option>
                            <option value="V-A" <?= $selectedBarangay === 'V-A' ? 'selected' : '' ?>>V-A</option>
                            <option value="V-B" <?= $selectedBarangay === 'V-B' ? 'selected' : '' ?>>V-B</option>
                            <option value="V-C" <?= $selectedBarangay === 'V-C' ? 'selected' : '' ?>>V-C</option>
                            <option value="V-D" <?= $selectedBarangay === 'V-D' ? 'selected' : '' ?>>V-D</option>
                            <option value="VI-A (Mavenida)" <?= $selectedBarangay === 'VI-A (Mavenida)' ? 'selected' : '' ?>>VI-A (Mavenida)</option>
                            <option value="VI-B (Sabang Mabini)" <?= $selectedBarangay === 'VI-B (Sabang Mabini)' ? 'selected' : '' ?>>VI-B (Sabang Mabini)</option>
                            <option value="VI-C (Bagong Pook)" <?= $selectedBarangay === 'VI-C (Bagong Pook)' ? 'selected' : '' ?>>VI-C (Bagong Pook)</option>
                            <option value="VI-D (Lakeside)" <?= $selectedBarangay === 'VI-D (Lakeside)' ? 'selected' : '' ?>>VI-D (Lakeside)</option>
                            <option value="VI-E (YMCA)" <?= $selectedBarangay === 'VI-E (YMCA)' ? 'selected' : '' ?>>VI-E (YMCA)</option>
                            <option value="VII-A (P.Alcantara)" <?= $selectedBarangay === 'VII-A (P.Alcantara)' ? 'selected' : '' ?>>VII-A (P.Alcantara)</option>
                            <option value="VII-B" <?= $selectedBarangay === 'VII-B' ? 'selected' : '' ?>>VII-B</option>
                            <option value="VII-C" <?= $selectedBarangay === 'VII-C' ? 'selected' : '' ?>>VII-C</option>
                            <option value="VII-D" <?= $selectedBarangay === 'VII-D' ? 'selected' : '' ?>>VII-D</option>
                            <option value="VII-E" <?= $selectedBarangay === 'VII-E' ? 'selected' : '' ?>>VII-E</option>
                            <option value="Atisan" <?= $selectedBarangay === 'Atisan' ? 'selected' : '' ?>>Atisan</option>
                            <option value="Bautista" <?= $selectedBarangay === 'Bautista' ? 'selected' : '' ?>>Bautista</option>
                            <option value="Concepcion (Bunot)" <?= $selectedBarangay === 'Concepcion (Bunot)' ? 'selected' : '' ?>>Concepcion (Bunot)</option>
                            <option value="Del Remedio (Wawa)" <?= $selectedBarangay === 'Del Remedio (Wawa)' ? 'selected' : '' ?>>Del Remedio (Wawa)</option>
                            <option value="Dolores" <?= $selectedBarangay === 'Dolores' ? 'selected' : '' ?>>Dolores</option>
                            <option value="San Antonio 1 (Balanga)" <?= $selectedBarangay === 'San Antonio 1 (Balanga)' ? 'selected' : '' ?>>San Antonio 1 (Balanga)</option>
                            <option value="San Antonio 2 (Sapa)" <?= $selectedBarangay === 'San Antonio 2 (Sapa)' ? 'selected' : '' ?>>San Antonio 2 (Sapa)</option>
                            <option value="San Bartolome (Matang-ag)" <?= $selectedBarangay === 'San Bartolome (Matang-ag)' ? 'selected' : '' ?>>San Bartolome (Matang-ag)</option>
                            <option value="San Buenaventura (Palakpakin)" <?= $selectedBarangay === 'San Buenaventura (Palakpakin)' ? 'selected' : '' ?>>San Buenaventura (Palakpakin)</option>
                            <option value="San Crispin (Lumbangan)" <?= $selectedBarangay === 'San Crispin (Lumbangan)' ? 'selected' : '' ?>>San Crispin (Lumbangan)</option>
                            <option value="San Cristobal" <?= $selectedBarangay === 'San Cristobal' ? 'selected' : '' ?>>San Cristobal</option>
                            <option value="San Diego (Tiim)" <?= $selectedBarangay === 'San Diego (Tiim)' ? 'selected' : '' ?>>San Diego (Tiim)</option>
                            <option value="San Francisco (Calihan)" <?= $selectedBarangay === 'San Francisco (Calihan)' ? 'selected' : '' ?>>San Francisco (Calihan)</option>
                            <option value="San Gabriel (Butucan)" <?= $selectedBarangay === 'San Gabriel (Butucan)' ? 'selected' : '' ?>>San Gabriel (Butucan)</option>
                            <option value="San Gregorio" <?= $selectedBarangay === 'San Gregorio' ? 'selected' : '' ?>>San Gregorio</option>
                            <option value="San Ignacio" <?= $selectedBarangay === 'San Ignacio' ? 'selected' : '' ?>>San Ignacio</option>
                            <option value="San Isidro (Balagbag)" <?= $selectedBarangay === 'San Isidro (Balagbag)' ? 'selected' : '' ?>>San Isidro (Balagbag)</option>
                            <option value="San Joaquin" <?= $selectedBarangay === 'San Joaquin' ? 'selected' : '' ?>>San Joaquin</option>
                            <option value="San Jose (Malamig)" <?= $selectedBarangay === 'San Jose (Malamig)' ? 'selected' : '' ?>>San Jose (Malamig)</option>
                            <option value="San Juan (Putol)" <?= $selectedBarangay === 'San Juan (Putol)' ? 'selected' : '' ?>>San Juan (Putol)</option>
                            <option value="San Lorenzo (Saluyan)" <?= $selectedBarangay === 'San Lorenzo (Saluyan)' ? 'selected' : '' ?>>San Lorenzo (Saluyan)</option>
                            <option value="San Lucas 1 (Malinaw)" <?= $selectedBarangay === 'San Lucas 1 (Malinaw)' ? 'selected' : '' ?>>San Lucas 1 (Malinaw)</option>
                            <option value="San Lucas 2 (Malinaw)" <?= $selectedBarangay === 'San Lucas 2 (Malinaw)' ? 'selected' : '' ?>>San Lucas 2 (Malinaw)</option>
                            <option value="San Marcos (Tikew)" <?= $selectedBarangay === 'San Marcos (Tikew)' ? 'selected' : '' ?>>San Marcos (Tikew)</option>
                            <option value="San Mateo (Imok)" <?= $selectedBarangay === 'San Mateo (Imok)' ? 'selected' : '' ?>>San Mateo (Imok)</option>
                            <option value="San Miguel (Balatuin)" <?= $selectedBarangay === 'San Miguel (Balatuin)' ? 'selected' : '' ?>>San Miguel (Balatuin)</option>
                            <option value="San Nicolas (Mag-ampon)" <?= $selectedBarangay === 'San Nicolas (Mag-ampon)' ? 'selected' : '' ?>>San Nicolas (Mag-ampon)</option>
                            <option value="San Pedro" <?= $selectedBarangay === 'San Pedro' ? 'selected' : '' ?>>San Pedro</option>
                            <option value="San Rafael (Buluburan)" <?= $selectedBarangay === 'San Rafael (Buluburan)' ? 'selected' : '' ?>>San Rafael (Buluburan)</option>
                            <option value="San Roque (Sambat)" <?= $selectedBarangay === 'San Roque (Sambat)' ? 'selected' : '' ?>>San Roque (Sambat)</option>
                            <option value="San Vicente" <?= $selectedBarangay === 'San Vicente' ? 'selected' : '' ?>>San Vicente</option>
                            <option value="Santa Ana" <?= $selectedBarangay === 'Santa Ana' ? 'selected' : '' ?>>Santa Ana</option>
                            <option value="Santa Catalina (Sandig)" <?= $selectedBarangay === 'Santa Catalina (Sandig)' ? 'selected' : '' ?>>Santa Catalina (Sandig)</option>
                            <option value="Santa Cruz (Putol)" <?= $selectedBarangay === 'Santa Cruz (Putol)' ? 'selected' : '' ?>>Santa Cruz (Putol)</option>
                            <option value="Santa Elena" <?= $selectedBarangay === 'Santa Elena' ? 'selected' : '' ?>>Santa Elena</option>
                            <option value="Santa Filomena (Banlagin)" <?= $selectedBarangay === 'Santa Filomena (Banlagin)' ? 'selected' : '' ?>>Santa Filomena (Banlagin)</option>
                            <option value="Santa Isabel" <?= $selectedBarangay === 'Santa Isabel' ? 'selected' : '' ?>>Santa Isabel</option>
                            <option value="Santa Maria" <?= $selectedBarangay === 'Santa Maria' ? 'selected' : '' ?>>Santa Maria</option>
                            <option value="Santa Maria Magdalena (Boe / Kuba)" <?= $selectedBarangay === 'Santa Maria Magdalena (Boe / Kuba)' ? 'selected' : '' ?>>Santa Maria Magdalena (Boe / Kuba)</option>
                            <option value="Santa Monica" <?= $selectedBarangay === 'Santa Monica' ? 'selected' : '' ?>>Santa Monica</option>
                            <option value="Santa Veronica (Bae)" <?= $selectedBarangay === 'Santa Veronica (Bae)' ? 'selected' : '' ?>>Santa Veronica (Bae)</option>
                            <option value="Santiago I (Bulaho)" <?= $selectedBarangay === 'Santiago I (Bulaho)' ? 'selected' : '' ?>>Santiago I (Bulaho)</option>
                            <option value="Santiago II (Bulaho)" <?= $selectedBarangay === 'Santiago II (Bulaho)' ? 'selected' : '' ?>>Santiago II (Bulaho)</option>
                            <option value="Santisimo Rosario (Balagbag)" <?= $selectedBarangay === 'Santisimo Rosario (Balagbag)' ? 'selected' : '' ?>>Santisimo Rosario (Balagbag)</option>
                            <option value="Santo Angel (Ilog)" <?= $selectedBarangay === 'Santo Angel (Ilog)' ? 'selected' : '' ?>>Santo Angel (Ilog)</option>
                            <option value="Santo Cristo" <?= $selectedBarangay === 'Santo Cristo' ? 'selected' : '' ?>>Santo Cristo</option>
                            <option value="Santo Niño (Arsum)" <?= $selectedBarangay === 'Santo Niño (Arsum)' ? 'selected' : '' ?>>Santo Niño (Arsum)</option>
                            <option value="Soledad (Macopa)" <?= $selectedBarangay === 'Soledad (Macopa)' ? 'selected' : '' ?>>Soledad (Macopa)</option>
                        </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Upload ID (JPG/PNG)</label>
                <input type="file" name="id_image" accept="image/*" class="form-control">
            </div>
            <div class="d-flex justify-content-end">
                <a href="index.php" class="btn btn-secondary me-2">Cancel</a>
                <button class="btn btn-primary" type="submit">Proceed to Survey</button>
            </div>
        </form>
    </div>
</div>
<!-- Add Select2 (searchable dropdown) so the barangay choices render nicely and open downward inside the card -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    function initSelect2() {
        if (typeof jQuery === 'undefined') return;
        // load Select2 CSS if not present
        if (!document.querySelector('link[href*="select2.min.css"]')) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css';
            document.head.appendChild(link);
        }
        // load Select2 JS if not present
        if (typeof jQuery.fn.select2 === 'undefined') {
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
            s.onload = function () {
                jQuery('.barangay-select').each(function () {
                    var $this = jQuery(this);
                    var container = $this.closest('.col-md-4, .form-group, .card-body, .card') || $this.parent();
                    $this.select2({
                        placeholder: 'Select Barangay',
                        width: '100%',
                        dropdownParent: container
                    });
                });
            };
            document.body.appendChild(s);
        } else {
            jQuery('.barangay-select').each(function () {
                var $this = jQuery(this);
                var container = $this.closest('.col-md-4, .form-group, .card-body, .card') || $this.parent();
                $this.select2({
                    placeholder: 'Select Barangay',
                    width: '100%',
                    dropdownParent: container
                });
            });
        }
    }

    // Ensure jQuery is available
    if (typeof jQuery === 'undefined') {
        var jq = document.createElement('script');
        jq.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        jq.onload = initSelect2;
        document.body.appendChild(jq);
    } else {
        initSelect2();
    }
});
</script>
</body>
</html>