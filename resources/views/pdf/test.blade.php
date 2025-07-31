<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <title>Test PDF</title>
<link rel="stylesheet" href="file:///{{ str_replace('\\', '/', public_path('css/pdf-pattern.css')) }}" />

</head>
<body>
    <h1>Ez egy teszt PDF</h1>
    <p>Próbáljuk meg a képet helyesen megjeleníteni:</p>
  <img src="file:///{{ str_replace('\\', '/', public_path('storage/uploads/images/peldakep.jpg')) }}" />

</body>
</html>
