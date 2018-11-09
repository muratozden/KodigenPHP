<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>500 Server Error</title>
    <meta name="author" content="https://github.com/kodigen/kodigenphp" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        html, body {
            margin: 20px;
            padding: 0;
            background: #fff;
            color: #333;
            font-family: sans-serif;
        }

        .box {
            width: 600px;
            margin: 0 auto;
            background: #f9f9f9;
            text-align: center;
            border: 1px solid #eee;
            border-radius: 2px;
            padding: 15px 0px;
        }

        @media only screen and (max-width: 600px) {
            .box {
                width: 100%;
                margin: 0;
            }
        }
    </style>
</head>
<body>
<div class="box">
    <h1>500 Server Error</h1>
    <p><?= isset($message) ? $message : "Sorry, we're having issues at this time. Please visit later."; ?></p>
</div>
</body>
</html>