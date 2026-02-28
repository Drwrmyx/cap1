/**
 * Opens the Stock Update Modal and populates it with existing product data.
 */
function openUpdateModal(data) {
    document.getElementById('up_id').value = data.id;
    document.getElementById('up_name').value = data.name;
    document.getElementById('up_code').value = data.item_code;
    document.getElementById('up_stock').value = data.stock;
    document.getElementById('updateModal').style.display = "block";
}

/**
 * Opens the Edit Item Modal and populates it with existing product data.
 */
function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_code').value = data.item_code;
    document.getElementById('edit_price').value = data.price;
    document.getElementById('edit_desc').value = data.description;
    document.getElementById('editModal').style.display = "block";
}

/**
 * Closes all active modals.
 */
function closeModals() {
    document.getElementById('updateModal').style.display = "none";
    document.getElementById('editModal').style.display = "none";
}

/**
 * Closes the modal if the user clicks anywhere outside of the modal content box.
 */
window.onclick = function(e) { 
    if(e.target.className === 'modal') { 
        closeModals();
    } 
}

// Add this to your script.js to catch errors before submission
document.addEventListener('input', function (e) {
    if (e.target.name === 'price' || e.target.name === 'new_qty') {
        if (e.target.value < 0) {
            e.target.style.borderColor = "red";
            // Optional: alert("Value cannot be negative");
        } else {
            e.target.style.borderColor = "#ccc";
        }
    }
});