<?php include('includes/header.php'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4>
                    Add Case
                    <a href="cases.php" class="btn btn-danger float-end">Back</a>
                </h4>
            </div>
            <div class="card-body">

                <?= alertmessage(); ?>

                <form action="code.php" method="POST" enctype="multipart/form-data">

                <h4>Case Details</h4>

                    <!-- Case Title -->
                     <div class="row">
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="num">Case No. (YYYY-MM-###)</label>
                        <input type="text" name="casenum" id="casenum" required class="form-control" placeholder="Enter Case No." />
                    </div>
                    </div>

                    <div class="col-md-4"> 
                    <div class="mb-3">
                        <label for="title">Type of Abuse</label>
                        <select name="title" id="title" required class="form-select">
                            <option value="">Select Type of Abuse</option>
                            <option value="Physical Abuse">Physical Abuse</option>
                            <option value="Sexual Abuse">Sexual Abuse</option>
                            <option value="Psychological or Emotional Abuse">Psychological or Emotional Abuse</option>
                            <option value="Economic or Financial Abuse">Economic or Financial Abuse</option>
                            <option value="Verbal Abuse">Verbal Abuse</option>
                            <option value="Digital or Cyber Harassment">Digital or Cyber Harassment</option>
                            <option value="Social Abuse or Isolation">Social Abuse or Isolation</option>
                            <option value="Stalking or Intimidation">Stalking or Intimidation</option>
                            <option value="Threats or Coercion">Threats or Coercion</option>
                            <option value="Property Damage or Destruction of Belongings">Property Damage or Destruction of Belongings</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    </div>

                    <div class="col-md-4">
                    <!-- Case Status Dropdown -->
                    <div class="mb-3">
                        <label for="status">Case Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">Select Case Status</option>
                            <option value="0">Open Case</option>
                            <option value="1">Closed Case</option>
                        </select>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="barangay">Incident Location</label>
                        <select name="barangay" id="barangay" class="form-select barangay-select" required>
                        <option value="">Select Barangay (Incident Location)</option>
                        <option value="I-A (Sambat)">I-A (Sambat)</option>
                            <option value="I-B (City Sub Riverside)">I-B (City Sub Riverside)</option>
                            <option value="I-C (Bagong Bayan)">I-C (Bagong Bayan)</option>
                            <option value="II-A (Triangulo/ Guadalupe 2)">II-A (Triangulo/ Guadalupe 2)</option>
                            <option value="II-B (Guadalupe 1)">II-B (Guadalupe 1)</option>
                            <option value="II-C (Unson)">II-C (Unson)</option>
                            <option value="II-D (Bulante)">II-D (Bulante)</option>
                            <option value="II-E (San Anton)">II-E (San Anton)</option>
                            <option value="II-F (Villa Rey)">II-F (Villa Rey)</option>
                            <option value="III-A (Hermanos Belen)">III-A (Hermanos Belen)</option>
                            <option value="III-B">III-B</option>
                            <option value="III-C (Labak/De Roma)">III-C (Labak/De Roma)</option>
                            <option value="III-D (Villongco)">III-D (Villongco)</option>
                            <option value="III-E">III-E</option>
                            <option value="III-F (Balagtas)">III-F (Balagtas)</option>
                            <option value="IV-A">IV-A</option>
                            <option value="IV-B">IV-B</option>
                            <option value="IV-C">IV-C</option>
                            <option value="V-A">V-A</option>
                            <option value="V-B">V-B</option>
                            <option value="V-C">V-C</option>
                            <option value="V-D">V-D</option>
                            <option value="VI-A (Mavenida)">VI-A (Mavenida)</option>
                            <option value="VI-B (Sabang Mabini)">VI-B (Sabang Mabini)</option>
                            <option value="VI-C (Bagong Pook)">VI-C (Bagong Pook)</option>
                            <option value="VI-D (Lakeside)">VI-D (Lakeside)</option>
                            <option value="VI-E (YMCA)">VI-E (YMCA)</option>
                            <option value="VII-A (P.Alcantara)">VII-A (P.Alcantara)</option>
                            <option value="VII-B">VII-B</option>
                            <option value="VII-C">VII-C</option>
                            <option value="VII-D">VII-D</option>
                            <option value="VII-E">VII-E</option>
                            <option value="Atisan">Atisan</option>
                            <option value="Bautista">Bautista</option>
                            <option value="Concepcion (Bunot)">Concepcion (Bunot)</option>
                            <option value="Del Remedio (Wawa)">Del Remedio (Wawa)</option>
                            <option value="Dolores">Dolores</option>
                            <option value="San Antonio 1 (Balanga)">San Antonio 1 (Balanga)</option>
                            <option value="San Antonio 2 (Sapa)">San Antonio 2 (Sapa)</option>
                            <option value="San Bartolome (Matang-ag)">San Bartolome (Matang-ag)</option>
                            <option value="San Buenaventura (Palakpakin)">San Buenaventura (Palakpakin)</option>
                            <option value="San Crispin (Lumbangan)">San Crispin (Lumbangan)</option>
                            <option value="San Cristobal">San Cristobal</option>
                            <option value="San Diego (Tiim)">San Diego (Tiim)</option>
                            <option value="San Francisco (Calihan)">San Francisco (Calihan)</option>
                            <option value="San Gabriel (Butucan)">San Gabriel (Butucan)</option>
                            <option value="San Gregorio">San Gregorio</option>
                            <option value="San Ignacio">San Ignacio</option>
                            <option value="San Isidro (Balagbag)">San Isidro (Balagbag)</option>
                            <option value="San Joaquin">San Joaquin</option>
                            <option value="San Jose (Malamig)">San Jose (Malamig)</option>
                            <option value="San Juan (Putol)">San Juan (Putol)</option>
                            <option value="San Lorenzo (Saluyan)">San Lorenzo (Saluyan)</option>
                            <option value="San Lucas 1 (Malinaw)">San Lucas 1 (Malinaw)</option>
                            <option value="San Lucas 2 (Malinaw)">San Lucas 2 (Malinaw)</option>
                            <option value="San Marcos (Tikew)">San Marcos (Tikew)</option>
                            <option value="San Mateo (Imok)">San Mateo (Imok)</option>
                            <option value="San Miguel (Balatuin)">San Miguel (Balatuin)</option>
                            <option value="San Nicolas (Mag-ampon)">San Nicolas (Mag-ampon)</option>
                            <option value="San Pedro">San Pedro</option>
                            <option value="San Rafael (Buluburan)">San Rafael (Buluburan)</option>
                            <option value="San Roque (Sambat)">San Roque (Sambat)</option>
                            <option value="San Vicente">San Vicente</option>
                            <option value="Santa Ana">Santa Ana</option>
                            <option value="Santa Catalina (Sandig)">Santa Catalina (Sandig)</option>
                            <option value="Santa Cruz (Putol)">Santa Cruz (Putol)</option>
                            <option value="Santa Elena">Santa Elena</option>
                            <option value="Santa Filomena (Banlagin)">Santa Filomena (Banlagin)</option>
                            <option value="Santa Isabel">Santa Isabel</option>
                            <option value="Santa Maria">Santa Maria</option>
                            <option value="Santa Maria Magdalena (Boe / Kuba)">Santa Maria Magdalena (Boe / Kuba)</option>
                            <option value="Santa Monica">Santa Monica</option>
                            <option value="Santa Veronica (Bae)">Santa Veronica (Bae)</option>
                            <option value="Santiago I (Bulaho)">Santiago I (Bulaho)</option>
                            <option value="Santiago II (Bulaho)">Santiago II (Bulaho)</option>
                            <option value="Santisimo Rosario (Balagbag)">Santisimo Rosario (Balagbag)</option>
                            <option value="Santo Angel (Ilog)">Santo Angel (Ilog)</option>
                            <option value="Santo Cristo">Santo Cristo</option>
                            <option value="Santo Niño (Arsum)">Santo Niño (Arsum)</option>
                            <option value="Soledad (Macopa)">Soledad (Macopa)</option>
                        </select>
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="mb-3">
                    <label for="date">Date of Incident</label>
                    <input name="date" id="date" required class="form-control" value="<?= date('Y-m-d') ?>" />
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Focal Person</label>
                        <input type="text" name="contactp" id="contactp" required class="form-control" placeholder="Enter Focal Person" />
                    </div>
                    </div>
                    </div>

                    <h4>Complainant Details</h4>

                    <!-- Complainant -->
                    <div class="row">
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Complainant</label>
                        <input type="text" name="complainant" id="complainant" required class="form-control" placeholder="Enter Complainant Name" />
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Age</label>
                        <input type="text" name="cage" id="cage" required class="form-control" placeholder="Enter Complainant Age" />
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Contact Number</label>
                        <input type="text" name="cnum" id="cnum" required class="form-control" placeholder="Enter Contact Number" />
                    </div>
                    </div>
                    </div>

                    <div class="mb-3">
                        <label for="name">Complainant Address</label>
                        <input type="text" name="caddress" id="caddress" required class="form-control" placeholder="Enter Complainant Address" />
                    </div>

                    <h4>Respondent Details</h4>

                    <!-- Respondent -->
                    <div class="row">
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Respondent</label>
                        <input type="text" name="respondent" id="respondent" required class="form-control" placeholder="Enter Respondent Name" />
                    </div>
                    </div>
                    
                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Age</label>
                        <input type="text" name="rage" id="rage" required class="form-control" placeholder="Enter Complainant Age" />
                    </div>
                    </div>

                    <div class="col-md-4">
                    <div class="mb-3">
                        <label for="name">Contact Number</label>
                        <input type="text" name="rnum" id="rnum" required class="form-control" placeholder="Enter Contact Number" />
                    </div>
                    </div>
                    </div>

                    <div class="mb-3">
                        <label for="name">Respondent Address</label>
                        <input type="text" name="raddress" id="raddress" required class="form-control" placeholder="Enter Respondent Address" />
                    </div>

                    <!-- Case Description -->
                    <div class="mb-3">
                        <label for="long_description">Case Description</label>
                        <textarea name="long_description" id="long_description" class="form-control mySummernote" rows="3" placeholder="Enter case details"></textarea>
                    </div>

                    <!-- Upload Case Image -->
                    <div class="mb-3">
                        <label for="image">Upload Case/Evidence Image</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*" />
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-3 text-end">
                        <button type="submit" name="saveCase" class="btn btn-primary">Save Case</button>
                    </div>
                </form>

            </div>
        </div>
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
                        dropdownParent: container // attach dropdown to closest container so it appears under the field
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

<script>
// Toggle the barangay list visibility when the button is clicked
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-barangay-list');
    const list = document.getElementById('barangay-list');
    if (toggleBtn && list) {
        toggleBtn.addEventListener('click', function () {
            if (list.style.display === 'none' || list.style.display === '') {
                list.style.display = 'block';
                toggleBtn.textContent = 'Hide barangay list';
            } else {
                list.style.display = 'none';
                toggleBtn.textContent = 'Show barangay list';
            }
        });
    }
});
</script>

<?php include('includes/footer.php'); ?>