<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RestartLabs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #0a0e27 50%, #1cd4c2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(10, 10, 10, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(28, 212, 194, 0.3);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            color: #1cd4c2;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #cccccc;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #ffffff;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(28, 212, 194, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #1cd4c2;
            background: rgba(255, 255, 255, 0.08);
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1cd4c2 0%, #0d9488 100%);
            border: none;
            border-radius: 8px;
            color: #000000;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(28, 212, 194, 0.4);
        }
        
        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-error {
            background: rgba(255, 68, 68, 0.2);
            border: 1px solid rgba(255, 68, 68, 0.5);
            color: #ff6b6b;
        }
        
        .alert-success {
            background: rgba(28, 212, 194, 0.2);
            border: 1px solid rgba(28, 212, 194, 0.5);
            color: #1cd4c2;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #1cd4c2;
        }
        
        .input-icon input {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
            <p>RestartLabs Control Center</p>
        </div>
        
        <div id="alert" class="alert"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
            </div>
            
            <button type="submit" class="btn-login" id="loginBtn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const alert = document.getElementById('alert');
            
            // Disable button
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert.className = 'alert alert-success';
                    alert.textContent = 'Login successful! Redirecting...';
                    alert.style.display = 'block';
                    
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    alert.className = 'alert alert-error';
                    alert.textContent = data.message || 'Invalid credentials';
                    alert.style.display = 'block';
                    
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
                }
            } catch (error) {
                alert.className = 'alert alert-error';
                alert.textContent = 'An error occurred. Please try again.';
                alert.style.display = 'block';
                
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            }
        });
    </script>
</body>
</html>

