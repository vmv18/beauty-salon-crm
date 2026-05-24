@push('styles')
<style>
    .logo-link {
        padding: 0.5rem 1rem;
        transition: transform 0.3s ease;
    }
    .logo-link:hover {
        transform: scale(1.05);
    }
    .logo-text {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
        position: relative;
    }
    .logo-text::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .logo-link:hover .logo-text::after {
        opacity: 1;
    }
    #notificationsDropdown {
        position: relative;
        align-self: flex-start;
    }
    #notificationsDropdown.navbar-item {
        display: flex !important;
        align-items: center !important;
        padding: 0 !important;
        flex-grow: 0 !important;
        flex-shrink: 0 !important;
        width: auto !important;
        min-width: auto !important;
        max-width: none !important;
    }
    .navbar-end #notificationsDropdown.navbar-item {
        flex-grow: 0 !important;
        flex-shrink: 0 !important;
    }
    .notifications-button {
        position: relative;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        padding: 0.5rem !important;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
        cursor: pointer;
        background: transparent !important;
        border: none !important;
        width: auto !important;
        min-width: auto !important;
        max-width: none !important;
        color: #4a5568 !important;
        line-height: 1 !important;
    }
    .notifications-button:hover {
        background: transparent !important;
        color: #9333ea !important;
    }
    .notifications-button svg {
        display: block !important;
        color: inherit !important;
        transition: color 0.3s ease;
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        fill: none !important;
        stroke: currentColor !important;
        flex-shrink: 0 !important;
    }
    .notification-badge {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.65rem;
        font-weight: 700;
        border: 2px solid white;
        padding: 0 0.25rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .notification-badge:empty {
        display: none;
    }
    .notifications-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        margin-top: 0.5rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 24rem;
        max-height: 500px;
        overflow-y: auto;
        z-index: 1000;
        border: 1px solid #e2e8f0;
    }
    .notifications-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .notifications-header strong {
        color: #2d3748;
        font-size: 1rem;
    }
    .notifications-divider {
        margin: 0;
        background-color: #e2e8f0;
    }
    .notifications-list {
        max-height: 400px;
        overflow-y: auto;
    }
    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f7fafc;
        transition: background-color 0.2s ease;
    }
    .notification-item:hover {
        background-color: #f7fafc;
    }
    .notification-item:last-child {
        border-bottom: none;
    }
    @media (max-width: 1023px) {
        #notificationsDropdown {
            width: auto !important;
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }
        #notificationsDropdown.navbar-item {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            width: auto !important;
            flex-grow: 0 !important;
            flex-shrink: 0 !important;
        }
        .notifications-button {
            width: auto !important;
            min-width: auto !important;
            flex-shrink: 0 !important;
        }
        .notifications-dropdown {
            width: calc(100vw - 2rem);
            right: 1rem;
            left: 1rem;
        }
    }

    /* Unified icon button styles */
    .navbar-item .button.is-text,
    .navbar-item .notifications-button {
        background: transparent;
        border: none;
        color: #4a5568;
        transition: color 0.3s ease;
        padding: 0.5rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }
    .navbar-item .button.is-text:hover,
    .navbar-item .notifications-button:hover {
        background: transparent;
        color: #9333ea;
    }
    .navbar-item .button.is-text:focus,
    .navbar-item .notifications-button:focus {
        box-shadow: none;
    }

    /* Icon styles in buttons */
    .navbar-item .button.is-text .icon,
    .navbar-item .button.is-text svg,
    .navbar-item .notifications-button .icon,
    .navbar-item .notifications-button svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
        display: inline-block !important;
        vertical-align: middle;
        color: inherit !important;
    }
    
    /* Specific styles for notifications SVG - override Bulma */
    #notificationsDropdown .notifications-button svg,
    .navbar-item#notificationsDropdown .notifications-button svg,
    .navbar-end .navbar-item#notificationsDropdown .notifications-button svg {
        width: 1.25rem !important;
        height: 1.25rem !important;
        max-width: 1.25rem !important;
        max-height: 1.25rem !important;
        color: #4a5568 !important;
        fill: none !important;
        stroke: currentColor !important;
        stroke-width: 2 !important;
        flex-shrink: 0 !important;
        vertical-align: middle !important;
    }
    #notificationsDropdown .notifications-button:hover svg,
    .navbar-item#notificationsDropdown .notifications-button:hover svg,
    .navbar-end .navbar-item#notificationsDropdown .notifications-button:hover svg {
        color: #9333ea !important;
    }
</style>
@endpush

