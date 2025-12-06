<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

try {
    // Base query with product count
    $baseQuery = "SELECT
        brands.brandId,
        brands.brandName,
        brands.image AS brandIcon,
        COUNT(spareparts.sparepartId) AS productCount
    FROM
        brands
    LEFT JOIN
        spareparts ON spareparts.brandId = brands.brandId
    WHERE
        brands.active = '1'
    GROUP BY
        brands.brandId, brands.brandName, brands.image";

    // Execute query
    $res = $db->query($baseQuery);

    // Fetch data
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = [
            'brandName' => $row['brandName'],
            'brandIcon' => 'http://gmerp.local/testworkouts/brand-images/' . $row['brandIcon'], // Path or URL to the brand icon
            'productCount' => $row['productCount']
        ];
    }

    // Response format
    $response = [
        "data" => $data
    ];

    // Output JSON response
    echo json_encode($response);
} catch (Exception $e) {
    // Handle error
    echo json_encode([ 
        "error" => "An error occurred: " . $e->getMessage()
    ]);
}
