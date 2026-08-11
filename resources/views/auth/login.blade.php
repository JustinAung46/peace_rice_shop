<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Rice Shop POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            overflow-y: auto;
            -webkit-tap-highlight-color: transparent;
            /* Animated Gradient Background */
            background: linear-gradient(-45deg, #ecfdf5, #d1fae5, #f0fdf4, #e0f2fe);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .app-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Entrance Animation Keyframes */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 1000px;
            display: flex;
            flex-direction: row; 
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(16, 185, 129, 0.15); /* Emerald tinted shadow */
            overflow: hidden;
            min-height: 600px;
            max-height: 90vh;
            opacity: 0;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Fallback for portrait */
        @media (max-width: 900px) {
            .app-container {
                padding: 1rem;
            }
            .login-card {
                flex-direction: column;
                max-height: none;
                border-radius: 1.5rem;
                min-height: auto;
            }
            .left-panel,
            .right-panel {
                padding: 2rem 1.25rem;
            }
            .left-panel {
                align-items: stretch;
            }
            .right-panel {
                border-left: none;
                border-top: 1px solid #f4f4f5;
            }
            .numpad-btn {
                width: 70px !important;
                height: 70px !important;
            }
        }

        @media (max-width: 640px) {
            .login-card {
                border-radius: 1rem;
            }
            .left-panel,
            .right-panel {
                padding: 1.5rem 1rem;
            }
            .left-panel {
                padding-bottom: 1rem;
            }
            .right-panel {
                padding-top: 1rem;
            }
            .numpad-btn {
                width: 60px !important;
                height: 60px !important;
            }
            .pin-display {
                font-size: 1.75rem;
            }
            .left-panel .text-3xl {
                font-size: 2rem;
            }
            .left-panel .text-lg {
                font-size: 0.95rem;
            }
            .login-card {
                min-height: auto;
            }
        }

        .left-panel {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Staggered entrance for left panel elements */
        .left-panel > div, .left-panel > button {
            opacity: 0;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .left-panel > div:nth-child(1) { animation-delay: 0.1s; }
        .left-panel > div#errorContainer { animation-delay: 0.1s; }
        .left-panel > div#step1 { animation-delay: 0.2s; }
        .left-panel > div#step2 { animation-delay: 0.2s; }
        .left-panel > button { animation-delay: 0.3s; }

        .right-panel {
            flex: 1;
            background-color: #fafafa;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 1px solid #f4f4f5;
        }
        
        .numpad-btn {
            width: 86px;
            height: 86px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            font-weight: 500;
            color: #0f172a;
            background-color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.03);
            transition: all 0.15s ease-out;
            margin: 0 auto;
            border: none;
            cursor: pointer;
            opacity: 0;
            animation: fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        /* Staggered entrance for numpad */
        .numpad-btn:nth-child(1) { animation-delay: 0.20s; }
        .numpad-btn:nth-child(2) { animation-delay: 0.25s; }
        .numpad-btn:nth-child(3) { animation-delay: 0.30s; }
        .numpad-btn:nth-child(4) { animation-delay: 0.25s; }
        .numpad-btn:nth-child(5) { animation-delay: 0.30s; }
        .numpad-btn:nth-child(6) { animation-delay: 0.35s; }
        .numpad-btn:nth-child(7) { animation-delay: 0.30s; }
        .numpad-btn:nth-child(8) { animation-delay: 0.35s; }
        .numpad-btn:nth-child(9) { animation-delay: 0.40s; }
        .numpad-btn:nth-child(10) { animation-delay: 0.35s; }
        .numpad-btn:nth-child(11) { animation-delay: 0.40s; }
        .numpad-btn:nth-child(12) { animation-delay: 0.45s; }

        .numpad-btn:hover {
            background-color: #f1f5f9;
            transform: scale(1.05); /* Hover expand animation */
        }
        .numpad-btn:active {
            background-color: #e2e8f0;
            transform: scale(0.92); /* Click compress animation */
            box-shadow: none;
        }
        .numpad-btn.text-only {
            background-color: transparent;
            box-shadow: none;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .numpad-btn.text-only:hover {
            background-color: #f1f5f9;
        }
        .numpad-btn.text-only:active {
            background-color: #e2e8f0;
        }

        .pin-display-wrapper {
            transition: transform 0.15s ease-out;
        }
        .pin-display-wrapper.pop {
            transform: scale(1.03) translateX(2px);
        }

        .pin-display {
            letter-spacing: 0.25em;
            font-size: 2.25rem;
            border: none;
            background: transparent;
            outline: none;
            color: #0f172a;
            font-weight: 600;
            width: 100%;
            padding: 0;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        .pin-display::placeholder {
            color: #cbd5e1;
            letter-spacing: normal;
            font-weight: 400;
            font-size: 1.5rem;
        }
        
        /* Shake animation for errors */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }
        .animate-shake {
            animation: shake 0.4s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <form method="POST" action="{{ route('authenticate') }}" id="loginForm" class="login-card">
            @csrf
            
            <!-- LEFT PANEL: Branding & Input -->
            <div class="left-panel">
                <div class="mb-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mb-5">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight" id="welcomeTitle">Rice Shop POS</h1>
                    <p class="text-gray-500 mt-2 text-lg" id="welcomeSubtitle">Enter your Account ID</p>
                </div>

                <div id="errorContainer" class="hidden bg-red-50 text-red-600 text-sm py-3 px-4 rounded-xl mb-6 font-medium border border-red-100 w-full max-w-sm"></div>

                <!-- Step 1: Account ID -->
                <div id="step1" class="mb-auto">
                    <div id="step1-wrapper" class="pin-display-wrapper">
                        <input type="text" id="account_id_display" class="pin-display" readonly placeholder="Account ID">
                        <input type="hidden" name="account_id" id="account_id">
                        <div class="h-1.5 w-16 bg-emerald-500 rounded-full mt-2 transition-all duration-300"></div>
                    </div>
                </div>

                <!-- Step 2: Passcode -->
                <div id="step2" class="hidden mb-auto">
                    <div class="inline-flex items-center gap-2 bg-gray-50 border border-gray-100 px-4 py-1.5 rounded-full mb-6">
                        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold" id="userAvatar">U</div>
                        <span class="text-sm font-medium text-gray-700" id="userNameDisplay">User</span>
                        <button type="button" onclick="resetToStep1()" class="text-gray-400 hover:text-gray-600 ml-1 p-1 transition-transform hover:scale-110 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div id="step2-wrapper" class="pin-display-wrapper">
                        <input type="password" id="password_display" class="pin-display" readonly placeholder="Passcode">
                        <input type="hidden" name="password" id="password">
                        <div class="h-1.5 w-16 bg-emerald-500 rounded-full mt-2"></div>
                    </div>
                </div>

                <button type="button" id="actionBtn" onclick="handleAction()" class="w-full max-w-sm bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-4 rounded-2xl transition-all text-lg shadow-lg shadow-emerald-200/50 mt-10 hover:shadow-emerald-300/50 hover:-translate-y-0.5 active:translate-y-0">
                    Continue
                </button>
            </div>

            <!-- RIGHT PANEL: Numpad -->
            <div class="right-panel">
                <div class="grid grid-cols-3 gap-y-6 gap-x-8 max-w-[340px] mx-auto w-full">
                    <button type="button" class="numpad-btn" onclick="appendInput('1')">1</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('2')">2</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('3')">3</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('4')">4</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('5')">5</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('6')">6</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('7')">7</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('8')">8</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('9')">9</button>
                    <button type="button" class="numpad-btn text-only text-red-500" onclick="clearInput()">C</button>
                    <button type="button" class="numpad-btn" onclick="appendInput('0')">0</button>
                    <button type="button" class="numpad-btn text-only text-gray-400" onclick="backspace()">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let currentStep = 1;
        let accountId = '';
        let passcode = '';

        const accountIdDisplay = document.getElementById('account_id_display');
        const accountIdInput = document.getElementById('account_id');
        const passwordDisplay = document.getElementById('password_display');
        const passwordInput = document.getElementById('password');
        const actionBtn = document.getElementById('actionBtn');
        const errorContainer = document.getElementById('errorContainer');
        const welcomeSubtitle = document.getElementById('welcomeSubtitle');
        
        function updateDisplay() {
            if (currentStep === 1) {
                accountIdDisplay.value = accountId;
                accountIdInput.value = accountId;
            } else {
                passwordDisplay.value = passcode;
                passwordInput.value = passcode;
            }
        }
        
        // Micro-animation for input
        function triggerPop(elementId) {
            const el = document.getElementById(elementId);
            if (el) {
                el.classList.remove('pop');
                void el.offsetWidth; // trigger reflow
                el.classList.add('pop');
                setTimeout(() => el.classList.remove('pop'), 150);
            }
        }

        function appendInput(val) {
            hideError();
            if (currentStep === 1) {
                if (accountId.length < 10) {
                    accountId += val;
                    updateDisplay();
                    triggerPop('step1-wrapper');
                }
            } else {
                if (passcode.length < 10) {
                    passcode += val;
                    updateDisplay();
                    triggerPop('step2-wrapper');
                }
            }
        }

        function backspace() {
            hideError();
            if (currentStep === 1) {
                accountId = accountId.slice(0, -1);
            } else {
                passcode = passcode.slice(0, -1);
            }
            updateDisplay();
        }

        function clearInput() {
            hideError();
            if (currentStep === 1) {
                accountId = '';
            } else {
                passcode = '';
            }
            updateDisplay();
        }

        function showError(msg) {
            errorContainer.textContent = msg;
            errorContainer.classList.remove('hidden');
            
            errorContainer.classList.remove('animate-shake');
            void errorContainer.offsetWidth; // trigger reflow
            errorContainer.classList.add('animate-shake');
        }

        function hideError() {
            errorContainer.classList.add('hidden');
            errorContainer.classList.remove('animate-shake');
        }

        async function handleAction() {
            if (currentStep === 1) {
                if (!accountId) {
                    showError('Please enter an Account ID');
                    return;
                }
                
                actionBtn.disabled = true;
                actionBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Checking...';
                actionBtn.classList.add('opacity-80', 'cursor-not-allowed');
                
                try {
                    const response = await axios.post('{{ route("auth.check") }}', {
                        account_id: accountId
                    }, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    });

                    if (response.data.exists) {
                        currentStep = 2;
                        
                        // Small transition between steps
                        const step1El = document.getElementById('step1');
                        const step2El = document.getElementById('step2');
                        
                        step1El.style.opacity = '0';
                        setTimeout(() => {
                            step1El.classList.add('hidden');
                            step2El.classList.remove('hidden');
                            step2El.style.opacity = '0';
                            
                            const name = response.data.name || 'User';
                            document.getElementById('userNameDisplay').textContent = name;
                            document.getElementById('userAvatar').textContent = name.charAt(0).toUpperCase();
                            
                            welcomeSubtitle.textContent = 'Enter your passcode';
                            actionBtn.textContent = 'Login';
                            
                            // Fade in step 2
                            setTimeout(() => {
                                step2El.style.transition = 'opacity 0.3s ease';
                                step2El.style.opacity = '1';
                            }, 50);
                        }, 200);
                        
                    } else {
                        showError('Account ID not found');
                        actionBtn.textContent = 'Continue';
                    }
                } catch (error) {
                    console.error(error);
                    showError('Account ID not found or server error');
                    actionBtn.textContent = 'Continue';
                } finally {
                    actionBtn.disabled = false;
                    actionBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                }

            } else {
                if (!passcode) {
                    showError('Please enter your passcode');
                    return;
                }
                
                actionBtn.disabled = true;
                actionBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Logging in...';
                
                document.getElementById('loginForm').submit();
            }
        }

        function resetToStep1() {
            currentStep = 1;
            passcode = '';
            updateDisplay();
            
            const step1El = document.getElementById('step1');
            const step2El = document.getElementById('step2');
            
            step2El.style.opacity = '0';
            setTimeout(() => {
                step2El.classList.add('hidden');
                step1El.classList.remove('hidden');
                
                welcomeSubtitle.textContent = 'Enter your Account ID';
                actionBtn.textContent = 'Continue';
                hideError();
                
                setTimeout(() => {
                    step1El.style.transition = 'opacity 0.3s ease';
                    step1El.style.opacity = '1';
                }, 50);
            }, 200);
        }

        // Keyboard support
        document.addEventListener('keydown', (e) => {
            if (e.key >= '0' && e.key <= '9') {
                appendInput(e.key);
            } else if (e.key === 'Backspace') {
                backspace();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                handleAction();
            } else if (e.key === 'Escape') {
                if (currentStep === 2) resetToStep1();
                else clearInput();
            }
        });
    </script>
</body>
</html>
