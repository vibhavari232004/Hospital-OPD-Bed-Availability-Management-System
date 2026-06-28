<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mint OPD · Receptionist Panel</title>
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
        .receptionist-container {
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
        input, select, button, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #c0ead8;
            border-radius: 10px;
            font-size: 1rem;
        }
        input:focus, select:focus, textarea:focus {
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
        .allocate-btn, .discharge-btn, .cancel-btn, .delete-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .allocate-btn:hover, .discharge-btn:hover {
            background: #218838;
        }
        .cancel-btn, .delete-btn {
            background: #dc3545;
        }
        .cancel-btn:hover, .delete-btn:hover {
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
        .hidden {
            display: none;
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
    <div class="receptionist-container">
        <a href="index.php" class="back-link">← Back to Home</a>
        <div class="connection-info">Connected to <strong><?php echo htmlspecialchars($config['dbname']); ?></strong> as <strong><?php echo htmlspecialchars($config['user']); ?></strong>.</div>
        <h1>📋 Mint OPD · Receptionist Panel</h1>

        <!-- Login Section -->
        <div id="login-section" class="section">
            <h2>Receptionist Login</h2>
            <div class="form-group">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" placeholder="Enter password">
            </div>
            <button onclick="loginReceptionist()">Login</button>
        </div>

        <!-- Dashboard (hidden initially) -->
        <div id="dashboard" class="hidden">
            <div class="section">
                <h2>Welcome, <span id="receptionist-name"></span> (<span id="receptionist-city"></span>)</h2>
                <button onclick="logoutReceptionist()">Logout</button>
            </div>

        <!-- Add Doctor Section -->
        <div class="section">
            <h2>Add Doctor</h2>
            <div class="form-group">
                    <label for="doc-hospital">Select Hospital</label>
                    <select id="doc-hospital"></select>
                </div>
                <div class="form-group">
                    <label for="doc-name">Doctor Name</label>
                    <input type="text" id="doc-name" placeholder="Enter doctor name">
                </div>
                <div class="form-group">
                    <label for="doc-email">Email</label>
                    <input type="email" id="doc-email" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="doc-password">Password</label>
                    <input type="password" id="doc-password" placeholder="Set password">
                </div>
                <div class="form-group">
                    <label for="doc-speciality">Speciality</label>
                    <input type="text" id="doc-speciality" placeholder="e.g., Cardiology">
                </div>
                <div class="form-group">
                    <label for="doc-department">Department</label>
                    <select id="doc-department">
                        <option value="cardiology">Cardiology</option>
                        <option value="gynecology">Gynecology</option>
                        <option value="pediatrics">Pediatrics</option>
                        <option value="orthopedics">Orthopedics</option>
                        <option value="dermatology">Dermatology</option>
                        <option value="neurology">Neurology</option>
                        <option value="general">General Medicine</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="doc-fees">Consultation Fees</label>
                    <input type="number" id="doc-fees" placeholder="Enter fees" min="0">
                </div>
                <button onclick="addDoctor()">Add Doctor</button>
            </div>

            <!-- Manage Doctors Section -->
            <div class="section">
                <h2>Manage Doctors</h2>
                <div class="form-group">
                    <label for="manage-hospital">Select Hospital</label>
                    <select id="manage-hospital" onchange="loadDoctorsForManagement()"></select>
                </div>
                <div id="doctors-management" class="grid"></div>
            </div>

            <!-- Manage Beds Section -->
            <div class="section">
                <h2>Manage Beds</h2>
                <div class="form-group">
                    <label for="bed-hospital">Select Hospital</label>
                    <select id="bed-hospital" onchange="loadBedData()"></select>
                </div>
                <div id="bed-management"></div>
            </div>

            <!-- Allocate Bed Section -->
            <div class="section">
                <h2>Allocate Bed</h2>
                <div class="form-group">
                    <label for="alloc-patient">Select Patient</label>
                    <select id="alloc-patient"></select>
                </div>
                <div class="form-group">
                    <label for="alloc-hospital">Select Hospital</label>
                    <select id="alloc-hospital" onchange="updateBedTypeSelect()"></select>
                </div>
                <div class="form-group">
                    <label for="alloc-bed-type">Bed Type</label>
                    <select id="alloc-bed-type"></select>
                </div>
                <button onclick="allocateBed()">Allocate Bed</button>
            </div>

            <!-- Discharge Patient Section -->
            <div class="section">
                <h2>Discharge Patient</h2>
                <div id="discharge-list" class="grid"></div>
            </div>

            <!-- Doctors and Patients Section -->
            <div class="section">
                <h2>Doctors and Their Patients</h2>
                <div id="doctors-patients" class="grid"></div>
            </div>

            <!-- Spot Registration and Appointment Section -->
            <div class="section">
                <h2>Spot Registration & Appointment</h2>
                <div class="form-group">
                    <label for="spot-name">Patient Name</label>
                    <input type="text" id="spot-name" placeholder="Enter patient name">
                </div>
                <div class="form-group">
                    <label for="spot-hospital">Select Hospital</label>
                    <select id="spot-hospital" onchange="updateDoctorSelectForSpot()"></select>
                </div>
                <div class="form-group">
                    <label for="spot-doctor">Select Doctor</label>
                    <select id="spot-doctor"></select>
                </div>
                <button onclick="spotBookAppointment()">Book Spot Appointment</button>
            </div>
        <div class="section">
            <h2>Manage Appointments</h2>
            <div id="appointments-management" class="grid"></div>
        </div>
    </div>

    <script>
        // Load data from localStorage
        let cities = JSON.parse(localStorage.getItem('cities')) || [];
        let hospitals = JSON.parse(localStorage.getItem('hospitals')) || [];
        let doctors = JSON.parse(localStorage.getItem('doctors')) || [];
        let patients = JSON.parse(localStorage.getItem('patients')) || [];
        let beds = JSON.parse(localStorage.getItem('beds')) || [];
        let appointments = JSON.parse(localStorage.getItem('appointments')) || [];

        // Pre-set receptionists
        const receptionists = [
            { name: 'Receptionist Jalgaon', email: 'jalgaon@opd.com', password: 'jalgaon123', city: 'Jalgaon' },
            { name: 'Receptionist Pune', email: 'pune@opd.com', password: 'pune123', city: 'Pune' }
        ];

        let currentReceptionist = null;

        // Maintain session if already logged in
        const loggedInReceptionist = JSON.parse(localStorage.getItem('currentReceptionist'));
        if (loggedInReceptionist) {
            currentReceptionist = loggedInReceptionist;
            showDashboard();
        }

        // Login Receptionist
        function loginReceptionist() {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            const receptionist = receptionists.find(r => r.email === email && r.password === password);
            if (receptionist) {
                currentReceptionist = receptionist;
                localStorage.setItem('currentReceptionist', JSON.stringify(receptionist));
                showDashboard();
            } else {
                alert('Invalid email or password.');
            }
        }

        // Logout Receptionist
        function logoutReceptionist() {
            currentReceptionist = null;
            localStorage.removeItem('currentReceptionist');
            document.getElementById('dashboard').classList.add('hidden');
            document.getElementById('login-section').classList.remove('hidden');
        }

        // Show Dashboard
        function showDashboard() {
            document.getElementById('login-section').classList.add('hidden');
            document.getElementById('dashboard').classList.remove('hidden');
            document.getElementById('receptionist-name').textContent = currentReceptionist.name;
            document.getElementById('receptionist-city').textContent = currentReceptionist.city;
            updateSelects();
            loadDoctorsForManagement();
            loadDischargeList();
            updateDoctorsPatients();
            updateAppointmentsManagement();
        }

        // Update selects
        function updateSelects() {
            if (!currentReceptionist) return;

            const cityHospitals = hospitals.filter(h => {
                const city = cities.find(c => c.id === h.cityId);
                return city && city.name === currentReceptionist.city;
            });

            // Hospital selects
            const hospitalSelects = ['doc-hospital', 'bed-hospital', 'alloc-hospital', 'spot-hospital', 'manage-hospital'];
            hospitalSelects.forEach(id => {
                const select = document.getElementById(id);
                select.innerHTML = '<option value="">Select Hospital</option>' +
                    cityHospitals.map(hospital => `<option value="${hospital.id}">${hospital.name}</option>`).join('');
            });

            const patientSelect = document.getElementById('alloc-patient');
            patientSelect.innerHTML = '<option value="">Select Patient</option>' +
                patients.map(patient => `<option value="${patient.id}">${patient.name}</option>`).join('');
        }

        // Add Doctor
        function addDoctor() {
            const hospitalId = document.getElementById('doc-hospital').value;
            const name = document.getElementById('doc-name').value.trim();
            const email = document.getElementById('doc-email').value.trim();
            const password = document.getElementById('doc-password').value;
            const speciality = document.getElementById('doc-speciality').value.trim();
            const department = document.getElementById('doc-department').value;
            const fees = parseFloat(document.getElementById('doc-fees').value);

            if (hospitalId && name && email && password && speciality && department && fees >= 0) {
                const hospital = hospitals.find(h => h.id === parseInt(hospitalId));
                if (!hospital) return;
                const cityId = hospital.cityId;
                if (doctors.find(d => d.email === email)) {
                    alert('Email already exists.');
                    return;
                }
                doctors.push({ id: Date.now(), cityId, hospitalId: parseInt(hospitalId), name, email, password, speciality, department, fees, availableSlots: [] });
                localStorage.setItem('doctors', JSON.stringify(doctors));
                alert('Doctor added successfully!');
                // Clear form
                document.getElementById('doc-name').value = '';
                document.getElementById('doc-email').value = '';
                document.getElementById('doc-password').value = '';
                document.getElementById('doc-speciality').value = '';
                document.getElementById('doc-fees').value = '';
                updateDoctorsPatients();
            } else {
                alert('All fields are required.');
            }
        }

        // Load Doctors for Management
        function loadDoctorsForManagement() {
            const hospitalId = document.getElementById('manage-hospital').value;
            const doctorsManagement = document.getElementById('doctors-management');
            if (hospitalId) {
                const hospitalDoctors = doctors.filter(d => d.hospitalId === parseInt(hospitalId));
                doctorsManagement.innerHTML = hospitalDoctors.map(doctor => `
                    <div class="item">
                        <h3>${doctor.name}</h3>
                        <p><strong>Speciality:</strong> ${doctor.speciality}</p>
                        <p><strong>Department:</strong> ${doctor.department}</p>
                        <p><strong>Email:</strong> ${doctor.email}</p>
                        <p><strong>Fees:</strong> ₹${doctor.fees}</p>
                        <button class="delete-btn" onclick="deleteDoctor(${doctor.id})">Delete Doctor</button>
                    </div>
                `).join('');
                if (hospitalDoctors.length === 0) {
                    doctorsManagement.innerHTML = '<p style="text-align: center; color: #3d6e58; padding: 2rem;">No doctors found in this hospital.</p>';
                }
            } else {
                doctorsManagement.innerHTML = '<p style="text-align: center; color: #3d6e58; padding: 2rem;">Please select a hospital to manage doctors.</p>';
            }
        }

        // Delete Doctor
        function deleteDoctor(doctorId) {
            if (confirm('Are you sure you want to delete this doctor? This will also cancel all their appointments and may affect patient care.')) {
                // Remove doctor from doctors array
                doctors = doctors.filter(d => d.id !== doctorId);

                // Cancel all appointments for this doctor
                appointments = appointments.filter(a => a.doctorId !== doctorId);

                // Update localStorage
                localStorage.setItem('doctors', JSON.stringify(doctors));
                localStorage.setItem('appointments', JSON.stringify(appointments));

                alert('Doctor deleted successfully! All their appointments have been cancelled.');
                loadDoctorsForManagement();
                updateDoctorsPatients();
                updateAppointmentsManagement();
            }
        }

        // Load Bed Data
        function loadBedData() {
            const hospitalId = document.getElementById('bed-hospital').value;
            const bedManagement = document.getElementById('bed-management');
            if (hospitalId) {
                const hospitalBeds = beds.filter(b => b.hospitalId === parseInt(hospitalId));
                bedManagement.innerHTML = `
                    <div class="form-group">
                        <label for="bed-type-select">Bed Type</label>
                        <select id="bed-type-select">
                            <option value="general">General</option>
                            <option value="icu">ICU</option>
                            <option value="maternity">Maternity</option>
                            <option value="emergency">Emergency</option>
                            <option value="pediatric">Pediatric</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bed-total">Total Beds</label>
                        <input type="number" id="bed-total" placeholder="Enter total beds" min="0">
                    </div>
                    <button onclick="setBedTotal(${hospitalId})">Set Total Beds</button>
                `;
                // Load existing
                const typeSelect = document.getElementById('bed-type-select');
                typeSelect.addEventListener('change', () => {
                    const type = typeSelect.value;
                    const existingBed = hospitalBeds.find(b => b.type === type);
                    document.getElementById('bed-total').value = existingBed ? existingBed.total : 0;
                });
            } else {
                bedManagement.innerHTML = '';
            }
        }

        // Set Bed Total
        function setBedTotal(hospitalId) {
            const type = document.getElementById('bed-type-select').value;
            const total = parseInt(document.getElementById('bed-total').value);
            if (type && total >= 0) {
                const existingIndex = beds.findIndex(b => b.hospitalId === parseInt(hospitalId) && b.type === type);
                if (existingIndex >= 0) {
                    beds[existingIndex].total = total;
                } else {
                    beds.push({ id: Date.now(), hospitalId: parseInt(hospitalId), cityId: hospitals.find(h => h.id === parseInt(hospitalId)).cityId, type, total, occupied: 0 });
                }
                localStorage.setItem('beds', JSON.stringify(beds));
                alert('Bed total updated!');
            }
        }

        // Update Bed Type Select
        function updateBedTypeSelect() {
            const hospitalId = document.getElementById('alloc-hospital').value;
            const bedTypeSelect = document.getElementById('alloc-bed-type');
            if (hospitalId) {
                const hospitalBeds = beds.filter(b => b.hospitalId === parseInt(hospitalId));
                bedTypeSelect.innerHTML = '<option value="">Select Bed Type</option>' +
                    hospitalBeds.map(bed => `<option value="${bed.type}">${bed.type.charAt(0).toUpperCase() + bed.type.slice(1)} (${bed.total - bed.occupied} available)</option>`).join('');
            } else {
                bedTypeSelect.innerHTML = '<option value="">Select Hospital First</option>';
            }
        }

        // Allocate Bed
        function allocateBed() {
            const patientId = document.getElementById('alloc-patient').value;
            const hospitalId = document.getElementById('alloc-hospital').value;
            const bedType = document.getElementById('alloc-bed-type').value;
            if (patientId && hospitalId && bedType) {
                const bedIndex = beds.findIndex(b => b.hospitalId === parseInt(hospitalId) && b.type === bedType);
                if (bedIndex >= 0 && beds[bedIndex].occupied < beds[bedIndex].total) {
                    beds[bedIndex].occupied++;
                    localStorage.setItem('beds', JSON.stringify(beds));
                    alert('Bed allocated!');
                    updateBedTypeSelect();
                    loadDischargeList();
                } else {
                    alert('No beds available.');
                }
            } else {
                alert('All fields are required.');
            }
        }

        // Load Discharge List
        function loadDischargeList() {
            const dischargeList = document.getElementById('discharge-list');
            // Assume occupied beds have patients, but for simplicity, show occupied beds
            dischargeList.innerHTML = beds.filter(b => b.occupied > 0).map(bed => `
                <div class="item">
                    <h3>${bed.type.charAt(0).toUpperCase() + bed.type.slice(1)} Bed</h3>
                    <p>Occupied: ${bed.occupied}</p>
                    <button class="discharge-btn" onclick="dischargeBed(${bed.id})">Discharge</button>
                </div>
            `).join('');
        }

        // Discharge Bed
        function dischargeBed(bedId) {
            const bedIndex = beds.findIndex(b => b.id === bedId);
            if (bedIndex >= 0 && beds[bedIndex].occupied > 0) {
                beds[bedIndex].occupied--;
                localStorage.setItem('beds', JSON.stringify(beds));
                alert('Patient discharged!');
                loadDischargeList();
                updateBedTypeSelect();
            }
        }

        // Update Doctors Patients
        function updateDoctorsPatients() {
            const doctorsPatients = document.getElementById('doctors-patients');
            doctorsPatients.innerHTML = doctors.map(doctor => {
                const doctorAppointments = appointments.filter(a => a.doctorId === doctor.id);
                const patientList = doctorAppointments.map(a => {
                    const patient = patients.find(p => p.id === a.patientId);
                    return patient ? patient.name : 'Unknown';
                }).join(', ');
                return `
                    <div class="item">
                        <h3>${doctor.name}</h3>
                        <p><strong>Patients:</strong> ${patientList || 'None'}</p>
                    </div>
                `;
            }).join('');
        }

        // Update Doctor Select for Spot
        function updateDoctorSelectForSpot() {
            const hospitalId = document.getElementById('spot-hospital').value;
            const doctorSelect = document.getElementById('spot-doctor');
            if (hospitalId) {
                const hospitalDoctors = doctors.filter(d => d.hospitalId === parseInt(hospitalId));
                doctorSelect.innerHTML = '<option value="">Select Doctor</option>' +
                    hospitalDoctors.map(doctor => `<option value="${doctor.id}">${doctor.name}</option>`).join('');
            } else {
                doctorSelect.innerHTML = '<option value="">Select Hospital First</option>';
            }
        }

        // Spot Book Appointment
        function spotBookAppointment() {
            const name = document.getElementById('spot-name').value.trim();
            const hospitalId = document.getElementById('spot-hospital').value;
            const doctorId = document.getElementById('spot-doctor').value;
            if (name && hospitalId && doctorId) {
                // Create temp patient
                const tempPatient = { id: Date.now(), name, email: '', password: '', age: 0, gender: '' };
                patients.push(tempPatient);
                localStorage.setItem('patients', JSON.stringify(patients));

                const doctor = doctors.find(d => d.id === parseInt(doctorId));
                const doctorAppointments = appointments.filter(a => a.doctorId === parseInt(doctorId));
                const queueNumber = doctorAppointments.length + 1;

                appointments.push({
                    id: Date.now(),
                    patientId: tempPatient.id,
                    doctorId: parseInt(doctorId),
                    hospitalId: parseInt(hospitalId),
                    cityId: doctor.cityId,
                    queueNumber: queueNumber,
                    slot: 'Walk-in',
                    date: new Date().toLocaleDateString(),
                    completed: false,
                    followUp: false
                });
                localStorage.setItem('appointments', JSON.stringify(appointments));
                alert(`Spot appointment booked! Queue number: ${queueNumber}`);
                document.getElementById('spot-name').value = '';
                updateAppointmentsManagement();
                updateDoctorsPatients();
            } else {
                alert('All fields are required.');
            }
        }

        // Update Appointments Management
        function updateAppointmentsManagement() {
            const appointmentsManagement = document.getElementById('appointments-management');
            appointmentsManagement.innerHTML = appointments.filter(a => !a.completed).map(appointment => {
                const patient = patients.find(p => p.id === appointment.patientId);
                const doctor = doctors.find(d => d.id === appointment.doctorId);
                return `
                    <div class="item">
                        <h3>${patient ? patient.name : 'Unknown'} - ${doctor ? doctor.name : 'Unknown'}</h3>
                        <p><strong>Queue:</strong> ${appointment.queueNumber}</p>
                        <button class="cancel-btn" onclick="cancelAppointment(${appointment.id})">Cancel</button>
                    </div>
                `;
            }).join('');
        }

        // Cancel Appointment
        function cancelAppointment(appointmentId) {
            appointments = appointments.filter(a => a.id !== appointmentId);
            localStorage.setItem('appointments', JSON.stringify(appointments));
            alert('Appointment cancelled!');
            updateAppointmentsManagement();
            updateDoctorsPatients();
        }

        // Initialize with default doctors if empty
        function initializeDefaultDoctors() {
            if (doctors.length === 0) {
                const today = new Date().toLocaleDateString();
                doctors = [
                    {
                        id: 1,
                        cityId: 1,
                        hospitalId: 1,
                        name: 'Dr. Rajesh Sharma',
                        email: 'rajesh@jcivil.com',
                        password: 'pass123',
                        speciality: 'Cardiology',
                        department: 'cardiology',
                        fees: 500.00,
                        availableSlots: [{
                            date: today,
                            slots: ['9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM']
                        }]
                    },
                    {
                        id: 2,
                        cityId: 1,
                        hospitalId: 1,
                        name: 'Dr. Priya Patel',
                        email: 'priya@jcivil.com',
                        password: 'pass123',
                        speciality: 'Gynecology',
                        department: 'gynecology',
                        fees: 400.00,
                        availableSlots: [{
                            date: today,
                            slots: ['9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '2:00 PM - 3:00 PM']
                        }]
                    },
                    {
                        id: 3,
                        cityId: 2,
                        hospitalId: 3,
                        name: 'Dr. Sunita Rao',
                        email: 'sunita@pune.com',
                        password: 'pass123',
                        speciality: 'Pediatrics',
                        department: 'pediatrics',
                        fees: 450.00,
                        availableSlots: [{
                            date: today,
                            slots: ['10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM', '2:00 PM - 3:00 PM']
                        }]
                    }
                ];
                localStorage.setItem('doctors', JSON.stringify(doctors));
            }
        }

        // Initialize
        initializeDefaultDoctors();
        updateSelects();
        loadDischargeList();
        updateDoctorsPatients();
        updateAppointmentsManagement();
    </script>
        </div>
    </div>
</body>
</html>
