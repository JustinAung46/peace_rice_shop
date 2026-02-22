<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Shop System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4f46e5 0%, #1e1b4b 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .numpad-btn {
            @apply w-32 h-32 rounded-3xl bg-white/10 text-white text-6xl font-normal flex items-center justify-center hover:bg-white/20 active:bg-white/30 transition-all duration-200 cursor-pointer select-none border border-white/10;
        }
        .numpad-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-indigo-500/30 blur-[100px]"></div>
        <div class="absolute top-[60%] -right-[10%] w-[30%] h-[50%] rounded-full bg-fuchsia-500/20 blur-[120px]"></div>
    </div>

    <div class="glass-card p-10 rounded-[2rem] w-full max-w-lg transition-all duration-500 ease-in-out relative z-10" id="loginCard">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2 tracking-tight" id="welcomeTitle">Rice Shop POS</h1>
            <p class="text-indigo-200 text-lg" id="welcomeSubtitle">Enter your Account ID</p>
        </div>

        <form method="POST" action="{{ route('authenticate') }}" id="loginForm">
            @csrf
            
            <div id="errorContainer" class="hidden bg-red-500/20 backdrop-blur-md text-white text-base p-4 rounded-xl mb-6 text-center border border-red-500/50 shadow-lg">
            </div>

            <!-- Step 1: Account ID -->
            <div id="step1">
                <div class="mb-8">
                    <input type="text" id="account_id_display" class="w-full px-6 py-5 rounded-2xl border border-white/20 bg-black/20 focus:bg-black/40 focus:border-indigo-400 outline-none transition-all text-center text-4xl font-light tracking-widest text-white placeholder-white/30" readonly placeholder="Enter ID (e.g. 777)">
                    <input type="hidden" name="account_id" id="account_id">
                </div>
            </div>

            <!-- Step 2: Passcode -->
            <div id="step2" class="hidden">
                 <div class="mb-6 flex justify-center">
                    <div class="bg-white/10 border border-white/20 text-white px-5 py-2.5 rounded-full text-base font-medium flex items-center gap-3 backdrop-blur-sm">
                        <span id="userNameDisplay">User</span>
                        <button type="button" onclick="resetToStep1()" class="text-indigo-300 hover:text-white transition-colors bg-white/10 p-1 rounded-full">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <input type="password" id="password_display" class="w-full px-6 py-5 rounded-2xl border border-white/20 bg-black/20 focus:bg-black/40 focus:border-indigo-400 outline-none transition-all text-center text-4xl font-light tracking-widest text-white placeholder-white/30" readonly placeholder="Passcode (••••)">
                    <input type="hidden" name="password" id="password">
                </div>
            </div>

            <!-- Numpad -->
            <div class="grid grid-cols-3 gap-5 mb-8 justify-items-center">
                <button type="button" class="numpad-btn" onclick="appendInput('1')">1</button>
                <button type="button" class="numpad-btn" onclick="appendInput('2')">2</button>
                <button type="button" class="numpad-btn" onclick="appendInput('3')">3</button>
                <button type="button" class="numpad-btn" onclick="appendInput('4')">4</button>
                <button type="button" class="numpad-btn" onclick="appendInput('5')">5</button>
                <button type="button" class="numpad-btn" onclick="appendInput('6')">6</button>
                <button type="button" class="numpad-btn" onclick="appendInput('7')">7</button>
                <button type="button" class="numpad-btn" onclick="appendInput('8')">8</button>
                <button type="button" class="numpad-btn" onclick="appendInput('9')">9</button>
                <button type="button" class="numpad-btn text-red-300 bg-red-500/10 border-red-500/20 hover:bg-red-500/20 font-medium" onclick="clearInput()">C</button>
                <button type="button" class="numpad-btn" onclick="appendInput('0')">0</button>
                <button type="button" class="numpad-btn bg-black/20 hover:bg-black/30 text-indigo-200" onclick="backspace()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                    </svg>
                </button>
            </div>

            <button type="button" id="actionBtn" onclick="handleAction()" class="w-full bg-gradient-to-r from-emerald-400 to-teal-500 hover:from-emerald-500 hover:to-teal-600 active:from-emerald-600 active:to-teal-700 text-white font-bold py-6 rounded-[2rem] transition-all shadow-lg shadow-emerald-500/30 text-2xl tracking-wide uppercase">
                Next
            </button>
        </form>
    </div>

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
                if (accountId.length > 0) {
                     actionBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                     actionBtn.disabled = false;
                } else {
                    // Optional: Disable button if empty
                }
            } else {
                passwordDisplay.value = passcode; // Browser handles masking for password type, but here we are using text input with mask or just regular password input
                // For custom display with dots we can do:
                // passwordDisplay.value = '•'.repeat(passcode.length); 
                // But since it's type="password", it works automatically.
                passwordInput.value = passcode;
            }
        }

        function appendInput(val) {
            hideError();
            if (currentStep === 1) {
                if (accountId.length < 10) {
                    accountId += val;
                    updateDisplay();
                }
            } else {
                if (passcode.length < 10) {
                    passcode += val;
                    updateDisplay();
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
            
            // Shake effect
            const card = document.getElementById('loginCard');
            card.classList.add('animate-pulse'); // Simple pulse as shake replacement or custom keyframes
            setTimeout(() => card.classList.remove('animate-pulse'), 500);
        }

        function hideError() {
            errorContainer.classList.add('hidden');
        }

        async function handleAction() {
            if (currentStep === 1) {
                if (!accountId) {
                    showError('Please enter an Account ID');
                    return;
                }
                
                // Check Account ID
                actionBtn.disabled = true;
                actionBtn.textContent = 'Checking...';
                
                try {
                    const response = await axios.post('{{ route("auth.check") }}', {
                        account_id: accountId
                    });

                    if (response.data.exists) {
                        // Success -> Move to Step 2
                        currentStep = 2;
                        document.getElementById('step1').classList.add('hidden');
                        document.getElementById('step2').classList.remove('hidden');
                        document.getElementById('userNameDisplay').textContent = response.data.name;
                        welcomeSubtitle.textContent = 'Enter your passcode';
                        actionBtn.textContent = 'Login';
                        actionBtn.disabled = false;
                        passwordDisplay.focus();
                    } else {
                        showError('Account ID not found');
                        actionBtn.textContent = 'Next';
                        actionBtn.disabled = false;
                    }
                } catch (error) {
                    console.error(error);
                    showError('Account ID not found or server error');
                    actionBtn.textContent = 'Next';
                    actionBtn.disabled = false;
                }

            } else {
                // Submit Form
                if (!passcode) {
                    showError('Please enter your passcode');
                    return;
                }
                document.getElementById('loginForm').submit();
            }
        }

        function resetToStep1() {
            currentStep = 1;
            passcode = '';
            passwordInput.value = '';
            passwordDisplay.value = '';
            
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            
            welcomeSubtitle.textContent = 'Enter your Account ID';
            actionBtn.textContent = 'Next';
            hideError();
        }

        // Handle Keyboard input partially (optional, since it's numpad focused)
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
