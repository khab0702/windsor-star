<?php
// Include database connection file
require 'db-connec.php';

// Create (Insert) operation
if(isset($_POST['form'])) {
    // Retrieve form data
    $menu = $_POST['menu'];
    $size = isset($_POST['without_fries']) ? $_POST['without_fries'] : (isset($_POST['with_fries']) ? 'With Fries' : '');
    $fries_sauce = isset($_POST['chili']) || isset($_POST['tomato']) || isset($_POST['mayonnaise']) ? 'Yes' : 'No';
    $menu_sauce = isset($_POST['ss']) ? implode(', ', $_POST['ss']) : '';
    $addons = isset($_POST['ss']) ? count($_POST['ss']) : 0;
    $instructions = $_POST['instructions'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1; // Assuming default quantity is 1
    
    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("INSERT INTO orders_table (menu, size, fries_sauce, menu_sauce, addons, instructions, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisi", $menu, $size, $fries_sauce, $menu_sauce, $addons, $instructions, $quantity);
    
    if ($stmt->execute()) {
        echo "Order successfully placed.";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close the statement
    $stmt->close();
}

// Read operation
$sql = "SELECT * FROM orders_table";
$result = $conn->query($sql);

echo "<h2>Orders</h2>";
echo "<table border='1'>";
echo "<tr><th>Menu</th><th>Size</th><th>Fries Sauce</th><th>Menu Sauce</th><th>Addons</th><th>Instructions</th><th>Quantity</th><th>Actions</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row["menu"]."</td>";
        echo "<td>".$row["size"]."</td>";
        echo "<td>".$row["fries_sauce"]."</td>";
        echo "<td>".$row["menu_sauce"]."</td>";
        echo "<td>".$row["addons"]."</td>";
        echo "<td>".$row["instructions"]."</td>";
        echo "<td>".$row["quantity"]."</td>";
        echo "<td>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='id' value='".$row["id"]."'>";
        echo "<input type='hidden' name='action' value='update'>";
        echo "<input type='submit' value='Update'>";
        echo "</form>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='id' value='".$row["id"]."'>";
        echo "<input type='hidden' name='action' value='delete'>";
        echo "<input type='submit' value='Delete'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>0 results</td></tr>";
}
echo "</table>";

// Update operation
if(isset($_POST['action']) && $_POST['action'] == 'update') {
    // Retrieve the order ID
    $id = $_POST['id'];
    
    // Fetch the order details
    $stmt = $conn->prepare("SELECT * FROM orders_table WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    // Display the update form
    echo "<h2>Update Order</h2>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='id' value='".$order["id"]."'>";
    echo "Menu: <input type='text' name='menu' value='".$order["menu"]."'><br>";
    echo "Size: <input type='text' name='size' value='".$order["size"]."'><br>";
    echo "Fries Sauce: <input type='text' name='fries_sauce' value='".$order["fries_sauce"]."'><br>";
    echo "Menu Sauce: <input type='text' name='menu_sauce' value='".$order["menu_sauce"]."'><br>";
    echo "Addons: <input type='text' name='addons' value='".$order["addons"]."'><br>";
    echo "Instructions: <input type='text' name='instructions' value='".$order["instructions"]."'><br>";
    echo "Quantity: <input type='number' name='quantity' value='".$order["quantity"]."'><br>";
    echo "<input type='submit' name='update' value='Update'>";
    echo "</form>";
}

// Update operation (continued)
if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $menu = $_POST['menu'];
    $size = $_POST['size'];
    $fries_sauce = $_POST['fries_sauce'];
    $menu_sauce = $_POST['menu_sauce'];
    $addons = $_POST['addons'];
    $instructions = $_POST['instructions'];
    $quantity = $_POST['quantity'];
    
    // Update the order
    $stmt = $conn->prepare("UPDATE orders_table SET menu=?, size=?, fries_sauce=?, menu_sauce=?, addons=?, instructions=?, quantity=? WHERE id=?");
    $stmt->bind_param("ssssisii", $menu, $size, $fries_sauce, $menu_sauce, $addons, $instructions, $quantity, $id);
    
    if ($stmt->execute()) {
        echo "Order successfully updated.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Delete operation
if(isset($_POST['action']) && $_POST['action'] == 'delete') {
    // Retrieve the order ID
    $id = $_POST['id'];
    
    // Delete the order
    $stmt = $conn->prepare("DELETE FROM orders_table WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Order successfully deleted.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Close the database connection
$conn->close();
?>
