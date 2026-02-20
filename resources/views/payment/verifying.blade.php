<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting - Quizly</title>
    <script>
        // Legacy redirect — callback handles everything now
        const params = window.location.search;
        window.location.href = '/payment/callback' + params;
    </script>
</head>
<body></body>
</html>
