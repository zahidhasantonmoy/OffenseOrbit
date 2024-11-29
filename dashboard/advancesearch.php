<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "sql302.infinityfree.com";
$username = "if0_37729401";
$password = "xOhEjKEk4uwo6AX";
$database = "if0_37729401_crms";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Advanced Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['advance_search'])) {
    $search_query = htmlspecialchars($_POST['search_query']);
    $query = $conn->prepare("
        SELECT * FROM criminal_data 
        WHERE 
            name LIKE ? OR 
            nid_no LIKE ? OR 
            phone_no LIKE ? OR 
            home_district LIKE ? OR 
            crime_format LIKE ? OR 
            case_id LIKE ?
    ");

    if (!$query) {
        die(json_encode(['error' => "SQL Error: " . $conn->error]));
    }

    $search_term = '%' . $search_query . '%';
    $query->bind_param("ssssss", $search_term, $search_term, $search_term, $search_term, $search_term, $search_term);
    $query->execute();
    $result = $query->get_result();

    if ($result) {
        $criminals = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($criminals);
    } else {
        echo json_encode(['error' => "Query Execution Error: " . $conn->error]);
    }

    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Search</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: linear-gradient(135deg, #ff9a9e, #fad0c4, #fbc2eb, #a18cd1, #fad0c4);
            background-size: 300% 300%;
            animation: gradientShift 10s ease infinite;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5rem;
            color: #333;
        }

        .search-section {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .search-section form {
            display: flex;
            gap: 10px;
            width: 100%;
            justify-content: center;
        }

        .search-section input {
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-section button {
            padding: 10px 20px;
            background-color: #5e60ce;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-section button:hover {
            background-color: #3a0ca3;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th, .results-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .results-table th {
            background-color: #5e60ce;
            color: #fff;
        }

        .no-results {
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            margin-top: 20px;
        }

        .homepage-link {
            text-align: center;
            margin-top: 20px;
        }

        .homepage-link a {
            color: #5e60ce;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .homepage-link a:hover {
            color: #3a0ca3;
        }

        .results-table img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }
          /* Footer notification */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #5e60ce;
            color: white;
            text-align: center;
            padding: 10px 0;
            animation: slideUp 2s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Advanced Search</h1>
            <p>Search criminal records by name, NID, phone, and more.</p>
        </div>
        <div class="search-section">
            <form id="advanceSearchForm">
                <input type="text" id="searchQuery" name="search_query" placeholder="Enter search query...">
                <button type="submit">Search</button>
            </form>
        </div>
        <div id="resultsContainer">
            <table class="results-table" id="resultsTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>NID</th>
                        <th>Phone</th>
                        <th>Home District</th>
                        <th>Crime Format</th>
                        <th>Crime Count</th>
                        <th>Wanted Status</th>
                        <th>Case ID</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Results will be dynamically added here -->
                </tbody>
            </table>
            <div class="no-results" id="noResults" style="display: none;">No matching records found.</div>
        </div>
        <div class="homepage-link">
            <a href="/index.php">Return to Homepage</a>
        </div>
    </div>
 <!-- Footer notification -->
    <div class="footer">
       <b><h1> Advanced Image Search is Coming Soon</h1></b>
    </div>

    <script>
        document.getElementById("advanceSearchForm").addEventListener("submit", function (e) {
            e.preventDefault();
            performSearch();
        });

        function performSearch() {
            const query = document.getElementById("searchQuery").value.trim();
            if (!query) {
                alert("Please enter a search query.");
                return;
            }

            axios.post("", new URLSearchParams({ advance_search: true, search_query: query }))
                .then(response => {
                    const results = response.data;
                    const resultsTable = document.getElementById("resultsTable").querySelector("tbody");
                    const noResults = document.getElementById("noResults");

                    resultsTable.innerHTML = ""; // Clear previous results
                    if (results.length > 0 && !results.error) {
                        results.forEach(criminal => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td><img src="${criminal.image_path || 'placeholder.jpg'}" alt="Criminal Image"></td>
                                <td>${criminal.id}</td>
                                <td>${criminal.name}</td>
                                <td>${criminal.nid_no}</td>
                                <td>${criminal.phone_no}</td>
                                <td>${criminal.home_district}</td>
                                <td>${criminal.crime_format}</td>
                                <td>${criminal.crime_count}</td>
                                <td>${criminal.wanted_status}</td>
                                <td>${criminal.case_id}</td>
                            `;
                            resultsTable.appendChild(row);
                        });
                        noResults.style.display = "none";
                    } else if (results.error) {
                        alert("Error: " + results.error);
                    } else {
                        noResults.style.display = "block";
                    }
                })
                .catch(error => {
                    console.error("Error performing search:", error);
                    alert("An error occurred while performing the search.");
                });
        }
    </script>
</body>
</html>
