<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laureates</title>
</head>
<body>
    <h1>Laureates</h1>
    <div id="laureates"></div>
    <script>
        async function fetchLaureates() {
            try {
                const response = await fetch('/cv3/api/v0/laureates');
                const data = await response.json();
                console.log(data);
                const laureatesDiv = document.getElementById('laureates');
                data.forEach(laureate => {
                    const laureateDiv = document.createElement('div');
                    laureateDiv.textContent = `Name: ${laureate.fullname ?? laureate.organisation}, Gender: ${laureate.gender ?? "ORG"}, Birth: ${laureate.birth ?? "Not known"}, Death: ${laureate.death ?? "Still alive"} `;
                    laureatesDiv.appendChild(laureateDiv);
                });
            } catch (error) {
                console.error('Error fetching laureates:', error);
            }
        }

        fetchLaureates();
    </script>
</body>
</html>

