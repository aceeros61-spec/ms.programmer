function confirmDelete(message) {
    if (confirm(message || "Are you sure you want to delete this item?")) {
        return true;
    } else {
        return false;
    }
}


function showAlert(message, type = 'success') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
   
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}


function searchFilter() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('productTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[2]; 
        if (td) {
            const txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}


function updateQuantity(cartId, quantity) {
    if (quantity < 1) {
        alert("Minimum quantity is 1");
        return false;
    }
    
    
    window.location.href = `cart.php?update=${cartId}&qty=${quantity}`;
}


function addToCartAnimation() {
    const btn = event.target;
    const originalText = btn.innerText;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        btn.innerText = 'Added!';
    }, 1000);
}

function printInvoice() {
    window.print();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("Kind Steps E-Commerce Loaded");
    
    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});