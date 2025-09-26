<?php
$servername = "192.168.1.245:3307";
$username   = "qrhandloom";
$password   = "qrhandloom";
$database   = "qrhandloom";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}


if (isset($_FILES['logfile']) && $_FILES['logfile']['error'] === UPLOAD_ERR_OK) {
    $filename = $_FILES['logfile']['tmp_name'];
    $destination = "logs/" . basename($_FILES['logfile']['name']);
    $destinationDir = __DIR__ . "/logs/";

    // if logs folder not exist, create it
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    $destination = $destinationDir . basename($_FILES['logfile']['name']);

    if (move_uploaded_file($filename, $destination)) {
        echo "<h3>✅ File uploaded successfully!</h3>";

        $handle = fopen($destination, "r");
        if ($handle) {
            $line_no = 0;
            $successCount = 0;
            $errorLogs = [];

            while (($line = fgets($handle)) !== false) {
                $line_no++;
                $line = trim($line);

                if (strpos($line, "query :") !== false) {
                    $parts = explode("query :", $line, 2);
                    $jsonStr = trim($parts[1]);

                    $jsonData = json_decode($jsonStr, true);

                    if ($jsonData === null) {
                        $errorLogs[] = "❌ Line $line_no invalid JSON";
                        continue;
                    }

                    if (isset($jsonData[0])) {
                        $jsonData = $jsonData[0];
                    }
                    $type = $jsonData['_id'] ?? null; // safe check

                    if ($type === 'organization') {
                        // INSERT INTO organization_logs
                        $sql = "INSERT INTO organization_logs
                            (organizationid, state, district, department, city, type, organization_name, isactive, createdate, updatedate)
                            VALUES (
                                '" . mysqli_real_escape_string($conn, $jsonData['organizationid'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['state'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['district'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['department'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['city'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['type'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['organization_name'] ?? '') . "',
                                " . (int)($jsonData['isactive'] ?? 0) . ",
                                " . (int)($jsonData['createdate'] ?? 0) . ",
                                " . (int)($jsonData['updatedate'] ?? 0) . "
                            )";
                    } elseif ($type === 'usermaster') {
                        // INSERT INTO usermaster_logs
                        $sql = "INSERT INTO usermaster_logs
                            (userid, state, district, department, city, role, weaverid, firstname, lastname, gender, dob, contactno, type, organization, password, userstatus, kyc_type, first_login, isdeleted, kyc_id, createdate, updatedate, approved_time, approvedby, email)
                            VALUES (
                                '" . mysqli_real_escape_string($conn, $jsonData['userid'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['state'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['district'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['department'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['city'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['role'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['weaverid'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['firstname'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['lastname'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['gender'] ?? '') . "',
                                " . (int)($jsonData['dob'] ?? 0) . ",
                                '" . mysqli_real_escape_string($conn, $jsonData['contactno'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['type'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['organization'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['password'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['userstatus'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['kyc_type'] ?? '') . "',
                                " . (int)($jsonData['first_login'] ?? 0) . ",
                                '" . mysqli_real_escape_string($conn, $jsonData['isdeleted'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['kyc_id'] ?? '') . "',
                                " . (int)($jsonData['createdate'] ?? 0) . ",
                                " . (int)($jsonData['updatedate'] ?? 0) . ",
                                " . (int)($jsonData['approved_time'] ?? 0) . ",
                                '" . mysqli_real_escape_string($conn, $jsonData['approvedby'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['email'] ?? '') . "'
                            )";
                    } elseif ($type === 'weaverdetailsmaster') {
                        // Insert into weaverdetailsmaster_logs
                        $sql = "INSERT INTO weaverdetailsmaster_logs
            (weaverid, state, district, department, city, role, weavername, gender, dob, contactno, type, organization, isactive, KYC_type, KYC_id, createdate, updatedate, email)
            VALUES (
                '" . mysqli_real_escape_string($conn, $jsonData['weaverid'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['state'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['district'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['department'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['city'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['role'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['weavername'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['gender'] ?? '') . "',
                " . (int)($jsonData['dob'] ?? 0) . ",
                '" . mysqli_real_escape_string($conn, $jsonData['contactno'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['type'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['organization'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['isactive'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['KYC_type'] ?? '') . "',
                '" . mysqli_real_escape_string($conn, $jsonData['KYC_id'] ?? '') . "',
                " . (int)($jsonData['createdate'] ?? 0) . ",
                " . (int)($jsonData['updatedate'] ?? 0) . ",
                '" . mysqli_real_escape_string($conn, $jsonData['email'] ?? '') . "'
            )";
                    }
                    if ($type === 'productdetailsmaster') {
                        $sql = "INSERT INTO productdetailsmaster_logs
                            (id, prddetailsid, productname, imagehash, loomtype, dimension, status, state, district, city, image, latitude, longitude, type, startdate, createddate, updateddate, qrcodevalue, yarntype, nature_dye, video, qrhash, qrimage, department, videohash, weavername, organization, dyestatus, devicecode, createdby, yarncount, weavetype)
                            VALUES (
                                " . (int)($jsonData['_id'] ?? 0) . ",
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/prddetailsid'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/productname'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/imagehash'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/loomtype'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/dimension'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/status'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/state'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/district'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/city'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/image'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/latitude'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/longitude'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/type'] ?? '') . "',
                                FROM_UNIXTIME(" . ((int)($jsonData['productdetailsmaster/startdate'] ?? 0)/1000) . "),
                                FROM_UNIXTIME(" . ((int)($jsonData['productdetailsmaster/createddate'] ?? 0)/1000) . "),
                                FROM_UNIXTIME(" . ((int)($jsonData['productdetailsmaster/updateddate'] ?? 0)/1000) . "),
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/qrcodevalue'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/yarntype'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/nature_dye'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/video'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/qrhash'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/qrimage'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/department'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/videohash'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/weavername'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/organization'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/dyestatus'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/devicecode'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/createdby'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/yarncount'] ?? '') . "',
                                '" . mysqli_real_escape_string($conn, $jsonData['productdetailsmaster/weavetype'] ?? '') . "'
                            )";
                            }
                    
                    else {
                        $errorLogs[] = "❌ Line $line_no unknown JSON type: " . htmlspecialchars($jsonData['_id'] ?? 'N/A');
                        continue;
                    }

                    if (mysqli_query($conn, $sql)) {
                        $successCount++;
                    } else {
                        $errorLogs[] = "❌ Line $line_no insert failed: " . mysqli_error($conn);
                    }
                }
            }

            fclose($handle);

            echo "<p>Inserted <b>$successCount</b> logs successfully.</p>";
            if (!empty($errorLogs)) {
                echo "<h4>Errors:</h4><ul>";
                foreach ($errorLogs as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";
            }
        }
    } else {
        echo "❌ File upload failed!";
    }
}
