{{-- Message Bubble Template --}}
<template id="msg-row-template">
    <div class="msg-row" data-msg-id="">
        <div class="msg-avatar"></div>
        <div class="bubble-wrapper" style="max-width: 68%; position: relative;">
            <div class="bubble"></div>
            
            <!-- Reactions Display -->
            <div class="reactions-container" style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 4px;"></div>
            
            <div class="msg-footer-row" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 4px;">
                <div class="bubble-meta" style="margin-top: 0;"></div>
                
                <!-- Quick Reaction Actions (👍 / ❤️) -->
                <div class="bubble-actions" style="display: flex; gap: 4px; opacity: 0; transition: opacity 0.2s;">
                    <button class="bact react-btn" data-reaction="👍" title="React 👍">👍</button>
                    <button class="bact react-btn" data-reaction="❤️" title="React ❤️">❤️</button>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- Email Card Template --}}
<template id="email-card-template">
    <div class="email-card" data-msg-id="">
        <div class="email-card-header">
            <div>
                <span class="email-card-sender"></span>
                <span class="email-card-email"></span>
            </div>
            <div class="email-card-meta"></div>
        </div>
        <div class="email-card-body"></div>
        
        <!-- Reactions Display -->
        <div class="reactions-container" style="display: flex; gap: 4px; flex-wrap: wrap; margin-top: 8px;"></div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; border-top: 0.5px solid var(--color-border-tertiary); padding-top: 6px;">
            <span class="attachment-link-container" style="font-size: 12px;"></span>
            
            <!-- Email Reactions Actions -->
            <div class="bubble-actions" style="display: flex; gap: 4px;">
                <button class="bact react-btn" data-reaction="👍" title="React 👍">👍</button>
                <button class="bact react-btn" data-reaction="❤️" title="React ❤️">❤️</button>
            </div>
        </div>
    </div>
</template>
