<div class="section-content">
    <h4 class="section-header">Patient Demographics</h4>
    <table class="table table-bordered table-custom">
        <tbody>
        <!-- Check if $patient exists, otherwise show a message -->
        @if ($patient)
            <tr>
                <th>Name</th>
                <td>{{ $patient->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Gender</th>
                <td>{{ $patient->gender ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Location</th>
                <td>{{ $patient->location ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>ID No.</th>
                <td>{{ $patient->id_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date of Birth</th>
                <td>{{ $patient->dob ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Nationality</th>
                <td>{{ $patient->nationality ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Visit No.</th>
                <td>{{ $patient->visit_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Age</th>
                <td>{{ $patient->age ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Race</th>
                <td>{{ $patient->race ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Allergies</th>
                <td>{{ $patient->allergies ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Medical Alerts</th>
                <td>{{ $patient->medical_alerts ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Blood Pressure</th>
                <td>{{ $patient->bloodpm ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>BMI</th>
                <td>{{ $patient->bmi ?? 'N/A' }} ({{ $patient->bmi_status ?? 'N/A' }})</td>
            </tr>
            <tr>
                <th>Height</th>
                <td>{{ $patient->height ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Weight</th>
                <td>{{ $patient->weight ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Pulse</th>
                <td>{{ $patient->plusec ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Respiratory Rate</th>
                <td>{{ $patient->respiratingr ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Temperature</th>
                <td>{{ $patient->tempreturec ?? 'N/A' }}</td>
            </tr>
        @else
            <!-- Show this message if $patient is not available -->
            <tr>
                <td colspan="2" class="text-center text-danger">Patient information not available.</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
