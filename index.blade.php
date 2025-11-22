<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تحلیل تصویر گیاه</title>
    <link href="https://cdn.fontcdn.ir/Font/Persian/Vazir/Vazir.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#F6F1E1',
                        header: '#F3A6B5',
                        primary: '#A8D5BA',
                        danger: '#F15B5B',
                        secondary: '#6DB7F2'
                    },
                    fontFamily: {
                        'vazir': ['Vazir', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Vazir', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #F6F1E1 0%, #FDFBF6 50%, #F6F1E1 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Enhanced color scheme */
        :root {
            --primary-green: #7BC4A4;
            --secondary-blue: #6DB7F2;
            --accent-pink: #F3A6B5;
            --soft-beige: #F6F1E1;
            --warm-cream: #FDFBF6;
        }

        /* Desktop flower background - infinite loop */
        .flower-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            transition: all 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .flower-background.active {
            left: -25%;
            width: 75%;
            opacity: 0.7;
        }

        .flower-slider-wrapper {
            position: absolute;
            width: 200%;
            height: 100%;
            display: flex;
            animation: slideFlowers 30s linear infinite;
        }

        .flower-slide {
            position: relative;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-around;
            flex-shrink: 0;
        }

        .flower-slide img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 20px;
            opacity: 0.6;
            filter: blur(1px);
            transition: all 0.8s ease;
            animation: floatFlower 4s ease-in-out infinite;
        }

        .flower-slide img:nth-child(1) { animation-delay: 0s; }
        .flower-slide img:nth-child(2) { animation-delay: 0.5s; }
        .flower-slide img:nth-child(3) { animation-delay: 1s; }
        .flower-slide img:nth-child(4) { animation-delay: 1.5s; }
        .flower-slide img:nth-child(5) { animation-delay: 2s; }

        @keyframes slideFlowers {
            0% { transform: translateX(0); }
            100% { transform: translateX(-0%); }
        }

        @keyframes floatFlower {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .flower-background.active .flower-slide img {
            width: 80px;
            height: 80px;
            opacity: 0.4;
        }

        /* Selected image display on right side */
        .selected-image-display {
            position: fixed;
            top: 50%;
            right: 5%;
            transform: translateY(-50%);
            width: 300px;
            height: 400px;
            z-index: 5;
            opacity: 0;
            pointer-events: none;
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .selected-image-display.active {
            opacity: 1;
        }

        .selected-image-display img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.95);
        }

        .selected-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(163, 213, 186, 0.1) 0%, rgba(109, 183, 242, 0.1) 100%);
            border: 3px solid rgba(163, 213, 186, 0.3);
            border-radius: 20px;
        }

        /* Page container transitions */
        .page-container {
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 10;
        }

        .page-container.shifted {
            transform: translateX(-35%);
        }

        /* Desktop wider results */
        @media (min-width: 769px) {
            #page-5 {
                max-width: 700px !important;
            }
        }

        /* Mobile flower sliders */
        .mobile-flower-slider-left,
        .mobile-flower-slider-right {
            position: fixed;
            top: 0;
            width: 40%;
            height: 120%;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
        }

        .mobile-flower-slider-left {
            left: 0;
        }

        .mobile-flower-slider-right {
            right: 0;
        }

        .mobile-slider-content {
            display: flex;
            flex-direction: column;
            gap: 50px;
            animation: slideMobileFlowers 236s linear infinite;
        }

        .mobile-slider-content img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 15px;
            opacity: 0.4;
            filter: blur(2px);
        }

        @keyframes slideMobileFlowers {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }

        /* Mobile specific */
        @media (max-width: 768px) {
            .flower-background {
                display: none;
            }

            .selected-image-display {
                display: none;
            }
            
            .page-container.shifted {
                transform: translateX(0);
            }
        }

        @media (min-width: 769px) {
            .mobile-flower-slider-left,
            .mobile-flower-slider-right,
            .mobile-slider-content {
                display: none;
            }
        }

        .card-shadow {
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(163, 213, 186, 0.2);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #7BC4A4 0%, #6DB7F2 100%);
        }

        /* Enhanced button styles */
        button.bg-primary {
            background: linear-gradient(135deg, #7BC4A4 0%, #8BD4B4 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        button.bg-primary:hover {
            background: linear-gradient(135deg, #6BB394 0%, #7BC4A4 100%);
            box-shadow: 0 10px 30px rgba(123, 196, 164, 0.4);
        }

        button.bg-secondary {
            background: linear-gradient(135deg, #6DB7F2 0%, #7DC7F2 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        button.bg-secondary:hover {
            background: linear-gradient(135deg, #5DA7E2 0%, #6DB7F2 100%);
            box-shadow: 0 10px 30px rgba(109, 183, 242, 0.4);
        }

        .btn-hover-effect:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.2);
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px) scale(0.95); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px) scale(0.95); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .page-enter {
            animation: slideInRight 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .page-enter-ltr {
            animation: slideInLeft 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        /* Google Lens-like scanning animation */
        .lens-scanner {
            position: relative;
            overflow: hidden;
        }

        .lens-scanner::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: scan 2s infinite;
            z-index: 10;
        }

        @keyframes scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .lens-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(rgba(163, 213, 186, 0.1) 1px, transparent 1px),
                linear-gradient(90deg, rgba(163, 213, 186, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: gridMove 10s linear infinite;
            z-index: 1;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(20px, 20px); }
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }

        .processing-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        /* Table styles */
        .results-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .results-table th {
            background: linear-gradient(135deg, #F3A6B5 0%, #FFB6C6 100%);
            color: white;
            font-weight: 600;
            padding: 14px 16px;
            text-align: right;
            font-size: 15px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .results-table tr:nth-child(odd) td {
            background-color: white;
        }

        .results-table tr:nth-child(even) td {
            background-color: #FDFBF6;
        }

        .results-table td {
            padding: 14px 16px;
            text-align: right;
            vertical-align: top;
            line-height: 1.6;
        }

        .results-table tr {
            animation: fadeInUp 0.4s ease-out backwards;
        }

        .results-table tr:hover td {
            background-color: #f8f8f8;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .fertilizer-row td {
            background-color: #f1f9f3 !important;
            border-top: 2px solid #A8D5BA;
            border-bottom: 2px solid #A8D5BA;
            font-weight: 600;
        }

        .fertilizer-row:hover td {
            background-color: #e5f2e9 !important;
        }

        /* Description box with typing animation */
        .description-box {
            background: linear-gradient(135deg, #FFF9E6 0%, #FFFBF0 100%);
            border: 2px solid #7BC4A4;
            border-radius: 16px;
            padding: 20px;
            margin: 16px 0;
            position: relative;
            overflow: hidden;
            min-height: 60px;
            box-shadow: 0 4px 15px rgba(123, 196, 164, 0.2);
        }

        .description-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(163, 213, 186, 0.2), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .typing-cursor {
            display: inline-block;
            width: 2px;
            height: 1em;
            background-color: #A8D5BA;
            margin-right: 2px;
            animation: blink 1s infinite;
            vertical-align: baseline;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        .copy-btn {
            color: #6DB7F2;
            transition: all 0.2s;
            opacity: 0.9;
        }

        .copy-btn:hover {
            color: #5AA0E0;
            transform: scale(1.2);
            opacity: 1;
        }

        /* Loading overlay */
        .loading-lens {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto 24px;
        }

        .loading-lens-circle {
            position: absolute;
            border-radius: 50%;
            border: 3px solid;
        }

        .loading-lens-circle:nth-child(1) {
            width: 100%;
            height: 100%;
            border-color: #A8D5BA;
            animation: pulse 2s ease-in-out infinite;
        }

        .loading-lens-circle:nth-child(2) {
            width: 75%;
            height: 75%;
            top: 12.5%;
            left: 12.5%;
            border-color: #6DB7F2;
            animation: pulse 2s ease-in-out 0.3s infinite;
        }

        .loading-lens-circle:nth-child(3) {
            width: 50%;
            height: 50%;
            top: 25%;
            left: 25%;
            border-color: #F3A6B5;
            animation: pulse 2s ease-in-out 0.6s infinite;
        }

        .loading-lens-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #A8D5BA, #6DB7F2);
            border-radius: 50%;
            animation: float 2s ease-in-out infinite;
        }

        /* Result image with effects */
        .result-image-container {
            position: relative;
            margin-bottom: 24px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .result-image-container img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            background: #f9fafb;
        }

        .analyzing-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(163, 213, 186, 0.1);
            animation: analyzePulse 2s ease-in-out infinite;
        }

        @keyframes analyzePulse {
            0%, 100% { opacity: 0.1; }
            50% { opacity: 0.3; }
        }

        .analyzing-grid {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                linear-gradient(rgba(163, 213, 186, 0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(163, 213, 186, 0.15) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: analyzeGrid 3s linear infinite;
        }

        @keyframes analyzeGrid {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 30px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-vazir relative">
    <!-- Mobile flower sliders -->
    <div class="mobile-flower-slider-left">
        <div class="mobile-slider-content">
            <img src="/img/emp/1.jpg" alt="Flower">
            <img src="/img/emp/2.jpg" alt="Flower">
            <img src="/img/emp/3.jpg" alt="Flower">
            <img src="/img/emp/4.jpg" alt="Flower">
            <img src="/img/emp/5.jpg" alt="Flower">
        </div>
        <div class="mobile-slider-content">
            <img src="/img/emp/1.jpg" alt="Flower">
            <img src="/img/emp/2.jpg" alt="Flower">
            <img src="/img/emp/3.jpg" alt="Flower">
            <img src="/img/emp/4.jpg" alt="Flower">
            <img src="/img/emp/5.jpg" alt="Flower">
        </div>
    </div>
    <div class="mobile-flower-slider-right">
        <div class="mobile-slider-content">
            <img src="/img/emp/1.jpg" alt="Flower">
            <img src="/img/emp/2.jpg" alt="Flower">
            <img src="/img/emp/3.jpg" alt="Flower">
            <img src="/img/emp/4.jpg" alt="Flower">
            <img src="/img/emp/5.jpg" alt="Flower">
        </div>
        <div class="mobile-slider-content">
            <img src="/img/emp/1.jpg" alt="Flower">
            <img src="/img/emp/2.jpg" alt="Flower">
            <img src="/img/emp/3.jpg" alt="Flower">
            <img src="/img/emp/4.jpg" alt="Flower">
            <img src="/img/emp/5.jpg" alt="Flower">
        </div>
    </div>

    <!-- Desktop flower background - infinite loop -->
    <div class="flower-background" id="flower-background">
        <div class="flower-slider-wrapper">
            <div class="flower-slide">
                <img src="/img/emp/1.jpg" alt="Flower 1">
                <img src="/img/emp/2.jpg" alt="Flower 2">
                <img src="/img/emp/3.jpg" alt="Flower 3">
                <img src="/img/emp/4.jpg" alt="Flower 4">
                <img src="/img/emp/5.jpg" alt="Flower 5">
            </div>
            <div class="flower-slide">
                <img src="/img/emp/1.jpg" alt="Flower 1">
                <img src="/img/emp/2.jpg" alt="Flower 2">
                <img src="/img/emp/3.jpg" alt="Flower 3">
                <img src="/img/emp/4.jpg" alt="Flower 4">
                <img src="/img/emp/5.jpg" alt="Flower 5">
            </div>
        </div>
    </div>

    <!-- Selected image display on right side (desktop only) -->
    <div class="selected-image-display" id="selected-image-display">
        <img id="selected-image" src="" alt="تصویر انتخاب شده">
        <div class="selected-image-overlay"></div>
    </div>

    <!-- Page Container -->
    <div class="page-container" id="page-container">
        <!-- Page 1: Main Selection -->
        <div id="page-1" class="w-full max-w-md mx-auto bg-white rounded-2xl card-shadow overflow-hidden relative z-10">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-header mb-8 text-center animate-fade-in">آنالیز هوشمند گیاه</h2>
                <p class="text-gray-600 mb-8 text-center">لطفاً روش مورد نظر برای ارسال تصویر گیاه خود را انتخاب کنید</p>

                <div class="space-y-4">
                    <button id="camera-button" class="w-full bg-primary hover:bg-primary/90 text-white py-4 px-6 rounded-xl flex items-center justify-center space-x-reverse space-x-3 transition-all duration-300 btn-hover-effect transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>عکس گرفتن با دوربین</span>
                    </button>

                    <button id="upload-button" class="w-full bg-secondary hover:bg-secondary/90 text-white py-4 px-6 rounded-xl flex items-center justify-center space-x-reverse space-x-3 transition-all duration-300 btn-hover-effect transform hover:scale-105">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                        </svg>
                        <span>بارگذاری از گالری</span>
                    </button>
                </div>
            </div>

            <div class="py-4 px-8 text-sm text-center text-gray-500">
                برای دریافت بهترین نتیجه، تصویری واضح با نور کافی تهیه کنید
            </div>
        </div>

        <!-- Page 2: File Upload -->
        <div id="page-2" class="w-full max-w-md bg-white rounded-2xl card-shadow overflow-hidden hidden relative z-10">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-header mb-8 text-center">بارگذاری تصویر</h2>

                <div class="mb-8">
                    <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-primary transition-colors duration-300">
                        <input type="file" id="file-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm text-gray-500 mb-1">فایل خود را بکشید و اینجا رها کنید، یا کلیک کنید</p>
                        <p id="file-name" class="text-sm font-medium text-secondary mt-2">هیچ فایلی انتخاب نشده است</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <button id="file-confirm-button" class="w-full bg-primary hover:bg-primary/90 text-white py-4 px-6 rounded-xl transition-all duration-300 btn-hover-effect" disabled>
                        ارسال برای تحلیل
                    </button>

                    <button id="back-to-main-2" class="w-full bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-xl transition-all duration-300">
                        بازگشت
                    </button>
                </div>
            </div>
        </div>


        <!-- Page 3: Camera Capture  -->
        <div id="page-3" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="w-full max-w-4xl h-[90vh] bg-white rounded-2xl card-shadow overflow-hidden relative">
                <div class="p-4 sm:p-8 h-full flex flex-col">
                    <h2 class="text-3xl font-bold text-header mb-4 text-center">گرفتن عکس</h2>


                    <div class="relative mb-4 flex-1 overflow-hidden bg-black rounded-xl">

                        <video id="camera-stream" autoplay playsinline muted class="w-full h-full object-cover"></video>

                        <div id="camera-loading" class="absolute inset-0 flex items-center justify-center bg-black/80 text-white">
                            <svg class="animate-spin h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>

                        <button id="toggle-fullscreen" class="absolute top-3 right-3 bg-white/80 backdrop-blur px-3 py-1 rounded-md shadow-sm text-sm">
                            تمام صفحه
                        </button>
                    </div>

                    <div class="space-y-3 mt-3">
                        <div class="grid grid-cols-1 gap-3">
                            <button id="capture-button" class="w-full gradient-bg text-white py-4 px-6 rounded-xl transition-all duration-300 flex items-center justify-center">
                            
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h2l1-2h10l1 2h2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                عکس بگیر
                            </button>

                            <button id="back-to-main-3" class="w-full bg-gray-50 hover:bg-gray-100 py-3 px-6 rounded-xl transition-all duration-300">
                                بازگشت
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page 4: Image Preview -->
        <div id="page-4" class="w-full max-w-md bg-white rounded-2xl card-shadow overflow-hidden hidden relative z-10">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-header mb-6 text-center">تصویر شما</h2>

                <div class="mb-6 rounded-xl overflow-hidden bg-gray-100 relative lens-scanner">
                    <div class="lens-grid"></div>
                    <img id="output-image" alt="تصویر انتخاب شده" class="w-full h-72 object-contain relative z-2">
                </div>

                <div class="space-y-3">
                    <button id="image-confirm-button" class="w-full bg-primary hover:bg-primary/90 text-white py-4 px-6 rounded-xl transition-all duration-300 btn-hover-effect">
                        ارسال برای تحلیل
                    </button>

                    <button id="retake-button" class="w-full bg-secondary hover:bg-secondary/90 text-white py-3 px-6 rounded-xl transition-all duration-300">
                        گرفتن عکس جدید
                    </button>

                    <button id="back-to-main-4" class="w-full bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-6 rounded-xl transition-all duration-300">
                        بازگشت
                    </button>
                </div>
            </div>
        </div>

        <!-- Page 5: Analysis Results -->
        <div id="page-5" class="w-full max-w-md md:max-w-2xl bg-white rounded-2xl card-shadow overflow-hidden hidden relative z-10">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-header mb-4 text-center">نتیجه تحلیل گیاه</h2>

                <!-- Uploaded Image with Analyzing Effects -->
                <div class="result-image-container hidden" id="result-image-container">
                    <img id="result-uploaded-image" src="" alt="تصویر آپلود شده">
                    <div class="analyzing-overlay"></div>
                    <div class="analyzing-grid"></div>
                </div>

                <!-- Description Box with typing animation -->
                <div id="description-box" class="description-box hidden">
                    <div class="flex items-start space-x-reverse space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary flex-shrink-0 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 mb-2">توضیحات تصویر</h3>
                            <p id="description-text" class="text-gray-700 leading-relaxed"></p>
                            <span class="typing-cursor hidden" id="typing-cursor"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-6 overflow-hidden rounded-xl">
                    <table id="results-table" class="results-table">
                        <thead>
                            <tr>
                                <th scope="col" class="text-right">ویژگی</th>
                                <th scope="col" class="text-right">نتیجه</th>
                            </tr>
                        </thead>
                        <tbody id="analysis-table">
                            <!-- Table rows will be dynamically generated -->
                        </tbody>
                    </table>
                </div>

                <div class="flex space-x-reverse space-x-4 mb-4">
                    <button id="download-pdf" class="flex-1 bg-primary hover:bg-primary/90 text-white py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center btn-hover-effect">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        دانلود PDF
                    </button>
                    <button id="back-to-main-5" class="flex-1 bg-secondary hover:bg-secondary/90 text-white py-3 px-4 rounded-xl transition-all duration-300">
                        بازگشت
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Component -->
    <div id="notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-xl text-white z-50 opacity-0 transition-all duration-300 hidden shadow-lg"></div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 hidden backdrop-blur-sm">
        <div class="bg-white p-8 rounded-2xl flex flex-col items-center max-w-sm w-full mx-4 shadow-2xl">
            <div class="loading-lens">
                <div class="loading-lens-circle"></div>
                <div class="loading-lens-circle"></div>
                <div class="loading-lens-circle"></div>
                <div class="loading-lens-center"></div>
            </div>
            <p class="text-gray-800 font-medium text-lg mb-2">در حال تحلیل تصویر</p>
            <p class="text-gray-500 text-sm processing-dots">لطفاً شکیبا باشید</p>
        </div>
    </div>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let uploadedImageSrc = '';
    
    const pages = {
        page1: document.getElementById("page-1"),
        page2: document.getElementById("page-2"),
        page3: document.getElementById("page-3"),
        page4: document.getElementById("page-4"),
        page5: document.getElementById("page-5"),
    };

    const cameraStream = document.getElementById("camera-stream");
    const cameraLoading = document.getElementById("camera-loading");
    const captureButton = document.getElementById("capture-button");
    const fileInput = document.getElementById("file-input");
    const fileName = document.getElementById("file-name");
    const fileConfirmButton = document.getElementById("file-confirm-button");
    const outputImage = document.getElementById("output-image");
    const loadingOverlay = document.getElementById("loading-overlay");
    const notification = document.getElementById("notification");
    const descriptionBox = document.getElementById("description-box");
    const descriptionText = document.getElementById("description-text");
    const typingCursor = document.getElementById("typing-cursor");
    const flowerBackground = document.getElementById("flower-background");
    const pageContainer = document.getElementById("page-container");
    const resultImageContainer = document.getElementById("result-image-container");
    const resultUploadedImage = document.getElementById("result-uploaded-image");
    const selectedImageDisplay = document.getElementById("selected-image-display");
    const selectedImage = document.getElementById("selected-image");

    const backButtons = {
        page2: document.getElementById("back-to-main-2"),
        page3: document.getElementById("back-to-main-3"),
        page4: document.getElementById("back-to-main-4"),
        page5: document.getElementById("back-to-main-5"),
    };

    let currentStream;
    let isDesktop = window.innerWidth > 768;

    // Desktop: Handle page shift and background animation
    function handlePageShift() {
        if (isDesktop) {
            setTimeout(() => {
                flowerBackground.classList.add('active');
                pageContainer.classList.add('shifted');
            }, 100);
        }
    }

    // Desktop: Show uploaded image on right side
    function replaceFlowersWithImage(imageSrc) {
        uploadedImageSrc = imageSrc;
        if (isDesktop) {
            selectedImage.src = imageSrc;
            setTimeout(() => {
                selectedImageDisplay.classList.add('active');
            }, 500);
        }
    }

    // Reset backgrounds when going back to page 1
    function resetBackgrounds() {
        if (isDesktop) {
            flowerBackground.classList.remove('active');
            selectedImageDisplay.classList.remove('active');
            pageContainer.classList.remove('shifted');
            flowerBackground.style.opacity = '1';
        }
    }

    // Typing animation for description
    function typeText(element, text, callback) {
        element.textContent = '';
        typingCursor.classList.remove('hidden');
        
        let index = 0;
        const typingInterval = setInterval(() => {
            if (index < text.length) {
                element.textContent += text.charAt(index);
                index++;
            } else {
                clearInterval(typingInterval);
                typingCursor.classList.add('hidden');
                if (callback) callback();
            }
        }, 30); // 30ms per character
    }

    openPage(pages.page1);

    // Event listeners
    document.getElementById("camera-button").addEventListener("click", () => {
        handlePageShift();
        openCamera();
    });
    
    document.getElementById("upload-button").addEventListener("click", () => {
        handlePageShift();
        openPage(pages.page2);
    });
    
    backButtons.page2.addEventListener("click", () => {
        resetBackgrounds();
        openPage(pages.page1);
    });
    
    backButtons.page3.addEventListener("click", () => {
        resetBackgrounds();
        handleBackToMainFromCamera();
    });
    
    backButtons.page4.addEventListener("click", () => {
        resetBackgrounds();
        handleBackToMainFromPreview();
    });
    
    backButtons.page5.addEventListener("click", () => {
        resetBackgrounds();
        openPage(pages.page1);
    });

    fileInput.addEventListener("change", handleFileInputChange);
    fileConfirmButton.addEventListener("click", handleFileConfirm);
    captureButton.addEventListener("click", handleCapture);
    document.getElementById("retake-button").addEventListener("click", openCamera);
    document.getElementById("image-confirm-button").addEventListener("click", handleImageConfirm);
    document.getElementById("download-pdf").addEventListener("click", generatePDF);

    // Page navigation
    function openPage(page) {
        Object.values(pages).forEach(p => {
            p.classList.add("hidden");
            p.classList.remove("page-enter", "page-enter-ltr");
        });
        
        page.classList.remove("hidden");
        const pageId = Object.keys(pages).find(key => pages[key] === page);
        
        if (pageId === 'page1') {
            page.classList.add("page-enter-ltr");
        } else {
            page.classList.add("page-enter");
        }
    }

    // Camera handling
    function openCamera() {
        openPage(pages.page3);
        if (cameraLoading) cameraLoading.classList.remove("hidden");

        const constraints = {
            video: {
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                currentStream = stream;
                cameraStream.srcObject = stream;
                if (cameraLoading) cameraLoading.classList.add("hidden");
            })
            .catch(err => {
                console.error("Error accessing camera: ", err);
                showNotification("دسترسی به دوربین محدود شده است , لطفا تنظیمات مرورگر خود را برسی کنید", "danger");
                openPage(pages.page1);
            });
    }

    function handleCapture() {
        const canvas = document.createElement("canvas");
        canvas.width = cameraStream.videoWidth;
        canvas.height = cameraStream.videoHeight;
        canvas.getContext("2d").drawImage(cameraStream, 0, 0);
        const dataUrl = canvas.toDataURL("image/png");
        outputImage.src = dataUrl;
        replaceFlowersWithImage(dataUrl);
        stopCamera();
        openPage(pages.page4);
    }

    function stopCamera() {
        if (currentStream) {
            const tracks = currentStream.getTracks();
            tracks.forEach(track => track.stop());
        }
    }

    function handleFileInputChange() {
        const file = fileInput.files[0];
        if (file) {
            fileConfirmButton.disabled = false;
            fileName.textContent = file.name;
            fileName.classList.add("text-secondary");

            const reader = new FileReader();
            reader.onload = e => {
                outputImage.src = e.target.result;
                replaceFlowersWithImage(e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            fileConfirmButton.disabled = true;
            fileName.textContent = "هیچ فایلی انتخاب نشده است";
            fileName.classList.remove("text-secondary");
        }
    }

    function handleFileConfirm() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const reader = new FileReader();
            reader.onload = e => {
                outputImage.src = e.target.result;
                replaceFlowersWithImage(e.target.result);
                openPage(pages.page4);
            };
            reader.readAsDataURL(file);
        } else {
            showNotification("لطفا یک فایل انتخاب کنید!", "danger");
        }
    }

    function handleBackToMainFromCamera() {
        stopCamera();
        openPage(pages.page1);
    }

    function handleBackToMainFromPreview() {
        openPage(pages.page1);
    }

    function handleImageConfirm() {
        const dataUrl = outputImage.src;
        loadingOverlay.classList.remove("hidden");

        setTimeout(() => {
            try {
                if (!dataUrl || dataUrl === '') {
                    throw new Error("تصویری برای تحلیل وجود ندارد");
                }

                const blob = dataURLtoBlob(dataUrl);

                if (!blob || blob.size === 0) {
                    throw new Error("تصویر انتخاب شده نامعتبر است");
                }

                if (blob.size > 5 * 1024 * 1024) {
                    showNotification("حجم تصویر بسیار زیاد است (حداکثر 5 مگابایت)", "danger");
                    loadingOverlay.classList.add("hidden");
                    return;
                }

                sendImageForAnalysis(blob);

            } catch (error) {
                console.error("Error processing image:", error);
                showNotification(error.message || "خطا در پردازش تصویر", "danger");
                loadingOverlay.classList.add("hidden");
            }
        }, 100);
    }

    function dataURLtoBlob(dataURL) {
        try {
            if (!dataURL || typeof dataURL !== 'string' || !dataURL.startsWith('data:')) {
                throw new Error('داده تصویر نامعتبر است');
            }

            const parts = dataURL.split(',');
            if (parts.length !== 2) {
                throw new Error('فرمت داده تصویر نامعتبر است');
            }

            const mime = parts[0].match(/:(.*?);/)[1];
            const base64 = parts[1];
            const byteString = atob(base64);
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);

            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }

            return new Blob([ab], { type: mime });
        } catch (error) {
            console.error('Error in dataURLtoBlob:', error);
            showNotification(error.message || 'خطا در پردازش تصویر', 'danger');
            throw error;
        }
    }

    function sendImageForAnalysis(imageBlob) {
        if (!imageBlob || !(imageBlob instanceof Blob)) {
            showNotification("فایل تصویر نامعتبر است", "danger");
            loadingOverlay.classList.add("hidden");
            return;
        }

        const formData = new FormData();
        formData.append('file', imageBlob, 'image.jpg');
        formData.append('_token', csrf);

        fetch('{{ route("analyze") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || `خطای سرور با کد ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            loadingOverlay.classList.add("hidden");

            if (data.error) {
                throw new Error(data.error);
            } else if (!data.fa) {
                throw new Error("پاسخی از سرور دریافت نشد");
            } else {
                displayAnalysisResult(data.fa, data.raw);
                openPage(pages.page5);
            }
        })
        .catch(error => {
            loadingOverlay.classList.add("hidden");
            console.error("API Error:", error);
            showNotification(error.message || "خطا در ارتباط با سرور", "danger");
        });
    }

    function displayAnalysisResult(result, rawData = {}) {
        window.currentResult = result;
        
        const analysisTable = document.getElementById("analysis-table");
        analysisTable.innerHTML = '';

        // Show uploaded image in results
        if (uploadedImageSrc) {
            resultUploadedImage.src = uploadedImageSrc;
            resultImageContainer.classList.remove('hidden');
        }

        const fieldOrder = [
            "نام فارسی",
            "نام علمی",
            "گروه گیاهی",
            "وضعیت فعلی",
            "شرایط نگهداری",
            "نیاز آبی",
            "نیاز نوری",
            "کود پیشنهادی"
        ];

        // Handle description with typing animation
        const description = result['توضیحات تصویر'] || result['توضیحات_تصویر'] || rawData['توضیحات_تصویر'] || rawData['description'];
        if (description && description !== '—' && description.trim() !== '') {
            descriptionBox.classList.remove('hidden');
            setTimeout(() => {
                typeText(descriptionText, description, () => {
                    // After typing is complete, show table rows
                    setTimeout(() => {
                        showTableRows(result, fieldOrder);
                    }, 300);
                });
            }, 500);
        } else {
            descriptionBox.classList.add('hidden');
            setTimeout(() => {
                showTableRows(result, fieldOrder);
            }, 300);
        }
    }

    function showTableRows(result, fieldOrder) {
        const analysisTable = document.getElementById("analysis-table");
        let rowIndex = 0;
        
        fieldOrder.forEach(field => {
            if (result[field] && result[field] !== '—') {
                const isFertilizer = field.includes("کود");
                addTableRow(field, result[field], rowIndex, isFertilizer);
                rowIndex++;
            }
        });

        setTimeout(() => {
            document.querySelectorAll('#analysis-table tr').forEach((row, i) => {
                row.style.opacity = '0';
                row.style.animation = `fadeInUp 0.4s ease-out ${i * 0.05}s forwards`;
            });
        }, 100);
    }

    function addTableRow(key, value, index, isFertilizer) {
        const row = document.createElement('tr');
        row.className = isFertilizer ? 'fertilizer-row' : '';
        
        let strValue = typeof value === 'string' ? value : String(value);
        const formattedValue = strValue.replace(/\n/g, '<br>');

        const fertilizerDesc = window.currentResult && window.currentResult['کود_پیشنهادی_توضیح'] && window.currentResult['کود_پیشنهادی_توضیح'] !== '—' 
            ? window.currentResult['کود_پیشنهادی_توضیح'] : '';

        if (isFertilizer) {
            row.innerHTML = `
                <td class="whitespace-nowrap font-medium text-gray-900">
                    ${key}
                </td>
                <td class="text-gray-700 flex justify-between items-start">
                    <div class="flex-1">
                        <span class="whitespace-pre-wrap break-words block">${formattedValue}</span>
                        ${fertilizerDesc ? `<span class="text-xs text-gray-500 mt-1 block">${fertilizerDesc}</span>` : ''}
                    </div>
                    <button class="copy-btn text-secondary hover:text-secondary/80 p-1 flex-shrink-0 ml-2"
                            data-content="${strValue.replace(/"/g, '&quot;')}"
                            title="کپی کردن">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </td>
            `;
        } else {
            row.innerHTML = `
                <td class="whitespace-nowrap font-medium text-gray-900">
                    ${key}
                </td>
                <td class="text-gray-700">
                    <span class="whitespace-pre-wrap break-words">${formattedValue}</span>
                </td>
            `;
        }

        document.getElementById("analysis-table").appendChild(row);
    }

    setTimeout(() => {
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-content');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    showNotification("متن کپی شد", "primary");
                }).catch(err => {
                    console.error('خطا در کپی کردن متن:', err);
                });
            });
        });
    }, 500);

    function showNotification(message, type) {
        notification.className = "fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-xl text-white z-50 transition-all duration-300 shadow-lg";
        
        if (type === "danger") {
            notification.classList.add("bg-danger");
        } else {
            notification.classList.add("bg-primary");
        }

        notification.textContent = message;
        notification.classList.remove("hidden", "opacity-0");
        notification.classList.add("opacity-100");

        setTimeout(() => {
            notification.classList.add("opacity-0");
            setTimeout(() => {
                notification.classList.add("hidden");
            }, 300);
        }, 3000);
    }

    function generatePDF() {
        showNotification("در حال آماده‌سازی PDF...", "primary");

        const rows = document.querySelectorAll('#analysis-table tr');
        if (rows.length === 0) {
            showNotification("داده‌ای برای دانلود وجود ندارد", "danger");
            return;
        }

        const contentDiv = document.createElement('div');
        contentDiv.style.position = 'absolute';
        contentDiv.style.left = '-9999px';
        contentDiv.style.fontFamily = 'Vazir, sans-serif';
        contentDiv.style.direction = 'rtl';
        contentDiv.style.textAlign = 'right';
        
        const imageHtml = uploadedImageSrc ? 
            `<div style="margin-bottom: 20px; text-align: center;">
                <img src="${uploadedImageSrc}" style="max-width: 100%; max-height: 300px; border-radius: 12px;" alt="تصویر">
            </div>` : '';

        const description = descriptionBox.classList.contains('hidden') ? '' : 
            `<div style="background: #FFF9E6; border: 2px solid #A8D5BA; border-radius: 12px; padding: 16px; margin: 16px 0;">
                <h3 style="font-weight: bold; color: #333; margin-bottom: 8px;">توضیحات تصویر</h3>
                <p style="color: #666; line-height: 1.6;">${descriptionText.textContent}</p>
            </div>`;

        contentDiv.innerHTML = `
            <h1 style="color: #F3A6B5; font-size: 24px; margin-bottom: 16px; text-align: center;">نتیجه تحلیل گیاه</h1>
            <p style="font-size: 14px; color: #666; text-align: center; margin-bottom: 24px;">
                تاریخ: ${new Date().toLocaleDateString('fa-IR')}
            </p>
            ${imageHtml}
            ${description}
            <div style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background-color: #F3A6B5;">
                            <th style="padding: 12px; text-align: right; color: white; font-weight: bold;">ویژگی</th>
                            <th style="padding: 12px; text-align: right; color: white; font-weight: bold;">نتیجه</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Array.from(rows).map((row, index) => {
                            const cells = row.querySelectorAll('td');
                            const key = cells[0].textContent;
                            let value = cells[1].textContent;

                            if (cells[1].querySelector('button')) {
                                value = cells[1].querySelector('span').textContent;
                            }

                            return `
                                <tr style="background-color: ${index % 2 === 0 ? 'white' : '#FDFBF6'};">
                                    <td style="padding: 12px; border-bottom: 1px solid #eee; font-weight: bold;">${key}</td>
                                    <td style="padding: 12px; border-bottom: 1px solid #eee;">${value}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            </div>
        `;

        document.body.appendChild(contentDiv);

        html2canvas(contentDiv, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            windowWidth: 800,
            windowHeight: 1200
        }).then(canvas => {
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgData = canvas.toDataURL('image/png');
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth() - 20;
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 10, 10, pdfWidth, pdfHeight);
                pdf.save('تحلیل-گیاه.pdf');
                showNotification("PDF با موفقیت دانلود شد", "primary");
                document.body.removeChild(contentDiv);
            } catch (error) {
                console.error("PDF generation error:", error);
                showNotification("خطا در ایجاد PDF", "danger");
                document.body.removeChild(contentDiv);
            }
        }).catch(error => {
            console.error("Error generating PDF:", error);
            showNotification("خطا در ایجاد PDF", "danger");
            document.body.removeChild(contentDiv);
        });
    }

    // Handle window resize
    window.addEventListener('resize', () => {
        isDesktop = window.innerWidth > 768;
        if (!isDesktop) {
            resetBackgrounds();
        }
    });
</script>
</body>
</html>
