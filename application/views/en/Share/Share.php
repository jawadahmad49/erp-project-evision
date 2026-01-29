<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opening App...</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .container {
            text-align: center;
            max-width: 400px;
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .status {
            font-size: 16px;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 15px 25px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-primary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-whatsapp {
            background: #25d366;
        }

        .btn-whatsapp:hover {
            background: #1da851;
            transform: translateY(-2px);
        }

        .btn-sms {
            background: #007AFF;
        }

        .btn-sms:hover {
            background: #0056CC;
            transform: translateY(-2px);
        }

        .hidden {
            display: none !important;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    .alert-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }
        
        .alert-content {
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 15px;
            max-width: 400px;
            margin: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .alert-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .alert-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        
        .alert-message {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
            color: #666;
        }
        
        .alert-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .alert-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .alert-btn-primary {
            background: #007AFF;
            color: white;
        }
        
        .alert-btn-primary:hover {
            background: #0056CC;
        }
        
        .alert-btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .alert-btn-secondary:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">📱</div>
        <h1>Opening App...</h1>
        <div class="status" id="status">
            Attempting to open the application
            <div class="loading"></div>
        </div>
        
        <div class="buttons hidden" id="fallback-options">
            <button class="btn btn-whatsapp" onclick="openWhatsApp()">
                💬 Open WhatsApp
            </button>
            <button class="btn btn-sms" onclick="openSMS()">
                📨 Send SMS
            </button>
            <button class="btn btn-primary" onclick="openPlayStore()">
                📲 Download App
            </button>
        </div>
    </div>

    <!-- Alert Popup for Redirect Permission -->
    <div id="redirect-alert" class="alert-popup hidden">
        <div class="alert-content">
            <div class="alert-icon">🚫</div>
            <div class="alert-title">Browser Security Notice</div>
            <div class="alert-message">
                Your browser is blocking automatic redirects for security reasons. 
                Please click "Allow" to proceed with opening the app or enable pop-ups 
                for this site in your browser settings.
            </div>
            <div class="alert-buttons">
                <button class="alert-btn alert-btn-primary" onclick="handleRedirectPermission(true)">
                    Allow Redirect
                </button>
                <button class="alert-btn alert-btn-secondary" onclick="handleRedirectPermission(false)">
                    Show Options
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const APP_PACKAGE = 'com.jamalenaqshband';
        const ANDROID_INTENT = `intent://open/#Intent;scheme=jamalenaqshband;package=${APP_PACKAGE};end`;
        const IOS_SCHEME = 'jamalenaqshband://open'; // This should match your app's URL scheme
        const ANDROID_STORE_URL = 'https://play.google.com/store/apps/details?id=com.jamalenaqshband';
        const IOS_STORE_URL = 'https://apps.apple.com/pk/app/jamal-e-naqshband/id6746319392';
        
        // Default contact info (you can customize these)
        const WHATSAPP_NUMBER = '+1234567890'; // Replace with your WhatsApp number
        const SMS_NUMBER = '+1234567890'; // Replace with your SMS number
        const DEFAULT_MESSAGE = 'Hello! I visited your page.';
        
        // Device detection
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isAndroid = /Android/.test(navigator.userAgent);
        const STORE_URL = isIOS ? IOS_STORE_URL : ANDROID_STORE_URL;
        
        let attemptCount = 0;
        let hasRedirected = false;
        let redirectBlocked = false;
        let userInteracted = false;
        
        // Track user interaction for redirect permission
        document.addEventListener('click', () => { userInteracted = true; });
        document.addEventListener('touchstart', () => { userInteracted = true; });
        
        function showRedirectAlert() {
            document.getElementById('redirect-alert').classList.remove('hidden');
        }
        
        function hideRedirectAlert() {
            document.getElementById('redirect-alert').classList.add('hidden');
        }
        
        function handleRedirectPermission(allow) {
            hideRedirectAlert();
            if (allow) {
                // User granted permission, try again with user gesture
                userInteracted = true;
                redirectBlocked = false;
                setTimeout(tryOpenApp, 100);
            } else {
                // User declined, show fallback options
                redirectBlocked = true;
                updateStatus('Choose an option to continue:');
                showFallbackOptions();
            }
        }
        
        function detectRedirectBlock(url, callback) {
            if (!userInteracted) {
                // No user interaction, likely to be blocked
                showRedirectAlert();
                return;
            }
            
            try {
                const startTime = Date.now();
                const testWindow = window.open('', '_blank');
                
                if (!testWindow) {
                    // Pop-up blocked
                    redirectBlocked = true;
                    showRedirectAlert();
                    return;
                }
                
                testWindow.close();
                
                // If we get here, redirects should work
                callback();
                
            } catch (error) {
                redirectBlocked = true;
                showRedirectAlert();
            }
        }
        
        function updateStatus(message) {
            document.getElementById('status').innerHTML = message;
        }
        
        function showFallbackOptions() {
            const options = document.getElementById('fallback-options');
            options.classList.remove('hidden');
            options.classList.add('fade-in');
        }
        
        function tryOpenApp() {
            if (hasRedirected || redirectBlocked) return;
            
            // Check for redirect permissions first
            if (!userInteracted) {
                detectRedirectBlock('', () => tryOpenApp());
                return;
            }
            
            try {
                if (isAndroid) {
                    // For Android, use intent URL with fallback
                    attemptAppOpen(ANDROID_INTENT);
                } else if (isIOS) {
                    // For iOS, try the custom scheme
                    attemptAppOpen(IOS_SCHEME);
                } else {
                    // For other platforms, go straight to store
                    handleAppNotInstalled();
                }
            } catch (error) {
                console.log('Error trying to open app:', error);
                handleAppNotInstalled();
            }
        }
        
        function attemptAppOpen(appUrl) {
            const startTime = Date.now();
            let appOpened = false;
            
            // Create a hidden iframe to attempt opening the app
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.style.width = '1px';
            iframe.style.height = '1px';
            
            const cleanup = () => {
                if (iframe.parentNode) {
                    document.body.removeChild(iframe);
                }
            };
            
            // Fallback timer
            const fallbackTimer = setTimeout(() => {
                if (!appOpened) {
                    cleanup();
                    handleAppNotInstalled();
                }
            }, 3000);
            
            // Listen for page visibility changes (indicates app opened)
            const visibilityHandler = () => {
                if (document.hidden) {
                    appOpened = true;
                    hasRedirected = true;
                    clearTimeout(fallbackTimer);
                    cleanup();
                    document.removeEventListener('visibilitychange', visibilityHandler);
                }
            };
            
            document.addEventListener('visibilitychange', visibilityHandler);
            
            try {
                document.body.appendChild(iframe);
                iframe.src = appUrl;
                
                // Additional fallback for iOS
                if (isIOS) {
                    setTimeout(() => {
                        if (!appOpened) {
                            // Try direct redirect for iOS
                            window.location.href = appUrl;
                        }
                    }, 1000);
                }
                
            } catch (error) {
                clearTimeout(fallbackTimer);
                cleanup();
                document.removeEventListener('visibilitychange', visibilityHandler);
                
                if (error.message.includes('scheme does not have a registered handler')) {
                    // App not installed
                    handleAppNotInstalled();
                } else {
                    console.log('App open attempt failed:', error);
                    handleAppNotInstalled();
                }
            }
        }
        
        function tryAppScheme() {
            // This function is now integrated into attemptAppOpen
            // Kept for compatibility but redirects to new method
            if (hasRedirected || redirectBlocked) return;
            handleAppNotInstalled();
        }
        
        function handleAppNotInstalled() {
            if (hasRedirected) return;
            
            updateStatus('App not found. Trying WhatsApp...');
            
            setTimeout(() => {
                tryOpenWhatsApp();
            }, 1000);
        }
        
        function tryOpenWhatsApp() {
            if (hasRedirected || redirectBlocked) return;
            
            try {
                const whatsappURL = `https://wa.me/${WHATSAPP_NUMBER.replace('+', '')}?text=${encodeURIComponent(DEFAULT_MESSAGE)}`;
                
                // Check if user has interacted
                if (!userInteracted) {
                    showRedirectAlert();
                    return;
                }
                
                // Create a temporary link to handle the redirect
                const tempLink = document.createElement('a');
                tempLink.href = whatsappURL;
                tempLink.target = '_blank';
                tempLink.style.display = 'none';
                document.body.appendChild(tempLink);
                
                try {
                    tempLink.click();
                    document.body.removeChild(tempLink);
                } catch (clickError) {
                    document.body.removeChild(tempLink);
                    if (clickError.message.includes('blocked') || clickError.name === 'SecurityError') {
                        showRedirectAlert();
                        return;
                    }
                }
                
                // Check if user is still on page after attempting WhatsApp
                setTimeout(() => {
                    if (!document.hidden && !hasRedirected) {
                        // User likely cancelled or WhatsApp not available
                        handleWhatsAppNotAvailable();
                    }
                }, 2000);
                
            } catch (error) {
                if (error.message.includes('blocked') || error.name === 'SecurityError') {
                    showRedirectAlert();
                } else {
                    handleWhatsAppNotAvailable();
                }
            }
        }
        
        function handleWhatsAppNotAvailable() {
            updateStatus('Choose an option to continue:');
            showFallbackOptions();
        }
        
        // Manual button functions
        function openWhatsApp() {
            try {
                const whatsappURL = `https://wa.me/${WHATSAPP_NUMBER.replace('+', '')}?text=${encodeURIComponent(DEFAULT_MESSAGE)}`;
                const newWindow = window.open(whatsappURL, '_blank');
                if (!newWindow) {
                    // Pop-up blocked
                    alert('Pop-ups are blocked. Please allow pop-ups for this site or copy this number: ' + WHATSAPP_NUMBER);
                }
            } catch (error) {
                alert('Unable to open WhatsApp. Please contact us at: ' + WHATSAPP_NUMBER);
            }
        }
        
        function openSMS() {
            try {
                const smsURL = `sms:${SMS_NUMBER}?body=${encodeURIComponent(DEFAULT_MESSAGE)}`;
                window.location.href = smsURL;
            } catch (error) {
                alert('Unable to open SMS. Please text us at: ' + SMS_NUMBER);
            }
        }
        
        function openPlayStore() {
            try {
                const newWindow = window.open(STORE_URL, '_blank');
                if (!newWindow) {
                    // Pop-up blocked
                    alert('Pop-ups are blocked. Please allow pop-ups for this site or manually visit the app store to download our app.');
                }
            } catch (error) {
                alert('Unable to open app store. Please search for "Jamal e Naqshband" in your device\'s app store.');
            }
        }
        
        // Detect if user returns to page (app didn't open)
        let pageHidden = false;
        
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                pageHidden = true;
                hasRedirected = true; // Assume app opened successfully
            } else if (pageHidden && !hasRedirected) {
                // User returned, app likely didn't open
                handleAppNotInstalled();
            }
        });
        
        // Start the process when page loads
        window.addEventListener('load', () => {
            setTimeout(tryOpenApp, 500);
        });
        
        // Prevent multiple redirections
        window.addEventListener('beforeunload', () => {
            hasRedirected = true;
        });
    </script>
</body>
</html>