<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Edit Case
                    <a href="cases.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

            <?= alertmessage(); ?>

            <form action="code.php" method="POST" enctype="multipart/form-data">

            <?php

                // Support both legacy ?caseno= and new ?id= (local numeric id). Prefer id if provided.
                $case = null;
                if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
                    $id = (int) $_GET['id'];
                    $query = "SELECT * FROM cases WHERE id = :id LIMIT 1";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([':id' => $id]);
                    $case = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if (!$case) {
                    if (!isset($_GET['caseno']) || empty($_GET['caseno'])) {
                        echo "<h5>Invalid or missing case identifier.</h5>";
                        return;
                    }
                    $caseno = validate($_GET['caseno']);
                    $query = "SELECT * FROM cases WHERE caseno = :caseno";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':caseno', $caseno);
                    $stmt->execute();
                    $case = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($case):
            ?>

            <input type="hidden" name="case_id" value="<?= htmlspecialchars($case['id'] ?? '') ?>">
            <input type="hidden" name="caseno" value="<?= htmlspecialchars($case['caseno']) ?>">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($case['image']) ?>">

            <!-- Case Details -->
            <h4>Case Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Case No.</label>
                    <input type="text" name="casenum_display" value="<?= htmlspecialchars($case['caseno']) ?>" class="form-control" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Case Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($case['title']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Case Status</label>
                    <select name="status" class="form-select">
                        <option value="0" <?= $case['status'] == '0' || $case['status'] == 0 ? 'selected' : '' ?>>Open Case</option>
                        <option value="1" <?= $case['status'] == '1' || $case['status'] == 1 ? 'selected' : '' ?>>Closed Case</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Incident Location</label>
                    <select name="barangay" class="form-select barangay-select" required>
                        <option value="">Select Barangay (Incident Location)</option>
                        <option value="I-A (Sambat)" <?= $case['brgy'] == 'I-A (Sambat)' ? 'selected' : '' ?>>I-A (Sambat)</option>
                        <option value="I-B (City Sub Riverside)" <?= $case['brgy'] == 'I-B (City Sub Riverside)' ? 'selected' : '' ?>>I-B (City Sub Riverside)</option>
                        <option value="I-C (Bagong Bayan)" <?= $case['brgy'] == 'I-C (Bagong Bayan)' ? 'selected' : '' ?>>I-C (Bagong Bayan)</option>
                        <option value="II-A (Triangulo/ Guadalupe 2)" <?= $case['brgy'] == 'II-A (Triangulo/ Guadalupe 2)' ? 'selected' : '' ?>>II-A (Triangulo/ Guadalupe 2)</option>
                        <option value="II-B (Guadalupe 1)" <?= $case['brgy'] == 'II-B (Guadalupe 1)' ? 'selected' : '' ?>>II-B (Guadalupe 1)</option>
                        <option value="II-C (Unson)" <?= $case['brgy'] == 'II-C (Unson)' ? 'selected' : '' ?>>II-C (Unson)</option>
                        <option value="II-D (Bulante)" <?= $case['brgy'] == 'II-D (Bulante)' ? 'selected' : '' ?>>II-D (Bulante)</option>
                        <option value="II-E (San Anton)" <?= $case['brgy'] == 'II-E (San Anton)' ? 'selected' : '' ?>>II-E (San Anton)</option>
                        <option value="II-F (Villa Rey)" <?= $case['brgy'] == 'II-F (Villa Rey)' ? 'selected' : '' ?>>II-F (Villa Rey)</option>
                        <option value="III-A (Hermanos Belen)" <?= $case['brgy'] == 'III-A (Hermanos Belen)' ? 'selected' : '' ?>>III-A (Hermanos Belen)</option>
                        <option value="III-B" <?= $case['brgy'] == 'III-B' ? 'selected' : '' ?>>III-B</option>
                        <option value="III-C (Labak/De Roma)" <?= $case['brgy'] == 'III-C (Labak/De Roma)' ? 'selected' : '' ?>>III-C (Labak/De Roma)</option>
                        <option value="III-D (Villongco)" <?= $case['brgy'] == 'III-D (Villongco)' ? 'selected' : '' ?>>III-D (Villongco)</option>
                        <option value="III-E" <?= $case['brgy'] == 'III-E' ? 'selected' : '' ?>>III-E</option>
                        <option value="III-F (Balagtas)" <?= $case['brgy'] == 'III-F (Balagtas)' ? 'selected' : '' ?>>III-F (Balagtas)</option>
                        <option value="IV-A" <?= $case['brgy'] == 'IV-A' ? 'selected' : '' ?>>IV-A</option>
                        <option value="IV-B" <?= $case['brgy'] == 'IV-B' ? 'selected' : '' ?>>IV-B</option>
                        <option value="IV-C" <?= $case['brgy'] == 'IV-C' ? 'selected' : '' ?>>IV-C</option>
                        <option value="V-A" <?= $case['brgy'] == 'V-A' ? 'selected' : '' ?>>V-A</option>
                        <option value="V-B" <?= $case['brgy'] == 'V-B' ? 'selected' : '' ?>>V-B</option>
                        <option value="V-C" <?= $case['brgy'] == 'V-C' ? 'selected' : '' ?>>V-C</option>
                        <option value="V-D" <?= $case['brgy'] == 'V-D' ? 'selected' : '' ?>>V-D</option>
                        <option value="VI-A (Mavenida)" <?= $case['brgy'] == 'VI-A (Mavenida)' ? 'selected' : '' ?>>VI-A (Mavenida)</option>
                        <option value="VI-B (Sabang Mabini)" <?= $case['brgy'] == 'VI-B (Sabang Mabini)' ? 'selected' : '' ?>>VI-B (Sabang Mabini)</option>
                        <option value="VI-C (Bagong Pook)" <?= $case['brgy'] == 'VI-C (Bagong Pook)' ? 'selected' : '' ?>>VI-C (Bagong Pook)</option>
                        <option value="VI-D (Lakeside)" <?= $case['brgy'] == 'VI-D (Lakeside)' ? 'selected' : '' ?>>VI-D (Lakeside)</option>
                        <option value="VI-E (YMCA)" <?= $case['brgy'] == 'VI-E (YMCA)' ? 'selected' : '' ?>>VI-E (YMCA)</option>
                        <option value="VII-A (P.Alcantara)" <?= $case['brgy'] == 'VII-A (P.Alcantara)' ? 'selected' : '' ?>>VII-A (P.Alcantara)</option>
                        <option value="VII-B" <?= $case['brgy'] == 'VII-B' ? 'selected' : '' ?>>VII-B</option>
                        <option value="VII-C" <?= $case['brgy'] == 'VII-C' ? 'selected' : '' ?>>VII-C</option>
                        <option value="VII-D" <?= $case['brgy'] == 'VII-D' ? 'selected' : '' ?>>VII-D</option>
                        <option value="VII-E" <?= $case['brgy'] == 'VII-E' ? 'selected' : '' ?>>VII-E</option>
                        <option value="Atisan" <?= $case['brgy'] == 'Atisan' ? 'selected' : '' ?>>Atisan</option>
                        <option value="Bautista" <?= $case['brgy'] == 'Bautista' ? 'selected' : '' ?>>Bautista</option>
                        <option value="Concepcion (Bunot)" <?= $case['brgy'] == 'Concepcion (Bunot)' ? 'selected' : '' ?>>Concepcion (Bunot)</option>
                        <option value="Del Remedio (Wawa)" <?= $case['brgy'] == 'Del Remedio (Wawa)' ? 'selected' : '' ?>>Del Remedio (Wawa)</option>
                        <option value="Dolores" <?= $case['brgy'] == 'Dolores' ? 'selected' : '' ?>>Dolores</option>
                        <option value="San Antonio 1 (Balanga)" <?= $case['brgy'] == 'San Antonio 1 (Balanga)' ? 'selected' : '' ?>>San Antonio 1 (Balanga)</option>
                        <option value="San Antonio 2 (Sapa)" <?= $case['brgy'] == 'San Antonio 2 (Sapa)' ? 'selected' : '' ?>>San Antonio 2 (Sapa)</option>
                        <option value="San Bartolome (Matang-ag)" <?= $case['brgy'] == 'San Bartolome (Matang-ag)' ? 'selected' : '' ?>>San Bartolome (Matang-ag)</option>
                        <option value="San Buenaventura (Palakpakin)" <?= $case['brgy'] == 'San Buenaventura (Palakpakin)' ? 'selected' : '' ?>>San Buenaventura (Palakpakin)</option>
                        <option value="San Crispin (Lumbangan)" <?= $case['brgy'] == 'San Crispin (Lumbangan)' ? 'selected' : '' ?>>San Crispin (Lumbangan)</option>
                        <option value="San Cristobal" <?= $case['brgy'] == 'San Cristobal' ? 'selected' : '' ?>>San Cristobal</option>
                        <option value="San Diego (Tiim)" <?= $case['brgy'] == 'San Diego (Tiim)' ? 'selected' : '' ?>>San Diego (Tiim)</option>
                        <option value="San Francisco (Calihan)" <?= $case['brgy'] == 'San Francisco (Calihan)' ? 'selected' : '' ?>>San Francisco (Calihan)</option>
                        <option value="San Gabriel (Butucan)" <?= $case['brgy'] == 'San Gabriel (Butucan)' ? 'selected' : '' ?>>San Gabriel (Butucan)</option>
                        <option value="San Gregorio" <?= $case['brgy'] == 'San Gregorio' ? 'selected' : '' ?>>San Gregorio</option>
                        <option value="San Ignacio" <?= $case['brgy'] == 'San Ignacio' ? 'selected' : '' ?>>San Ignacio</option>
                        <option value="San Isidro (Balagbag)" <?= $case['brgy'] == 'San Isidro (Balagbag)' ? 'selected' : '' ?>>San Isidro (Balagbag)</option>
                        <option value="San Joaquin" <?= $case['brgy'] == 'San Joaquin' ? 'selected' : '' ?>>San Joaquin</option>
                        <option value="San Jose (Malamig)" <?= $case['brgy'] == 'San Jose (Malamig)' ? 'selected' : '' ?>>San Jose (Malamig)</option>
                        <option value="San Juan (Putol)" <?= $case['brgy'] == 'San Juan (Putol)' ? 'selected' : '' ?>>San Juan (Putol)</option>
                        <option value="San Lorenzo (Saluyan)" <?= $case['brgy'] == 'San Lorenzo (Saluyan)' ? 'selected' : '' ?>>San Lorenzo (Saluyan)</option>
                        <option value="San Lucas 1 (Malinaw)" <?= $case['brgy'] == 'San Lucas 1 (Malinaw)' ? 'selected' : '' ?>>San Lucas 1 (Malinaw)</option>
                        <option value="San Lucas 2 (Malinaw)" <?= $case['brgy'] == 'San Lucas 2 (Malinaw)' ? 'selected' : '' ?>>San Lucas 2 (Malinaw)</option>
                        <option value="San Marcos (Tikew)" <?= $case['brgy'] == 'San Marcos (Tikew)' ? 'selected' : '' ?>>San Marcos (Tikew)</option>
                        <option value="San Mateo (Imok)" <?= $case['brgy'] == 'San Mateo (Imok)' ? 'selected' : '' ?>>San Mateo (Imok)</option>
                        <option value="San Miguel (Balatuin)" <?= $case['brgy'] == 'San Miguel (Balatuin)' ? 'selected' : '' ?>>San Miguel (Balatuin)</option>
                        <option value="San Nicolas (Mag-ampon)" <?= $case['brgy'] == 'San Nicolas (Mag-ampon)' ? 'selected' : '' ?>>San Nicolas (Mag-ampon)</option>
                        <option value="San Pedro" <?= $case['brgy'] == 'San Pedro' ? 'selected' : '' ?>>San Pedro</option>
                        <option value="San Rafael (Buluburan)" <?= $case['brgy'] == 'San Rafael (Buluburan)' ? 'selected' : '' ?>>San Rafael (Buluburan)</option>
                        <option value="San Roque (Sambat)" <?= $case['brgy'] == 'San Roque (Sambat)' ? 'selected' : '' ?>>San Roque (Sambat)</option>
                        <option value="San Vicente" <?= $case['brgy'] == 'San Vicente' ? 'selected' : '' ?>>San Vicente</option>
                        <option value="Santa Ana" <?= $case['brgy'] == 'Santa Ana' ? 'selected' : '' ?>>Santa Ana</option>
                        <option value="Santa Catalina (Sandig)" <?= $case['brgy'] == 'Santa Catalina (Sandig)' ? 'selected' : '' ?>>Santa Catalina (Sandig)</option>
                        <option value="Santa Cruz (Putol)" <?= $case['brgy'] == 'Santa Cruz (Putol)' ? 'selected' : '' ?>>Santa Cruz (Putol)</option>
                        <option value="Santa Elena" <?= $case['brgy'] == 'Santa Elena' ? 'selected' : '' ?>>Santa Elena</option>
                        <option value="Santa Filomena (Banlagin)" <?= $case['brgy'] == 'Santa Filomena (Banlagin)' ? 'selected' : '' ?>>Santa Filomena (Banlagin)</option>
                        <option value="Santa Isabel" <?= $case['brgy'] == 'Santa Isabel' ? 'selected' : '' ?>>Santa Isabel</option>
                        <option value="Santa Maria" <?= $case['brgy'] == 'Santa Maria' ? 'selected' : '' ?>>Santa Maria</option>
                        <option value="Santa Maria Magdalena (Boe / Kuba)" <?= $case['brgy'] == 'Santa Maria Magdalena (Boe / Kuba)' ? 'selected' : '' ?>>Santa Maria Magdalena (Boe / Kuba)</option>
                        <option value="Santa Monica" <?= $case['brgy'] == 'Santa Monica' ? 'selected' : '' ?>>Santa Monica</option>
                        <option value="Santa Veronica (Bae)" <?= $case['brgy'] == 'Santa Veronica (Bae)' ? 'selected' : '' ?>>Santa Veronica (Bae)</option>
                        <option value="Santiago I (Bulaho)" <?= $case['brgy'] == 'Santiago I (Bulaho)' ? 'selected' : '' ?>>Santiago I (Bulaho)</option>
                        <option value="Santiago II (Bulaho)" <?= $case['brgy'] == 'Santiago II (Bulaho)' ? 'selected' : '' ?>>Santiago II (Bulaho)</option>
                        <option value="Santisimo Rosario (Balagbag)" <?= $case['brgy'] == 'Santisimo Rosario (Balagbag)' ? 'selected' : '' ?>>Santisimo Rosario (Balagbag)</option>
                        <option value="Santo Angel (Ilog)" <?= $case['brgy'] == 'Santo Angel (Ilog)' ? 'selected' : '' ?>>Santo Angel (Ilog)</option>
                        <option value="Santo Cristo" <?= $case['brgy'] == 'Santo Cristo' ? 'selected' : '' ?>>Santo Cristo</option>
                        <option value="Santo Niño (Arsum)" <?= $case['brgy'] == 'Santo Niño (Arsum)' ? 'selected' : '' ?>>Santo Niño (Arsum)</option>
                        <option value="Soledad (Macopa)" <?= $case['brgy'] == 'Soledad (Macopa)' ? 'selected' : '' ?>>Soledad (Macopa)</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Date of Incident</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($case['date']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Person</label>
                    <input type="text" name="contactp" value="<?= htmlspecialchars($case['contactp']) ?>" class="form-control" required>
                </div>
            </div>

            <!-- Complainant -->
            <h4>Complainant Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name</label>
                    <input type="text" name="complainant" value="<?= htmlspecialchars($case['comp_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Age</label>
                    <input type="text" name="cage" value="<?= htmlspecialchars($case['comp_age']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="cnum" value="<?= htmlspecialchars($case['comp_num']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="caddress" value="<?= htmlspecialchars($case['comp_address']) ?>" class="form-control" required>
            </div>

            <!-- Respondent -->
            <h4>Respondent Details</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Name</label>
                    <input type="text" name="respondent" value="<?= htmlspecialchars($case['resp_name']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Age</label>
                    <input type="text" name="rage" value="<?= htmlspecialchars($case['resp_age']) ?>" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Contact Number</label>
                    <input type="text" name="rnum" value="<?= htmlspecialchars($case['resp_num']) ?>" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Address</label>
                <input type="text" name="raddress" value="<?= htmlspecialchars($case['resp_address']) ?>" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Case Description</label>
                <textarea name="long_description" class="form-control mySummernote" rows="4"><?= htmlspecialchars($case['long_description']) ?></textarea>
            </div>

                <div class="mb-3">
                <label>Upload Case/Evidence Image</label>
                <input type="file" name="image" class="form-control">
                <?php if (!empty($case['image'])): ?>
                    <?php
                    $raw = trim($case['image']);
                    if (preg_match('#^https?://#i', $raw)) {
                        $preview = htmlspecialchars($raw);
                    } elseif (preg_match('#assets[\\/]+uploads[\\/]+(.+)#i', $raw, $m)) {
                        $preview = '../' . 'assets/uploads/' . str_replace('\\', '/', $m[1]);
                    } else {
                        $preview = '../assets/uploads/cases/' . ltrim(str_replace('\\', '/', $raw), '/');
                    }
                    ?>
                    <img src="<?= htmlspecialchars($preview) ?>" width="100" height="100" class="mt-2" alt="Old Image">
                <?php endif; ?>
            </div>

            <div class="mb-3 text-end">
                <button type="submit" name="updateCase" class="btn btn-primary">Update Case</button>
            </div>

            <?php else: ?>
                <h5>Case Not Found</h5>
            <?php endif; ?>

            </form>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
// Initialize Select2 for barangay on edit page too
(function(){
    function loadScript(src, cb){ var s=document.createElement('script'); s.src=src; s.onload=cb; document.head.appendChild(s); }
    function loadCSS(href){ var l=document.createElement('link'); l.rel='stylesheet'; l.href=href; document.head.appendChild(l); }
    function init(){ if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') return; $('.barangay-select').each(function(){ var parent=$(this).closest('.card').length?$(this).closest('.card'):$(this).parent(); $(this).select2({width:'100%', dropdownParent: parent}); }); }
    if (typeof $ === 'undefined') { loadScript('https://code.jquery.com/jquery-3.6.0.min.js', function(){ loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'); loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', init); }); }
    else if (typeof $.fn.select2 === 'undefined') { loadCSS('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'); loadScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', init); }
    else { init(); }
})();
</script>