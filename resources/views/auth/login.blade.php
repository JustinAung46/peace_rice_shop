<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rice Shop System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .numpad-btn {
            @apply w-20 h-20 rounded-2xl bg-gray-100 text-2xl font-semibold flex items-center justify-center hover:bg-gray-200 active:bg-gray-300 transition-all duration-200 cursor-pointer select-none shadow-sm;
        }
        .numpad-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-lg transition-all duration-500 ease-in-out" id="loginCard">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800" id="welcomeTitle">Welcome</h1>
            <p class="text-gray-500 text-base mt-2" id="welcomeSubtitle">Enter your Account ID</p>
        </div>

        <form method="POST" action="{{ route('authenticate') }}" id="loginForm">
            @csrf
            
            <div id="errorContainer" class="hidden bg-red-50 text-red-500 text-sm p-4 rounded-xl mb-6 text-center border border-red-100">
            </div>

            <!-- Step 1: Account ID -->
            <div id="step1">
                <div class="mb-8">
                    <input type="text" id="account_id_display" class="w-full px-6 py-4 rounded-xl border-2 border-transparent bg-gray-100 focus:bg-white focus:border-blue-500 focus:ring-0 outline-none transition-all text-center text-3xl font-bold tracking-widest text-gray-800 placeholder-gray-400" readonly placeholder="Enter ID (e.g. 777)">
                    <input type="hidden" name="account_id" id="account_id">
                </div>
            </div>

            <!-- Step 2: Passcode -->
            <div id="step2" class="hidden">
                 <div class="mb-4 flex justify-center">
                    <div class="bg-blue-50 text-blue-800 px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2">
                        <span id="userNameDisplay">User</span>
                        <button type="button" onclick="resetToStep1()" class="text-blue-400 hover:text-blue-600">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="mb-8 relative">
                    <input type="password" id="password_display" class="w-full px-6 py-4 rounded-xl border-2 border-transparent bg-gray-100 focus:bg-white focus:border-blue-500 focus:ring-0 outline-none transition-all text-center text-3xl font-bold tracking-widest text-gray-800 placeholder-dots" readonly placeholder="••••">
                    <input type="hidden" name="password" id="password">
                </div>
            </div>

            <!-- Numpad -->
            <div class="grid grid-cols-3 gap-4 mb-8 justify-items-center">
                <button type="button" class="numpad-btn" onclick="appendInput('1')">1</button>
                <button type="button" class="numpad-btn" onclick="appendInput('2')">2</button>
                <button type="button" class="numpad-btn" onclick="appendInput('3')">3</button>
                <button type="button" class="numpad-btn" onclick="appendInput('4')">4</button>
                <button type="button" class="numpad-btn" onclick="appendInput('5')">5</button>
                <button type="button" class="numpad-btn" onclick="appendInput('6')">6</button>
                <button type="button" class="numpad-btn" onclick="appendInput('7')">7</button>
                <button type="button" class="numpad-btn" onclick="appendInput('8')">8</button>
                <button type="button" class="numpad-btn" onclick="appendInput('9')">9</button>
                <button type="button" class="numpad-btn text-red-500 bg-red-50 hover:bg-red-100" onclick="clearInput()">C</button>
                <button type="button" class="numpad-btn" onclick="appendInput('0')">0</button>
                <button type="button" class="numpad-btn text-gray-500 bg-gray-100 hover:bg-gray-200" onclick="backspace()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                    </svg>
                </button>
            </div>

            <button type="button" id="actionBtn" onclick="handleAction()" class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-blue-500/30 text-lg">
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
