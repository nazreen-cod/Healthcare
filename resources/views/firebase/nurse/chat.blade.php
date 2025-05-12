
@extends('firebase.layoutnurse')

@section('title', 'Patient Conversation')

@section('content')
    <style>
        .chat-container {
            background-color: #111b21;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 420px; /* Fixed width for WhatsApp-like appearance */
            margin: 0 auto; /* Center the container horizontally */
        }

        .chat-header {
            background-color: #202c33;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(134, 150, 160, 0.15);
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--success));
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            color: black;
            margin-right: 15px;
        }

        .chat-body {
            padding: 16px;
            background-color: #0b141a;
            position: relative;
            height: 65vh; /* Use viewport height for better responsiveness */
            overflow-y: auto;
        }

        .chat-day {
            text-align: center;
            margin: 15px 0;
        }

        .chat-day-pill {
            display: inline-block;
            background-color: #222e35;
            color: #8696a0;
            font-size: 12.5px;
            padding: 5px 12px;
            border-radius: 8px;
        }

        .message-out {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 8px;
        }

        .message-in {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 8px;
        }

        .message-bubble-out {
            background-color: #005c4b;
            color: #e9edef;
            border-radius: 7.5px;
            padding: 6px 10px;
            max-width: 70%;
            min-width: 120px;
            word-wrap: break-word;
            position: relative;
            border-top-right-radius: 0;
        }

        .message-bubble-in {
            background-color: #202c33;
            color: #e9edef;
            border-radius: 7.5px;
            padding: 6px 10px;
            max-width: 70%;
            min-width: 120px;
            word-wrap: break-word;
            position: relative;
            border-top-left-radius: 0;
        }

        .message-text {
            font-size: 14.2px;
            margin-bottom: 4px;
        }

        .message-meta {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .message-time {
            font-size: 11px;
            color: rgba(233, 237, 239, 0.6);
            margin-right: 4px;
        }

        .chat-footer {
            background-color: #202c33;
            padding: 10px;
            display: flex;
            align-items: center;
            border-top: 1px solid rgba(134, 150, 160, 0.15);
        }

        .chat-input {
            flex-grow: 1;
            background-color: #2a3942;
            border-radius: 24px;
            margin-right: 8px;
            padding: 0 16px;
        }

        .chat-input-field {
            background-color: transparent;
            border: none;
            color: #e9edef;
            padding: 9px 12px;
            font-size: 15px;
            width: 100%;
            outline: none; /* Remove outline focus */
        }

        .chat-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(0, 255, 157, 0.8);
            color: #000;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .chat-empty {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #8696a0;
        }

        .read-check {
            color: rgba(0, 255, 157, 0.8);
        }

        /* Custom scrollbar */
        .chat-body::-webkit-scrollbar {
            width: 6px;
        }

        .chat-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-body::-webkit-scrollbar-thumb {
            background-color: rgba(134, 150, 160, 0.3);
            border-radius: 3px;
        }

        /* Center the container in the page */
        .chat-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 85vh;
            padding: 0;
            margin: 0 auto;
        }

        /* Improved nurse name display */
        .nurse-label {
            font-size: 11px;
            color: rgba(233, 237, 239, 0.5);
            margin-bottom: 3px;
            text-align: right;
            font-style: italic;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        /* Current nurse indicator */
        .current-nurse {
            font-size: 10px;
            background-color: rgba(0, 255, 157, 0.15);
            color: rgba(0, 255, 157, 0.8);
            padding: 1px 5px;
            border-radius: 10px;
            margin-left: 5px;
            font-weight: 500;
            font-style: normal;
        }

        /* Optional: Add a subtle divider between nurse name and message */
        .message-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 2px 0 4px;
        }
    </style>

    <div class="chat-wrapper">
        <div class="chat-container">
            <!-- Chat Header -->
            <div class="chat-header">
                <a href="{{ route('firebase.nurse.dashboard') }}" class="btn btn-sm me-3" style="background: rgba(0, 0, 0, 0.3); color: var(--success);">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="chat-avatar">
                    {{ strtoupper(substr($patientData['name'] ?? 'U', 0, 1)) }}
                </div>
                <div class="ms-3">
                    <div class="text-white fw-bold">{{ $patientData['name'] ?? 'Unknown Patient' }}</div>
                    <div style="font-size: 0.8rem; color: var(--success);">
                        <i class="fas fa-phone-alt me-1"></i>
                        {{ $patientData['contact'] ?? 'No contact info' }}
                    </div>
                </div>
                <div class="ms-auto">
                    @if(isset($patientId))
                        <a href="{{ route('firebase.nurse.search') }}?patient_id={{ $patientId }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                            <i class="fas fa-user-circle me-1"></i> Profile
                        </a>
                    @endif
                </div>
            </div>

            <!-- Chat Body -->
            <div class="chat-body" id="chat-messages">
                @if(isset($groupedMessages) && count($groupedMessages) > 0)
                    @foreach($groupedMessages as $date => $msgs)
                        <div class="chat-day">
                        <span class="chat-day-pill">
                            {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                        </span>
                        </div>

                        @foreach($msgs as $message)
                            <div class="{{ $message['isFromNurse'] ? 'message-out' : 'message-in' }}">
                                <div class="{{ $message['isFromNurse'] ? 'message-bubble-out' : 'message-bubble-in' }}">
                                    @if($message['isFromNurse'] && isset($message['nurse_name']))
                                        <div class="nurse-label">
                                            {{ $message['nurse_name'] }}
                                            @php
                                                $nurseId = session('nurse_id');
                                                $messageNurseId = $message['nurse_id'] ?? null;
                                            @endphp
                                            @if($nurseId && $messageNurseId && $nurseId == $messageNurseId)
                                                <span class="current-nurse">You</span>
                                            @endif
                                        </div>
                                        <div class="message-divider"></div>
                                    @endif
                                    <div class="message-text">{{ $message['text'] }}</div>
                                    <div class="message-meta">
                                        <span class="message-time">{{ $message['time'] }}</span>
                                        @if($message['isFromNurse'])
                                            <small class="ms-2">
                                                <i class="fas fa-check read-check"></i>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @else
                    <div class="chat-empty">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>No messages yet</p>
                        <p class="small">Send a message to start the conversation</p>
                    </div>
                @endif
            </div>

            <!-- Chat Footer -->
            <div class="chat-footer">
                <form action="{{ route('firebase.nurse.sendMessage', ['chatId' => $chatId]) }}" method="POST" class="d-flex w-100">
                    @csrf
                    <div class="chat-input">
                        <input type="text" name="message" class="chat-input-field" placeholder="Type a message..." required>
                    </div>
                    <button type="submit" class="chat-send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Scroll to bottom of chat on load
        document.addEventListener('DOMContentLoaded', function() {
            const chatContainer = document.getElementById('chat-messages');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        });

        // Auto-refresh the chat every 10 seconds to see new messages
        setInterval(function() {
            // Get the current scroll position
            const chatContainer = document.getElementById('chat-messages');
            const isScrolledToBottom = chatContainer.scrollHeight - chatContainer.clientHeight <= chatContainer.scrollTop + 1;

            // Only reload if user is at the bottom of the chat
            if (isScrolledToBottom) {
                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newChatBody = doc.getElementById('chat-messages').innerHTML;
                        document.getElementById('chat-messages').innerHTML = newChatBody;

                        // Restore scroll position to bottom
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    });
            }
        }, 10000);
    </script>
@endsection
