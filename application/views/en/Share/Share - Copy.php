<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nerian Sharif - Official App</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 20px 0px 20px;
        }

        /* Header Section */
        .header {
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }

        /* .teal-circle {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: #2d6a6b;
            border-radius: 50%;
            z-index: -1;
        } */

        .logo {
            background: white;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .logo::before {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d6a6b 0%, #4a9b9c 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo::after {
            content: '🕌';
            position: absolute;
            font-size: 40px;
            z-index: 1;
        }

        .arabic-title {
            font-size: 36px;
            font-weight: bold;
            color: #2d6a6b;
            margin-bottom: 10px;
            font-family: 'Times New Roman', serif;
        }

        .subtitle {
            color: #2d6a6b;
            font-size: 16px;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .description {
            color: #2d6a6b;
            font-size: 14px;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Phone Mockups Section */
        .phones-section {
            position: relative;
            height: 600px;
            margin: 40px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .phone {
            position: absolute;
            width: 250px;
            height: 500px;
            background: #000;
            border-radius: 30px;
            padding: 8px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .phone:hover {
            transform: scale(1.05);
            z-index: 10;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 22px;
            overflow: hidden;
            position: relative;
        }

        .notch {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 20px;
            background: #000;
            border-radius: 10px;
            z-index: 10;
        }

        /* Main center phone */
        .phone-main {
            z-index: 5;
            transform: translateY(0);
        }

        .phone-main .phone-screen {
            background: linear-gradient(to bottom, #f8f9fa 0%, white 100%);
        }

        /* Left phone */
        .phone-left {
            left: -80px;
            transform: rotate(-15deg) scale(0.85);
            z-index: 3;
        }

        /* Right phone */
        .phone-right {
            right: -80px;
            transform: rotate(15deg) scale(0.85);
            z-index: 3;
        }

        /* App Content Mockup */
        .app-header {
            background: #2d6a6b;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .app-image {
            height: 120px;
            background: linear-gradient(135deg, #87ceeb 0%, #4682b4 100%);
            position: relative;
            overflow: hidden;
        }

        .app-image::after {
            content: '🕌';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 48px;
        }

        .app-navigation {
            display: flex;
            justify-content: space-around;
            padding: 15px 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-icon {
            width: 30px;
            height: 30px;
            background: #2d6a6b;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
        }

        .app-content {
            padding: 20px 15px;
            background: white;
            height: calc(100% - 180px);
            overflow: hidden;
        }

        .content-card {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .content-title {
            font-size: 10px;
            font-weight: bold;
            color: #856404;
            margin-bottom: 5px;
        }

        .content-text {
            font-size: 8px;
            color: #666;
            line-height: 1.3;
        }

        /* Bottom Section */
        .bottom-section {
            background: #2d6a6b;
            color: white;
            padding: 40px 20px;
            text-align: center;
            margin-top: 40px;
            border-radius: 20px 20px 0 0;
        }

        .bottom-description {
            font-size: 16px;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .download-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .app-store-btn,
        .google-play-btn {
            background: #000;
            border: 2px solid #fff;
            border-radius: 12px;
            padding: 12px 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .app-store-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
            background: #1a1a1a;
        }

        /* .google-play-btn {
            opacity: 0.7;
            cursor: not-allowed;
        } */

        /* .google-play-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            opacity: 0.8;
        } */

        .google-play-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
            background: #1a1a1a;
        }

        .app-store-btn:active {
            transform: translateY(0);
        }

        .btn-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .apple-logo,
        .google-logo {
            display: flex;
            align-items: center;
        }

        .btn-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
            position: relative;
        }

        .download-text-small {
            color: white;
            font-size: 11px;
            line-height: 1;
            margin-bottom: 2px;
            font-weight: 400;
        }

        .app-store-text {
            color: white;
            font-size: 18px;
            font-weight: 600;
            line-height: 1;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .coming-soon-badge {
            position: absolute;
            top: -8px;
            right: -40px;
            background: linear-gradient(135deg, #ff6b6b, #ffa500);
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 12px;
            animation: pulse 2s infinite;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.4);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @media (max-width: 480px) {
            .download-container {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .app-store-btn,
            .google-play-btn {
                width: 200px;
                justify-content: center;
            }
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .phone-main {
            animation: float 6s ease-in-out infinite;
        }

        .phone-left {
            animation: float 8s ease-in-out infinite;
        }

        .phone-right {
            animation: float 7s ease-in-out infinite;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .phones-section {
                height: 400px;
            }

            .phone {
                width: 180px;
                height: 360px;
            }

            .phone-left {
                left: -60px;
            }

            .phone-right {
                right: -60px;
            }

            .arabic-title {
                font-size: 28px;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .phones-section {
                height: 300px;
            }

            .phone {
                width: 140px;
                height: 280px;
            }

            .phone-left,
            .phone-right {
                display: none;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="teal-circle"></div>
            <input class="logo" type="image" src="<?php echo SURL . 'assets/images/AppLogo.png'; ?>" alt="">
            <p class="subtitle">The official app inspired by Nerian Sharif</p>
            <p class="description">Access trusted guidance, teachings of the Mashaikh, and opportunities to serve through faith.</p>
        </div>
        <!-- Phone Mockups Section -->
        <div class="phones-section">
            <!-- Left Phone -->
            <div class="phone phone-left">
                <div class="phone-screen">
                    <!-- <div class="notch"></div> -->

                    <input class="phone-screen" type="image" src="<?php echo SURL . 'assets/images/screen1.jpeg'; ?>" alt="">
                </div>
            </div>

            <!-- Main Center Phone -->
            <div class="phone phone-main">
                <div class="phone-screen">
                    <!-- <div class="notch"></div> -->
                    <input class="phone-screen" type="image" src="<?php echo SURL . 'assets/images/screen2.jpeg'; ?>" alt="">
                </div>
            </div>

            <!-- Right Phone -->
            <div class="phone phone-right">
                <div class="phone-screen">
                    <!-- <div class="notch"></div> -->
                    <input class="phone-screen" type="image" src="<?php echo SURL . 'assets/images/screen3.jpeg'; ?>" alt="">
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <p class="bottom-description">
                Experience the spiritual heritage of Nerian Sharif — access trusted guidance,
                teachings of the Mashaikh, and opportunities to serve through faith.
            </p>

            <div class="download-container">
                <button class="app-store-btn" onclick="downloadApp()">
                    <div class="btn-content">
                        <div class="apple-logo">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z" />
                            </svg>
                        </div>
                        <div class="btn-text">
                            <div class="download-text-small">Download on the</div>
                            <div class="app-store-text">App Store</div>
                        </div>
                    </div>
                </button>

                <button class="google-play-btn" onclick="downloadgoogle()">
                    <div class="btn-content">
                        <div class="google-logo">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.61 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.92 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z" />
                            </svg>
                        </div>
                        <div class="btn-text">
                            <div class="download-text-small">Get it on</div>
                            <div class="app-store-text">Google Play</div>
                            <!-- <div class="coming-soon-badge">Coming Soon</div> -->
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
    <script>
        // Add interactive hover effects
        document.addEventListener('DOMContentLoaded', function () {
            const phones = document.querySelectorAll('.phone');

            phones.forEach(phone => {
                phone.addEventListener('mouseenter', function () {
                    this.style.transform = this.style.transform.replace('scale(0.85)', 'scale(0.9)');
                });

                phone.addEventListener('mouseleave', function () {
                    if (this.classList.contains('phone-left')) {
                        this.style.transform = 'rotate(-15deg) scale(0.85)';
                    } else if (this.classList.contains('phone-right')) {
                        this.style.transform = 'rotate(15deg) scale(0.85)';
                    }
                });
            });

            // Add scroll animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            // Animate download buttons
            const downloadBtns = document.querySelectorAll('.app-store-btn, .google-play-btn');
            downloadBtns.forEach((btn, index) => {
                btn.style.opacity = '0';
                btn.style.transform = 'translateY(20px)';
                btn.style.transition = `all 0.6s ease ${index * 0.2}s`;
                observer.observe(btn);
            });
        });

        // Download app function
        function downloadApp() {
            // In a real implementation, this would redirect to the iOS App Store
            window.open('https://apps.apple.com/pk/app/jamal-e-naqshband/id6746319392', '_blank');
            // For demo purposes, show an alert
            alert('Redirecting to iOS App Store...\n\nThis would normally open:\nhttps://apps.apple.com/pk/app/jamal-e-naqshband/id6746319392');
        }
        function downloadgoogle() {
            // In a real implementation, this would redirect to the iOS App Store
            window.open('https://play.google.com/store/apps/details?id=com.jamalenaqshband', '_blank');
            // For demo purposes, show an alert
            alert('Redirecting to Google App Store...\n\nThis would normally open:\nhttps://play.google.com/store/apps/details?id=com.jamalenaqshband');
        }

        // Coming soon function for Android
        function comingSoon() {
            alert('🚀 Android App Coming Soon!\n\nWe\'re working hard to bring the Nerian Sharif app to Google Play Store. Stay tuned for updates!');
        }
    </script>
</body>

</html>