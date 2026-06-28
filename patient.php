<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mint OPD · Patient Panel</title>
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
        .patient-container {
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
        .book-btn {
            background: #2d8b65;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        .book-btn:hover {
            background: #1a3b2e;
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
        .radio-group {
            display: flex;
            gap: 1rem;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }
        .radio-group input {
            width: auto;
            margin-right: 0.5rem;
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
    <div class="patient-container">
        <a href="index.php" class="back-link">← Back to Home</a>
        <div class="connection-info">Connected to <strong><?php echo htmlspecialchars($config['dbname']); ?></strong> as <strong><?php echo htmlspecialchars($config['user']); ?></strong>.</div>
        <h1>👤 Mint OPD · Patient Panel</h1>

        <!-- Registration Section -->
        <div id="register-section" class="section">
            <h2>Register</h2>
            <div class="form-group">
                <label for="reg-name">Full Name</label>
                <input type="text" id="reg-name" placeholder="Enter your full name">
            </div>
            <div class="form-group">
                <label for="reg-email">Email</label>
                <input type="email" id="reg-email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" placeholder="Enter password">
            </div>
            <div class="form-group">
                <label for="reg-age">Age</label>
                <input type="number" id="reg-age" placeholder="Enter your age" min="1">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male"> Male</label>
                    <label><input type="radio" name="gender" value="female"> Female</label>
                    <label><input type="radio" name="gender" value="other"> Other</label>
                </div>
            </div>
            <button onclick="register()">Register</button>
        </div>

        <!-- Login Section -->
        <div id="login-section" class="section">
            <h2>Login</h2>
            <div class="form-group">
                <label for="login-email">Email</label>
                <input type="email" id="login-email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label for="login-password">Password</label>
                <input type="password" id="login-password" placeholder="Enter password">
            </div>
            <button onclick="login()">Login</button>
        </div>

        <!-- Patient Dashboard (hidden initially) -->
        <div id="dashboard" class="hidden">
            <div class="section">
                <h2>Welcome, <span id="patient-name"></span>!</h2>
                <button onclick="logout()">Logout</button>
            </div>

            <!-- Book Appointment Section -->
            <div class="section">
                <h2>Book Appointment</h2>
                <div class="form-group">
                    <label for="book-city">Select City</label>
                    <select id="book-city" onchange="updateHospitalSelectForBooking()"></select>
                </div>
                <div class="form-group">
                    <label for="book-hospital">Select Hospital</label>
                    <select id="book-hospital" onchange="updateDoctorSelect()"></select>
                </div>
                <div id="doctors-container">
                    <h3>Select Doctor</h3>
                    <div id="doctors-list" class="grid"></div>
                </div>
            </div>

            <!-- My Appointments Section -->
            <div class="section">
                <h2>My Appointments</h2>
                <div id="appointments-list" class="grid"></div>
            </div>

            <!-- Follow-up Status Section -->
            <div class="section">
                <h2>Follow-up Status</h2>
                <div id="follow-up-status" class="grid"></div>
            </div>

            <!-- Bed Availability Section -->
            <div class="section">
                <h2>Bed Availability</h2>
                <div class="form-group">
                    <label for="bed-city-select">Select City</label>
                    <select id="bed-city-select" onchange="updateHospitalSelectForBeds()"></select>
                </div>
                <div class="form-group">
                    <label for="bed-hospital-select">Select Hospital</label>
                    <select id="bed-hospital-select" onchange="showBedAvailability()"></select>
                </div>
                <div id="bed-availability" class="grid"></div>
            </div>
        </div>
    </div>

    <script>
        // Load data from localStorage
        let patients = JSON.parse(localStorage.getItem('patients')) || [];
        let cities = JSON.parse(localStorage.getItem('cities')) || [];
        let hospitals = JSON.parse(localStorage.getItem('hospitals')) || [];
        let doctors = JSON.parse(localStorage.getItem('doctors')) || [];
        let beds = JSON.parse(localStorage.getItem('beds')) || [];
        let appointments = JSON.parse(localStorage.getItem('appointments')) || [];

        let currentPatient = null;
        let refreshIntervalId = null;

        // Check if patient is logged in
        const loggedInPatient = JSON.parse(localStorage.getItem('currentPatient'));
        if (loggedInPatient) {
            currentPatient = loggedInPatient;
            showDashboard();
        }

        // Register
        function register() {
            const name = document.getElementById('reg-name').value.trim();
            const email = document.getElementById('reg-email').value.trim();
            const password = document.getElementById('reg-password').value;
            const age = parseInt(document.getElementById('reg-age').value);
            const gender = document.querySelector('input[name="gender"]:checked')?.value;

            if (name && email && password && age && gender) {
                if (patients.find(p => p.email === email)) {
                    alert('Email already registered.');
                    return;
                }
                patients.push({ id: Date.now(), name, email, password, age, gender });
                localStorage.setItem('patients', JSON.stringify(patients));
                alert('Registration successful! Please login.');
                document.getElementById('reg-name').value = '';
                document.getElementById('reg-email').value = '';
                document.getElementById('reg-password').value = '';
                document.getElementById('reg-age').value = '';
                document.querySelector('input[name="gender"]:checked').checked = false;
            } else {
                alert('All fields are required.');
            }
        }

        // Login
        function login() {
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            const patient = patients.find(p => p.email === email && p.password === password);
            if (patient) {
                currentPatient = patient;
                localStorage.setItem('currentPatient', JSON.stringify(patient));
                showDashboard();
            } else {
                alert('Invalid email or password.');
            }
        }

        // Logout
        function logout() {
            currentPatient = null;
            localStorage.removeItem('currentPatient');
            document.getElementById('dashboard').classList.add('hidden');
            document.getElementById('register-section').classList.remove('hidden');
            document.getElementById('login-section').classList.remove('hidden');

            if (refreshIntervalId) {
                clearInterval(refreshIntervalId);
                refreshIntervalId = null;
            }
        }

        // Reload appointment data (used to keep follow-up status in sync with doctor actions)
        function reloadAppointmentData() {
            appointments = JSON.parse(localStorage.getItem('appointments')) || [];
            displayAppointments();
            displayFollowUpStatus();
        }

        // Show Dashboard
        function showDashboard() {
            document.getElementById('register-section').classList.add('hidden');
            document.getElementById('login-section').classList.add('hidden');
            document.getElementById('dashboard').classList.remove('hidden');
            document.getElementById('patient-name').textContent = currentPatient.name;
            updateBookingSelects();
            reloadAppointmentData();

            // Refresh every 3 seconds so follow-up changes made by the doctor appear automatically.
            if (!refreshIntervalId) {
                refreshIntervalId = setInterval(reloadAppointmentData, 3000);
            }
        }

        // Update Booking Selects
        function updateBookingSelects() {
            const citySelect = document.getElementById('book-city');
            citySelect.innerHTML = '<option value="">Select City</option>' +
                cities.map(city => `<option value="${city.id}">${city.name}</option>`).join('');

            const bedCitySelect = document.getElementById('bed-city-select');
            bedCitySelect.innerHTML = '<option value="">Select City</option>' +
                cities.map(city => `<option value="${city.id}">${city.name}</option>`).join('');
        }

        // Update Hospital Select for Booking
        function updateHospitalSelectForBooking() {
            const cityId = document.getElementById('book-city').value;
            const hospitalSelect = document.getElementById('book-hospital');
            if (cityId) {
                const cityHospitals = hospitals.filter(h => h.cityId === parseInt(cityId));
                hospitalSelect.innerHTML = '<option value="">Select Hospital</option>' +
                    cityHospitals.map(hospital => `<option value="${hospital.id}">${hospital.name}</option>`).join('');
            } else {
                hospitalSelect.innerHTML = '<option value="">Select City First</option>';
            }
        }

        // Update Doctor Select
        function updateDoctorSelect() {
            const hospitalId = document.getElementById('book-hospital').value;
            const doctorsList = document.getElementById('doctors-list');
            if (hospitalId) {
                const hospitalDoctors = doctors.filter(d => d.hospitalId === parseInt(hospitalId));
                doctorsList.innerHTML = hospitalDoctors.map(doctor => {
                    const today = new Date().toLocaleDateString();
                    const availableSlots = doctor.availableSlots?.find(s => s.date === today)?.slots || [];

                    // If no slots set, show default slots
                    const defaultSlots = ['9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM', '2:00 PM - 3:00 PM', '3:00 PM - 4:00 PM', '4:00 PM - 5:00 PM'];
                    const slotsToShow = availableSlots.length > 0 ? availableSlots : defaultSlots;

                    return `
                        <div class="item">
                            <h3>${doctor.name}</h3>
                            <p><strong>Specialization:</strong> ${doctor.speciality || 'General'}</p>
                            <p><strong>Department:</strong> ${doctor.department || 'General Medicine'}</p>
                            <p><strong>Fees:</strong> ₹${doctor.fees || 0}</p>
                            <div class="form-group" style="margin-top: 1rem;">
                                <label for="slot-${doctor.id}" style="display: block; margin-bottom: 0.5rem; color: #3d6e58; font-weight: 500;">Select Time Slot:</label>
                                <select id="slot-${doctor.id}" class="slot-select" style="width: 100%; padding: 0.5rem; border: 2px solid #c0ead8; border-radius: 5px;">
                                    <option value="">Select Slot</option>
                                    ${slotsToShow.map(slot => `<option value="${slot}">${slot}</option>`).join('')}
                                </select>
                            </div>
                            <button class="book-btn" onclick="bookAppointment(${doctor.id})" style="margin-top: 1rem;">Book Appointment</button>
                        </div>
                    `;
                }).join('');

                if (hospitalDoctors.length === 0) {
                    doctorsList.innerHTML = '<p style="text-align: center; color: #3d6e58; padding: 2rem;">No doctors available in this hospital.</p>';
                }
            } else {
                doctorsList.innerHTML = '<p style="text-align: center; color: #3d6e58; padding: 2rem;">Please select a hospital first.</p>';
            }
        }

        // Book Appointment
        function bookAppointment(doctorId) {
            const slotSelect = document.getElementById(`slot-${doctorId}`);
            const slot = slotSelect ? slotSelect.value : '';

            if (!slot) {
                alert('Please select a time slot before booking.');
                slotSelect.focus();
                return;
            }

            const doctor = doctors.find(d => d.id === doctorId);
            if (!doctor) {
                alert('Doctor not found. Please try again.');
                return;
            }

            // Check if slot is already booked
            const today = new Date().toLocaleDateString();
            const existingAppointment = appointments.find(a =>
                a.doctorId === doctorId &&
                a.date === today &&
                a.slot === slot &&
                !a.completed
            );

            if (existingAppointment) {
                alert('This slot is already booked. Please select a different time slot.');
                return;
            }

            // Find next queue number for this doctor and slot
            const doctorAppointments = appointments.filter(a => a.doctorId === doctorId && a.slot === slot && a.date === today);
            const queueNumber = doctorAppointments.length + 1;

            const hospital = hospitals.find(h => h.id === doctor.hospitalId);
            const city = cities.find(c => c.id === doctor.cityId);

            appointments.push({
                id: Date.now(),
                patientId: currentPatient.id,
                doctorId: doctorId,
                hospitalId: doctor.hospitalId,
                cityId: doctor.cityId,
                queueNumber: queueNumber,
                slot: slot,
                date: today,
                completed: false,
                followUp: false
            });

            localStorage.setItem('appointments', JSON.stringify(appointments));
            displayAppointments();

            alert(`✅ Appointment booked successfully!\n\nDoctor: ${doctor.name}\nHospital: ${hospital ? hospital.name : 'N/A'}\nSlot: ${slot}\nQueue Number: ${queueNumber}\n\nPlease arrive 15 minutes before your slot time.`);

            // Reset the slot selection
            slotSelect.value = '';
        }

        // Display Appointments
        function displayAppointments() {
            const appointmentsList = document.getElementById('appointments-list');
            const patientAppointments = appointments.filter(a => a.patientId === currentPatient.id);
            appointmentsList.innerHTML = patientAppointments.map(appointment => {
                const doctor = doctors.find(d => d.id === appointment.doctorId);
                const hospital = hospitals.find(h => h.id === appointment.hospitalId);
                const city = cities.find(c => c.id === appointment.cityId);
                return `
                    <div class="item">
                        <h3>${doctor ? doctor.name : 'Unknown Doctor'}</h3>
                        <p><strong>Hospital:</strong> ${hospital ? hospital.name : 'Unknown'}</p>
                        <p><strong>City:</strong> ${city ? city.name : 'Unknown'}</p>
                        <p><strong>Queue Number:</strong> ${appointment.queueNumber}</p>
                        <p><strong>Slot:</strong> ${appointment.slot}</p>
                        <p><strong>Date:</strong> ${appointment.date}</p>
                        <p><strong>Status:</strong> ${appointment.completed ? 'Completed' : (appointment.followUp ? 'Follow-up Ongoing' : 'Pending')}</p>
                    </div>
                `;
            }).join('');
        }

        // Display Follow-up Status
        function displayFollowUpStatus() {
            const followUpDiv = document.getElementById('follow-up-status');
            const patientFollowUps = appointments.filter(a => a.patientId === currentPatient.id && a.followUp);
            followUpDiv.innerHTML = patientFollowUps.map(appointment => {
                const doctor = doctors.find(d => d.id === appointment.doctorId);
                const hospital = hospitals.find(h => h.id === appointment.hospitalId);
                return `
                    <div class="item">
                        <h3>${doctor ? doctor.name : 'Unknown Doctor'}</h3>
                        <p><strong>Hospital:</strong> ${hospital ? hospital.name : 'Unknown'}</p>
                        <p><strong>Last Visit:</strong> ${appointment.date}</p>
                        <p><strong>Status:</strong> Treatment Ongoing - Consultant Patient</p>
                    </div>
                `;
            }).join('');
            if (patientFollowUps.length === 0) {
                followUpDiv.innerHTML = '<p>No ongoing treatments.</p>';
            }
        }

        // Update Hospital Select for Beds
        function updateHospitalSelectForBeds() {
            const cityId = document.getElementById('bed-city-select').value;
            const hospitalSelect = document.getElementById('bed-hospital-select');
            if (cityId) {
                const cityHospitals = hospitals.filter(h => h.cityId === parseInt(cityId));
                hospitalSelect.innerHTML = '<option value="">Select Hospital</option>' +
                    cityHospitals.map(hospital => `<option value="${hospital.id}">${hospital.name}</option>`).join('');
            } else {
                hospitalSelect.innerHTML = '<option value="">Select City First</option>';
            }
        }

        // Show Bed Availability
        function showBedAvailability() {
            const hospitalId = document.getElementById('bed-hospital-select').value;
            const bedAvailability = document.getElementById('bed-availability');
            if (hospitalId) {
                const hospitalBeds = beds.filter(b => b.hospitalId === parseInt(hospitalId));
                bedAvailability.innerHTML = hospitalBeds.map(bed => `
                    <div class="item">
                        <h3>${bed.type.charAt(0).toUpperCase() + bed.type.slice(1)} Beds</h3>
                        <p><strong>Available:</strong> ${bed.total - bed.occupied}</p>
                    </div>
                `).join('');
            } else {
                bedAvailability.innerHTML = '';
            }
        }
    </script>
</body>
</html>
