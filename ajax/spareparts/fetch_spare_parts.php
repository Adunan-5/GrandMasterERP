<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../includes/baseIncludes.php";

$filter = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_SPECIAL_CHARS);

try {
    // Query to fetch spareparts details with brandName and brandImage
    $query = "SELECT
        spareparts.sparepartId,
        spareparts.partNumber,
        spareparts.serialNumber,
        spareparts.internalReference,
        spareparts.name,
        spareparts.image,
        spareparts.description,
        spareparts.brandId,
        spareparts.leadTime,
        spareparts.origin,
        spareparts.salesPrice,
        spareparts.uomId,
        spareparts.cost,
        spareparts.hsCode,
        spareparts.hsPercentage,
        spareparts.publishedOnEcommerce,
        spareparts.notes,
        spareparts.active,
        spareparts.createdAt,
        spareparts.updatedAt,
        spareparts.createdBy,
        spareparts.updatedBy,
        brands.brandName,
        brands.image AS brandImage
    FROM
        spareparts
    LEFT JOIN
        brands ON spareparts.brandId = brands.brandId
    WHERE
        spareparts.active = '1' AND spareparts.isDeleted = 0";

    if (!empty($filter) && in_array($filter, ['SPAREPART', 'MACHINE'])) {
        $query .= " AND spareparts.itemType = ?s";
        $res = $db->query($query, $filter);
    } else {
        $res = $db->query($query);
    }

    // Execute query
    // $res = $db->query($query);

    // Fetch data
    $defaultImage = '/uploads/sparepart-images/no-image-placeholder.png';
    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $salesPrice = (float)$row['salesPrice'];
        $cost = (float)$row['cost'];
        $margin = $salesPrice > 0 ? round((($salesPrice - $cost) / $salesPrice) * 100, 2) : 0;

        $data[] = [
            'sparepartId' => $row['sparepartId'],
            'partNumber' => $row['partNumber'],
            'serialNumber' => $row['serialNumber'],
            'internalReference' => $row['internalReference'],
            'name' => $row['name'],
            'image' => !empty($row['image']) ? $row['image'] : $defaultImage, // Use default image if empty
            'description' => $row['description'],
            'brandId' => $row['brandId'],
            'brandName' => $row['brandName'],
            'brandIcon' => '/uploads/brand-images/' . $row['brandImage'], // Path or URL to the brand image
            'leadTime' => $row['leadTime'],
            'origin' => $row['origin'],
            'salesPrice' => $row['salesPrice'],
            'uomId' => $row['uomId'],
            'cost' => $row['cost'],
            'hsCode' => $row['hsCode'],
            'hsPercentage' => $row['hsPercentage'],
            'publishedOnEcommerce' => $row['publishedOnEcommerce'],
            'notes' => $row['notes'],
            'active' => $row['active'],
            'createdAt' => $row['createdAt'],
            'updatedAt' => $row['updatedAt'],
            'margin' => $margin .'%',
            'createdBy' => getDisplayNameFromUserID($row['createdBy']),
            'updatedBy' => getDisplayNameFromUserID($row['updatedBy']) ?? '-',
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
