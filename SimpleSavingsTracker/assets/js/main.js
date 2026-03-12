document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        if (!input.value) {
            const today = new Date().toISOString().split('T')[0];
            input.value = today;
        }
    });

    const expenseForm = document.querySelector('.expense-form');
    if (expenseForm) {
        expenseForm.addEventListener('submit', function(e) {
            const amountInput = this.querySelector('input[name="amount"]');
            const amount = parseFloat(amountInput.value);
            
            if (isNaN(amount) || amount <= 0) {
                alert('Please enter a valid positive amount');
                e.preventDefault();
                amountInput.focus();
                return false;
            }
            return true;
        });
    }

    const progressBars = document.querySelectorAll('progress');
    progressBars.forEach(bar => {
        const value = parseFloat(bar.value);
        const max = parseFloat(bar.max);
        
        bar.value = 0;
        
        setTimeout(() => {
            bar.value = value;
        }, 100);
    });

    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this?')) {
                e.preventDefault();
            }
        });
    });
});