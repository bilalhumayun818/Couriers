<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · CX Courier</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:24px;background:#080f1e;color:#f1f5f9;font-family:system-ui,sans-serif;background-image:radial-gradient(ellipse at top left,#12334b,transparent 65%)}
        .shell{width:min(960px,100%);display:grid;grid-template-columns:1fr 1fr;border:1px solid #26364c;border-radius:24px;overflow:hidden;box-shadow:0 28px 90px #0005}.intro{padding:52px 40px;background:linear-gradient(180deg,#0b2539bb,#081321f5),url('{{ asset('images/van_hero_bg.png') }}') center/cover;display:flex;flex-direction:column;justify-content:space-between;min-height:530px}.brand{font-size:15px;letter-spacing:3px;font-weight:800;color:#7dd3fc}.intro h1{font-size:40px;line-height:1.15;letter-spacing:-1.5px;margin:32px 0 18px}.intro p{color:#bacbda;line-height:1.7;font-size:14px}.panel{padding:52px 40px;background:#101b2c}.eyebrow{color:#7dd3fc;font-size:12px;letter-spacing:2px;text-transform:uppercase}h2{font-size:28px;margin:12px 0}.muted{font-size:14px;color:#9eafc3;line-height:1.6}form{margin-top:30px}label{display:block;font-size:13px;font-weight:600;margin:20px 0 8px}input{width:100%;padding:13px 14px;border:1px solid #35465e;border-radius:9px;background:#0a1423;color:#fff;font:inherit}input:focus{outline:2px solid #38bdf8;outline-offset:2px}.password{position:relative}.password input{padding-right:68px}.toggle{position:absolute;right:8px;top:7px;background:none;border:0;color:#7dd3fc;padding:8px;cursor:pointer}.submit{width:100%;margin:26px 0 10px;padding:14px;border:0;border-radius:9px;background:#38bdf8;color:#062237;font-weight:750;font-size:15px;cursor:pointer}.submit:hover{background:#7dd3fc}button:focus-visible,a:focus-visible{outline:2px solid #7dd3fc;outline-offset:4px}.error{padding:12px;border:1px solid #f8717170;background:#7f1d1d40;border-radius:8px;color:#fecaca;font-size:13px}.foot{font-size:12px;color:#93a8bf;line-height:1.6}.demo{display:block;margin-top:24px;color:#7dd3fc;font-size:13px;text-decoration:none}
        @media(max-width:680px){.shell{grid-template-columns:1fr}.intro{min-height:auto;padding:28px}.intro h1{font-size:28px;margin:20px 0 10px}.intro p{margin-bottom:0}.panel{padding:28px}}
    </style>
</head>
<body>
<main class="shell">
    <section class="intro" aria-label="CX Courier">
        <div class="brand">CX COURIER</div>
        <div><h1>Your fleet.<br>Your business.<br>One workspace.</h1><p>Manage vehicles, deliveries, people, and finances from your courier workspace.</p></div>
        <p>Fleet management &amp; courier operations</p>
    </section>
    <section class="panel">
        <div class="eyebrow">Welcome back</div>
        <h2>Sign in to your account</h2>
        <p class="muted">Enter your credentials to access your workspace.</p>
        @if($errors->any())<div class="error" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus maxlength="255" placeholder="you@company.com">
            <label for="password">Password</label>
            <div class="password">
                <input id="password" name="password" type="password" autocomplete="current-password" required maxlength="255">
                <button class="toggle" type="button" aria-label="Show password" aria-pressed="false" onclick="const p=document.getElementById('password');const show=p.type==='password';p.type=show?'text':'password';this.textContent=show?'Hide':'Show';this.setAttribute('aria-pressed',show);this.setAttribute('aria-label',show?'Hide password':'Show password')">Show</button>
            </div>
            <button class="submit" type="submit">Sign in &rarr;</button>
            <p class="foot">Stay signed in for 30 days. Each visit renews this period. Sign out when using a shared device.</p>
        </form>
        <a class="demo" href="{{ route('demo.dashboard') }}">Explore the demo &rarr;</a>
    </section>
</main>
</body>
</html>
