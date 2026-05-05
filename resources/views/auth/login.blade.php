<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – AquaSync</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f0f6fc;position:relative;overflow:hidden}
    body::before{content:'';position:fixed;top:-120px;left:-120px;width:500px;height:500px;background:radial-gradient(circle,rgba(29,111,164,.1) 0%,transparent 70%);pointer-events:none}
    body::after{content:'';position:fixed;bottom:-80px;right:-80px;width:400px;height:400px;background:radial-gradient(circle,rgba(15,41,66,.08) 0%,transparent 70%);pointer-events:none}

    .wrap{width:100%;max-width:420px;padding:20px;position:relative;z-index:1}

    .brand{text-align:center;margin-bottom:28px}
    .brand-logo{width:58px;height:58px;border-radius:16px;background:#0f2942;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.5rem;box-shadow:0 8px 24px rgba(15,41,66,.28)}
    .brand h1{font-size:1.55rem;font-weight:700;color:#1a2b3c;letter-spacing:-.4px}
    .brand p{font-size:.84rem;color:#6b7c93;margin-top:4px}

    .card{background:#fff;border-radius:16px;border:1px solid #d1dce8;padding:32px;box-shadow:0 4px 24px rgba(0,0,0,.07)}
    .card h2{font-size:1.05rem;font-weight:700;color:#1a2b3c;margin-bottom:4px}
    .card .sub{font-size:.84rem;color:#6b7c93;margin-bottom:24px}

    .role-tabs{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:22px}
    .role-tab{padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:9px;cursor:pointer;text-align:center;font-size:.82rem;font-weight:600;color:#6b7c93;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:6px;background:#fff;font-family:'DM Sans',sans-serif}
    .role-tab:hover{border-color:#1d6fa4;color:#1d6fa4}
    .role-tab.active{border-color:#1d6fa4;background:rgba(29,111,164,.07);color:#1d6fa4}

    .fg{margin-bottom:18px}
    .fg label{display:block;font-size:.79rem;font-weight:600;color:#1a2b3c;margin-bottom:7px}
    .iw{position:relative}
    .iico{position:absolute;left:13px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#6b7c93;pointer-events:none}
    .inp{width:100%;padding:11px 14px 11px 40px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:.875rem;font-family:'DM Sans',sans-serif;color:#1a2b3c;background:#fff;transition:border-color .15s,box-shadow .15s;outline:none}
    .inp:focus{border-color:#1d6fa4;box-shadow:0 0 0 3px rgba(29,111,164,.1)}
    .inp.err{border-color:#e63946}
    .pw-btn{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#6b7c93;padding:0}
    .pw-btn:hover{color:#1d6fa4}

    .cb-row{display:flex;align-items:center;gap:8px;margin-bottom:22px}
    .cb-row input{width:15px;height:15px;accent-color:#1d6fa4;cursor:pointer}
    .cb-row label{font-size:.84rem;color:#6b7c93;cursor:pointer;margin:0}

    .btn-login{width:100%;padding:13px;background:#1d6fa4;color:#fff;border:none;border-radius:9px;font-size:.9rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .15s,transform .1s,box-shadow .15s}
    .btn-login:hover{background:#155e8e;transform:translateY(-1px);box-shadow:0 4px 14px rgba(29,111,164,.32)}

    .alert-err{background:#fff5f5;border:1px solid #fecaca;border-radius:9px;padding:11px 15px;font-size:.84rem;color:#c53030;margin-bottom:18px;display:flex;align-items:center;gap:8px}

    .foot{text-align:center;margin-top:20px;font-size:.77rem;color:#6b7c93}
    </style>
</head>
<body>
<div class="wrap">
    <div class="brand">
        <div class="brand-logo">〜</div>
        <h1>AquaSync</h1>
        <p>Sistem Monitoring Kolam Lele Otomatis</p>
    </div>

    <div class="card">
        <h2>Masuk ke Sistem</h2>
        <p class="sub">Silakan login dengan akun yang terdaftar</p>

        @if($errors->any() || session('error'))
        <div class="alert-err">
            <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0;"></i>
            {{ $errors->first() ?: session('error') }}
        </div>
        @endif

        {{-- Role tabs --}}
        <div class="role-tabs">
            <button type="button" class="role-tab active" id="tab-admin" onclick="setRole('admin')">
                <i data-lucide="shield-check" style="width:14px;height:14px;"></i> Admin
            </button>
            <button type="button" class="role-tab" id="tab-owner" onclick="setRole('owner')">
                <i data-lucide="user" style="width:14px;height:14px;"></i> Owner
            </button>
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="fg">
                <label for="email">Email</label>
                <div class="iw">
                    <i data-lucide="mail" class="iico"></i>
                    <input type="email" id="email" name="email" class="inp @error('email') err @enderror"
                           value="{{ old('email','admin@aquasync.id') }}" placeholder="email@domain.com" required autofocus>
                </div>
            </div>

            <div class="fg">
                <label for="password">Password</label>
                <div class="iw">
                    <i data-lucide="lock" class="iico"></i>
                    <input type="password" id="password" name="password" class="inp" placeholder="••••••••" required>
                    <button type="button" class="pw-btn" onclick="togglePw()">
                        <i data-lucide="eye" id="pw-ico" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </div>

            <div class="cb-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn-login">Masuk ke AquaSync</button>
        </form>
    </div>

    <div class="foot">© 2026 AquaSync · CV Ranu Slawu · Jember</div>
</div>

<script>
lucide.createIcons();

function setRole(role) {
    document.getElementById('tab-admin').classList.toggle('active', role === 'admin');
    document.getElementById('tab-owner').classList.toggle('active', role === 'owner');
    document.getElementById('email').value = role === 'admin' ? 'admin@aquasync.id' : 'owner@aquasync.id';
    document.getElementById('password').focus();
}

function togglePw() {
    const inp = document.getElementById('password');
    const ico = document.getElementById('pw-ico');
    inp.type = inp.type === 'password' ? 'text' : 'password';
    ico.setAttribute('data-lucide', inp.type === 'password' ? 'eye' : 'eye-off');
    lucide.createIcons();
}
</script>
</body>
</html>