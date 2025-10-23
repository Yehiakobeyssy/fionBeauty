document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.book-workshop');
    const notifContainer = document.getElementById('notification-container');

    function showNotification(message, type='success') {
        const notif = document.createElement('div');
        notif.textContent = message;
        notif.style.padding = '10px 15px';
        notif.style.borderRadius = '6px';
        notif.style.minWidth = '200px';
        notif.style.color = '#fff';
        notif.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
        notif.style.opacity = '0';
        notif.style.transition = 'opacity 0.3s ease';
        
        // type: success / error / warning
        if (type === 'success') notif.style.backgroundColor = '#28A745';
        else if (type === 'error') notif.style.backgroundColor = '#D64545';
        else if (type === 'warning') notif.style.backgroundColor = '#FFA500';
        
        notifContainer.appendChild(notif);

        // show notification
        setTimeout(() => { notif.style.opacity = '1'; }, 10);

        // hide after 3 seconds
        setTimeout(() => {
            notif.style.opacity = '0';
            setTimeout(() => notif.remove(), 400);
        }, 3000);
    }

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const workshopId = this.getAttribute('data-id');

            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'workshop_id=' + workshopId
            })
            .then(response => response.text())
            .then(data => {
                // تحديد نوع الرسالة
                if (data.includes('added') || data.includes('already booked')) {
                    window.location.href = 'user/checkoutworkshop.php';
                } else {
                    alert(data); // في حال وجود خطأ
                }
            })
            .catch(error => {
                showNotification('Error adding workshop.', 'error');
                console.error(error);
            });
        });
    });
});
