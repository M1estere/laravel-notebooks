<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swagger Documentation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.15.5/swagger-ui.css" />
    <style>
        html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.15.5/swagger-ui-bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/4.15.5/swagger-ui-standalone-preset.js"></script>
    <script>
        const swaggerJson = {!! json_encode($swaggerJson) !!};

        window.onload = function() {
            const ui = SwaggerUIBundle({
                spec: swaggerJson,
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIStandalonePreset,
                    SwaggerUIBundle.presets.apis,
                ],
                layout: "StandaloneLayout",
            });

            window.ui = ui;
        };
    </script>
</body>
</html>
