<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />

<style>
  body {
    font-size: 9px;
    color: #666;
    margin: 0;
    padding: 0 20mm;
    font-family: 'DejaVu Sans', sans-serif;
  }
  .footer {
    width: 100%;
    position: relative;
  }
  .page-number {
    position: absolute;
    top: 0;
    font-weight: bold;
  }
  /* Páros oldalak - balra */
  .even .page-number {
    left: 0;
  }
  /* Páratlan oldalak - jobbra */
  .odd .page-number {
    right: 0;
  }
</style>
</head>
<body>
  <div class="footer">
    <div class="footer-content" style="text-align:center;">
      © {{ date('Y') }} Szántó Gizella - Amigurumi Minta
    </div>
    <div class="page-number">
      <span class="page"></span> / <span class="topage"></span>
    </div>
  </div>
</body>
</html>
