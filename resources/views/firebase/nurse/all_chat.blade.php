
@extends('firebase.layoutnurse')

@section('title', 'All Conversations')

@section('content')
    <div class="container py-3">
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">
                <div class="cyber-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="section-header mb-0">
                            <i class="fas fa-comments me-2"></i>All Conversations
                        </h3>
                        <a href="{{ route('firebase.nurse.dashboard') }}" class="btn btn-sm" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                        </a>
                    </div>

                    @if(isset($allChats) && count($allChats) > 0)
                        @foreach($allChats as $chat)
                            <a href="{{ route('firebase.nurse.viewChat', ['chatId' => $chat['chat_id']]) }}"
                               class="d-flex align-items-center p-3 mb-3 text-decoration-none"
                               style="background: rgba(0, 0, 0, 0.2); border-radius: 8px; border: 1px solid rgba(0, 255, 157, 0.2);">
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--secondary), var(--success)); color: black; display: flex; justify-content: center; align-items: center; margin-right: 15px;">
                                    {{ strtoupper(substr($chat['patient_name'] ?? 'U', 0, 1)) }}
                                </div>
                                <div style="flex: 1; overflow: hidden;">
                                    <div class="d-flex justify-content-between">
                                        <span style="color: var(--success); font-weight: 500; font-size: 1.1rem;">{{ $chat['patient_name'] }}</span>
                                        @if($chat['unread_count'] > 0)
                                            <span style="background: var(--success); color: black; font-size: 0.8rem; padding: 2px 8px; border-radius: 10px;">
                                            {{ $chat['unread_count'] }} new
                                        </span>
                                        @endif
                                    </div>
                                    <div style="color: #e1e1e1; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 3px;">
                                        @if(isset($chat['last_message']))
                                            <span style="color: rgba(255, 255, 255, 0.6);">
                                            {{ $chat['last_message']['isFromNurse'] ? 'You: ' : '' }}
                                        </span>
                                            {{ $chat['last_message']['text'] }}
                                            <span style="color: rgba(255, 255, 255, 0.5); font-size: 0.8rem; margin-left: 5px;">
                                            {{ \Carbon\Carbon::createFromTimestampMs($chat['last_message']['timestamp'])->format('g:i A') }}
                                        </span>
                                        @else
                                            <span style="font-style: italic; color: rgba(255, 255, 255, 0.4);">No messages yet</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center p-5" style="background: rgba(0, 0, 0, 0.2); border-radius: 8px;">
                            <i class="fas fa-comments mb-3" style="font-size: 3rem; color: var(--success);"></i>
                            <p class="mb-2">No conversations yet</p>
                            <p class="small text-muted mb-3">When patients message you, they'll appear here</p>
                            <a href="{{ route('firebase.nurse.search') }}" class="btn" style="background: rgba(0, 0, 0, 0.3); border: 1px solid var(--success); color: var(--success);">
                                <i class="fas fa-search me-1"></i> Find Patients
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
