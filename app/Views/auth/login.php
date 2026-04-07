<!doctype html>
<html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login PLPI</title></head>
<body style="font-family:Inter,Arial,sans-serif;background:#f4f7fb;display:grid;place-items:center;min-height:100vh">
<form method="post" action="<?= site_url('login') ?>" style="background:#fff;border:1px solid #dbe4ef;border-radius:14px;padding:20px;min-width:300px">
  <h3 style="margin-top:0">Login PLPI</h3>
  <?php if (session('error')): ?><p style="color:#b91c1c"><?= esc(session('error')) ?></p><?php endif; ?>
  <label>Username</label><br><input type="text" name="username" style="width:100%;padding:8px;margin:6px 0 10px"><br>
  <label>Password</label><br><input type="password" name="password" style="width:100%;padding:8px;margin:6px 0 14px"><br>
  <button type="submit" style="background:#123c6b;color:#fff;border:0;padding:10px 12px;border-radius:10px;width:100%">Masuk</button>
</form>
</body></html>
