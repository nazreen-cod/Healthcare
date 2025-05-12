
@extends('firebase.layoutnurse')

@section('title', 'Patient Check-in QR Code')

@section('content')
    <style>
        /* QR Code Container - Compact Styles */
        .qr-container {
            padding: 10px 0;
        }

        .qr-header {
            font-family: 'Orbitron', sans-serif;
            color: var(--success);
            margin-bottom: 1rem;
            font-size: 1.5rem;
            position: relative;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            text-shadow: 0 0 15px rgba(0, 255, 157, 0.3);
        }

        .qr-header::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 60px;
            height: 2px;
            background: linear-gradient(to right, var(--success), transparent);
        }

        /* Cyber Card - Compact */
        .cyber-card {
            background: rgba(16, 25, 36, 0.7);
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 157, 0.2);
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        /* Two-column layout for compact design */
        .card-flex-container {
            display: grid;
            grid-template-columns: minmax(250px, 1fr) 1fr;
            gap: 15px;
        }

        /* QR Display - Compact */
        .qr-display {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            margin: 0 auto;
            max-width: 220px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: visible;
            text-align: center;
        }

        .qr-display svg {
            position: relative;
            z-index: 2;
            display: block;
            margin: 0 auto;
            max-width: 100%;
            height: auto;
        }

        .qr-display::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(0, 255, 157, 0) 0%,
                rgba(0, 255, 157, 0.2) 50%,
                rgba(0, 255, 157, 0) 100%
            );
            animation: qrScan 2s linear infinite;
            z-index: 1;
            pointer-events: none;
        }

        @keyframes qrScan {
            0% {
                transform: translateY(-100%) translateX(-100%);
            }
            100% {
                transform: translateY(100%) translateX(100%);
            }
        }

        /* Patient Info - Compact */
        .patient-info {
            text-align: center;
            color: #fff;
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(0, 255, 157, 0.1);
            border: 1px solid rgba(0, 255, 157, 0.2);
        }

        .patient-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--success);
        }

        .patient-id {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Instructions - Compact */
        .instructions {
            color: #e1e1e1;
            padding: 12px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.2);
            border-left: 3px solid var(--success);
        }

        .instructions h5 {
            color: var(--success);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }

        .instructions h5 i {
            margin-right: 10px;
        }

        .instructions ol {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .instructions li {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        /* Action Buttons - Compact */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-cyber-back {
            background: linear-gradient(45deg, var(--secondary), var(--success));
            border: none;
            color: white;
            border-radius: 6px;
            padding: 8px 15px;
            font-weight: 500;
            letter-spacing: 1px;
            text-decoration: none;
            text-transform: uppercase;
            transition: all 0.3s;
            flex: 1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cyber-back i {
            margin-right: 5px;
        }

        /* Status Indicator */
        .status-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 8px var(--success);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
            100% {
                transform: scale(0.8);
                opacity: 0.8;
            }
        }

        @media (max-width: 767.98px) {
            .card-flex-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="container qr-container">
        <h2 class="qr-header"><i class="fas fa-qrcode me-2"></i>Patient Check-in</h2>

        <div class="cyber-card">
            <div class="status-indicator"></div>

            <div class="card-flex-container">
                <!-- Left column: QR code & patient info -->
                <div>
                    <div class="qr-display">
                        {!! $qrCode !!}
                    </div>

                    <div class="patient-info">
                        <div class="patient-name">{{ $patient['name'] ?? 'Unknown Patient' }}</div>
                        <div class="patient-id">ID: {{ $patient['id_no'] ?? 'No ID available' }}</div>
                    </div>
                </div>

                <!-- Right column: Instructions & actions -->
                <div>
                    <div class="instructions">
                        <h5><i class="fas fa-info-circle"></i>Instructions</h5>
                        <ol>
                            <li>Position QR code visible to scanner</li>
                            <li>Wait for confirmation sound/light</li>
                            <li>Verify patient details on screen</li>
                            <li>Print a copy if needed</li>
                        </ol>
                    </div>

                    <div class="action-buttons">
                        <button onclick="printQRCode()" class="btn-cyber-back">
                            <i class="fas fa-print"></i> Print QR Code
                        </button>
                        <a href="{{ route('firebase.nurse.search') }}" class="btn-cyber-back">
                            <i class="fas fa-arrow-left"></i> Back to Search
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printQRCode() {
            // Create a new window
            const printWindow = window.open('', '_blank');

            // Write the print content to the new window
            printWindow.document.write(`
        <html>
        <head>
            <title>Patient QR Code</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 20px;
                }
                .qr-container {
                    margin: 0 auto;
                    max-width: 400px;
                }
                .patient-info {
                    margin-top: 20px;
                }
                .patient-name {
                    font-size: 18px;
                    font-weight: bold;
                }
                .patient-id {
                    font-size: 14px;
                    color: #555;
                    margin-top: 5px;
                }
                .hospital-info {
                    margin-top: 30px;
                    font-size: 12px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="qr-container">
                <h2>Patient Check-in QR Code</h2>
                <div>
                    ${document.querySelector('.qr-display').innerHTML}
                </div>
                <div class="patient-info">
                    <div class="patient-name">{{ $patient['name'] ?? 'Unknown Patient' }}</div>
                    <div class="patient-id">ID: {{ $patient['id_no'] ?? 'No ID available' }}</div>
                </div>
                <div class="hospital-info">
                    <p>Please present this QR code during your hospital visit.</p>
                    <p>{{ date('Y-m-d H:i') }}</p>
                </div>
            </div>
            <script>
                // Print automatically then close
                window.onload = function() {
                    window.print();
                    setTimeout(function() {
                        window.close();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);

            // Close the document
            printWindow.document.close();
        }
    </script>
@endsection
