<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // voorbeeld login check (pas aan naar jouw situatie)
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['is_admin'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'Onjuiste gebruikersnaam of wachtwoord.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin login</title>

<style>
:root{
  --green-dark:#658C6E;
  --green:#85A898;
  --green-light:#EFF9E8;
  --yellow:#DFCD80;
  --sand:#F5E2B0;
  --ink:#453E3E;

  --bg: var(--green-light);
  --surface:#ffffff;
  --border: rgba(69,62,62,.18);
  --shadow: 0 10px 24px rgba(69,62,62,.12);
  --radius:16px;

  --focus:#1d4ed8;
  --danger:#7a2e2e;
}

*{ box-sizing:border-box; }

body{
  margin:0;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;

  font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
  color:var(--ink);
  background:
    radial-gradient(900px 400px at 10% 0%, rgba(133,168,152,.35), transparent 60%),
    radial-gradient(800px 380px at 90% 0%, rgba(223,205,128,.35), transparent 55%),
    linear-gradient(180deg, var(--bg), #fff 70%);

  font-size:16px;
  line-height:1.6;
  font-weight:500;
  padding:16px;
}

/* =========================
   LOGIN CARD
   ========================= */
.login-card{
  width:100%;
  max-width:420px;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
  padding:22px 20px 24px;
}

.login-title{
  margin:0;
  font-size:26px;
  font-weight:800;
}

.login-subtitle{
  margin:8px 0 18px;
  font-size:15px;
  color:rgba(69,62,62,.8);
}

/* =========================
   FORM
   ========================= */
.form-group{
  margin-bottom:16px;
}

label{
  display:block;
  font-weight:800;
  margin-bottom:6px;
}

input[type="text"],
input[type="password"]{
  width:100%;
  padding:12px 14px;
  font-size:16px;
  font-weight:600;
  border-radius:12px;
  border:1.5px solid rgba(69,62,62,.22);
  background:#fff;
  color:var(--ink);
}

input::placeholder{
  color:rgba(69,62,62,.55);
}

input:focus-visible{
  outline:3px solid var(--focus);
  outline-offset:2px;
}

/* =========================
   BUTTON
   ========================= */
.btn{
  width:100%;
  display:inline-flex;
  justify-content:center;
  align-items:center;
  padding:14px 16px;
  font-size:15px;
  font-weight:900;
  border-radius:14px;
  border:1.5px solid rgba(101,140,110,.5);
  background:linear-gradient(
    180deg,
    rgba(101,140,110,.28),
    rgba(133,168,152,.28)
  );
  color:var(--ink);
  cursor:pointer;
  box-shadow:0 2px 0 rgba(69,62,62,.12);
  transition:.15s;
}

.btn:hover{
  background:linear-gradient(
    180deg,
    rgba(101,140,110,.35),
    rgba(133,168,152,.35)
  );
}

/* =========================
   ERROR
   ========================= */
.error{
  margin-bottom:16px;
  padding:14px;
  border-radius:14px;
  border:1px solid rgba(122,46,46,.35);
  background:rgba(122,46,46,.06);
  font-weight:700;
  display:flex;
  gap:10px;
  align-items:flex-start;
}

.error-icon{
  width:22px;
  height:22px;
  flex:0 0 auto;
  display:grid;
  place-items:center;
  border-radius:999px;
  border:1px solid rgba(122,46,46,.35);
  background:rgba(122,46,46,.12);
  font-size:13px;
}

/* =========================
   FOOTER LINK
   ========================= */
.login-footer{
  margin-top:18px;
  text-align:center;
  font-size:14px;
}

.login-footer a{
  color:var(--ink);
  font-weight:800;
  text-decoration:none;
}

.login-footer a:focus-visible{
  outline:3px solid var(--focus);
  outline-offset:2px;
  border-radius:8px;
}

/* =========================
   REDUCED MOTION
   ========================= */
@media (prefers-reduced-motion:reduce){
  *{ transition:none !important; }
}
</style>
</head>

<body>

<div class="login-card">
  <h1 class="login-title">Admin login</h1>
  <p class="login-subtitle">
    Log in om het beheerpaneel te openen.
  </p>

  <?php if ($error): ?>
    <div class="error" role="alert" aria-live="polite">
      <span class="error-icon">!</span>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label for="username">Gebruikersnaam</label>
      <input
        type="text"
        id="username"
        name="username"
        required
        autocomplete="username"
      >
    </div>

    <div class="form-group">
      <label for="password">Wachtwoord</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        autocomplete="current-password"
      >
    </div>

    <button type="submit" class="btn">Inloggen</button>
  </form>

  <div class="login-footer">
    <a href="../activiteitenKaart.php">← Terug naar website</a>
  </div>
</div>

</body>
</html>
