<?php
session_start();
require("config.php");

// Check if property ID is provided
if(!isset($_GET['pid']) || empty($_GET['pid'])) {
    die("Property ID is required");
}

$property_id = intval($_GET['pid']);

// Get property details with owner and buyer information
$query = "
    SELECT 
        p.*,
        owner.uname as owner_name,
        owner.uemail as owner_email,
        owner.uphone as owner_phone,
        buyer.uname as buyer_name,
        buyer.uemail as buyer_email,
        buyer.uphone as buyer_phone,
        pur.purchase_date,
        pur.purchase_price,
        pur.payment_status,
        pur.notes as purchase_notes
    FROM property p
    LEFT JOIN user owner ON p.uid = owner.uid
    LEFT JOIN purchases pur ON p.pid = pur.property_id
    LEFT JOIN user buyer ON pur.buyer_id = buyer.uid
    WHERE p.pid = ?
";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if(!$property) {
    die("Property not found");
}

// Create PDF using basic HTML and CSS
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: attachment; filename="property_invoice_' . str_pad($property_id, 6, '0', STR_PAD_LEFT) . '.html"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Company details
$company_name = "Ventura Real Estate";
$company_address = "123 Real Estate Avenue, Nairobi, Kenya";
$company_phone = "+254 700 123 456";
$company_email = "info@venturarealestate.co.ke";

// Invoice details
$invoice_number = "INV-" . date('Y') . "-" . str_pad($property_id, 6, '0', STR_PAD_LEFT);
$invoice_date = date('F j, Y');
$due_date = date('F j, Y', strtotime('+30 days'));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Property Invoice #<?php echo str_pad($property_id, 6, '0', STR_PAD_LEFT); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .invoice-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #007bff;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-header clearfix">
        <div class="company-info">
            <h1 style="color: #007bff; margin: 0;">PROPERTY INVOICE</h1>
            <div><strong><?php echo htmlspecialchars($company_name); ?></strong></div>
            <div><?php echo htmlspecialchars($company_address); ?></div>
            <div>Phone: <?php echo htmlspecialchars($company_phone); ?></div>
            <div>Email: <?php echo htmlspecialchars($company_email); ?></div>
        </div>
        <div class="invoice-info">
            <div><strong>Invoice #:</strong> <?php echo htmlspecialchars($invoice_number); ?></div>
            <div><strong>Date:</strong> <?php echo htmlspecialchars($invoice_date); ?></div>
            <div><strong>Due Date:</strong> <?php echo htmlspecialchars($due_date); ?></div>
        </div>
    </div>

    <div class="section-title">Property Information</div>
    <table>
        <tr>
            <th>Property ID</th>
            <td>#<?php echo str_pad($property_id, 6, '0', STR_PAD_LEFT); ?></td>
            <th>Title</th>
            <td><?php echo htmlspecialchars($property['title']); ?></td>
        </tr>
        <tr>
            <th>Type</th>
            <td><?php echo htmlspecialchars($property['type']); ?></td>
            <th>BHK</th>
            <td><?php echo htmlspecialchars($property['bhk']); ?></td>
        </tr>
        <tr>
            <th>Location</th>
            <td><?php echo htmlspecialchars($property['location']) . ', ' . htmlspecialchars($property['city']) . ', ' . htmlspecialchars($property['state']); ?></td>
            <th>Area</th>
            <td><?php echo htmlspecialchars($property['size']); ?> sq ft</td>
        </tr>
        <tr>
            <th>Bedrooms</th>
            <td><?php echo htmlspecialchars($property['bedroom']); ?></td>
            <th>Bathrooms</th>
            <td><?php echo htmlspecialchars($property['bathroom']); ?></td>
        </tr>
        <tr>
            <th>Floor</th>
            <td><?php echo htmlspecialchars($property['floor']) . '/' . htmlspecialchars($property['totalfloor']); ?></td>
            <th>Status</th>
            <td><?php echo ucfirst($property['status']); ?></td>
        </tr>
    </table>

    <div class="section-title">Owner Information</div>
    <table>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($property['owner_name']); ?></td>
            <th>Email</th>
            <td><?php echo htmlspecialchars($property['owner_email']); ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo htmlspecialchars($property['owner_phone']); ?></td>
            <th>Property Listed</th>
            <td><?php echo date('F j, Y', strtotime($property['date'])); ?></td>
        </tr>
    </table>

    <?php if($property['purchase_status'] == 'sold' && $property['buyer_name']): ?>
    <div class="section-title">Buyer Information</div>
    <table>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($property['buyer_name']); ?></td>
            <th>Email</th>
            <td><?php echo htmlspecialchars($property['buyer_email']); ?></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><?php echo htmlspecialchars($property['buyer_phone']); ?></td>
            <th>Purchase Date</th>
            <td><?php echo date('F j, Y', strtotime($property['purchase_date'])); ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <div class="section-title">Pricing Details</div>
    <table>
        <tr>
            <th>Property Price</th>
            <td>Ksh <?php echo number_format($property['price'], 2); ?></td>
        </tr>
        <?php if($property['purchase_status'] == 'sold' && $property['purchase_price']): ?>
        <tr>
            <th>Sold Price</th>
            <td>Ksh <?php echo number_format($property['purchase_price'], 2); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="total-row">
            <th>Total Amount</th>
            <td>Ksh <?php echo number_format($property['purchase_status'] == 'sold' ? $property['purchase_price'] : $property['price'], 2); ?></td>
        </tr>
    </table>

    <?php if($property['purchase_status'] == 'sold'): ?>
    <div class="section-title">Payment Information</div>
    <table>
        <tr>
            <th>Payment Status</th>
            <td><?php echo ucfirst($property['payment_status']); ?></td>
        </tr>
        <?php if($property['purchase_notes']): ?>
        <tr>
            <th>Notes</th>
            <td><?php echo htmlspecialchars($property['purchase_notes']); ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <div class="section-title">Terms and Conditions</div>
    <div style="font-size: 12px; line-height: 1.4;">
        <p>1. This invoice is generated for property record purposes.</p>
        <p>2. All property details are as per the listing information provided by the owner.</p>
        <p>3. For sold properties, buyer information is based on completed purchase transactions.</p>
        <p>4. Payment terms and conditions apply as per the purchase agreement.</p>
        <p>5. For any queries regarding this invoice, please contact our support team.</p>
    </div>

    <div class="footer">
        <p><strong>Contact Information:</strong></p>
        <p>Modiac Housing</p>
        <p>123 Real Estate Avenue, Nairobi, Kenya</p>
        <p>Phone: +254 700 123 456 | Email: info@modiachousing.co.ke</p>
        <p><em>This is a computer-generated invoice. No signature required.</em></p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Invoice</button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>



<edit_file>
<path>admin/propertyview.php</path>
<content>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Property Invoice #' . str_pad($property_id, 6, '0', STR_PAD_LEFT) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .invoice-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 2px solid #007bff;
            margin-bottom: 20px;
            overflow: hidden;
        }
        .company-info {
            float: left;
            width: 50%;
        }
        .invoice-info {
            float: right;
            width: 50%;
            text-align: right;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
