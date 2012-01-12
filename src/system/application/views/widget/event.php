<html>

<head>
    <style>
        body {
            background-color: #28569C;
            color: #FFFFFF;
            font-size: 11px;
        }
        a {
            color: #FFFFFF;
            text-decoration: none;
        }
        a:hover { text-decoration: underline; }
    </style>
</head>

<body>
    <a href="/event/view/<?php echo $event->ID; ?>"><?php echo $event->event_name; ?></a>
</body>

</html>
