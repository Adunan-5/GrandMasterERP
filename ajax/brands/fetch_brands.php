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
    $defaultImage = '/uploads/brand-images/no-image-available-placeholder.png';
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $brandIcon = !empty($row['brandIcon']) ? '/uploads/brand-images/' . $row['brandIcon'] : $defaultImage;
        $data[] = [
            'brandId' => $row['brandId'],
            'brandName' => $row['brandName'],
            'brandIcon' => $brandIcon,
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