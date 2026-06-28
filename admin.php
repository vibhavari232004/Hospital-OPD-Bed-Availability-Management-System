<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mint OPD · Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }
        body {
            background: #f0faf0;
            min-height: 100vh;
            padding: 2rem;
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #1a3b2e;
            font-size: 2.8rem;
            margin-bottom: 2rem;
            border-left: 12px solid #2d8b65;
            padding-left: 1.5rem;
        }
        .section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px -5px #a3d8b2;
            border: 2px solid #c0ead8;
        }
        .section h2 {
            color: #1c5e45;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #3d6e58;
            font-weight: 500;
        }
        input, select, button {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #c0ead8;
            border-radius: 10px;
            font-size: 1rem;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #2d8b65;
        }
        button {
            background: #2d8b65;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }
        button:hover {
            background: #1a3b2e;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        .item {
            background: #f8fdf8;
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid #c0ead8;
        }
        .item h3 {
            color: #1c5e45;
            margin-bottom: 0.5rem;
        }
        .item p {
            color: #3d6e58;
            margin-bottom: 0.25rem;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .delete-btn:hover {
            background: #c82333;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #2d8b65;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .connection-info {
            margin-bottom: 1.5rem;
            padding: 0.75rem 1rem;
            background: #e6fff0;
            border: 1px solid #b9edd4;
            border-radius: 10px;
            color: #2d8b65;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <a href="index.php" class="back-link">← Back to Home</a>
        <div class="connection-info">Connected to <strong><?php echo htmlspecialchars($config['dbname']); ?></strong> as <strong><?php echo htmlspecialchars($config['user']); ?></strong>.</div>
        <h1>🛠️ Mint OPD · Admin Panel</h1>

        <!-- Add City Section -->
        <div class="section">
            <h2>Add City</h2>
            <div class="form-group">
                <label for="city-name">City Name</label>
                <input type="text" id="city-name" placeholder="Enter city name">
            </div>
            <button onclick="addCity()">Add City</button>
        </div>

        <!-- Cities List -->
        <div class="section">
            <h2>Cities</h2>
            <div id="cities-list" class="grid"></div>
        </div>

        <!-- Add Hospital Section -->
        <div class="section">
            <h2>Add Hospital</h2>
            <div class="form-group">
                <label for="hospital-city">Select City</label>
                <select id="hospital-city"></select>
            </div>
            <div class="form-group">
                <label for="hospital-name">Hospital Name</label>
                <input type="text" id="hospital-name" placeholder="Enter hospital name">
            </div>
            <div class="form-group">
                <label for="hospital-address">Address</label>
                <input type="text" id="hospital-address" placeholder="Enter hospital address">
            </div>
            <button onclick="addHospital()">Add Hospital</button>
        </div>

        <!-- Hospitals List -->
        <div class="section">
            <h2>Hospitals</h2>
            <div id="hospitals-list" class="grid"></div>
        </div>

        <!-- Add Beds Section -->
        <div class="section">
            <h2>Add Beds</h2>
            <div class="form-group">
                <label for="bed-city">Select City</label>
                <select id="bed-city" onchange="updateHospitalSelect()"></select>
            </div>
            <div class="form-group">
                <label for="bed-hospital">Select Hospital</label>
                <select id="bed-hospital"></select>
            </div>
            <div class="form-group">
                <label for="bed-type">Bed Type</label>
                <select id="bed-type">
                    <option value="all">All</option>
                    <option value="general">General</option>
                    <option value="icu">ICU</option>
                    <option value="maternity">Maternity</option>
                    <option value="emergency">Emergency</option>
                    <option value="pediatric">Pediatric</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bed-count">Number of Beds</label>
                <input type="number" id="bed-count" placeholder="Enter number of beds" min="1">
            </div>
            <button onclick="addBeds()">Add Beds</button>
        </div>

        <!-- Beds List -->
        <div class="section">
            <h2>Bed Availability</h2>
            <div id="beds-list" class="grid"></div>
        </div>
    </div>

    <script>
        // Load data from localStorage
        let cities = JSON.parse(localStorage.getItem('cities')) || [];
        let hospitals = JSON.parse(localStorage.getItem('hospitals')) || [];
        let beds = JSON.parse(localStorage.getItem('beds')) || [];

        // Update displays
        function updateDisplays() {
            displayCities();
            displayHospitals();
            displayBeds();
            updateSelects();
        }

        // Add City
        function addCity() {
            const cityName = document.getElementById('city-name').value.trim();
            if (cityName && !cities.find(c => c.name === cityName)) {
                cities.push({ id: Date.now(), name: cityName });
                localStorage.setItem('cities', JSON.stringify(cities));
                document.getElementById('city-name').value = '';
                updateDisplays();
            } else {
                alert('City name is required and must be unique.');
            }
        }

        // Display Cities
        function displayCities() {
            const citiesList = document.getElementById('cities-list');
            citiesList.innerHTML = cities.map(city => `
                <div class="item">
                    <h3>${city.name}</h3>
                    <button class="delete-btn" onclick="deleteCity(${city.id})">Delete</button>
                </div>
            `).join('');
        }

        // Delete City
        function deleteCity(id) {
            if (confirm('Are you sure? This will also delete all hospitals and beds in this city.')) {
                cities = cities.filter(c => c.id !== id);
                hospitals = hospitals.filter(h => h.cityId !== id);
                beds = beds.filter(b => b.cityId !== id);
                localStorage.setItem('cities', JSON.stringify(cities));
                localStorage.setItem('hospitals', JSON.stringify(hospitals));
                localStorage.setItem('beds', JSON.stringify(beds));
                updateDisplays();
            }
        }

        // Add Hospital
        function addHospital() {
            const cityId = document.getElementById('hospital-city').value;
            const name = document.getElementById('hospital-name').value.trim();
            const address = document.getElementById('hospital-address').value.trim();
            if (cityId && name && address) {
                hospitals.push({ id: Date.now(), cityId: parseInt(cityId), name, address });
                localStorage.setItem('hospitals', JSON.stringify(hospitals));
                document.getElementById('hospital-name').value = '';
                document.getElementById('hospital-address').value = '';
                updateDisplays();
            } else {
                alert('All fields are required.');
            }
        }

        // Display Hospitals
        function displayHospitals() {
            const hospitalsList = document.getElementById('hospitals-list');
            hospitalsList.innerHTML = hospitals.map(hospital => {
                const city = cities.find(c => c.id === hospital.cityId);
                return `
                    <div class="item">
                        <h3>${hospital.name}</h3>
                        <p><strong>City:</strong> ${city ? city.name : 'Unknown'}</p>
                        <p><strong>Address:</strong> ${hospital.address}</p>
                        <button class="delete-btn" onclick="deleteHospital(${hospital.id})">Delete</button>
                    </div>
                `;
            }).join('');
        }

        // Delete Hospital
        function deleteHospital(id) {
            if (confirm('Are you sure? This will also delete all beds in this hospital.')) {
                hospitals = hospitals.filter(h => h.id !== id);
                beds = beds.filter(b => b.hospitalId !== id);
                localStorage.setItem('hospitals', JSON.stringify(hospitals));
                localStorage.setItem('beds', JSON.stringify(beds));
                updateDisplays();
            }
        }

        // Add Beds
        function addBeds() {
            const cityId = document.getElementById('bed-city').value;
            const hospitalId = document.getElementById('bed-hospital').value;
            const type = document.getElementById('bed-type').value;
            const count = parseInt(document.getElementById('bed-count').value);
            if (cityId && hospitalId && type && count > 0) {
                const existingBed = beds.find(b => b.hospitalId === parseInt(hospitalId) && b.type === type);
                if (existingBed) {
                    existingBed.count += count;
                } else {
                    beds.push({ id: Date.now(), cityId: parseInt(cityId), hospitalId: parseInt(hospitalId), type, count: parseInt(count), total: parseInt(count), occupied: 0 });
                }
                localStorage.setItem('beds', JSON.stringify(beds));
                document.getElementById('bed-count').value = '';
                updateDisplays();
            } else {
                alert('All fields are required and bed count must be positive.');
            }
        }

        // Display Beds
        function displayBeds() {
            const bedsList = document.getElementById('beds-list');
            // Group beds by city
            const bedsByCity = beds.reduce((acc, bed) => {
                if (!acc[bed.cityId]) {
                    acc[bed.cityId] = [];
                }
                acc[bed.cityId].push(bed);
                return acc;
            }, {});

            bedsList.innerHTML = Object.keys(bedsByCity).map(cityId => {
                const city = cities.find(c => c.id === parseInt(cityId));
                const cityBeds = bedsByCity[cityId];
                // Group beds by hospital within the city
                const bedsByHospitalInCity = cityBeds.reduce((acc, bed) => {
                    if (!acc[bed.hospitalId]) {
                        acc[bed.hospitalId] = [];
                    }
                    acc[bed.hospitalId].push(bed);
                    return acc;
                }, {});

                const hospitalsHtml = Object.keys(bedsByHospitalInCity).map(hospitalId => {
                    const hospital = hospitals.find(h => h.id === parseInt(hospitalId));
                    const hospitalBeds = bedsByHospitalInCity[hospitalId];
                    const bedTypesHtml = hospitalBeds.map(bed => `
                        <div style="margin-bottom: 0.5rem; padding: 0.5rem; background: #f0faf0; border-radius: 5px;">
                            <p><strong>Type:</strong> ${bed.type.charAt(0).toUpperCase() + bed.type.slice(1)}</p>
                            <p><strong>Total:</strong> ${bed.total}, <strong>Occupied:</strong> ${bed.occupied}, <strong>Available:</strong> ${bed.total - bed.occupied}</p>
                            <button class="delete-btn" onclick="deleteBeds(${bed.id})" style="margin-top: 0.25rem; padding: 0.2rem 0.4rem; font-size: 0.7rem;">Delete</button>
                        </div>
                    `).join('');

                    return `
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background: #e8f5e8; border-radius: 10px; border: 1px solid #c0ead8;">
                            <h4 style="color: #1c5e45; margin-bottom: 0.5rem;">${hospital ? hospital.name : 'Unknown Hospital'}</h4>
                            <p style="margin-bottom: 0.5rem;"><strong>Address:</strong> ${hospital ? hospital.address : 'Unknown'}</p>
                            <div>
                                ${bedTypesHtml}
                            </div>
                            <button class="delete-btn" onclick="deleteHospitalBeds(${hospitalId})" style="margin-top: 0.5rem;">Delete All Beds</button>
                        </div>
                    `;
                }).join('');

                return `
                    <div class="item">
                        <h3>${city ? city.name : 'Unknown City'}</h3>
                        <div style="margin-top: 1rem;">
                            <h4 style="color: #1c5e45; margin-bottom: 0.5rem;">Hospitals & Bed Availability:</h4>
                            ${hospitalsHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Delete Beds
        function deleteBeds(id) {
            if (confirm('Are you sure you want to delete this bed type?')) {
                beds = beds.filter(b => b.id !== id);
                localStorage.setItem('beds', JSON.stringify(beds));
                updateDisplays();
            }
        }

        // Delete All Beds for a Hospital
        function deleteHospitalBeds(hospitalId) {
            if (confirm('Are you sure you want to delete all beds for this hospital?')) {
                beds = beds.filter(b => b.hospitalId !== parseInt(hospitalId));
                localStorage.setItem('beds', JSON.stringify(beds));
                updateDisplays();
            }
        }

        // Update Select Options
        function updateSelects() {
            // City selects
            const citySelects = ['hospital-city', 'bed-city'];
            citySelects.forEach(id => {
                const select = document.getElementById(id);
                select.innerHTML = '<option value="">Select City</option>' +
                    cities.map(city => `<option value="${city.id}">${city.name}</option>`).join('');
            });

            // Hospital select for beds
            updateHospitalSelect();
        }

        // Update Hospital Select based on selected city
        function updateHospitalSelect() {
            const cityId = document.getElementById('bed-city').value;
            const hospitalSelect = document.getElementById('bed-hospital');
            if (cityId) {
                const cityHospitals = hospitals.filter(h => h.cityId === parseInt(cityId));
                hospitalSelect.innerHTML = '<option value="">Select Hospital</option>' +
                    cityHospitals.map(hospital => `<option value="${hospital.id}">${hospital.name}</option>`).join('');
            } else {
                hospitalSelect.innerHTML = '<option value="">Select City First</option>';
            }
        }

        // Initialize with default data if empty
        function initializeDefaultData() {
            if (cities.length === 0) {
                cities = [
                    { id: 1, name: 'Jalgaon' },
                    { id: 2, name: 'Pune' },
                    { id: 3, name: 'Mumbai' }
                ];
                localStorage.setItem('cities', JSON.stringify(cities));
            }

            if (hospitals.length === 0) {
                hospitals = [
                    { id: 1, cityId: 1, name: 'Jalgaon Civil Hospital', address: 'Near Railway Station, Jalgaon' },
                    { id: 2, cityId: 1, name: 'Apollo Hospital Jalgaon', address: 'Ring Road, Jalgaon' },
                    { id: 3, cityId: 2, name: 'Pune General Hospital', address: 'FC Road, Pune' },
                    { id: 4, cityId: 3, name: 'Lilavati Hospital', address: 'Bandra, Mumbai' }
                ];
                localStorage.setItem('hospitals', JSON.stringify(hospitals));
            }

            if (beds.length === 0) {
                beds = [
                    { id: 1, cityId: 1, hospitalId: 1, type: 'general', count: 50, total: 50, occupied: 15 },
                    { id: 2, cityId: 1, hospitalId: 1, type: 'icu', count: 10, total: 10, occupied: 3 },
                    { id: 3, cityId: 2, hospitalId: 3, type: 'general', count: 40, total: 40, occupied: 12 },
                    { id: 4, cityId: 3, hospitalId: 4, type: 'general', count: 30, total: 30, occupied: 8 }
                ];
                localStorage.setItem('beds', JSON.stringify(beds));
            }
        }

        // Initialize
        initializeDefaultData();
        updateDisplays();
    </script>
</body>
</html>
